<?php
/**
 * @author Ladislav Vondráček <lad.von@gmail.com>
 */

namespace Lawondyss\PhpStruct;

class Meta
{
  /** @var string */
  public $name;

  /** @var mixed */
  public $value;

  /** @var string */
  public $type;

  /** @var bool */
  public $isOnlyRead;

  /** @var bool */
  public $isCollection;

  /** @var bool */
  public $isNullable;

  /** @var bool */
  public $isClass;


  public function __construct(string $name, string $type, bool $isOnlyRead, bool $isCollection, bool $isNullable, bool $isClass)
  {
    $this->name = $name;
    $this->type = $type;
    $this->isOnlyRead = $isOnlyRead;
    $this->isCollection = $isCollection;
    $this->isNullable = $isNullable;
    $this->isClass = $isClass;
  }


  public static function fromMatch(array $match): Meta
  {
    $isOnlyRead = $match['access'] !== '' && strtolower($match['access']) === 'read' ? true : false;

    return new self($match['name'], $match['type'], $isOnlyRead, $match['isCollection'], $match['isNullable'], $match['isClass']);
  }
}
