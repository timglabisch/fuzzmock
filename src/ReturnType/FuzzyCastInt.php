<?php

namespace Tg\Fuzzymock\ReturnType;

class FuzzyCastInt implements ReturnTypeInterface
{
    /** @var ReturnTypeInterface */
    private $decorated;

    public function __construct(ReturnTypeInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function getCode()
    {
        return 'return (float)(function() {' . "\n" . $this->decorated->getCode() . "\n" . '})();';
    }

}