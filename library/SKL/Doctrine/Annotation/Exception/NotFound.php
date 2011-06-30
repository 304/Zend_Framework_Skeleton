<?php
namespace Annotation\Exception;

/**
 * Not found exception
 * @author Yaklushin Vasiliy <3a3a3a3@gmail.com>
 */
class NotFound extends \Annotation\Exception
{
    public function __construct($variableName, $className)
    {
        $errorMessage = 'Variable ['.$variableName.'] is not found ';
        $errorMessage .= 'in class ['.$className.']';        
        
        parent::__construct($errorMessage);
    }
}