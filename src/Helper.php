<?php

namespace Stax;

class Helper extends ApiResource
{
	public function setupGatewayForMerchant(string $id): void
	{
		$this->request('post', "merchant/{$id}/gateway/", [
			'name'   => 'test',
			'type'   => 'TEST',
			'vendor' => 'spreedly',
		]);
	}
}
