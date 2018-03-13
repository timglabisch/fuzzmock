<?php

namespace Tg\Fuzzymock\ReturnType;

class FuzzyBool implements ReturnTypeInterface
{
    public function getCode()
    {
        return 'return (bool)\mt_rand(0,1);';
    }

}