<?php

namespace Stax\Tests;

use PHPUnit\Framework\TestCase;
use Stax\Paginator;
use Stax\PaymentMethod;

/**
 * @see PaymentMethod
 */
class PaymentMethodTest extends TestCase
{
	use TestHelper;

	public function testAssertFindByCustomer(): void
	{
		$this->expectsRequest(
			'get',
			'customer/123/payment-method'
		);

		$resource = (new PaymentMethod())->findByCustomer('123');
		static::assertInstanceOf(Paginator::class, $resource);
		static::assertInstanceOf(PaymentMethod::class, $resource->data[0]);
	}

	public function testAssertFind(): void
	{
		$this->expectsRequest(
			'get',
			'payment-method/123'
		);

		$resource = (new PaymentMethod())->find('123');
		static::assertInstanceOf(PaymentMethod::class, $resource);
	}

	public function testAssertCreate(): void
	{
		$this->expectsRequest(
			'post',
			'payment-method'
		);

		$resource = (new PaymentMethod())->create([]);
		static::assertInstanceOf(PaymentMethod::class, $resource);
	}

	public function testAssertUpdate(): void
	{
		$this->expectsRequest(
			'put',
			'payment-method/123'
		);

		$resource = (new PaymentMethod())->update('123', []);
		static::assertInstanceOf(PaymentMethod::class, $resource);
	}

	public function testAssertDelete(): void
	{
		$this->expectsRequest(
			'delete',
			'payment-method/123'
		);

		$resource = (new PaymentMethod())->delete('123');
		static::assertInstanceOf(PaymentMethod::class, $resource);
	}
}
