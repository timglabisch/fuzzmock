<?php

namespace Tg\Fuzzymock\ReturnType;

class FuzzyCastString implements ReturnTypeInterface
{
    /** @var ReturnTypeInterface */
    private $decorated;

    public function __construct(ReturnTypeInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function getCode()
    {
        return 'return (string)(function() {' . "\n" . $this->decorated->getCode() . "\n" . '})();';
    }

}