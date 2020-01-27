<?php
/**
 * @author Ladislav Vondráček <lad.von@gmail.com>
 */

namespace Lawondyss\PhpStruct;

abstract class Struct implements \JsonSerializable, \IteratorAggregate
{
  /** @var IParser */
  private $parser;

  /** @var Meta[] */
  private $meta = [];


  public function __construct(iterable $properties, bool $requiredAll = true, ?IParser $parser = null)
  {
    $this->parser = $parser ?? new Parser;

    $this->loadMeta();

    $initProperties = [];
    foreach ($properties as $name => $value) {
      $this->setProperty($name, $value);
      $initProperties[] = $name;
    }

    // find not initiated properties
    $diff = array_diff(array_keys($this->meta), $initProperties);

    if ($requiredAll && count($diff) > 0) {
      $suffix = count($diff) === 1 ? 'y' : 'ies';
      $diff = array_map(function(string $property) {
        return static::class . '::$' . $property;
      }, $diff);
      throw new InvalidArgumentException(sprintf('Missing data for propert%s %s', $suffix, implode(', ', $diff)));
    }
  }


  public function __get($name)
  {
    $this->checkPropertyExists($name);

    $meta = $this->meta[$name];

    if ($meta->value === null && !$meta->isNullable) {
      throw new InvalidValueException(sprintf('Value of %s::$%s is NULL, but property is not nullable', static::class, $name));
    }

    return $meta->value;
  }


  public function __set($name, $value)
  {
    $this->checkPropertyExists($name);

    if ($this->meta[$name]->isOnlyRead) {
      throw new AccessException(sprintf('Property %s::$%s is only for read', static::class, $name));
    }

    $this->setProperty($name, $value);
  }


  public function __isset($name)
  {
    return array_key_exists($name, $this->meta);
  }


  public function __unset($name)
  {
    if (array_key_exists($name, $this->meta)) {
      throw new NotExistsException(sprintf('Property %s::$%s not exists', static::class, $name));
    }

    if ($this->meta[$name]->isOnlyRead) {
      throw new AccessException(sprintf('Property %s::$%s is only for read', static::class, $name));
    }

    if (!$this->meta[$name]->isNullable) {
      throw new InvalidValueException(sprintf('Property %s::$%s cannot be NULL', static::class, $name));
    }

    $this->meta[$name]->value = null;
  }


  public function toArray(): array
  {
    $arr = [];

    /** @var Meta $meta */
    foreach ($this->meta as $name => $meta) {
      $arr[$name] = $this->{$name};
    }

    return $arr;
  }


  public function jsonSerialize()
  {
    return static::toArray();
  }


  public function getIterator()
  {
    return new \ArrayIterator(static::toArray());
  }


  private function setProperty(string $name, $value)
  {
    $this->checkPropertyExists($name);

    $meta = $this->meta[$name];

    if ($value === null && !$meta->isNullable) {
      throw new InvalidValueException(sprintf('Property %s::$%s cannot be NULL', static::class, $name));
    }

    if ($meta->isCollection && !is_array($value)) {
      throw new InvalidValueException(sprintf('Property %s::$%s must be collection (array)', static::class, $name));
    }

    if ($meta->isClass && !($value instanceof $meta->type)) {
      throw new InvalidValueException(sprintf('Property %s::$%s must be instance of "%s"', static::class, $name, $meta->type));
    }

    if (!is_array($value) && !$meta->isClass && $meta->type !== Helpers::getType($value)) {
      $value = Helpers::asType($value, $meta->type, $meta->isNullable);

    } elseif (is_array($value) && $meta->isCollection) {
      foreach ($value as $key => $item) {
        $typedItem = Helpers::asType($item, $meta->type, $meta->isNullable);
        if (Helpers::getType($item) !== $meta->type && $item != $typedItem) {
          throw new InvalidValueException(sprintf('All items of %s::$%s must be type of "%s"', static::class, $name, $meta->type));
        }
        $value[$key] = $typedItem;
      }
    }

    $meta->value = $value;
  }


  private function checkPropertyExists($name)
  {
    if (!array_key_exists($name, $this->meta)) {
      throw new NotExistsException(sprintf('Property %s::$%s not exists', static::class, $name));
    }
  }


  private function loadMeta(): void
  {
    $rf = new \ReflectionObject($this);
    $doc = $rf->getDocComment();

    $metas = $this->parser->parseDoc($doc);
    foreach ($metas as $meta) {
      $this->meta[$meta->name] = $meta;
    }
  }
}
