<?php
/**
 * @author Ladislav Vondráček <lad.von@gmail.com>
 */

namespace Lawondyss\PhpStruct;

abstract class Struct implements \JsonSerializable, \IteratorAggregate
{
  /** @var IParser */
  private $parser;

  /** @var array<string, mixed> */
  private $values = [];

  /** @var array<string, Meta> */
  private $meta = [];


  public function __construct(iterable $properties, bool $requiredAll = true, ?IParser $parser = null)
  {
    $this->parser = $parser ?? new Parser;

    $this->loadMeta();

    $initProperties = [];
    foreach ($properties as $name => $value) {
      $this->setProperty($name, $value, true);
      $initProperties[] = $name;
    }

    // find not initiated properties
    $diff = array_diff(array_keys($this->values), $initProperties);

    if ($requiredAll && count($diff) > 0) {
      $suffix = count($diff) === 1 ? 'y' : 'ies';
      $diff = array_map(function(string $property) {
        return '$' . $property;
      }, $diff);
      throw new InvalidArgumentException(sprintf('In class %s missing data for propert%s %s', static::class, $suffix, implode(', ', $diff)));
    }
  }


  public function __get(string $name)
  {
    $this->checkPropertyExists($name);

    if ($this->values[$name] === null && !$this->meta[$name]->isNullable) {
      throw new InvalidValueException(sprintf('Value of %s::$%s is NULL, but property is not nullable', static::class, $name));
    }

    return $this->values[$name];
  }


  public function __set(string $name, $value): void
  {
    $this->checkPropertyExists($name);

    if ($this->meta[$name]->isOnlyRead) {
      throw new AccessException(sprintf('Property %s::$%s is only for read', static::class, $name));
    }

    $this->setProperty($name, $value);
  }


  public function __isset(string $name): bool
  {
    return array_key_exists($name, $this->values);
  }


  public function __unset(string $name): void
  {
    $this->checkPropertyExists($name);

    if ($this->meta[$name]->isOnlyRead) {
      throw new AccessException(sprintf('Property %s::$%s is only for read', static::class, $name));
    }

    if (!$this->meta[$name]->isNullable) {
      throw new InvalidValueException(sprintf('Property %s::$%s cannot be NULL', static::class, $name));
    }

    $this->values[$name] = null;
  }


  public function toArray(bool $recursive = true): array
  {
    $arr = [];

    foreach ($this->values as $name => $meta) {
      $value = $this->{$name};
      if ($recursive && $value instanceof self) {
        $value = $value->toArray($recursive);
      }
      $arr[$name] = $value;
    }

    return $arr;
  }


  public function jsonSerialize(): array
  {
    return static::toArray();
  }


  public function getIterator(): \ArrayIterator
  {
    return new \ArrayIterator(static::toArray());
  }


  private function setProperty(string $name, $value, bool $init = false): void
  {
    $this->checkPropertyExists($name);

    $meta = $this->meta[$name];

    if ($value === null && !$meta->isNullable) {
      throw new InvalidValueException(sprintf('Property %s::$%s cannot be NULL', static::class, $name));
    }

    if ($meta->isCollection && !is_iterable($value)) {
      throw new InvalidValueException(sprintf('Property %s::$%s must be collection (iterable)', static::class, $name));
    }

    if (!$init && $meta->isClass && !$meta->isCollection && isset($value) && !($value instanceof $meta->type)) {
      throw new InvalidValueException(sprintf('Property %s::$%s must be instance of "%s"', static::class, $name, $meta->type));
    }

    if (!is_iterable($value)) {
      $value = $meta->isClass
        ? Helpers::asClass($value, $meta->type, $meta->isNullable)
        : Helpers::asType($value, $meta->type, $meta->isNullable);
    } elseif ($meta->isClass && !$meta->isCollection) {
      $value = Helpers::asClass($value, $meta->type, $meta->isNullable);
    } else {
      foreach ($value as $key => $item) {
        $value[$key] = $meta->isClass
          ? Helpers::asClass($value[$key], $meta->type, $meta->isNullable)
          : Helpers::asType($value[$key], $meta->type, $meta->isNullable);
      }
    }

    $this->values[$name] = $value;
  }


  private function checkPropertyExists(string $name): void
  {
    if (!array_key_exists($name, $this->meta)) {
      throw new NotExistsException(sprintf('Property %s::$%s not exists', static::class, $name));
    }
  }


  private function loadMeta(): void
  {
    $rf = new \ReflectionObject($this);
    $doc = $rf->getDocComment();

    $metas = $this->parser->parseDoc($doc, $rf->getNamespaceName());
    foreach ($metas as $meta) {
      $this->meta[$meta->name] = $meta;
    }
  }
}
