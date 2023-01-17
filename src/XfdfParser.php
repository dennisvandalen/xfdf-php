<?php

namespace DennisVanDalen\XfdfPhp;

class XfdfParser
{
    /**
     * @var XfdfField[]
     */
    private array $fields = [];

    public function parseFields(string $input): array
    {
        $lines = explode(PHP_EOL, $input);
        $field = new XfdfField();
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }
            if ($line == '---') {
                if (! empty($field->name)) {
                    $this->fields[] = $field;
                }
                $field = new XfdfField();

                continue;
            }
            $parts = explode(':', $line);
            $key = trim($parts[0]);
            $value = trim($parts[1]);
            switch ($key) {
                case 'FieldType':
                    if ($value == 'Button') {
                        $field->type = XfdfFieldType::BUTTON;
                    } elseif ($value == 'Text') {
                        $field->type = XfdfFieldType::TEXT;
                    } else {
                        $field->type = XfdfFieldType::UNKNOWN;
                    }
                    break;
                case 'FieldName':
                    $field->name = $value;
                    break;
                case 'FieldFlags':
                    $field->flags = intval($value);
                    break;
                case 'FieldValue':
                    $field->value = $value;
                    break;
                case 'FieldJustification':
                    $field->justification = $value;
                    break;
                case 'FieldStateOption':
                    if (! $field->stateOptions) {
                        $field->stateOptions = [];
                    }
                    $field->stateOptions[] = $value;
                    break;
            }
        }
        if (! empty($field->name)) {
            $this->fields[] = $field;
        }

        return $this->fields;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getField(string $name): ?XfdfField
    {
        foreach ($this->fields as $field) {
            if ($field->name == $name) {
                return $field;
            }
        }

        return null;
    }

    /**
     * Export the fields to XFDF format
     */
    public function exportFields($onlyChanged = true): string
    {
        $output = '<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL;
        $output .= '<xfdf xmlns="http://ns.adobe.com/xfdf/" xml:space="preserve">'.PHP_EOL;
        $output .= '<fields>'.PHP_EOL;
        foreach ($this->fields as $field) {
            // only if changed
            if ($onlyChanged && ! $field->changed) {
                continue;
            }

            $output .= '<field name="'.$field->name.'">'.PHP_EOL;
            if ($field->stateOptions) {
                $output .= '<choice>'.$field->value.'</choice>'.PHP_EOL;
            } else {
                $output .= '<value>'.htmlspecialchars($field->value).'</value>'.PHP_EOL;
            }
            $output .= '</field>'.PHP_EOL;
        }
        $output .= '</fields>'.PHP_EOL;
        $output .= '</xfdf>'.PHP_EOL;

        return $output;
    }
}
