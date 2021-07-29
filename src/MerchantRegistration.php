<?php

namespace Stax;

/**
 * This is an object representing a Stax merchant registration data. You can retrieve it to see
 * what data you need fill to complete registration.
 *
 * @see https://fattmerchant.docs.apiary.io/#reference/0/enrollment/get-merchant-registration-data
 * @see https://fattmerchant.docs.apiary.io/#reference/0/enrollment/update-merchant-registration-data
 *
 * @property string|null $underwriting_status                 Main underwriting status.  Can be null, APPROVED, PENDED, REJECTED.
 * @property array       $underwriting_substatuses            Reason(s) associated to the main underwriting status.
 * @property string|null $annual_volume                       Annual credit card volume.
 * @property string|null $avg_trans_size                      Average credit card transaction size
 * @property string|null $b2b_percent                         Percentage of processing volume taken where the transaction was a business to business transaction
 * @property string|null $bank_routing_number                 Business primary bank routing number
 * @property string|null $bank_account_type
 * @property string|null $bank_account_number                 Business primary bank account number (where funds will be deposited)
 * @property string|null $bank_account_owner_name             Business primary bank account owner name
 * @property string|null $secondary_bank_routing_number       Business secondary bank routing number
 * @property string|null $secondary_bank_account_number       Business secondary bank account number
 * @property string|null $secondary_bank_account_owner_name   Business secondary bank account owner name
 * @property string|null $secondary_bank_account_type         Business secondary bank account type
 * @property string|null $secondary_bank_account_purpose      Business secondary bank account purpose
 * @property string|null $business_address_1                  Business address line 1
 * @property string|null $business_address_2                  Business address line 2
 * @property string|null $business_address_city               Business address city
 * @property string|null $business_address_country            Business address country
 * @property string|null $business_address_state              Business address state
 * @property string|null $business_address_zip                Business address zip code
 * @property string|null $business_dba                        Business "Doing Business As" company name
 * @property string|null $business_email                      Business email address
 * @property string|null $business_fax                        Business fax number
 * @property string|null $business_legal_name                 Business legal name
 * @property string|null $business_open_date                  business open date
 * @property string|null $business_phone_number               Business phone number
 * @property string|null $business_tax_id                     Tax ID/EIN of the business. In some business types, this field will be the principal owner's SSN
 * @property string|null $business_website                    Business website address (including https://)
 * @property string|null $card_not_present_percent            Percentage of processing volume taken card not present
 * @property string|null $card_present_percent                percentage of processing volume taken card present
 * @property string|null $card_swiped_percent                 Percentage of processing volume taken where the card was physically swiped
 * @property string|null $chosen_plan
 * @property string|null $chosen_processing_method
 * @property string|null $company_type                        company type (i.e. "L" for LLC). See docs for more details:  https://fattmerchant.docs.apiary.io/#reference/0/enrollment/update-merchant-registration-data
 * @property string|null $email                               Principal signer email
 * @property string|null $first_name                          Principal signer first name
 * @property string|null $highest_trans_amount                Highest credit card transaction amount taken or expected in dollars. This value is used to determine the account high transaction limit for credit card
 * @property string|null $international                       Merchant international volume percentage
 * @property string|null $internet                            Merchant volume percentage taken over the internet
 * @property string|null $job_title                           Principal signer job title
 * @property string|null $last_name                           Principal signer last name
 * @property string|null $location_type
 * @property array       $meta                                Object containing additional required information about the merchant's registration. See docs for more details: https://fattmerchant.docs.apiary.io/#reference/0/catalog/update-merchant-registration-data
 * @property string|null $moto_percent                        Percentage of processing volume taken over mail order telephone order -- typically the same value as card_not_present_percent
 * @property string|null $network
 * @property string|null $owner_address_1                     Principal signer address line 1
 * @property string|null $owner_address_2                     Principal signer address line 2
 * @property string|null $owner_address_city                  Principal signer address city
 * @property string|null $owner_address_country               Principal signer address country
 * @property string|null $owner_address_state                 Principal signer address state
 * @property string|null $owner_address_zip                   Principal signer address zip code
 * @property string|null $phone_number                        Principal signer phone number
 * @property string|null $plan
 * @property string|null $principal_owners_name               Principal signer full name
 * @property string|null $reason_for_applying                 Reason why the business is applying for payment processing
 * @property string|null $referred_by
 * @property string|null $refund_policy                       Business refund policy
 * @property string|null $seasonal_flag
 * @property string|null $seasonal_months
 * @property string|null $service_you_provide                 Business description of what they sell/ what services they provide
 * @property string|null $title                               Principal signer title/job title
 * @property string|null $user_dob                            Principal signer date of birth
 * @property string|null $user_ssn                            Principal signer social security number
 * @property string|null $merchant_id                         Merchant identifier
 * @property string|null $user_id                             User identifier
 * @property string|null $updated_at                          Date the merchant registration was last updated
 * @property string|null $created_at                          Date the merchant registration was created
 * @property array       $files
 * @property array       $electronic_signature                Electronic signature representing the business signer entering into the merchant payment processing agreement with Stax. This signature takes the place of any physical signature.
 * @property array       $credit_inquiry_electronic_signature Electronic signature to authorize a soft credit pull on the signer(s)
 */
class MerchantRegistration extends ApiResource
{
	/**
	 * @return $this
	 */
	public function find(string $merchantId): self
	{
		$this->request('get', "merchant/{$merchantId}/registration");

		return $this;
	}

	public function update(string $merchantId, array $data): self
	{
		$this->request('put', "merchant/{$merchantId}/registration", $data);

		return $this;
	}
}
