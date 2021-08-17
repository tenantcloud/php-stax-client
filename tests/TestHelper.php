<?php

namespace Stax\Tests;

use PHPUnit\Framework\MockObject\Builder\InvocationMocker;
use PHPUnit\Framework\MockObject\MockObject;
use Stax\ApiRequestor;
use Stax\Stax;

/**
 * Helper trait for Stax test cases.
 */
trait TestHelper
{
	protected MockObject $clientMock;

	/**
	 * @before
	 */
	protected function setUpConfig()
	{
		Stax::setApiKey('api_key');

		// Set up the HTTP client mocker
		$this->clientMock = $this->createMock('\Stax\HttpClient\ClientInterface');

		// By default, use the real HTTP client
		ApiRequestor::setHttpClient($this->clientMock);
	}

	/**
	 * Sets up a request expectation with the provided parameters. The request
	 * will actually go through and be emitted.
	 *
	 * @param string        $method  HTTP method (e.g. 'post', 'get', etc.)
	 * @param string        $path    relative path (e.g. '/customer')
	 * @param array|null    $params  array of parameters. If null, parameters will
	 *                               not be checked.
	 * @param string[]|null $headers array of headers. Does not need to be
	 *                               exhaustive. If null, headers are not checked.
	 * @param bool          $hasFile Whether the request parameters contains a file.
	 *                               Defaults to false.
	 * @param string|null   $base    base URL (e.g. 'https://apiprod.fattlabs.com')
	 */
	protected function expectsRequest(
		$method,
		$path,
		$params = null,
		$headers = null,
		$hasFile = false,
		$base = null
	) {
		$this->prepareRequestMock($method, $path, $params, $headers, $hasFile, $base);
	}

	/**
	 * Sets up a request expectation with the provided parameters. The request
	 * will not actually be emitted, instead the provided response parameters
	 * will be returned.
	 *
	 * @param string        $method   HTTP method (e.g. 'post', 'get', etc.)
	 * @param string        $path     relative path (e.g. '/transaction')
	 * @param array|null    $params   array of parameters. If null, parameters will
	 *                                not be checked.
	 * @param string[]|null $headers  array of headers. Does not need to be
	 *                                exhaustive. If null, headers are not checked.
	 * @param bool          $hasFile  Whether the request parameters contains a file.
	 *                                Defaults to false.
	 * @param array         $response
	 * @param int           $rcode
	 * @param string|null   $base
	 *
	 * @return array
	 */
	protected function stubRequest(
		$method,
		$path,
		$params = null,
		$headers = null,
		$hasFile = false,
		$response = [],
		$rcode = 200,
		$base = null
	) {
		$this->prepareRequestMock($method, $path, $params, $headers, $hasFile, $base)
			->willReturn([\json_encode($response), $rcode, []])
		;
	}

	/**
	 * Prepares the client mocker for an invocation of the `request` method.
	 * This helper method is used by both `expectsRequest` and `stubRequest` to
	 * prepare the client mocker to expect an invocation of the `request` method
	 * with the provided arguments.
	 *
	 * @param string        $method  HTTP method (e.g. 'post', 'get', etc.)
	 * @param string        $path    relative path (e.g. '/customer')
	 * @param array|null    $params  array of parameters. If null, parameters will
	 *                               not be checked.
	 * @param string[]|null $headers array of headers. Does not need to be
	 *                               exhaustive. If null, headers are not checked.
	 * @param bool          $hasFile Whether the request parameters contains a file.
	 *                               Defaults to false.
	 * @param string|null   $base    base URL (e.g. 'https://apiprod.fattlabs.com')
	 */
	private function prepareRequestMock(
		$method,
		$path,
		$params = null,
		$headers = null,
		$hasFile = false,
		$base = null
	): InvocationMocker {
		if ($base === null) {
			$base = Stax::$apiBase;
		}
		$absUrl = $base . $path;

		return $this->clientMock
			->expects(static::once())
			->method('request')
			->with(
				static::identicalTo(\mb_strtolower($method)),
				static::identicalTo($absUrl),
				// for headers, we only check that all of the headers provided in $headers are
				// present in the list of headers of the actual request
				$headers === null ? static::anything() : static::callback(function ($array) use ($headers) {
					foreach ($headers as $header) {
						if (!\in_array($header, $array, true)) {
							return false;
						}
					}

					return true;
				}),
				$params === null ? static::anything() : static::identicalTo($params),
				static::identicalTo($hasFile)
			)
			->willReturn(['{"data": [[]], "current_page": 1}', 200, []])
			;
	}
}
