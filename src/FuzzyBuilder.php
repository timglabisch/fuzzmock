<?php

namespace Tg\Fuzzymock;

use Tg\Fuzzymock\ReturnType\FuzzyInstanceCreatorInterface;

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

    public function build(): FuzzyInstanceCreatorInterface
    {
        $refl = new \ReflectionClass($this->classname);

        $klass = 'TgFuzzBuilder' . preg_replace("/[^a-zA-Z0-9]+/", "", uniqid('', true) . uniqid('', true));
        $proxyKlass = $this->buildProxy();


        $out = '';
        $out .= 'namespace Tg\FuzzProxy;' . "\n";
        $out .= 'class ' . $klass . ' implements \Tg\Fuzzymock\ReturnType\FuzzyInstanceCreatorInterface {' . "\n";

        foreach ($refl->getMethods() as $method) {

            if (!$method->isPublic()) {
                continue;
            }

            $out .= ' public $_fuzzyMethodValue' . $method->getName() . ';' . "\n";

        }

        $out .= 'public function __construct() {}' . "\n";

        $out .= 'public function createNew() {' . "\n";

        foreach ($refl->getMethods() as $method) {

            if (!$method->isPublic()) {
                continue;
            }

            $out .= '$proxy = new ' . $proxyKlass . '();'."\n";

            $out .= '$proxy->_fuzzyMethodValue' . $method->getName() . ' = (function() {' . "\n";

            $out .= $this->buildMethodContent($method->getName()) . "\n";

            $out .= '})();' . "\n";

        }

        $out .= 'return $proxy;' . "\n";

        $out .= '}' . "\n";
        $out .= '}' . "\n";

        eval($out);

        $fqn = '\Tg\FuzzProxy\\' . $klass;

        return new $fqn;
    }

    public function buildProxy()
    {
        $refl = new \ReflectionClass($this->classname);

        $klass = 'TgFuzzProxy' . preg_replace("/[^a-zA-Z0-9]+/", "", uniqid('', true) . uniqid('', true));

        $out = '';
        $out .= 'namespace Tg\FuzzProxy;' . "\n";
        $out .= 'class ' . $klass . ' {' . "\n";

        foreach ($refl->getMethods() as $method) {

            if (!$method->isPublic()) {
                continue;
            }

            $out .= 'public $_fuzzyMethodValue' . $method->getName() . ';' . "\n";

        }

        foreach ($refl->getMethods() as $method) {

            if (!$method->isPublic()) {
                continue;
            }

            $methodParameters = [];

            foreach ($method->getParameters() as $parameter) {
                $methodParameters[] = '$' . $parameter->getName();
            }

            $out .= 'public function ' . $method->getName() . '(' . join(', ', $methodParameters) . ') {' . "\n";

            $out .= 'return $this->_fuzzyMethodValue' . $method->getName().';'."\n";

            $out .= '}' . "\n";


        }

        $out .= '}' . "\n";

        eval($out);

        return '\Tg\FuzzProxy\\' . $klass;
    }


}