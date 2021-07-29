<?php

namespace Stax;

/**
 * Transactions are "activity" within the Fattmerchant core data API.
 * A transaction represents an exchange of money between a merchant and a customer.
 * They are attached to a payment_method and a customer.
 * For docs for more details. @see https://fattmerchant.docs.apiary.io/#reference/0/transactions
 *
 * @property string        $id                 Unique identifier for the transaction.
 * @property string|null   $invoice_id         The id of the Invoice associated with this transaction (if any).
 * @property string|null   $reference_id       The id of the parent transaction. Will only be filled if this transaction is a child refund or void of a transaction.
 * @property string        $customer_id        The id of the Customer associated with this transaction.
 * @property string        $merchant_id        The id of the Merchant that owns this transaction.
 * @property string        $user_id            The id of the User who made this transaction.
 * @property string        $payment_method_id  The id of the Payment Method associated with this transaction.
 * @property string|null   $schedule_id        The id of the associated Schedule for this transaction (if any).
 * @property string|null   $auth_id            The if of the associated pre-auth transaction for this transaction (if this transaction is a capture).
 * @property string        $type               The type of this transaction. Possible values are "charge", "void", "refund", "credit", "pre_auth", "capture".
 * @property string|null   $source             The source of this transaction. Will be empty if this transaction originated in Stax, but will have a value if this transaction came from an external source such as a Terminal or Mobile Reader.
 * @property string|null   $is_manual
 * @property bool          $success            Whether or not this transaction was successful.
 * @property string|null   $message            The error message if this transaction was unsuccessful.
 * @property array         $meta               Metadata for this transaction (if any). Usually will only contain what you place into it when running POST /charge or similar calls.
 * @property int|null      $total              The total of this transaction.
 * @property string        $method             The method of this transaction. Possible values are "card", "bank", "cash", "check", "giftcard".
 * @property bool          $pre_auth           Whether or not this transaction is a pre-authorization.
 * @property string|null   $last_four          The last_four of the Payment Method card number for this transaction (if any).
 * @property string|null   $receipt_email_at   When the email receipt was sent for this transaction.
 * @property string|null   $receipt_sms_at     When the sms receipt was sent for this transaction.
 * @property string|null   $settled_at         When this transaction was settled.
 * @property string|null   $created_at         When this transaction was created.
 * @property string|null   $updated_at         When this transaction was last modified.
 * @property string|null   $issuer_auth_code   The gateway authorization code for this transactions. Transactions originating outside of Stax may not have an issuer_auth_code.
 * @property int|null      $total_refunded     The total amount of any refunds for this transaction (if any).
 * @property bool          $is_refundable      Whether or not this transaction is refundable.
 * @property bool          $is_voided          Whether or not this transaction is voided. (To see if a transaction is refunded, check that total_refunded > 0)
 * @property bool          $is_voidable        Whether or not this transaction is voidable.
 * @property bool          $is_settling        Whether or not this transaction is still settling. Will always be null for non-ACH transactions. May have a boolean value if this transaction was made on an ACH Gateway.
 * @property array         $child_transactions Any child Transactions for this transaction. Examples of child transactions include refunds, voids, and captures (if this transaction is a pre-auth).
 * @property array         $files              The Files attached to this transaction (if any).
 * @property Customer      $customer           The Customer for this transaction.
 * @property PaymentMethod $payment_method     The Payment Method for this transaction.
 */
class Transaction extends ApiResource
{
	/**
	 * @param array $filter Array of filters. See docs to find all possible filters https://fattmerchant.docs.apiary.io/#reference/0/transactions/list-and-filter-all-transactions
	 */
	public function all(array $filter = []): Paginator
	{
		return $this->request('get', 'transaction', $filter);
	}

	/**
	 * @param string $id the ID of the transaction to find
	 *
	 * @return Transaction
	 */
	public function find(string $id): self
	{
		$this->request('get', "transaction/{$id}");

		return $this;
	}

	/**
	 * @return Transaction
	 */
	public function create(array $data): self
	{
		$this->request('post', 'charge', $data);

		return $this;
	}

	/**
	 * @param string $id the ID of the transaction to update
	 *
	 * @return Transaction
	 */
	public function update(string $id, array $data): self
	{
		$this->request('put', "transaction/{$id}", $data);

		return $this;
	}

	/**
	 * @param string $id the ID of the transaction to capture
	 *
	 * @return Transaction
	 */
	public function capture(string $id, array $data): self
	{
		$this->request('post', "transaction/{$id}/capture", $data);

		return $this;
	}

	/**
	 * Using the ID of a transaction, this function will send out an email receipt of that transaction to the associated customer's email on file.
	 * Can be sent to multiple emails by attaching additional emails in the ccEmails field in the body, but ccEmails does not require a value in the array.
	 *
	 * @param string $id the ID of the transaction to send emails
	 *
	 * @return Transaction
	 */
	public function receiptEmail(string $id, array $data): self
	{
		$this->request('post', "transaction/{$id}/receipt/email", $data);

		return $this;
	}

	/**
	 * Similar to the transaction ID function; it gathers information on a transaction and sends an SMS (text message) receipt to the customer.
	 * Must be enabled by customers with a list of CC numbers.
	 *
	 * @param string $id the ID of the transaction to send sms
	 *
	 * @return Transaction
	 */
	public function receiptSMS(string $id, array $data): self
	{
		$this->request('post', "transaction/{$id}/receipt/sms", $data);

		return $this;
	}

	/**
	 * Sets a transaction to void, ending the transaction before it's processed.
	 *
	 * @param string $id the ID of the transaction to void
	 *
	 * @return Transaction
	 */
	public function void(string $id, array $data): self
	{
		$this->request('post', "transaction/{$id}/void", $data);

		return $this;
	}

	/**
	 * To refund, a transaction must be older than 24-hours. This returns money from the merchant to a customer.
	 * The user may request how much is to be refunded. No less than 1 cent and no more than the total amount may be refunded.
	 * After a refund is processed, a child refund transaction is attached to the original transaction.
	 *
	 * @param string $id the ID of the transaction to refund
	 *
	 * @return Transaction
	 */
	public function refund(string $id, array $data): self
	{
		$this->request('post', "transaction/{$id}/refund", $data);

		return $this;
	}

	/**
	 * Will void or refund a transaction based on whichever option is available.
	 * Voiding a transactions ends the transaction before it's batched, preventing fees from being assessed on the transaction.
	 * Refunding a transaction will return the funds back to the customer.
	 * Void will be used if the transaction is less than 18-24 hours old and refunds can be issued anytime after that.
	 *
	 * @param string $id the ID of the transaction to void or refund
	 *
	 * @return Transaction
	 */
	public function voidOrRefund(string $id, array $data): self
	{
		$this->request('post', "transaction/{$id}/void-or-refund", $data);

		return $this;
	}
}
