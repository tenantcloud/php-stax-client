<?php

namespace Stax;

/**
 * Class Paginator
 */
class Paginator
{
	public array $data = [];

	public int $total;

	public int $per_page;

	public int $current_page;

	public int $last_page;

	public ?string $next_page_url;

	public ?string $prev_page_url;

	public ?int $from;

	public ?int $to;

	public function buildFromResponse(array $response, ApiResource $class): self
	{
		foreach ($response as $key => $value) {
			if (property_exists(self::class, $key)) {
				if ($key === 'data') {
					foreach ($value as $data) {
						$this->data[] = clone $class->fromResponse($data);
					}
				} else {
					$this->{$key} = $value;
				}
			}
		}

		return $this;
	}
}
