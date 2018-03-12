<?php

namespace Tg\Fuzzymock;

use Tg\Fuzzymock\ReturnType\FuzzyOr;
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

        return (new FuzzyOr($this->possibleReturns))->getCode();
    }

}