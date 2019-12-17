<?php
/**
 * @author Ladislav Vondráček <lad.von@gmail.com>
 */

namespace Lawondyss\PhpStruct;


interface Exception
{
}


class NotExistsException extends \RuntimeException implements Exception
{
}


class NotSupportedException extends \RuntimeException implements Exception
{
}


class InvalidArgumentException extends \InvalidArgumentException implements Exception
{
}


class InvalidValueException extends \RuntimeException implements Exception
{
}


class AccessException extends \RuntimeException implements Exception
{
}
