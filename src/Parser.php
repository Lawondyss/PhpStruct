<?php
/**
 * @author Ladislav Vondráček <lad.von@gmail.com>
 */

namespace Lawondyss\PhpStruct;

class Parser
{
  public function parseDoc(string $docComment): array
  {
    $arr = [];

    $docComment = preg_replace('~^\s*\*\s?~ms', '', trim($docComment, '/*'));

    $pattern = '~^\s*@property\-?(?P<access>[read|write]*)\s+(?P<type>[_a-zA-Z][_a-zA-Z0-9\|\[\]\\\\]*)\s+\$(?P<name>[_a-zA-Z][_a-zA-Z0-9]*)~m';

    preg_match_all($pattern, $docComment, $matches, PREG_SET_ORDER);

    foreach ($matches as $match) {
      $match = $this->parseType($match['type']) + $match;
      $meta = Meta::fromMatch($match);
      $arr[] = $meta;
    }

    return $arr;
  }


  public function parseType(string $docType): array
  {
    $data = [
      'type' => null,
      'isNullable' => false,
      'isCollection' => false,
      'isClass' => false,
    ];

    if (strpos($docType, '|') === false) {
      $docType = Helpers::sanitizeType($docType);

    } else {
      $types = explode('|', $docType);
      $types = Helpers::sanitizeTypes($types);

      if (count($types) > 2 || !in_array('null', $types)) {
        throw new NotSupportedException('Multiple types of property is not supported');
      }

      $data['isNullable'] = true;
      $nullKey = array_search('null', $types);
      unset($types[$nullKey]);
      $docType = array_pop($types);
    }

    $data['isCollection'] = Helpers::isCollection($docType);

    $data['type'] = $data['isCollection'] ? Helpers::typeOfCollection($docType) : $docType;

    $data['isClass'] = class_exists($data['type']);

    if (!Helpers::isAllowedType($data['type'])) {
      $format = Helpers::isCommonType($data['type']) ? 'Type "%s" is not supported' : 'Class "%s" not found, use fully qualified class name';
      throw new NotSupportedException(sprintf($format, $docType));
    }

    return $data;
  }
}
