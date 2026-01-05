<?php

namespace Mvdnbrk\DhlParcel\Resources;

class CustomsGoodsItem extends BaseResource
{
    /** @var string */
    public $code;

    /** @var string */
    public $description;

    /** @var int */
    public $quantity;

    /** @var int */
    public $netWeightKg;

    /** @var int */
    public $value;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function setCodeAttribute(string $value): self
    {
        $this->code = $value;

        return $this;
    }

    public function setDescriptionAttribute(string $value): self
    {
        $this->description = $value;

        return $this;
    }

    public function setQuantityAttribute(int $value): self
    {
        $this->quantity = $value;

        return $this;
    }

    public function setNetWeightKgAttribute(int $value): self
    {
        $this->netWeightKg = $value;

        return $this;
    }

    public function setValueAttribute(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function toArray(): array
    {
        return array_filter([
            'code'        => $this->code,
            'description' => $this->description,
            'quantity'    => $this->quantity,
            'netWeightKg' => $this->netWeightKg,
            'value'       => $this->value,
        ]);
    }
}
