<?php
/**
 * @author Ladislav Vondráček <lad.von@gmail.com>
 */

namespace Lawondyss\PhpStruct;

class Types
{
  const BOOLEAN = 'boolean';
  const BOOL = 'bool';
  const INT = 'int';
  const INTEGER = 'integer';
  const NUMBER = 'number';
  const DOUBLE = 'double';
  const REAL = 'real';
  const FLOAT = 'float';
  const STRING = 'string';
  const ARRAY = 'array';
  const OBJECT = 'object';
  const NULL = 'null';
  const ITERABLE = 'iterable';
  const CALLABLE = 'callable';
  const CALLBACK = 'callback';
  const RESOURCE = 'resource';
  const MIXED = 'mixed';

  private function __construct()
  {
  }
}
