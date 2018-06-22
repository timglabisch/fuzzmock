<?php

namespace Tg\Fuzzymock\ReturnType;

class FuzzyFunctionResult implements ReturnTypeInterface
{

    private static $indexCounter = 0;

    private static $variableStorage = [];

    /** @var int */
    private $index;

    public function __construct(callable $var)
    {
        $this->index = self::$indexCounter++;
        self::$variableStorage[$this->index] = $var;
    }

    public function getCode()
    {
        $code = '$r = \\' . self::class . '::unsafeGetVariable(' . $this->index . ');'."\n";
        $code .= 'return $r($proxy);'."\n";
        return $code;
    }

    public static function unsafeGetVariable(int $index)
    {
        return self::$variableStorage[$index];
    }

    public function __destruct()
    {
        unset(self::$variableStorage[$this->index]);
    }

}