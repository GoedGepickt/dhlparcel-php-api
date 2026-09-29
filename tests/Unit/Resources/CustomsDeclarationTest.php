<?php

namespace Mvdnbrk\DhlParcel\Tests\Unit\Resources;

use Mvdnbrk\DhlParcel\Resources\CustomsDeclaration;
use Mvdnbrk\DhlParcel\Resources\CustomsDeclarationGood;
use Mvdnbrk\DhlParcel\Resources\Parcel;
use Mvdnbrk\DhlParcel\Resources\Recipient;
use Mvdnbrk\DhlParcel\Resources\TaxReference;
use Mvdnbrk\DhlParcel\Tests\TestCase;

class CustomsDeclarationTest extends TestCase
{
    /** @test */
    public function it_defaults_to_euro_without_goods()
    {
        $declaration = new CustomsDeclaration;

        $this->assertEquals('EUR', $declaration->currency);
        $this->assertCount(0, $declaration->goods);
        $this->assertEquals(['currency' => 'EUR', 'goods' => []], $declaration->toArray());
    }

    /** @test */
    public function to_array()
    {
        $declaration = new CustomsDeclaration([
            'currency' => 'CHF',
            'invoice_number' => 'INV-123',
            'invoice_type' => CustomsDeclaration::INVOICE_TYPE_COMMERCIAL,
            'export_type' => CustomsDeclaration::EXPORT_TYPE_PERMANENT,
            'export_reason' => CustomsDeclaration::EXPORT_REASON_SALE_OF_GOODS,
            'inco_terms' => 'DDU',
            'sender_inbound_vat_number' => 'GB123456789',
            'shipping_fee' => ['currency' => 'CHF', 'value' => 4.95],
            'goods' => [
                [
                    'code' => '61091000',
                    'description' => 'T-shirt',
                    'origin' => 'nl',
                    'quantity' => 2,
                    'value' => 39.9,
                    'weight' => 0.4,
                ],
            ],
        ]);

        $this->assertEquals([
            'currency' => 'CHF',
            'invoiceNumber' => 'INV-123',
            'invoiceType' => 'commercial',
            'exportType' => 'Permanent',
            'exportReason' => 'SaleOfGoods',
            'incoTerms' => 'DDU',
            'senderInboundVatNumber' => 'GB123456789',
            'shippingFee' => ['currency' => 'CHF', 'value' => '4.95'],
            'goods' => [
                [
                    'code' => '61091000',
                    'description' => 'T-shirt',
                    'origin' => 'NL',
                    'quantity' => '2',
                    'value' => '39.9',
                    'weight' => '0.4',
                ],
            ],
        ], $declaration->toArray());
    }

    /** @test */
    public function goods_can_be_added()
    {
        $declaration = new CustomsDeclaration;

        $declaration->addGood(new CustomsDeclarationGood(['code' => '61091000']));
        $declaration->addGood(['code' => '42022100']);

        $this->assertCount(2, $declaration->goods);
        $this->assertContainsOnlyInstancesOf(CustomsDeclarationGood::class, $declaration->goods);
        $this->assertEquals('42022100', $declaration->goods->last()->code);
    }

    /** @test */
    public function a_parcel_includes_the_customs_declaration()
    {
        $parcel = new Parcel([
            'customs_declaration' => [
                'invoice_number' => 'INV-123',
                'goods' => [['code' => '61091000']],
            ],
        ]);

        $this->assertInstanceOf(CustomsDeclaration::class, $parcel->customsDeclaration);
        $this->assertArrayHasKey('customsDeclaration', $parcel->toArray());
        $this->assertEquals('INV-123', $parcel->toArray()['customsDeclaration']['invoiceNumber']);
    }

    /** @test */
    public function a_parcel_without_customs_declaration_omits_the_key()
    {
        $this->assertArrayNotHasKey('customsDeclaration', (new Parcel)->toArray());
    }

    /** @test */
    public function a_recipient_includes_tax_references()
    {
        $recipient = new Recipient([
            'company_name' => 'Test Company B.V.',
            'tax_references' => [
                ['type' => TaxReference::TYPE_VAT, 'value' => 'NL123456789B01'],
            ],
        ]);
        $recipient->addTaxReference(TaxReference::TYPE_EORI, 'NL123456789');

        $array = $recipient->toArray();

        $this->assertEquals([
            ['type' => 'VAT', 'value' => 'NL123456789B01'],
            ['type' => 'EORI', 'value' => 'NL123456789'],
        ], $array['taxReferences']);
        $this->assertArrayNotHasKey('tax_references', $array['address']);
    }

    /** @test */
    public function a_recipient_without_tax_references_omits_the_key()
    {
        $this->assertArrayNotHasKey('taxReferences', (new Recipient(['company_name' => 'Test']))->toArray());
    }
}
