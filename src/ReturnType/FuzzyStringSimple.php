<?php

namespace Tg\Fuzzymock\ReturnType;

class FuzzyStringSimple implements ReturnTypeInterface
{
    /** @var int  */
    private $length;

    public function __construct(int $length = 10)
    {
        $this->length = $length;
    }

    public function getCode()
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ \'"';

        $len = mb_strlen($chars);

        $repeatedChars = str_replace("'", "\\'", str_repeat($chars, ceil($this->length / $len)));

        return 'return substr(str_shuffle(\''.$repeatedChars.'\'), 0, '.$this->length.');';
    }
}