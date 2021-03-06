<?php

namespace Stax;

use CURLFile;
use Stax\Exception\AuthenticationException;
use Stax\Exception\ForbiddenException;
use Stax\Exception\InvalidArgumentException;
use Stax\Exception\InvalidRequestException;
use Stax\Exception\NotFoundException;
use Stax\Exception\TooManyRequestsException;
use Stax\Exception\UnauthorizedException;
use Stax\Exception\UnexpectedValueException;
use Stax\HttpClient\ClientInterface;

/**
 * Class ApiRequestor.
 */
class ApiRequestor
{
	private static ?ClientInterface $httpClient = null;

	private static array $OPTIONS_KEYS = ['api_key', 'api_base'];

	private ?string $apiKey;

	/** @var string */
	private ?string $apiBase;

	/**
	 * ApiRequestor constructor.
	 *
	 * @param string|null $apiKey
	 * @param string|null $apiBase
	 */
	public function __construct($apiKey = null, $apiBase = null)
	{
		$this->apiKey = $apiKey;

		if (!$apiBase) {
			$apiBase = Stax::$apiBase;
		}
		$this->apiBase = $apiBase;
	}

	/**
	 * @static
	 *
	 * @param mixed $client
	 */
	public static function setHttpClient($client): void
	{
		self::$httpClient = $client;
	}

	/**
	 * @static
	 */
	private static function _defaultHeaders(string $apiKey): array
	{
		return [
			'Authorization' => 'Bearer ' . $apiKey,
		];
	}

	/**
	 * @return array tuple containing (ApiResponse, API key)
	 */
	public function request(string $method, string $url, array $params = null, array $headers = null): array
	{
		$params = $params ?: [];
		$headers = $headers ?: [];
		[$responseBody, $responseCode, $responseHeaders, $myApiKey] = $this->requestRaw($method, $url, $params, $headers);
		$json = $this->interpretResponse($responseBody, $responseCode, $responseHeaders);
		$resp = new ApiResponse($responseBody, $responseCode, $responseHeaders, $json);

		return [$resp, $myApiKey];
	}

	/**
	 * @param string $responseBody a JSON string
	 */
	public function handleErrorResponse(string $responseBody, int $responseCode, array $resp, array $responseHeaders)
	{
		switch ($responseCode) {
			case 429:
				throw TooManyRequestsException::factory('Too many requests error', $responseCode, $responseBody, $resp, $responseHeaders);
			case 422:
				throw InvalidRequestException::factory('Validation error', $responseCode, $responseBody, $resp, $responseHeaders, $resp);
			case 401:
				throw UnauthorizedException::factory('Unauthorized error', $responseCode, $responseBody, $resp, $responseHeaders);
			case 403:
				throw ForbiddenException::factory('Forbidden error', $responseCode, $responseBody, $resp, $responseHeaders);
			case 404:
				throw NotFoundException::factory('Not found error', $responseCode, $responseBody, $resp, $responseHeaders);

			default:
				throw new UnexpectedValueException();
		}
	}

	private function requestRaw(string $method, string $url, array $params, array $headers): array
	{
		$myApiKey = $this->apiKey;

		if (!$myApiKey) {
			$myApiKey = Stax::$apiKey;
		}

		if (!$myApiKey) {
			$msg = 'No API key provided.  (HINT: set your API key using '
			  . '"Stax::setApiKey(<API-KEY>)".  You can find API keys in '
			  . 'the Stax web interface.  See https://docs.paywithomni.com/quickstart/ for details.';

			throw new AuthenticationException($msg);
		}

		if ($params && \is_array($params)) {
			$optionKeysInParams = \array_filter(
				static::$OPTIONS_KEYS,
				fn ($key) => \array_key_exists($key, $params)
			);

			if (\count($optionKeysInParams) > 0) {
				$message = \sprintf('Options found in $params: %s. Options should '
				  . 'be passed in their own array after $params. (HINT: pass an '
				  . 'empty array to $params if you do not have any.)', \implode(', ', $optionKeysInParams));
				\trigger_error($message, \E_USER_WARNING);
			}
		}

		$absUrl = $this->apiBase . $url;
		$defaultHeaders = $this->_defaultHeaders($myApiKey);

		$hasFile = false;

		foreach ($params as $k => $v) {
			if (\is_resource($v)) {
				$hasFile = true;
				$params[$k] = self::processResourceParam($v);
			} elseif ($v instanceof CURLFile) {
				$hasFile = true;
			}
		}

		if ($hasFile) {
			$defaultHeaders['Content-Type'] = 'multipart/form-data';
		} else {
			$defaultHeaders['Content-Type'] = 'application/json';
		}

		$combinedHeaders = \array_merge($defaultHeaders, $headers);
		$rawHeaders = [];

		foreach ($combinedHeaders as $header => $value) {
			$rawHeaders[] = $header . ': ' . $value;
		}

		[$responseBody, $responseCode, $responseHeaders] = $this->httpClient()->request(
			$method,
			$absUrl,
			$rawHeaders,
			$params,
			$hasFile
		);

		return [$responseBody, $responseCode, $responseHeaders, $myApiKey];
	}

	/**
	 * @param resource $resource
	 *
	 * @return CURLFile|string
	 */
	private function processResourceParam($resource)
	{
		if ('stream' !== \get_resource_type($resource)) {
			throw new InvalidArgumentException('Attempted to upload a resource that is not a stream');
		}

		$metaData = \stream_get_meta_data($resource);

		if ($metaData['wrapper_type'] !== 'plainfile') {
			throw new InvalidArgumentException('Only plainfile resource streams are supported');
		}

		// We don't have the filename or mimetype, but the API doesn't care
		return new CURLFile($metaData['uri']);
	}

	private function interpretResponse(string $responseBody, int $responseCode, array $responseHeaders = []): array
	{
		$resp = \json_decode($responseBody, true);
		$jsonError = \json_last_error();

		if ($resp === null && $jsonError !== \JSON_ERROR_NONE) {
			$msg = "Invalid response body from API: {$responseBody} "
			  . "(HTTP response code was {$responseCode}, json_last_error() was {$jsonError})";

			throw new UnexpectedValueException($msg, $responseCode);
		}

		if ($responseCode < 200 || $responseCode >= 300) {
			$this->handleErrorResponse($responseBody, $responseCode, $resp, $responseHeaders);
		}

		if (\is_string($resp)) {
			$resp = [
				'html' => $resp,
			];
		}

		return $resp;
	}

	private function httpClient(): ClientInterface
	{
		if (!self::$httpClient) {
			self::$httpClient = HttpClient\CurlClient::instance();
		}

		return self::$httpClient;
	}
}
