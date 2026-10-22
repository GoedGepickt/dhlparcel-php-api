<?php

namespace Mvdnbrk\DhlParcel\Resources;

class Recipient extends Address
{
    /** @var string */
    public $is_business = null;

    /** @var string */
    public $company_name;

    /** @var string */
    public $first_name;

    /** @var string */
    public $last_name;

    /** @var string */
    public $email;

    /** @var string */
    public $phone;

    /** @var \Illuminate\Support\Collection<array-key, TaxReference>|null */
    public $tax_references = null;

    public function setCompanyAttribute(string $value): void
    {
        $this->company_name = $value;
    }

    /**
     * Set the tax references (VAT, EORI, etc.) for this recipient.
     *
     * @param  \Illuminate\Support\Collection<array-key, TaxReference|array>|array<array-key, TaxReference|array>|null  $value
     * @return void
     */
    public function setTaxReferencesAttribute($value): void
    {
        if ($value === null) {
            $this->tax_references = null;

            return;
        }

        $this->tax_references = collect($value)->map(function ($reference) {
            return $reference instanceof TaxReference ? $reference : new TaxReference($reference);
        })->values();
    }

    public function addTaxReference(string $type, string $value): self
    {
        if ($this->tax_references === null) {
            $this->tax_references = collect();
        }

        $this->tax_references->push(new TaxReference([
            'type' => $type,
            'value' => $value,
        ]));

        return $this;
    }

    private function addressToArray(): array
    {
        return collect(parent::toArray())
            ->reject(function ($value, $key) {
                return $key === 'is_business';
            })
            ->diffKeys([
                'company_name' => '',
                'first_name' => '',
                'last_name' => '',
                'email' => '',
                'phone' => '',
                'tax_references' => '',
            ])
            ->when($this->is_business !== null, function ($collection) {
                return $collection->put('isBusiness', $this->is_business);
            })
            ->when(! empty($this->company_name), function ($collection) {
                return $collection->put('isBusiness', $this->is_business === null ? false : $this->is_business);
            })
            ->all();
    }

    private function nameToArray(): array
    {
        return collect([
            'firstName' => $this->first_name,
            'lastName' => $this->last_name,
            'companyName' => $this->company_name,
        ])
            ->filter()
            ->all();
    }

    public function toArray(): array
    {
        return collect([
            'name' => $this->nameToArray(),
            'address' => $this->addressToArray(),
        ])
            ->when(! empty($this->email), function ($collection) {
                return $collection->put('email', $this->email);
            })
            ->when(! empty($this->phone), function ($collection) {
                return $collection->put('phoneNumber', $this->phone);
            })
            ->when($this->tax_references !== null && $this->tax_references->isNotEmpty(), function ($collection) {
                return $collection->put('taxReferences', $this->tax_references->map(function (TaxReference $reference) {
                    return $reference->toArray();
                })->all());
            })
            ->all();
    }
}
