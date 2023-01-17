<?php

namespace DennisVanDalen\XfdfPhp\Exceptions;

use Exception;

class InvalidFieldOptionException extends Exception
{
    public static function create(): self
    {
        return new static('Invalid state option');
    }
}
