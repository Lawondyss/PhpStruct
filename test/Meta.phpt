<?php
/**
 * @author Ladislav Vondráček <lad.von@gmail.com>
 */

require_once __DIR__ . '/bootstrap.php';

use Lawondyss\PhpStruct\Meta;
use Tester\Assert;

test(function() {
  $match = [
    'access' => '',
    'type' => 'integer',
    'name' => 'age',
    'isCollection' => false,
    'isNullable' => false,
    'isClass' => false,
  ];
  $meta = Meta::fromMatch($match);
  Assert::type(Meta::class, $meta);
  Assert::same(false, $meta->isOnlyRead);
  Assert::same('integer', $meta->type);
  Assert::same('age', $meta->name);
  Assert::same(false, $meta->isCollection);
  Assert::same(false, $meta->isNullable);
  Assert::same(false, $meta->isClass);
});

test(function() {
  $match = [
    'access' => 'write',
    'type' => 'string',
    'name' => 'city',
    'isCollection' => false,
    'isNullable' => true,
    'isClass' => false,
  ];
  $meta = Meta::fromMatch($match);
  Assert::type(Meta::class, $meta);
  Assert::same(false, $meta->isOnlyRead);
  Assert::same('string', $meta->type);
  Assert::same('city', $meta->name);
  Assert::same(false, $meta->isCollection);
  Assert::same(true, $meta->isNullable);
  Assert::same(false, $meta->isClass);
});

test(function() {
  $match = [
    'access' => 'read',
    'type' => 'Jobs',
    'name' => 'jobs',
    'isCollection' => true,
    'isNullable' => false,
    'isClass' => true,
  ];
  $meta = Meta::fromMatch($match);
  Assert::type(Meta::class, $meta);
  Assert::same(true, $meta->isOnlyRead);
  Assert::same('Jobs', $meta->type);
  Assert::same('jobs', $meta->name);
  Assert::same(true, $meta->isCollection);
  Assert::same(false, $meta->isNullable);
  Assert::same(true, $meta->isClass);
});
