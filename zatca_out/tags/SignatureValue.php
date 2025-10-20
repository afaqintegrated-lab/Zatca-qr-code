<?php

namespace ZATCA\Tags;

use ZATCA\Tag;

class SignatureValue extends Tag
{
    public function __construct($value)
    {
        parent::__construct(9, $value);
    }
}
