<?php

namespace Mvdnbrk\DhlParcel\Resources;

use Illuminate\Support\Collection;

class EntrySummaryDeclaration extends BaseResource
{
    /** @var string */
    public $currency;

    /**
     * @var Collection<array-key, CustomsGoodsItem>
     */
    public $customsGoods;

    public function __construct(array $attributes = [])
    {
        $this->setDefaultOptions();

        parent::__construct($attributes);
    }

    public function setDefaultOptions(): self
    {
        $this->currency = 'EUR';

        return $this;
    }

    public function setCurrencyAttribute(string $value): self
    {
        $this->currency = $value;

        return $this;
    }

    /**
     * @param  Collection<array-key, CustomsGoodsItem>|array  $value
     * @return $this
     */
    public function setCustomsGoodsAttribute($value): self
    {
        if (is_array($value)) {
            $value = collect($value);
        }

        $this->customsGoods = $value->map(function ($value) {
            if ($value instanceof CustomsGoodsItem) {
                return $value;
            }

            return new CustomsGoodsItem($value);
        });

        return $this;
    }

    public function toArray(): array
    {
        return array_filter([
            'currency'     => $this->currency,
            'customsGoods' => $this->customsGoods->map(function ($item) {
                return $item->toArray();
            })->toArray(),
        ]);
    }
}
