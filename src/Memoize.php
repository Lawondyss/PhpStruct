<?php
/**
 * @author Ladislav Vondráček <lad.von@gmail.com>
 */

namespace Lawondyss\PhpStruct;

class Memoize
{
  private static $cache = [];


  /**
   * @param callable $compute
   * @return mixed
   */
  public static function load(string $key, $compute)
  {
    if (!isset(self::$cache[$key])) {
      self::$cache[$key] = $compute();
    }

    return self::$cache[$key];
  }
}
