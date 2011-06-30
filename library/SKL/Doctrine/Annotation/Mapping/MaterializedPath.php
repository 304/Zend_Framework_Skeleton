<?php

namespace Annotation\Mapping;

use Doctrine\Common\Annotations\Annotation;

/**
 * Materialized path mapping
 * @author Yaklushin Vasiliy <3a3a3a3@gmail.com>
 */
final class MaterializedPath extends Annotation
{
    /**
     * Pointer to parent field name
     * @var string
     */
    public $parent = null;
    
    /**
     * Pointer to level field name
     * @var string
     */
    public $level  = null;
    
    /**
     * Use example:
     * 
     * @skl:MaterializedPath(parent="parent", level="level")
     * 
     * parent field - is required
     */
}