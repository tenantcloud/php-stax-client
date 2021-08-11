<?php

namespace Stax;

/**
 * This is an object representing a Stax merchant account. You can retrieve it to see
 * properties on the account like its current e-mail address or account status.
 *
 * @property string                    $id                     Unique identifier for the object.
 * @property string                    $status                 Merchant account status.
 * @property string                    $company_name
 * @property string                    $contact_name
 * @property string                    $contact_email
 * @property string                    $contact_phone
 * @property string                    $address_1
 * @property string                    $address_2
 * @property string                    $address_city
 * @property string                    $address_state
 * @property string                    $address_zip
 * @property string                    $hosted_payments_token
 * @property array                     $plan
 * @property array                     $options
 * @property string                    $notes
 * @property string|null               $gateway_type
 * @property array                     $vendor_keys
 * @property string                    $processor
 * @property string|null               $partner
 * @property string                    $product_type
 * @property bool                      $is_enterprise
 * @property bool                      $is_payfac
 * @property string|null               $fm_billing_schedule_id
 * @property string|null               $welcome_email_sent_at
 * @property string|null               $created_at
 * @property string|null               $updated_at
 * @property string|null               $deleted_at
 * @property string|null               $brand
 * @property string|null               $branding
 * @property bool                      $allow_ach
 * @property bool                      $is_portal
 * @property bool                      $allow_credits
 * @property bool                      $allow_terminal
 * @property array                     $users
 * @property MerchantRegistration|null $registration
 */
class Merchant extends ApiResource
{
	public function create(array $data): self
	{
		$this->request('post', 'merchant', $data);

		return $this;
	}

	/**
	 * @param string $id the ID of the merchant account to find
	 *
	 * @return Merchant
	 */
	public function find(string $id): self
	{
		$this->request('get', "merchant/{$id}");

		return $this;
	}

	/**
	 * @param string $relation the relation what you want to load
	 *
	 * @return Merchant
	 */
	public function load(string $relation): self
	{
		if ($this->{$relation}) {
			return $this;
		}

		switch ($relation) {
			case 'registration':
				$this->registration = (new MerchantRegistration($this->apiKey))->find($this->id);

				break;
		}

		return $this;
	}
}
