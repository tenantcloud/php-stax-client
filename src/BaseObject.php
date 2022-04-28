<?php

namespace Stax;

use ArrayAccess;
use JsonSerializable;
use ReturnTypeWillChange;

/**
 * Class BaseObject.
 */
class BaseObject implements ArrayAccess, JsonSerializable
{
	protected array $fields;

	protected ?string $apiKey;

	public function __construct(string $apiKey = null)
	{
		if ($apiKey) {
			$this->apiKey = $apiKey;
		} else {
			$this->apiKey = Stax::getApiKey();
		}
	}

	public function __set($k, $v)
	{
		$this->fields[$k] = $v;
	}

	public function __toString(): string
	{
		$class = static::class;

		return $class . ' JSON: ' . $this->toJSON();
	}

	public function &__get($k)
	{
		// function should return a reference, using $nullval to return a reference to null
		$nullVal = null;

		if (!empty($this->fields) && \array_key_exists($k, $this->fields)) {
			return $this->fields[$k];
		}

		return $nullVal;
	}

	public function fromResponse(array $response): self
	{
		$this->fields = $response;

		return $this;
	}

	// ArrayAccess methods
	#[ReturnTypeWillChange]
	public function offsetSet($k, $v)
	{
		$this->{$k} = $v;
	}

	public function offsetExists($k): bool
	{
		return \array_key_exists($k, $this->fields);
	}

	#[ReturnTypeWillChange]
	public function offsetUnset($k)
	{
		unset($this->{$k});
	}

	#[ReturnTypeWillChange]
	public function offsetGet($k)
	{
		return \array_key_exists($k, $this->fields) ? $this->fields[$k] : null;
	}

	public function jsonSerialize(): array
	{
		return $this->toArray();
	}

	/**
	 * Returns an associative array with the key and values composing the Stax object.
	 *
	 * @return array the associative array
	 */
	public function toArray(): array
	{
		$maybeToArray = function ($value) {
			if ($value === null) {
				return null;
			}

			return \is_object($value) && \method_exists($value, 'toArray') ? $value->toArray() : $value;
		};

		return \array_reduce(\array_keys($this->fields), function ($acc, $k) use ($maybeToArray) {
			if ('_' === \mb_substr((string) $k, 0, 1)) {
				return $acc;
			}
			$v = $this->fields[$k];

			if (\is_array($v)) {
				$acc[$k] = \array_map($maybeToArray, $v);
			} else {
				$acc[$k] = $maybeToArray($v);
			}

			return $acc;
		}, []);
	}

	/**
	 * Returns a pretty JSON representation of the Stax object.
	 *
	 * @return string the JSON representation of the Stax object
	 */
	public function toJSON(): string
	{
		return \json_encode($this->toArray(), \JSON_PRETTY_PRINT);
	}
}
