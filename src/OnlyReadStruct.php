<?php
/**
 * @author Ladislav Vondráček <lad.von@gmail.com>
 */

namespace Lawondyss\PhpStruct;

abstract class OnlyReadStruct extends Struct
{
  public function __set(string $name, $value): void
  {
    throw new AccessException(sprintf('Property %s::$%s is only for read', static::class, $name));
  }
}
