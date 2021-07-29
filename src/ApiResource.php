<?php

namespace Stax;

/**
 * Class ApiResource.
 */
abstract class ApiResource extends BaseObject
{
	/**
	 * @param string $method  HTTP method ('get', 'post', etc.)
	 * @param string $url     URL for the request
	 * @param array  $params  list of parameters for the request
	 * @param null   $headers
	 *
	 * @return array|Paginator tuple containing (the JSON response, $options)
	 */
	protected function request(string $method, string $url, array $params = [], $headers = null)
	{
		$requestor = new ApiRequestor($this->apiKey);
		[$response] = $requestor->request($method, $url, $params, $headers);

		if (\array_key_exists('id', $response->json)) {
			$this->fields = $response->json;

			return $response->json;
		}

		// Fix for response with static text
		if (\array_key_exists('html', $response->json)) {
			$this->fields = $response->json;

			return $response->json;
		}

		if (\array_key_exists('data', $response->json)) {
			return (new Paginator())->buildFromResponse($response->json, $this);
		}

		// Fix to custom build pagination
		if (\is_array($response->json) && !empty($response->json[0]) && !\is_array($response->json[0])) {
			$total = \count($response->json);
			$pagination = [
				'data'         => $response->json,
				'total'        => $total,
				'per_page'     => $total,
				'current_page' => 1,
				'last_page'    => 1,
				'from'         => 1,
				'to'           => $total,
			];

			return (new Paginator())->buildFromResponse($pagination, $this);
		}

		return $response->json;
	}
}
