<?php

namespace Stax\Exception;

use Exception;

/**
 * Implements properties and methods common to all (non-SPL) Stax exceptions.
 */
abstract class ApiErrorException extends Exception implements ExceptionInterface
{
	protected ?string $httpBody;

	protected ?array $httpHeaders;

	protected ?int $httpStatus;

	protected ?array $jsonBody;

	/**
	 * Returns the string representation of the exception.
	 */
	public function __toString(): string
	{
		$statusStr = (null === $this->getHttpStatus()) ? '' : "(Status {$this->getHttpStatus()}) ";

		return "{$statusStr}{$this->getMessage()}";
	}

	/**
	 * Creates a new API error exception.
	 *
	 * @param string      $message     the exception message
	 * @param int|null    $httpStatus  the HTTP status code
	 * @param string|null $httpBody    the HTTP body as a string
	 * @param array|null  $jsonBody    the JSON deserialized body
	 * @param array|null  $httpHeaders the HTTP headers array
	 *
	 * @return static
	 */
	public static function factory(
		string $message,
		$httpStatus = null,
		$httpBody = null,
		$jsonBody = null,
		$httpHeaders = null
	): self {
		$instance = new static($message);
		$instance->setHttpStatus($httpStatus);
		$instance->setHttpBody($httpBody);
		$instance->setJsonBody($jsonBody);
		$instance->setHttpHeaders($httpHeaders);

		return $instance;
	}

	/**
	 * Gets the HTTP body as a string.
	 */
	public function getHttpBody(): ?string
	{
		return $this->httpBody;
	}

	/**
	 * Sets the HTTP body as a string.
	 */
	public function setHttpBody(?string $httpBody): void
	{
		$this->httpBody = $httpBody;
	}

	/**
	 * Gets the HTTP headers array.
	 */
	public function getHttpHeaders(): ?array
	{
		return $this->httpHeaders;
	}

	/**
	 * Sets the HTTP headers array.
	 */
	public function setHttpHeaders(?array $httpHeaders): void
	{
		$this->httpHeaders = $httpHeaders;
	}

	/**
	 * Gets the HTTP status code.
	 */
	public function getHttpStatus(): ?int
	{
		return $this->httpStatus;
	}

	/**
	 * Sets the HTTP status code.
	 */
	public function setHttpStatus(?int $httpStatus): void
	{
		$this->httpStatus = $httpStatus;
	}

	/**
	 * Gets the JSON deserialized body.
	 */
	public function getJsonBody(): ?array
	{
		return $this->jsonBody;
	}

	/**
	 * Sets the JSON deserialized body.
	 *
	 * @param array<string, mixed>|null $jsonBody
	 */
	public function setJsonBody(?array $jsonBody): void
	{
		$this->jsonBody = $jsonBody;
	}
}
