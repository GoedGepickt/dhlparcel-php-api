<?php

namespace Mvdnbrk\DhlParcel\Resources;

class CustomsDeclarationGood extends BaseResource
{
    /** @var string HS code, 8 or 10 positions. */
    public $code;

    /** @var string */
    public $description;

    /** @var string Country of origin, ISO 3166-1 alpha-2. */
    public $origin;

    /** @var int */
    public $quantity;

    /** @var int|float Total value of the goods on this line. */
    public $value;

    /** @var int|float Total net weight in kg of the goods on this line. */
    public $weight;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * Quantity, value and weight are numbers in the OpenAPI spec of POST /shipments.
     */
    public function toArray(): array
    {
        return [
            'code'        => (string) $this->code,
            'description' => (string) $this->description,
            'origin'      => strtoupper((string) $this->origin),
            'quantity'    => (int) $this->quantity,
            'value'       => round((float) $this->value, 2),
            'weight'      => round((float) $this->weight, 3),
        ];
    }
}
