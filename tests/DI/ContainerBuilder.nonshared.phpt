<?php

/**
 * Test: Nette\DI\ContainerBuilder and non-shared services.
 */

use Nette\DI,
	Tester\Assert;


require __DIR__ . '/../bootstrap.php';


class Service
{
	function __construct()
	{
	}
}


$builder = new DI\ContainerBuilder;
$builder->addDefinition('one')
	->setClass('Service', array(new Nette\DI\Statement('@two', array('foo'))));

$two = $builder->addDefinition('two')
	->setParameters(array('foo', 'bar' => FALSE, 'array foobar' => NULL))
	->setClass('stdClass')
	->addSetup('$foo', array($builder::literal('$foo')));

$builder->addDefinition('three')
	->setFactory($two, array('hello'));


$container = createContainer($builder);

Assert::type( 'Service', $container->getService('one') );
Assert::true( $container->hasService('two') );
Assert::true( method_exists($container, 'createServiceTwo') );
Assert::type( 'stdClass', $container->getService('three') );
Assert::same( 'hello', $container->getService('three')->foo );
