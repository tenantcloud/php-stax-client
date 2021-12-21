<?php

namespace Stax;

/**
 * Shows granular detail about a specific deposit.
 * As deposits may sometimes have the same batch id, be sure to use batchId, startDate, and endDate when making your query.
 * For docs for more details. @see https://fattmerchant.docs.apiary.io/#reference/0/reports/get-detail-of-specific-deposit
 *
 * @property string $batch_id
 * @property string $batched_at
 * @property string $created_at
 * @property int    $total
 * @property string $method
 * @property string $last_four
 * @property float  $fees
 * @property string $transaction_id
 */
class DepositDetails extends ApiResource
{
	/**
	 * @param array $filter Array of filters. See docs to find all possible filters https://fattmerchant.docs.apiary.io/#reference/0/reports/get-detail-of-specific-deposit
	 */
	public function all(array $filter = []): Paginator
	{
		return $this->request('get', 'query/depositDetail', $filter);
	}
}
