<?php
/**
 * @author Ladislav Vondráček <lad.von@gmail.com>
 */

require_once __DIR__ . '/bootstrap.php';

use Lawondyss\PhpStruct\Memoize;
use Tester\Assert;

test(function() {
  $called = false;
  $compute = function() use (&$called) {
    $called = true;
    return 'Lorem ipsum';
  };

  // on first loading calls the compute
  Assert::same('Lorem ipsum', Memoize::load('lipsum', $compute));
  Assert::true($called);

  // on second loading return from cache without calls the compute
  $called = false;
  Assert::same('Lorem ipsum', Memoize::load('lipsum', $compute));
  Assert::false($called);
});
