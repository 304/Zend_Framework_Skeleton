<?php
namespace Annotation\Exception;

/**
 * Invalid type exception
 * @author Yaklushin Vasiliy <3a3a3a3@gmail.com>
 */
class InvalidType extends \Annotation\Exception
{
    public function __construct($propertyName, array $validTypes)
    {
        $errorMessage  = 'Incorrect field type for property ['.$propertyName.']';
        $errorMessage .= '. Type must be ('. \implode(', ', $validTypes).').';
        
        parent::__construct($errorMessage);
    }
}