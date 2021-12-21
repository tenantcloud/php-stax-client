<?php

namespace Stax\Tests;

use PHPUnit\Framework\TestCase;
use Stax\DepositDetails;
use Stax\Paginator;

/**
 * @see DepositDetails
 */
class DepositDetailsTest extends TestCase
{
	use TestHelper;

	public function testAssertAll(): void
	{
		$this->expectsRequest(
			'get',
			'query/depositDetail'
		);

		$resource = (new DepositDetails())->all([]);
		static::assertInstanceOf(Paginator::class, $resource);
		static::assertInstanceOf(DepositDetails::class, $resource->data[0]);
	}
}
