<?php

namespace Stax\Tests;

use PHPUnit\Framework\TestCase;
use Stax\MerchantRegistration;

/**
 * @see MerchantRegistration
 */
class MerchantRegistrationTest extends TestCase
{
	use TestHelper;

	public function testAssertUpdate(): void
	{
		$this->expectsRequest(
			'put',
			'merchant/123/registration'
		);

		$resource = (new MerchantRegistration())->update('123', []);
		static::assertInstanceOf(MerchantRegistration::class, $resource);
	}

	public function testAssertFind(): void
	{
		$this->expectsRequest(
			'get',
			'merchant/123/registration'
		);

		$resource = (new MerchantRegistration())->find('123');
		static::assertInstanceOf(MerchantRegistration::class, $resource);
	}
}
