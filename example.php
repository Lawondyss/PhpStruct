<?php
namespace Foo;

require_once __DIR__ . '/vendor/autoload.php';

use Lawondyss\PhpStruct\Struct;
use Tracy\Debugger;

Debugger::enable(Debugger::DEVELOPMENT, __DIR__);
Debugger::$maxDepth = 5;

class Bar{}

/**
 * @property Bar $namespace
 * @property Bar[] $namespaces
 * @property string[] $name
 * @property-read int $age
 * @property-write null|bool $reserved
 * @method array toArray()
 * @property \Tracy\Bar $debugger
 * @property callable $func
 * @property resource $handle
 * @property iterable $iter
 * @property mixed|null $unknow
 */
class Example extends Struct
{
}

$e = new Example(['namespace' => new Bar, 'namespaces' => [new Bar, new Bar], 'iter' => [], 'unknow' => null, 'func' => [Debugger::class, 'enable'], 'handle' => fopen(__FILE__, 'r'), 'name' => ['Ladislav', 'Vondráček'], 'age' => '38', 'debugger' => new \Tracy\Bar], false);
dump($e);

$e->name = ['Lawondyss'];

foreach ($e as $name => $value) {
  dump($name, $value);
  echo PHP_EOL;
}

dump(isset($e->unknow));
