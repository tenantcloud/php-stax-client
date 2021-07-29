<?php

namespace Stax;

/**
 * Retrieves the terms and conditions matching the given merchantId.
 * Returns the default terms and conditions if no merchantId is given.
 * This route is used to generate the string of html which is used in the
 * Update Merchant Registration Data route to populate the electronic_signature.html_content.
 *
 * @property string $data terms and conditions html content.
 */
class MerchantTermsAndConditions extends ApiResource
{
	public function find(string $merchantId): self
	{
		$this->request('get', "underwriting/terms-and-conditions/{$merchantId}");

		return $this;
	}
}
