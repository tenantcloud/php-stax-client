<?php

namespace Stax;

/**
 * Shows list of your merchant's deposits filtered by the batched_at date by default.
 * Results may be filtered by specifying a date range.
 * For docs for more details. @see https://fattmerchant.docs.apiary.io/#reference/0/reports/get-list-of-deposits
 *
 * @property string      $batch_id
 * @property string      $batched_at
 * @property string|null $last_transaction
 * @property int         $count
 * @property float       $sum
 * @property float       $avg
 * @property float       $min
 * @property float       $max
 * @property float       $std
 */
class Deposit extends ApiResource
{
	/**
	 * @param array $filter Array of filters. See docs to find all possible filters https://fattmerchant.docs.apiary.io/#reference/0/customers/get-list-of-deposits
	 */
	public function all(array $filter = []): Paginator
	{
		return $this->request('get', 'query/deposit', $filter);
	}
}
