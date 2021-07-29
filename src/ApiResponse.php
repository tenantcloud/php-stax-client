<?php

namespace Stax;

/**
 * Class ApiResponse.
 */
class ApiResponse
{
	public ?array $headers;

	public string $body;

	public ?array $json;

	public int $code;

	public function __construct(string $body, int $code, ?array $headers, ?array $json)
	{
		$this->body = $body;
		$this->code = $code;
		$this->headers = $headers;
		$this->json = $json;
	}
}
