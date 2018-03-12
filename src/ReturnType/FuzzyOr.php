<?php

namespace Tg\Fuzzymock\ReturnType;

class FuzzyOr implements ReturnTypeInterface
{
    /** @var ReturnTypeInterface[] */
    private $possibleReturns;

    /** @param array ReturnTypeInterface[] */
    public function __construct(array $possibleReturns)
    {
        $this->possibleReturns = $possibleReturns;
    }

    public function getCode()
    {
        $possibleReturnsCount = \count($this->possibleReturns);

        if ($possibleReturnsCount === 1) {
            return $this->possibleReturns[0]->getCode();
        }

        $code = '';
        $code .= '$rand = \mt_rand(0, '.($possibleReturnsCount - 1).');'."\n";

        foreach ($this->possibleReturns as $i => $possibleReturn) {
            $code .= "\n".'if ($rand === '.$i.') {'."\n";
            $code .= $possibleReturn->getCode();
            $code .= "\n".'}'."\n";
        }

        return $code;
    }
}