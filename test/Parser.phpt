<?php
/**
 * @author Ladislav Vondráček <lad.von@gmail.com>
 */

require_once __DIR__ . '/bootstrap.php';

use Lawondyss\PhpStruct\Parser;
use Tester\Assert;

test(function() {
  $parser = new Parser;
  Assert::same([
      'type' => 'string',
      'isNullable' => false,
      'isCollection' => false,
      'isClass' => false,
    ], $parser->parseType('string'));
  Assert::same([
      'type' => 'integer',
      'isNullable' => true,
      'isCollection' => false,
      'isClass' => false,
    ], $parser->parseType('int|null'));
  Assert::same([
      'type' => 'stdClass',
      'isNullable' => false,
      'isCollection' => false,
      'isClass' => true,
    ], $parser->parseType('stdClass'));
  Assert::same([
      'type' => 'boolean',
      'isNullable' => false,
      'isCollection' => true,
      'isClass' => false,
    ], $parser->parseType('bool[]'));
});
