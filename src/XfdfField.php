<?php

namespace DennisVanDalen\XfdfPhp;

use DennisVanDalen\XfdfPhp\Exceptions\InvalidFieldOptionException;

class XfdfField
{
    public XfdfFieldType $type;

    public string $name;

    public int $flags;

    public string $value;

    public string $justification;

    public ?array $stateOptions = null;

    public bool $changed = false;

    /**
     * @throws InvalidFieldOptionException
     */
    public function setValue(string $value): void
    {
        $this->changed = true;

        // Check if field is a button, if so, check if the value is a valid state option, if not, throw an exception
        if ($this->type == XfdfFieldType::BUTTON) {
            if (! in_array($value, $this->stateOptions)) {
                throw new InvalidFieldOptionException();
            }
        }

        $this->value = $value;
    }
}
