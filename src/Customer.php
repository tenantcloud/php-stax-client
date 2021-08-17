<?php

namespace Stax;

use Stax\Exception\InvalidArgumentException;

/**
 * A customer is a client to the merchant that wishes to make a transaction through Fattmerchant.
 * Customers can be tied to one or more merchants, but don't require their own Fattmerchant account.
 * Customer processes are done through a merchant's account.
 * Once a customer makes a transaction, they will be automatically saved under a merchants account.
 * For docs for more details. @see https://fattmerchant.docs.apiary.io/#reference/0/customers
 *
 * @property string      $id              Unique identifier for the object.
 * @property string|null $firstname
 * @property string|null $lastname
 * @property string|null $company
 * @property string|null $email
 * @property array       $cc_emails
 * @property string|null $phone
 * @property string|null $address_1
 * @property string|null $address_2
 * @property string|null $address_city
 * @property string|null $address_state
 * @property string|null $address_zip
 * @property string|null $address_country
 * @property string|null $notes
 * @property string|null $reference
 * @property string|null $options
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $gravatar
 */
class Customer extends ApiResource
{
	/**
	 * @param array $filter Array of filters. See docs to find all possible filters https://fattmerchant.docs.apiary.io/#reference/0/customers/find-all-customers
	 */
	public function all(array $filter = []): Paginator
	{
		return $this->request('get', 'customer', $filter);
	}

	/**
	 * @param string $id the ID of the customer to find
	 *
	 * @return Customer
	 */
	public function find(string $id): self
	{
		$this->request('get', "customer/{$id}");

		return $this;
	}

	/**
	 * @return Customer
	 */
	public function create(array $data): self
	{
		$this->request('post', 'customer', $data);

		return $this;
	}

	/**
	 * @param string $id the ID of the customer to update
	 *
	 * @return Customer
	 */
	public function update(string $id, array $data): self
	{
		$this->request('put', "customer/{$id}", $data);

		return $this;
	}

	/**
	 * @param string $id the ID of the customer to delete
	 *
	 * @return Customer
	 */
	public function delete(string $id): self
	{
		$this->request('delete', "customer/{$id}");

		return $this;
	}

	public function paymentMethods(): Paginator
	{
		if (!$this->id) {
			throw new InvalidArgumentException();
		}

		return (new PaymentMethod($this->apiKey))->findByCustomer($this->id);
	}
}
