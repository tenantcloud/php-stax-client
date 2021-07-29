<?php

namespace Stax\Exception;

/**
 * InvalidRequestException is thrown when a request is initiated with invalid
 * parameters.
 */
class InvalidRequestException extends ApiErrorException
{
	protected array $fields;

	/**
	 * Creates a new InvalidRequestException exception.
	 *
	 * @param string      $message     the exception message
	 * @param int|null    $httpStatus  the HTTP status code
	 * @param string|null $httpBody    the HTTP body as a string
	 * @param array|null  $jsonBody    the JSON deserialized body
	 * @param array|null  $httpHeaders the HTTP headers array
	 * @param array       $fields      the fields to the error
	 *
	 * @return InvalidRequestException
	 */
	public static function factory(
		string $message,
		$httpStatus = null,
		$httpBody = null,
		$jsonBody = null,
		$httpHeaders = null,
		array $fields = []
	): self {
		$instance = parent::factory($message, $httpStatus, $httpBody, $jsonBody, $httpHeaders);
		$instance->setFields($fields);

		return $instance;
	}

	/**
	 * Gets the fields related to the error.
	 */
	public function getFields(): array
	{
		return $this->fields;
	}

	/**
	 * Sets the fields related to the error.
	 */
	public function setFields(array $fields)
	{
		$this->fields = $fields;
	}
}
