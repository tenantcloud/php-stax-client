<?php

namespace Stax\Tests;

use PHPUnit\Framework\TestCase;
use Stax\Merchant;

/**
 * @see Merchant
 */
class MerchantTest extends TestCase
{
	use TestHelper;

	public function testAssertCreate(): void
	{
		$this->expectsRequest(
			'post',
			'merchant'
		);

		$resource = (new Merchant())->create([]);
		static::assertInstanceOf(Merchant::class, $resource);
	}

	public function testAssertFind(): void
	{
		$this->expectsRequest(
			'get',
			'merchant/123'
		);

		$resource = (new Merchant())->find('123');
		static::assertInstanceOf(Merchant::class, $resource);
	}
}
