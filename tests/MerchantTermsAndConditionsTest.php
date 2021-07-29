<?php

namespace Stax\Tests;

use PHPUnit\Framework\TestCase;
use Stax\MerchantTermsAndConditions;

/**
 * @see MerchantTermsAndConditions
 */
class MerchantTermsAndConditionsTest extends TestCase
{
	use TestHelper;

	public function testAssertFind(): void
	{
		$this->expectsRequest(
			'get',
			'underwriting/terms-and-conditions/123'
		);

		$resource = (new MerchantTermsAndConditions())->find('123');
		static::assertInstanceOf(MerchantTermsAndConditions::class, $resource);
	}
}
