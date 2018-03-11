<?php

namespace Tg\Fuzzymock\ReturnType;

class FuzzyFloat implements ReturnTypeInterface
{
    /** @var float */
    private $min;

    /** @var float */
    private $max;

    private $precision;

    public function __construct(float $min = PHP_INT_MIN, float $max = PHP_INT_MAX, int $precision = null)
    {
        $this->min = $min;
        $this->max = $max;
        $this->precision = $precision;
    }

    public function getCode()
    {
        if ($this->precision) {
            return 'return round(' . $this->min . ' + \mt_rand() / \mt_getrandmax() * (' . $this->max . ' - ' . $this->min . '), ' . $this->precision . ');';
        }

        return 'return ' . $this->min . ' + \mt_rand() / \mt_getrandmax() * (' . $this->max . ' - ' . $this->min . ');';
    }

}