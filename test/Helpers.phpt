<?php
/**
 * @author Ladislav Vondráček <lad.von@gmail.com>
 */

require_once __DIR__ . '/bootstrap.php';

use Lawondyss\PhpStruct\Helpers;
use Lawondyss\PhpStruct\Types;
use Tester\Assert;

test(function() {
  Assert::same(Types::BOOLEAN, Helpers::sanitizeType('BOOL'));
  Assert::same(Types::BOOLEAN, Helpers::sanitizeType(Types::BOOL));
  Assert::same(Types::INTEGER, Helpers::sanitizeType(Types::INT));
  Assert::same(Types::FLOAT, Helpers::sanitizeType(Types::REAL));
  Assert::same(Types::FLOAT, Helpers::sanitizeType(Types::DOUBLE));
  Assert::same(Types::FLOAT, Helpers::sanitizeType(Types::NUMBER));
  Assert::same(Types::CALLABLE, Helpers::sanitizeType(Types::CALLBACK));
  Assert::same(Types::STRING . '[]', Helpers::sanitizeType('String[]'));
  Assert::same('Foo\Bar', Helpers::sanitizeType('Foo\Bar'));
});

test(function() {
  Assert::same([Types::BOOLEAN, Types::STRING . '[]', 'Foo\Bar'], Helpers::sanitizeTypes([Types::BOOL, 'String[]', 'Foo\Bar']));
});

test(function() {
  Assert::true(Helpers::isCollection('STRing[]'));
  Assert::false(Helpers::isCollection('STRing'));
});

test(function() {
  Assert::same(Types::STRING, Helpers::typeOfCollection('string[]'));
  Assert::same('Foo\Bar', Helpers::typeOfCollection('Foo\Bar[]'));
});

test(function() {
  Assert::same(Types::STRING, Helpers::getType('42'));
  Assert::same(Types::BOOLEAN, Helpers::getType(true));
  Assert::same(Types::RESOURCE, Helpers::getType(fopen('php://output', 'r')));
  Assert::same(Types::NULL, Helpers::getType(null));
});

test(function() {
  Assert::same(true, Helpers::asType(1, Types::BOOL));
  Assert::same(true, Helpers::asType(1, Types::BOOLEAN));
  Assert::same(42, Helpers::asType('42', Types::INT));
  Assert::same(42, Helpers::asType('42', Types::INTEGER));
  Assert::same(3.14, Helpers::asType('3.14', Types::NUMBER));
  Assert::same(3.14, Helpers::asType('3.14', Types::REAL));
  Assert::same(3.14, Helpers::asType('3.14', Types::DOUBLE));
  Assert::same(3.14, Helpers::asType('3.14', Types::FLOAT));
  Assert::same('', Helpers::asType(false, Types::STRING));
  Assert::same('1', Helpers::asType(true, Types::STRING));
});

test(function() {
  Assert::same(Types::BOOLEAN, Helpers::getType(true));
  Assert::same(Types::INTEGER, Helpers::getType(1980));
  Assert::same(Types::STRING, Helpers::getType('Lawondyss'));
  Assert::same(Types::ARRAY, Helpers::getType([]));
  Assert::same(Types::OBJECT, Helpers::getType(new stdClass));
  Assert::same(Types::RESOURCE, Helpers::getType(fopen('php://output', 'r')));
  Assert::same(Types::NULL, Helpers::getType(null));
  Assert::same(Types::FLOAT, Helpers::getType(3.14));
});

test(function() {
  Assert::true(Helpers::isAllowedType(Types::STRING));
  Assert::true(Helpers::isAllowedType(Types::STRING . '[]'));
  Assert::false(Helpers::isAllowedType(Types::BOOL));
  Assert::true(Helpers::isAllowedType(Types::BOOLEAN));
  Assert::true(Helpers::isAllowedType('\Closure'));
});

test(function() {
  Assert::true(Helpers::isCommonType(Types::BOOLEAN));
  Assert::true(Helpers::isCommonType(Types::STRING));
  Assert::true(Helpers::isCommonType(Types::RESOURCE));
  Assert::false(Helpers::isCommonType('\Closure'));
  Assert::false(Helpers::isCommonType('\stdClass'));
});
