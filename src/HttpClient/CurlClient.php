<?php

namespace Stax\HttpClient;

use Stax\Exception;
// @codingStandardsIgnoreStart
// PSR2 requires all constants be upper case. Sadly, the CURL_SSLVERSION
// constants do not abide by those rules.

// Note the values come from their position in the enums that
// defines them in cURL's source code.

// @codingStandardsIgnoreEnd

// Available since PHP 7.0.7 and cURL 7.47.0
use Stax\Stax;

if (!\defined('CURL_HTTP_VERSION_2TLS')) {
	\define('CURL_HTTP_VERSION_2TLS', 4);
}

class CurlClient implements ClientInterface
{
	// USER DEFINED TIMEOUTS

	public const DEFAULT_TIMEOUT = 80;
	public const DEFAULT_CONNECT_TIMEOUT = 30;

	private static ?CurlClient $instance = null;

	protected $defaultOptions;

	protected array $userAgentInfo;

	protected bool $enablePersistentConnections = true;

	protected bool $enableHttp2;

	protected $curlHandle;

	protected $requestStatusCallback;

	private int $timeout = self::DEFAULT_TIMEOUT;

	private int $connectTimeout = self::DEFAULT_CONNECT_TIMEOUT;

	/**
	 * CurlClient constructor.
	 *
	 * Pass in a callable to $defaultOptions that returns an array of CURLOPT_* values to start
	 * off a request with, or an flat array with the same format used by curl_setopt_array() to
	 * provide a static set of options. Note that many options are overridden later in the request
	 * call, including timeouts, which can be set via setTimeout() and setConnectTimeout().
	 *
	 * Note that request() will silently ignore a non-callable, non-array $defaultOptions, and will
	 * throw an exception if $defaultOptions returns a non-array value.
	 *
	 * @param array|callable|null $defaultOptions
	 */
	public function __construct($defaultOptions = null)
	{
		$this->defaultOptions = $defaultOptions;
		$this->initUserAgentInfo();

		$this->enableHttp2 = $this->canSafelyUseHttp2();
	}

	public function __destruct()
	{
		$this->closeCurlHandle();
	}

	public static function instance(): self
	{
		if (!self::$instance) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function initUserAgentInfo()
	{
		$curlVersion = \curl_version();
		$this->userAgentInfo = [
			'httplib' => 'curl ' . $curlVersion['version'],
			'ssllib'  => $curlVersion['ssl_version'],
		];
	}

	public function getDefaultOptions()
	{
		return $this->defaultOptions;
	}

	public function getUserAgentInfo(): array
	{
		return $this->userAgentInfo;
	}

	public function getEnablePersistentConnections(): bool
	{
		return $this->enablePersistentConnections;
	}

	public function setEnablePersistentConnections(bool $enable): void
	{
		$this->enablePersistentConnections = $enable;
	}

	public function getEnableHttp2(): bool
	{
		return $this->enableHttp2;
	}

	public function setEnableHttp2(bool $enable): void
	{
		$this->enableHttp2 = $enable;
	}

	public function getRequestStatusCallback(): ?callable
	{
		return $this->requestStatusCallback;
	}

	/**
	 * Sets a callback that is called after each request. The callback will
	 * receive the following parameters:
	 * <ol>
	 *   <li>string $rbody The response body</li>
	 *   <li>integer $rcode The response status code</li>
	 *   <li>array $rheaders The response headers</li>
	 *   <li>integer $errno The curl error number</li>
	 *   <li>string|null $message The curl error message</li>
	 *   <li>boolean $shouldRetry Whether the request will be retried</li>
	 *   <li>integer $numRetries The number of the retry attempt</li>
	 * </ol>.
	 */
	public function setRequestStatusCallback(?callable $requestStatusCallback): void
	{
		$this->requestStatusCallback = $requestStatusCallback;
	}

	public function setTimeout($seconds): self
	{
		$this->timeout = (int) \max($seconds, 0);

		return $this;
	}

	public function setConnectTimeout($seconds): self
	{
		$this->connectTimeout = (int) \max($seconds, 0);

		return $this;
	}

	public function getTimeout(): int
	{
		return $this->timeout;
	}

	public function getConnectTimeout(): int
	{
		return $this->connectTimeout;
	}

	// END OF USER DEFINED TIMEOUTS

	public function request($method, $absUrl, $headers, $params, $hasFile): array
	{
		$method = \mb_strtolower($method);

		$opts = [];

		if (\is_callable($this->defaultOptions)) { // call defaultOptions callback, set options to return value
			$opts = \call_user_func_array($this->defaultOptions, \func_get_args());

			if (!\is_array($opts)) {
				throw new Exception\UnexpectedValueException('Non-array value returned by defaultOptions CurlClient callback');
			}
		} elseif (\is_array($this->defaultOptions)) { // set default curlopts from array
			$opts = $this->defaultOptions;
		}

		switch ($method) {
			case 'get':
				if ($hasFile) {
					throw new Exception\UnexpectedValueException('Issuing a GET request with a file parameter');
				}
				$opts[\CURLOPT_HTTPGET] = 1;

				if (\count($params) > 0) {
					$encoded = $this->encodeParameters($params);
					$absUrl = "{$absUrl}?{$encoded}";
				}

				break;
			case 'post':
				$opts[\CURLOPT_POST] = 1;
				$opts[\CURLOPT_POSTFIELDS] = $hasFile ? $params : $this->encodeParameters($params);

				break;
			case 'put':
				$opts[\CURLOPT_CUSTOMREQUEST] = 'PUT';
				$opts[\CURLOPT_POSTFIELDS] = $hasFile ? $params : $this->encodeParameters($params);

				break;
			case 'delete':
				$opts[\CURLOPT_CUSTOMREQUEST] = 'DELETE';

				if (\count($params) > 0) {
					$encoded = $this->encodeParameters($params);
					$absUrl = "{$absUrl}?{$encoded}";
				}

				break;

			default:
				throw new Exception\UnexpectedValueException("Unrecognized method {$method}");
		}

		// By default for large request body sizes (> 1024 bytes), cURL will
		// send a request without a body and with a `Expect: 100-continue`
		// header, which gives the server a chance to respond with an error
		// status code in cases where one can be determined right away (say
		// on an authentication problem for example), and saves the "large"
		// request body from being ever sent.
		//
		// Unfortunately, the bindings don't currently correctly handle the
		// success case (in which the server sends back a 100 CONTINUE), so
		// we'll error under that condition. To compensate for that problem
		// for the time being, override cURL's behavior by simply always
		// sending an empty `Expect:` header.
		$headers[] = 'Expect: ';

		$opts[\CURLOPT_URL] = $absUrl;
		$opts[\CURLOPT_RETURNTRANSFER] = true;
		$opts[\CURLOPT_CONNECTTIMEOUT] = $this->connectTimeout;
		$opts[\CURLOPT_TIMEOUT] = $this->timeout;
		$opts[\CURLOPT_HTTPHEADER] = $headers;
		$opts[\CURLOPT_IPRESOLVE] = \CURL_IPRESOLVE_V4; // TODO need check

		if (!isset($opts[\CURLOPT_HTTP_VERSION]) && $this->getEnableHttp2()) {
			// For HTTPS requests, enable HTTP/2, if supported
			$opts[\CURLOPT_HTTP_VERSION] = \CURL_HTTP_VERSION_2TLS;
		}

		[$responseBody, $responseCode, $responseHeaders] = $this->executeRequestWithRetries($opts, $absUrl);

		return [$responseBody, $responseCode, $responseHeaders];
	}

	/**
	 * @param array $opts cURL options
	 */
	private function executeRequestWithRetries(array $opts, string $absUrl): array
	{
		$numRetries = 0;

		while (true) {
			$responseCode = 0;
			$errno = 0;
			$message = null;

			// Create a callback to capture HTTP headers for the response
			$responseHeaders = [];
			$headerCallback = function ($curl, $header_line) use (&$responseHeaders) {
				// Ignore the HTTP request line (HTTP/1.1 200 OK)
				if (false === \mb_strpos($header_line, ':')) {
					return \mb_strlen($header_line);
				}
				[$key, $value] = \explode(':', \trim($header_line), 2);
				$responseHeaders[\trim($key)] = \trim($value);

				return \mb_strlen($header_line);
			};
			$opts[\CURLOPT_HEADERFUNCTION] = $headerCallback;

			$this->resetCurlHandle();
			\curl_setopt_array($this->curlHandle, $opts);
			$responseBody = \curl_exec($this->curlHandle);

			if ($responseBody === false) {
				$errno = \curl_errno($this->curlHandle);
				$message = \curl_error($this->curlHandle);
			} else {
				$responseCode = \curl_getinfo($this->curlHandle, \CURLINFO_HTTP_CODE);
			}

			if (!$this->getEnablePersistentConnections()) {
				$this->closeCurlHandle();
			}

			$shouldRetry = $this->shouldRetry($errno, $responseCode, $numRetries);

			if (\is_callable($this->getRequestStatusCallback())) {
				\call_user_func_array(
					$this->getRequestStatusCallback(),
					[$responseBody, $responseCode, $responseHeaders, $errno, $message, $shouldRetry, $numRetries]
				);
			}

			if ($shouldRetry) {
				$numRetries++;
				$sleepSeconds = $this->sleepTime($numRetries);
				\usleep((int) ($sleepSeconds * 1000000));
			} else {
				break;
			}
		}

		if ($responseBody === false) {
			$this->handleCurlError($absUrl, $errno, $message, $numRetries);
		}

		return [$responseBody, $responseCode, $responseHeaders];
	}

	/**
	 * @param string $url
	 * @param int    $errno
	 * @param string $message
	 * @param int    $numRetries
	 */
	private function handleCurlError($url, $errno, $message, $numRetries)
	{
		switch ($errno) {
			case \CURLE_COULDNT_CONNECT:
			case \CURLE_COULDNT_RESOLVE_HOST:
			case \CURLE_OPERATION_TIMEOUTED:
				$msg = "Could not connect to Stax ({$url}).  Please check your "
				 . 'internet connection and try again.';

				break;

			case \CURLE_SSL_CACERT:
			case \CURLE_SSL_PEER_CERTIFICATE:
				$msg = "Could not verify Stax's SSL certificate.  Please make sure "
				 . 'that your network is not intercepting certificates.  '
				 . "(Try going to {$url} in your browser.)  "
				 . 'If this problem persists,';

				break;

			default:
				$msg = 'Unexpected error communicating with Stax.  '
				 . 'If this problem persists,';
		}

		$msg .= "\n\n(Network error [errno {$errno}]: {$message})";

		if ($numRetries > 0) {
			$msg .= "\n\nRequest was retried {$numRetries} times.";
		}

		throw new Exception\ApiConnectionException($msg);
	}

	/**
	 * Checks if an error is a problem that we should retry on. This includes both
	 * socket errors that may represent an intermittent problem and some special
	 * HTTP statuses.
	 */
	private function shouldRetry(int $errno, int $responseCode, int $numRetries): bool
	{
		if ($numRetries >= Stax::getMaxNetworkRetries()) {
			return false;
		}

		// Retry on timeout-related problems (either on open or read).
		if ($errno === \CURLE_OPERATION_TIMEOUTED) {
			return true;
		}

		// Destination refused the connection, the connection was reset, or a
		// variety of other connection failures. This could occur from a single
		// saturated server, so retry in case it's intermittent.
		if ($errno === \CURLE_COULDNT_CONNECT) {
			return true;
		}

		// 409 Conflict
		return (bool) ($responseCode === 409)

		 ;
	}

	/**
	 * Provides the number of seconds to wait before retrying a request.
	 */
	private function sleepTime(int $numRetries): int
	{
		// Apply exponential backoff with $initialNetworkRetryDelay on the
		// number of $numRetries so far as inputs. Do not allow the number to exceed
		// $maxNetworkRetryDelay.
		$sleepSeconds = \min(
			Stax::getInitialNetworkRetryDelay() * 1.0 * 2 ** ($numRetries - 1),
			Stax::getMaxNetworkRetryDelay()
		);

		// Apply some jitter by randomizing the value in the range of
		// ($sleepSeconds / 2) to ($sleepSeconds).
		$sleepSeconds *= 0.5 * (1 + \mt_rand() / \mt_getrandmax() * 0.1);

		// But never sleep less than the base sleep seconds.
		return \max(Stax::getInitialNetworkRetryDelay(), $sleepSeconds);
	}

	/**
	 * Initializes the curl handle. If already initialized, the handle is closed first.
	 */
	private function initCurlHandle()
	{
		$this->closeCurlHandle();
		$this->curlHandle = \curl_init();
	}

	/**
	 * Closes the curl handle if initialized. Do nothing if already closed.
	 */
	private function closeCurlHandle()
	{
		if ($this->curlHandle !== null) {
			\curl_close($this->curlHandle);
			$this->curlHandle = null;
		}
	}

	/**
	 * Resets the curl handle. If the handle is not already initialized, or if persistent
	 * connections are disabled, the handle is reinitialized instead.
	 */
	private function resetCurlHandle()
	{
		if ($this->curlHandle !== null && $this->getEnablePersistentConnections()) {
			\curl_reset($this->curlHandle);
		} else {
			$this->initCurlHandle();
		}
	}

	/**
	 * Indicates whether it is safe to use HTTP/2 or not.
	 */
	private function canSafelyUseHttp2(): bool
	{
		// Versions of curl older than 7.60.0 don't respect GOAWAY frames
		// (cf. https://github.com/curl/curl/issues/2416), which Stripe use.
		$curlVersion = \curl_version()['version'];

		return \version_compare($curlVersion, '7.60.0') >= 0;
	}

	private function encodeParameters(array $params): string
	{
		$flattenedParams = $this->flattenParams($params);
		$pieces = [];

		foreach ($flattenedParams as $param) {
			[$k, $v] = $param;
			$pieces[] = $this->urlEncode($k) . '=' . $this->urlEncode($v);
		}

		return \implode('&', $pieces);
	}

	private function flattenParams(array $params, ?string $parentKey = null): array
	{
		$result = [];

		foreach ($params as $key => $value) {
			$calculatedKey = $parentKey ? "{$parentKey}[{$key}]" : $key;

			if ($this->isList($value)) {
				$result = \array_merge($result, $this->flattenParamsList($value, $calculatedKey));
			} elseif (\is_array($value)) {
				$result = \array_merge($result, $this->flattenParams($value, $calculatedKey));
			} else {
				\array_push($result, [$calculatedKey, $value]);
			}
		}

		return $result;
	}

	private function flattenParamsList(array $value, string $calculatedKey): array
	{
		$result = [];

		foreach ($value as $i => $elem) {
			if ($this->isList($elem)) {
				$result = \array_merge($result, $this->flattenParamsList($elem, $calculatedKey));
			} elseif (\is_array($elem)) {
				$result = \array_merge($result, $this->flattenParams($elem, "{$calculatedKey}[{$i}]"));
			} else {
				\array_push($result, ["{$calculatedKey}[{$i}]", $elem]);
			}
		}

		return $result;
	}

	/**
	 * @param string $key a string to URL-encode
	 *
	 * @return string the URL-encoded string
	 */
	private function urlEncode(string $key): string
	{
		$s = \urlencode((string) $key);

		// Don't use strict form encoding by changing the square bracket control
		// characters back to their literals. This is fine by the server, and
		// makes these parameter strings easier to read.
		$s = \str_replace('%5B', '[', $s);

		return \str_replace('%5D', ']', $s);
	}

	/**
	 * Whether the provided array (or other) is a list rather than a dictionary.
	 * A list is defined as an array for which all the keys are consecutive
	 * integers starting at 0. Empty arrays are considered to be lists.
	 *
	 * @param array|mixed $array
	 *
	 * @return bool true if the given object is a list
	 */
	private function isList($array): bool
	{
		if (!\is_array($array)) {
			return false;
		}

		if ($array === []) {
			return true;
		}

		return !(\array_keys($array) !== \range(0, \count($array) - 1))

		 ;
	}
}
