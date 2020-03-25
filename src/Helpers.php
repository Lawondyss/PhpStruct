<?php
/**
 * @author Ladislav Vondráček <lad.von@gmail.com>
 */

namespace Lawondyss\PhpStruct;

class Helpers
{
  /** @var array */
  private static $types = [
    Types::BOOLEAN,
    Types::INTEGER,
    Types::FLOAT,
    Types::STRING,
    Types::ARRAY,
    Types::OBJECT,
    Types::NULL,
    Types::ITERABLE,
    Types::CALLABLE,
    Types::RESOURCE,
    Types::MIXED,
  ];

  /** @var array */
  private static $typesAlias = [
    Types::BOOL => Types::BOOLEAN,
    Types::INT => Types::INTEGER,
    Types::DOUBLE => Types::FLOAT,
    Types::REAL => Types::FLOAT,
    Types::NUMBER => Types::FLOAT,
    Types::CALLBACK => Types::CALLABLE,
  ];


  public static function sanitizeType(string $type): string
  {
    $lowerType = strtolower($type);

    $isCollection = self::isCollection($lowerType);
    if ($isCollection) {
      $lowerType = self::typeOfCollection($lowerType);
    }

    $lowerType = self::$typesAlias[$lowerType] ?? $lowerType;

    if (in_array($lowerType, self::$types)) {
      $type = $lowerType . ($isCollection ? '[]' : '');
    }

    return $type;
  }


  public static function sanitizeTypes(array $types): array
  {
    return array_map([Helpers::class, 'sanitizeType'], $types);
  }


  public static function isCollection(string $type): bool
  {
    return substr($type, -2) === '[]';
  }


  public static function typeOfCollection(string $type): string
  {
    if (!self::isCollection($type)) {
      throw new InvalidArgumentException(sprintf('Type "%s" is not collection', $type));
    }

    return substr($type, 0, strlen($type) - 2);
  }


  public static function getType($var): string
  {
    switch ($type = gettype($var)) {
      case Types::BOOLEAN:
      case Types::INTEGER:
      case Types::STRING:
      case Types::ARRAY:
      case Types::RESOURCE:
        return $type;
      case 'NULL':
        return Types::NULL;
      case Types::DOUBLE:
      case Types::FLOAT:
        return Types::FLOAT;
      case Types::OBJECT:
        return get_class($var);
    }

    if (is_callable($var)) {
      return Types::CALLABLE;
    } elseif (is_iterable($var)) {
      return Types::ITERABLE;
    }

    throw new NotSupportedException(sprintf('Type "%s" is not supported', $type));
  }


  public static function asType($var, string $type, bool $canBeNull)
  {
    if ($canBeNull && (is_null($var) || $var === '')) {
      return null;
    }

    switch ($type) {
      case Types::BOOL:
      case Types::BOOLEAN:
        return (boolean)$var;
      case Types::INT:
      case Types::INTEGER:
        return (integer)$var;
      case Types::DOUBLE:
      case Types::REAL:
      case Types::NUMBER:
      case Types::FLOAT:
        return (float)$var;
      case Types::STRING:
        return (string)$var;
      case Types::ARRAY:
        return (array)$var;
      case Types::OBJECT:
        return (object)$var;
      case Types::CALLABLE:
      case Types::CALLBACK:
      case Types::RESOURCE:
      case Types::MIXED:
        return $var;
      default:
        throw new NotSupportedException(sprintf('Type "%s" is not supported', $type));
    }
  }


  public static function isAllowedType(string $type): bool
  {
    if (self::isCollection($type)) {
      $type = self::typeOfCollection($type);
    }

    return in_array($type, self::$types) || class_exists($type);
  }


  public static function isCommonType(string $type): bool
  {
    return in_array($type, self::$types);
  }
}
