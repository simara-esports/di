<?php

/**
 * Test: Nette\DI\Compiler and ExtensionsExtension.
 */

use Nette\DI,
	Tester\Assert;


require __DIR__ . '/../bootstrap.php';


class FooExtension extends DI\CompilerExtension
{
	function loadConfiguration()
	{
		$this->getContainerBuilder()->parameters['foo'] = 'hello';
	}
}


class BarExtension extends DI\CompilerExtension
{
	private $param;

	public function __construct($param)
	{
		$this->param = $param;
	}

	function loadConfiguration()
	{
		$this->getContainerBuilder()->parameters['bar'] = $this->param;
	}
}


class FirstExtension extends DI\CompilerExtension
{
	private $param;

	function loadConfiguration()
	{
		$this->getContainerBuilder()->parameters['first'] = array_keys($this->compiler->getExtensions());
	}
}


$compiler = new DI\Compiler;
$compiler->addExtension('first', new FirstExtension);
$compiler->addExtension('extensions', new Nette\DI\Extensions\ExtensionsExtension);
$container = createContainer($compiler, '
parameters:
	param: test

extensions:
	foo: FooExtension
	bar: BarExtension(%param%)

foo:
	key: value
');


Assert::same( 'hello', $container->parameters['foo'] );
Assert::same( 'test', $container->parameters['bar'] );
Assert::same( array('first', 'extensions', 'foo', 'bar'), $container->parameters['first'] );
