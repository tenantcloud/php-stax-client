<?php

namespace Stax\Tests;

use PHPUnit\Framework\TestCase;
use Stax\Deposit;
use Stax\Paginator;

/**
 * @see Deposit
 */
class DepositTest extends TestCase
{
	use TestHelper;

	public function testAssertAll(): void
	{
		$this->expectsRequest(
			'get',
			'query/deposit'
		);

		$resource = (new Deposit())->all([]);
		static::assertInstanceOf(Paginator::class, $resource);
		static::assertInstanceOf(Deposit::class, $resource->data[0]);
	}
}
