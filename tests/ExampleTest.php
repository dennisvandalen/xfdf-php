<?php

declare(strict_types=1);

use DennisVanDalen\XfdfPhp\Exceptions\InvalidFieldOptionException;
use DennisVanDalen\XfdfPhp\XfdfFieldType;
use DennisVanDalen\XfdfPhp\XfdfParser;

it('can test', function () {
    expect(true)->toBeTrue();
});

it('can parse button and text fields', function () {
    $xfdf = new XfdfParser();
    $xfdf->parseFields(
        <<<'PDFFIELD'
---
FieldType: Button
FieldName: 1.1.a
FieldFlags: 0
FieldValue: Off
FieldJustification: Left
FieldStateOption: Nee. Ga verder met vraag 1d.
FieldStateOption: Off
FieldStateOption: Ja. Ga verder met vraag 1b.
---
FieldType:
FieldName: 1.1
FieldFlags: 0
FieldJustification: Left
---
FieldType: Text
FieldName: 1.2.b
FieldFlags: 12582912
FieldJustification: Left
PDFFIELD
    );

    expect($xfdf->getFields())->toHaveCount(3)
        ->and($xfdf->getFields()[0]->type)->toBe(XfdfFieldType::BUTTON)
        ->and($xfdf->getFields()[0]->stateOptions)->toBeArray()
        ->and($xfdf->getFields()[0]->stateOptions)->toBe([
            'Nee. Ga verder met vraag 1d.',
            'Off',
            'Ja. Ga verder met vraag 1b.',
        ]);

    $xfdf->getFields()[0]->setValue('Ja. Ga verder met vraag 1b.');
    expect($xfdf->getFields()[0]->value)->toBe('Ja. Ga verder met vraag 1b.');
});

it('can generate xfdf', function () {
    $xfdf = new XfdfParser();
    $xfdf->parseFields(
        <<<'PDFFIELD'
---
FieldType: Button
FieldName: 1.1.a
FieldFlags: 0
FieldValue: Off
FieldJustification: Left
FieldStateOption: Nee. Ga verder met vraag 1d.
FieldStateOption: Off
FieldStateOption: Ja. Ga verder met vraag 1b.
---
FieldType: Text
FieldName: 1.2.b
FieldFlags: 12582912
FieldJustification: Left
PDFFIELD
    );

    $xfdf->getFields()[0]->setValue('Ja. Ga verder met vraag 1b.');
    $xfdf->getFields()[1]->setValue('Any text');

    expect($xfdf->exportFields())->toBe(
        <<<'XFDF'
<?xml version="1.0" encoding="UTF-8"?>
<xfdf xmlns="http://ns.adobe.com/xfdf/" xml:space="preserve">
<fields>
<field name="1.1.a">
<choice>Ja. Ga verder met vraag 1b.</choice>
</field>
<field name="1.2.b">
<value>Any text</value>
</field>
</fields>
</xfdf>

XFDF
    );
});

it('throws exception when setting invalid value', function () {
    $this->expectException(InvalidFieldOptionException::class);
    $xfdf = new XfdfParser();
    $xfdf->parseFields(
        <<<'PDFFIELD'
---
FieldType: Button
FieldName: 1.1.a
FieldFlags: 0
FieldValue: Off
FieldJustification: Left
FieldStateOption: Nee. Ga verder met vraag 1d.
FieldStateOption: Off
FieldStateOption: Ja. Ga verder met vraag 1b.
PDFFIELD
    );

    $xfdf->getFields()[0]->setValue('invalid');
});
