<?php

namespace Annotation\Mapping;

use Doctrine\Common\Annotations\Annotation;

/**
 * Timestampable mapping
 * @author Yaklushin Vasiliy <3a3a3a3@gmail.com>
 */
final class Timestampable extends Annotation
{
    /**
     * Type of timestampable (may be "create" or "update" )
     * @var string
     */
    public $on = '';
}