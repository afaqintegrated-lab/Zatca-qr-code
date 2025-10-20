<?php

namespace ZATCA\Tags;

use ZATCA\Tag;

class DigitalSignature extends Tag
{
    public function __construct($value)
    {
        parent::__construct(7, $value);
    }
}
