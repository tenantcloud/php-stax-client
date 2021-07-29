<?php

namespace Stax;

class Stax
{
	/** @var string The Stax API key to be used for requests. */
	public static string $apiKey;

	/** @var string The base URL for the Stax API. */
	public static string $apiBase = 'https://apiprod.fattlabs.com/';

	/** @var int Maximum number of request retries */
	public static int $maxNetworkRetries = 0;

	/** @var float Maximum delay between retries, in seconds */
	private static float $maxNetworkRetryDelay = 2.0;

	/** @var float Maximum delay between retries, in seconds, that will be respected from the Stax API */
	private static float $maxRetryAfter = 60.0;

	/** @var float Initial delay between retries, in seconds */
	private static float $initialNetworkRetryDelay = 0.5;

	/**
	 * @return string the API key used for requests
	 */
	public static function getApiKey(): string
	{
		return self::$apiKey;
	}

	/**
	 * Sets the API key to be used for requests.
	 */
	public static function setApiKey(string $apiKey): void
	{
		self::$apiKey = $apiKey;
	}

	/**
	 * @return int Maximum number of request retries
	 */
	public static function getMaxNetworkRetries(): int
	{
		return self::$maxNetworkRetries;
	}

	/**
	 * @param int $maxNetworkRetries Maximum number of request retries
	 */
	public static function setMaxNetworkRetries(int $maxNetworkRetries): void
	{
		self::$maxNetworkRetries = $maxNetworkRetries;
	}

	/**
	 * @return float Maximum delay between retries, in seconds
	 */
	public static function getMaxNetworkRetryDelay(): float
	{
		return self::$maxNetworkRetryDelay;
	}

	/**
	 * @return float Maximum delay between retries, in seconds, that will be respected from the Stripe API
	 */
	public static function getMaxRetryAfter(): float
	{
		return self::$maxRetryAfter;
	}

	/**
	 * @return float Initial delay between retries, in seconds
	 */
	public static function getInitialNetworkRetryDelay(): float
	{
		return self::$initialNetworkRetryDelay;
	}
}
