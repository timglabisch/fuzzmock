<?php

namespace Tg\Fuzzymock\ReturnType;

class FuzzyInt implements ReturnTypeInterface
{
    /** @var int */
    private $min;

    /** @var int */
    private $max;

    public function __construct(int $min = PHP_INT_MIN, int $max = PHP_INT_MAX)
    {
        $this->min = $min;
        $this->max = $max;
    }

    public function getCode()
    {
        return 'return \mt_rand(' . $this->min . ', ' . $this->max . ');';
    }

}