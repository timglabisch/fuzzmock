<?php

namespace Tg\Fuzzymock;

class FuzzyBuilder
{
    private $classname;

    /** @var FuzzyMethodBuilder[] */
    private $fuzzyMethods = [];

    public function __construct(string $classname)
    {
        $this->classname = $classname;
    }

    public function fn(string $methodName): FuzzyMethodBuilder
    {
        $this->fuzzyMethods[$methodName] = $method = new FuzzyMethodBuilder($methodName);

        return $method;
    }

    private function buildMethodContent(string $methodName): string
    {
        if (!isset($this->fuzzyMethods[$methodName])) {
            return 'throw new \\LogicException(\'unconfigured method "' . $methodName . '"\');';
        }

        return $this->fuzzyMethods[$methodName]->getCode();

    }

    public function build()
    {
        $refl = new \ReflectionClass($this->classname);

        $klass = 'TgFuzzProxy' . preg_replace("/[^a-zA-Z0-9]+/", "", uniqid('', true) . uniqid('', true));

        $out = '';
        $out .= 'namespace Tg\FuzzProxy;' . "\n";
        $out .= 'class ' . $klass . ' {' . "\n";

        $out .= 'public function __construct() {}' . "\n";

        foreach ($refl->getMethods() as $method) {

            if (!$method->isPublic()) {
                continue;
            }

            $methodParameters = [];

            foreach ($method->getParameters() as $parameter) {
                $methodParameters[] = '$' . $parameter->getName();
            }

            $out .= 'function ' . $method->getName() . '(' . join(', ', $methodParameters) . ') {' . "\n";

            $out .= $this->buildMethodContent($method->getName()) . "\n";

            $out .= '}' . "\n";


        }

        $out .= '}' . "\n";

        eval($out);

        $fqn = '\Tg\FuzzProxy\\' . $klass;

        return new $fqn;
    }


}