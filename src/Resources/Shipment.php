<?php

namespace Mvdnbrk\DhlParcel\Resources;

use Mvdnbrk\DhlParcel\Resources\Shipment as ShipmentResource;
use Illuminate\Support\Collection;

class Shipment extends BaseResource
{
    /** @var string */
    public $id;

    /** @var string */
    public $barcode;

    /** @var string */
    public $label_id;

    /** @var \Illuminate\Support\Collection */
    public $pieces;

    /** @var ShipmentResource */
    public $returnShipment;

    public function __construct(array $attributes = [])
    {
        $this->pieces = new Collection;

        parent::__construct($attributes);
    }
}
