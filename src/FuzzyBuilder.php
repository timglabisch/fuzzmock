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
            return '';
            return 'throw new \\LogicException(\'unconfigured method "' . $methodName . '"\');';
        }

        return $this->fuzzyMethods[$methodName]->getCode();

    }

    public function createBuilder(): FuzzyInstanceCreatorInterface
    {
        $refl = new \ReflectionClass($this->classname);

        $klass = 'TgFuzzBuilder' . preg_replace("/[^a-zA-Z0-9]+/", "", uniqid('', true) . uniqid('', true));
        $proxyKlass = $this->buildProxy();


        $out = '';
        $out .= 'namespace Tg\FuzzProxy;' . "\n";
        $out .= 'class ' . $klass . ' implements \Tg\Fuzzymock\ReturnType\FuzzyInstanceCreatorInterface {' . "\n";

        foreach ($refl->getMethods() as $method) {

            if ($method->isConstructor()) {
                continue;
            }

            if (!$method->isPublic()) {
                continue;
            }

            $out .= ' public $_fuzzyMethodValue' . $method->getName() . ';' . "\n";

        }

        $out .= 'public function __construct() {}' . "\n";

        $out .= 'public function createNew() {' . "\n";

        $out .= '$proxy = new ' . $proxyKlass . '();' . "\n";

        foreach ($refl->getMethods() as $method) {

            if ($method->isConstructor()) {
                continue;
            }

            if (!$method->isPublic()) {
                continue;
            }

            $out .= '$proxy->_fuzzyMethodValue' . $method->getName() . ' = (function() use ($proxy) {' . "\n";

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

    private function buildReturnType(\ReflectionMethod $method): string
    {
        if (!$method->hasReturnType()) {
            return '';
        }

        $out = ': ';

        if ($method->getReturnType()->allowsNull()) {
            $out .= '?';
        }

        if ($method->getReturnType()->getName() === 'self') {
            return $out . '\\' . $this->classname;
        }

        if ($method->getReturnType()->isBuiltin()) {
            return $out . '' . $method->getReturnType()->getName();
        }

        return ' : \\' . $method->getReturnType()->getName();
    }

    public function buildParameterType(\ReflectionParameter $parameter)
    {
        if (!$parameter->hasType()) {
            return '';
        }

        $param = '';

        if ($parameter->getType()->allowsNull()) {
            $param .= '?';
        }

        if ($parameter->getType()->isBuiltin()) {
            return $param . $parameter->getType()->getName();
        }

        return $param . '\\' . $parameter->getType()->getName();
    }

    public function buildParameterDefault(\ReflectionParameter $parameter)
    {
        if (!$parameter->isDefaultValueAvailable()) {
            return '';
        }

        return '= ' . var_export($parameter->getDefaultValue(), true);
    }

    public function buildProxy()
    {
        $refl = new \ReflectionClass($this->classname);

        $klass = 'TgFuzzProxy' . preg_replace("/[^a-zA-Z0-9]+/", "", uniqid('', true) . uniqid('', true));

        $out = '';
        $out .= 'namespace Tg\FuzzProxy;' . "\n";

        $out .= 'class ' . $klass . ' extends \\' . $this->classname . ' {' . "\n";

        $out .= 'public function __construct() {}' . "\n";

        foreach ($refl->getMethods() as $method) {

            if ($method->isConstructor()) {
                continue;
            }

            if ($method->isStatic()) {
                continue;
            }

            if (!$method->isPublic()) {
                continue;
            }

            $out .= 'public $_fuzzyMethodValue' . $method->getName() . ';' . "\n";

        }

        foreach ($refl->getMethods() as $method) {

            if ($method->isConstructor()) {
                continue;
            }

            if ($method->isStatic()) {
                continue;
            }

            if (!$method->isPublic()) {
                continue;
            }

            $methodParameters = [];

            foreach ($method->getParameters() as $parameter) {
                $methodParameters[] = $this->buildParameterType($parameter) . ' $' . $parameter->getName() . $this->buildParameterDefault($parameter);
            }

            $returnType = $this->buildReturnType($method);
            $out .= 'public function ' . $method->getName() . '(' . join(', ', $methodParameters) . ') ' . $this->buildReturnType($method) . ' {' . "\n";

            if ($method->hasReturnType() && $method->getReturnType()->getName() === 'void') {
                $out .= '}' . "\n";
                continue;
            }

            $out .= 'return $this->_fuzzyMethodValue' . $method->getName() . ';' . "\n";

            $out .= '}' . "\n";


        }

        $out .= '}' . "\n";

        eval($out);

        return '\Tg\FuzzProxy\\' . $klass;
    }


}