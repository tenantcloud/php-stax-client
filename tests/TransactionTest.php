<?php

namespace Stax\Tests;

use PHPUnit\Framework\TestCase;
use Stax\Paginator;
use Stax\Transaction;

/**
 * @see Transaction
 */
class TransactionTest extends TestCase
{
	use TestHelper;

	public function testAssertAll(): void
	{
		$this->expectsRequest(
			'get',
			'transaction'
		);

		$resource = (new Transaction())->all();
		static::assertInstanceOf(Paginator::class, $resource);
		static::assertInstanceOf(Transaction::class, $resource->data[0]);
	}

	public function testAssertFind(): void
	{
		$this->expectsRequest(
			'get',
			'transaction/123'
		);

		$resource = (new Transaction())->find('123');
		static::assertInstanceOf(Transaction::class, $resource);
	}

	public function testAssertCreate(): void
	{
		$this->expectsRequest(
			'post',
			'charge'
		);

		$resource = (new Transaction())->create([]);
		static::assertInstanceOf(Transaction::class, $resource);
	}

	public function testAssertUpdate(): void
	{
		$this->expectsRequest(
			'put',
			'transaction/123'
		);

		$resource = (new Transaction())->update('123', []);
		static::assertInstanceOf(Transaction::class, $resource);
	}

	public function testAssertCapture(): void
	{
		$this->expectsRequest(
			'post',
			'transaction/123/capture'
		);

		$resource = (new Transaction())->capture('123', []);
		static::assertInstanceOf(Transaction::class, $resource);
	}

	public function testAssertReceiptEmail(): void
	{
		$this->expectsRequest(
			'post',
			'transaction/123/receipt/email'
		);

		$resource = (new Transaction())->receiptEmail('123', []);
		static::assertInstanceOf(Transaction::class, $resource);
	}

	public function testAssertReceiptSMS(): void
	{
		$this->expectsRequest(
			'post',
			'transaction/123/receipt/sms'
		);

		$resource = (new Transaction())->receiptSMS('123', []);
		static::assertInstanceOf(Transaction::class, $resource);
	}

	public function testAssertVoid(): void
	{
		$this->expectsRequest(
			'post',
			'transaction/123/void'
		);

		$resource = (new Transaction())->void('123', []);
		static::assertInstanceOf(Transaction::class, $resource);
	}

	public function testAssertRefund(): void
	{
		$this->expectsRequest(
			'post',
			'transaction/123/refund'
		);

		$resource = (new Transaction())->refund('123', []);
		static::assertInstanceOf(Transaction::class, $resource);
	}

	public function testAssertVoidOrRefund(): void
	{
		$this->expectsRequest(
			'post',
			'transaction/123/void-or-refund'
		);

		$resource = (new Transaction())->voidOrRefund('123', []);
		static::assertInstanceOf(Transaction::class, $resource);
	}
}
