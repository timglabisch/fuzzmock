<?php

namespace Tg\Fuzzymock;

use Tg\Fuzzymock\ReturnType\ReturnTypeInterface;

class FuzzyMethodBuilder
{
    /** @var string */
    private $methodName;

    /** @var ReturnTypeInterface[] */
    private $possibleReturns = [];

    public function __construct(string $methodName)
    {
        $this->methodName = $methodName;
    }

    public function couldReturn(ReturnTypeInterface $returnType): FuzzyMethodBuilder
    {
        $this->possibleReturns[] = $returnType;

        return $this;
    }

    public function getMethodName(): string
    {
        return $this->methodName;
    }

    public function getPossibleReturns(): array
    {
        return $this->possibleReturns;
    }

    public function getCode(): string
    {
        if (empty($this->possibleReturns)) {
            return 'throw new \\LogicException(\'method "'.$this->methodName.'" cant be called, couldReturn is not configured\');';
        }

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