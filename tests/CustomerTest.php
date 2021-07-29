<?php

namespace Stax\Tests;

use PHPUnit\Framework\TestCase;
use Stax\Customer;
use Stax\Paginator;

/**
 * @see Customer
 */
class CustomerTest extends TestCase
{
	use TestHelper;

	public function testAssertAll(): void
	{
		$this->expectsRequest(
			'get',
			'customer'
		);

		$resource = (new Customer())->all();
		static::assertInstanceOf(Paginator::class, $resource);
		static::assertInstanceOf(Customer::class, $resource->data[0]);
	}

	public function testAssertFind(): void
	{
		$this->expectsRequest(
			'get',
			'customer/123'
		);

		$resource = (new Customer())->find('123');
		static::assertInstanceOf(Customer::class, $resource);
	}

	public function testAssertCreate(): void
	{
		$this->expectsRequest(
			'post',
			'customer'
		);

		$resource = (new Customer())->create([]);
		static::assertInstanceOf(Customer::class, $resource);
	}

	public function testAssertUpdate(): void
	{
		$this->expectsRequest(
			'put',
			'customer/123'
		);

		$resource = (new Customer())->update('123', []);
		static::assertInstanceOf(Customer::class, $resource);
	}

	public function testAssertDelete(): void
	{
		$this->expectsRequest(
			'delete',
			'customer/123'
		);

		$resource = (new Customer())->delete('123');
		static::assertInstanceOf(Customer::class, $resource);
	}
}
