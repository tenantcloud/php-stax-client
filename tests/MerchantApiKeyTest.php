<?php

namespace Stax\Tests;

use PHPUnit\Framework\TestCase;
use Stax\MerchantApiKey;
use Stax\Paginator;

/**
 * @see MerchantApiKey
 */
class MerchantApiKeyTest extends TestCase
{
	use TestHelper;

	public function testAssertAll(): void
	{
		$this->expectsRequest(
			'get',
			'merchant/123/apikey'
		);

		$resource = (new MerchantApiKey())->all('123');
		static::assertInstanceOf(Paginator::class, $resource);
		static::assertInstanceOf(MerchantApiKey::class, $resource->data[0]);
	}

	public function testAssertCreate(): void
	{
		$this->expectsRequest(
			'post',
			'merchant/123/apikey'
		);

		$resource = (new MerchantApiKey())->create('123');
		static::assertInstanceOf(MerchantApiKey::class, $resource);
	}
}
