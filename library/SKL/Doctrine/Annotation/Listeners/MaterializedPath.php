<?php
namespace Annotation\Listeners;
use \Doctrine\Common\EventSubscriber,
    \Doctrine\ORM\Events,
    \Doctrine\ORM\Event\LifecycleEventArgs,
    \Doctrine\ORM\Event\OnFlushEventArgs,
    \Annotation\Exception;

/**
 * Materialized path behavior
 * @author Yaklushin Vasiliy <3a3a3a3@gmail.com>
 */
class MaterializedPath extends SKLSubscriber
{
    /**
     * Materialized path delimiter
     */
    const PATH_DELIMITER = '/';
    
    /**
     * Annotation name
     * @var string
     */
    protected $_annotationName = 'MaterializedPath';

    /**
     * Valid types for path field
     * @var array
     */
    protected $_validTypes = array(
        'text',
    );

    /**
     * Accepted events
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(Events::postPersist);
    }

    /**
     * Post insert action
     * 
     * @param LifecycleEventArgs $eventArgs 
     */
    public function postPersist(LifecycleEventArgs $eventArgs)
    {
        $evm         = $eventArgs->getEntityManager()->getEventManager();
        $entity      = $eventArgs->getEntity();
        $entityClass = get_class($entity);
        $meta        = $this->_getClassMetadata($eventArgs);

        $reader     = $this->_initAnnotationReader();
        $properties = $this->_getEntityProperties($meta);

        foreach($properties as $property) {

    	    $annotation = $reader->getPropertyAnnotation($property, $this->_namespace . $this->_annotationName);

            if ( $annotation ) {

                if ( ! $this->_isValidType($meta, $property) ) {
                    throw new Exception\InvalidType($property->getName(), $this->_validTypes);
                }
                
                $reflEntity = new \ReflectionObject($entity);
                
                if ( ! $reflEntity->hasProperty($annotation->parent ) ) {
                    throw new Exception\NotFound($annotation->parent, $entityClass);
                }

                // get parent property
                $parentProperty = $reflEntity->getProperty($annotation->parent);
                $parentProperty->setAccessible(true);
                
                // get parent entity
                $parentEntity = $parentProperty->getValue($entity);
                
                // default parent path
                $parentPath = '';
                
                // parent entity must be object of the same class
                if ( $parentEntity instanceof $entityClass ) {
                    $parentPath = $property->getValue($parentEntity);
                }
                
                // get entity id
                $id = $this->_getId($entity);
                
                // generate path for entity
                $path = $this->_generateMaterializedPath($id, $parentPath);
                
                // set generated path
                $property->setValue($entity, $path);

                // Set level variable
                if ( $annotation->level ) {

                    $reflObject = new \ReflectionObject($entity);
                    
                    if ( ! $reflObject->hasProperty($annotation->level ) ) {
                        throw new Exception\NotFound($annotation->level, $entityClass);
                    }

                    // get level property
                    $levelProperty = $reflObject->getProperty($annotation->level);
                    $levelProperty->setAccessible(true);
                    
                    // get path property
                    $path = $property->getValue($entity);
                    
                    // calculate level value and set it
                    $levelProperty->setValue($entity, $this->_getLevel($path));
                }
                
                // block events which can be dangerous
                $blockEvents = array(Events::onFlush, Events::preUpdate);
                $this->_blockEvents($evm, $blockEvents);
                
                // save entity
                $eventArgs->getEntityManager()->flush();
                
                // unblock events
                $this->_unblockEvents($evm);
                
            }
        }
    }
    
    /**
     * Try to get identifier from entity
     * 
     * @param type $entity
     * @return int
     * @exception SKL_Doctrine_Annotation_Exception
     */
    protected function _getId($entity)
    {
        $entityClass = get_class($entity);
        
        $reflEntity = new \ReflectionObject($entity);
        
        // if getId method not found
        if ( ! $reflEntity->hasMethod('getId') ) {
            $errorMessage = '['.$entityClass.'] doesn\'t implement getId() method';
            throw new Exception($errorMessage);
        }
        
        return $entity->getId();
    }

    /**
     * Get level of path
     * 
     * @param string $path
     * @return int|false
     */
    protected function _getLevel($path)
    {
        $pathArray = explode(self::PATH_DELIMITER, $path);
        
        if ( count($pathArray) < 2 ) {
            throw new Exception('Incorrect path value! Materialized path has an error.');
        }
        
        return count($pathArray) - 2;
    }
    
    /**
     * Generate full materialized path
     * 
     * @param int $id
     * @param string $path
     * @return string
     */
    protected function _generateMaterializedPath($id, $path)
    {
        $encodeId = $this->_encodeId($id);
        $encodeId = str_pad($encodeId, 6, '0', STR_PAD_LEFT);

        // has parent path
        if ( $path ) {
            return $path . $encodeId . self::PATH_DELIMITER;
        } else {
            return $encodeId . self::PATH_DELIMITER;
        }
    }
 
    /**
     * Encode id
     * 
     * @param int $number
     * @return string
     */
    protected function _encodeId($number)
    {
        return base_convert($number, 10, 36);
    }
}