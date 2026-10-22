<?php

namespace Mvdnbrk\DhlParcel\Resources;

class TaxReference extends BaseResource
{
    public const TYPE_VAT = 'VAT';
    public const TYPE_EORI = 'EORI';
    public const TYPE_VOEC = 'VOEC';
    public const TYPE_UID = 'UID';

    /** @var string */
    public $type;

    /** @var string */
    public $value;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function toArray(): array
    {
        return [
            'type'  => (string) $this->type,
            'value' => (string) $this->value,
        ];
    }
}
