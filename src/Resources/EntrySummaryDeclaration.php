<?php

namespace Mvdnbrk\DhlParcel\Resources;

use Illuminate\Support\Collection;

class EntrySummaryDeclaration extends BaseResource
{
    /** @var string */
    public $currency;

    /**
     * @var Collection<array-key, CustomGoodsItem>
     */
    public $customGoods;

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
     * @param  Collection<array-key, CustomGoodsItem>|array  $value
     * @return $this
     */
    public function setCustomGoodsAttribute($value): self
    {
        if (is_array($value)) {
            $value = collect($value);
        }

        $this->customGoods = $value->map(function ($value) {
            if ($value instanceof CustomGoodsItem) {
                return $value;
            }

            return new CustomGoodsItem($value);
        });

        return $this;
    }

    public function toArray(): array
    {
        return array_filter([
            'currency'    => $this->currency,
            'customGoods' => $this->customGoods->map(function ($item) {
                return $item->toArray();
            })->toArray(),
        ]);
    }
}
