<?php

namespace Mvdnbrk\DhlParcel\Resources;

use Illuminate\Support\Collection;

/**
 * Customs declaration sent along with a shipment (POST /shipments).
 * DHL generates the CN23 form or commercial invoice based on this data.
 *
 * @see https://api-gw.dhlparcel.nl/docs/guide/chapters/04-labels.html
 */
class CustomsDeclaration extends BaseResource
{
    public const INVOICE_TYPE_COMMERCIAL = 'commercial';

    public const EXPORT_TYPE_PERMANENT = 'Permanent';
    public const EXPORT_TYPE_REPAIR = 'Repair';
    public const EXPORT_TYPE_RETURN = 'Return';
    public const EXPORT_TYPE_TEMPORARY = 'Temporary';

    public const EXPORT_REASON_OTHER = 'Other';
    public const EXPORT_REASON_SALE_OF_GOODS = 'SaleOfGoods';
    public const EXPORT_REASON_RETURNED_GOODS = 'ReturnedGoods';
    public const EXPORT_REASON_GIFT = 'Gift';
    public const EXPORT_REASON_COMMERCIAL_SAMPLE = 'CommercialSample';
    public const EXPORT_REASON_DOCUMENTS = 'Documents';

    /** @var string */
    public $currency;

    /** @var string|null */
    public $invoice_number;

    /** @var string|null */
    public $invoice_type;

    /** @var string|null */
    public $remarks;

    /** @var string|null */
    public $export_type;

    /** @var string|null */
    public $export_reason;

    /** @var string|null */
    public $inco_terms;

    /** @var string|null */
    public $inco_terms_city;

    /** @var string|null Only applicable for shipments to GB. */
    public $sender_inbound_vat_number;

    /** @var bool|null */
    public $vat_reverse_charge;

    /** @var array{currency: string, value: int|float|string}|null */
    public $shipping_fee;

    /** @var Collection<array-key, CustomsDeclarationGood> */
    public $goods;

    public function __construct(array $attributes = [])
    {
        $this->currency = 'EUR';
        $this->goods = new Collection;

        parent::__construct($attributes);
    }

    /**
     * @param  Collection<array-key, CustomsDeclarationGood|array>|array<array-key, CustomsDeclarationGood|array>  $value
     * @return $this
     */
    public function setGoodsAttribute($value): self
    {
        $this->goods = collect($value)->map(function ($good) {
            return $good instanceof CustomsDeclarationGood ? $good : new CustomsDeclarationGood($good);
        })->values();

        return $this;
    }

    /**
     * @param  CustomsDeclarationGood|array  $good
     * @return $this
     */
    public function addGood($good): self
    {
        $this->goods->push($good instanceof CustomsDeclarationGood ? $good : new CustomsDeclarationGood($good));

        return $this;
    }

    public function toArray(): array
    {
        return collect([
            'currency'               => $this->currency,
            'invoiceNumber'          => $this->invoice_number,
            'invoiceType'            => $this->invoice_type,
            'remarks'                => $this->remarks,
            'exportType'             => $this->export_type,
            'exportReason'           => $this->export_reason,
            'incoTerms'              => $this->inco_terms,
            'incoTermsCity'          => $this->inco_terms_city,
            'senderInboundVatNumber' => $this->sender_inbound_vat_number,
            'vatReverseCharge'       => $this->vat_reverse_charge,
            'shippingFee'            => $this->shipping_fee === null ? null : [
                'currency' => (string) $this->shipping_fee['currency'],
                'value'    => (string) $this->shipping_fee['value'],
            ],
            'goods'                  => $this->goods->map(function (CustomsDeclarationGood $good) {
                return $good->toArray();
            })->all(),
        ])
            ->reject(function ($value) {
                return $value === null || $value === '';
            })
            ->all();
    }
}
