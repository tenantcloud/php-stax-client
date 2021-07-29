<?php

namespace Stax;

/**
 * Payment methods which are associated with a customer
 * For docs for more details. @see https://fattmerchant.docs.apiary.io/#reference/0/payment-methods
 *
 * @property string      $id                Unique identifier for the object.
 * @property string      $customer_id       Unique customer identifier.
 * @property string      $merchant_id       Unique merchant identifier.
 * @property string      $user_id           Unique user identifier.
 * @property string|null $nickname
 * @property bool        $is_default
 * @property string      $method
 * @property array       $meta
 * @property string|null $bin_type
 * @property string|null $person_name
 * @property string|null $card_type
 * @property string|null $card_last_four
 * @property string|null $card_exp
 * @property string|null $bank_name
 * @property string|null $bank_type
 * @property string|null $bank_holder_type
 * @property string|null $address_1
 * @property string|null $address_2
 * @property string|null $address_city
 * @property string|null $address_state
 * @property string|null $address_zip
 * @property string|null $address_country
 * @property string|null $purged_at
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $card_exp_datetime
 * @property Customer    $customer
 */
class PaymentMethod extends ApiResource
{
	/**
	 * @param string $customerId Retrieves a non-paginated data array of all non-deleted payment methods associated with a customer. This route is a GET operation and can return a validation error if the customer id is invalid.
	 */
	public function findByCustomer(string $customerId): Paginator
	{
		return $this->request('get', "customer/{$customerId}/payment-method");
	}

	/**
	 * @param string $id the ID of the payment method to find
	 *
	 * @return PaymentMethod
	 */
	public function find(string $id): self
	{
		$this->request('get', "payment-method/{$id}");

		return $this;
	}

	/**
	 * @return PaymentMethod
	 */
	public function create(array $data): self
	{
		$this->request('post', 'payment-method', $data);

		return $this;
	}

	/**
	 * @param string $id the ID of the payment method to update
	 *
	 * @return PaymentMethod
	 */
	public function update(string $id, array $data): self
	{
		$this->request('put', "payment-method/{$id}", $data);

		return $this;
	}

	/**
	 * @param string $id the ID of the payment method to delete
	 *
	 * @return PaymentMethod
	 */
	public function delete(string $id): self
	{
		$this->request('delete', "payment-method/{$id}");

		return $this;
	}
}
