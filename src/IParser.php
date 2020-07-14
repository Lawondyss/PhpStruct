<?php
/**
 * @author Ladislav Vondráček <lad.von@gmail.com>
 */

namespace Lawondyss\PhpStruct;

interface IParser
{
  /**
   * @param string $docComment
   * @return Meta[]
   */
  public function parseDoc(string $docComment): array;
}
