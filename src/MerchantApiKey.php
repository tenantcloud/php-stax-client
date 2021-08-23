<?php

namespace Stax;

/**
 * This will return an api key value for the merchant.
 * For docs for more details. @see https://fattmerchant.docs.apiary.io/#reference/0/enrollment/create-a-new-api-key-for-merchant
 *
 * @property string      $id
 * @property string|null $name
 * @property string|null $email
 * @property string|null $email_verification_sent_at
 * @property string|null $email_verified_at
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_ats
 * @property string|null $team_role
 * @property string|null $team_admin
 * @property string      $api_key
 */
class MerchantApiKey extends ApiResource
{
	/**
	 * @param string $merchantId
	 * @param array $data
	 *
	 * @return MerchantApiKey
	 */
	public function create(string $merchantId, array $data = []): self
	{
		$this->request('post', "merchant/{$merchantId}/apikey", $data);

		return $this;
	}

	/**
	 * @param string $merchantId
	 */
	public function all(string $merchantId): Paginator
	{
		return $this->request('get', "merchant/{$merchantId}/apikey");
	}

}
