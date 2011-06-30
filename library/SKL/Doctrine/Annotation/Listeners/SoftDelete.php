<?php
namespace Annotation\Listeners;
use \Doctrine\Common\EventSubscriber,
    \Doctrine\ORM\Events,
    \Doctrine\ORM\Event\LifecycleEventArgs,
    Annotation\Exception;

/**
 * Soft-Delete behavior
 * @author Yaklushin Vasiliy <3a3a3a3@gmail.com>
 */
class SoftDelete extends SKLSubscriber
{
    /**
     * Annotaion name
     * @var string
     */
    protected $_annotationName = 'SoftDelete';

    /**
     * Valid types for soft-delete field
     * @var array
     */
    protected $_validTypes = array(
        'boolean',
    );

    /**
     * Accepted events
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(Events::preRemove);
    }
	
    /**
     * @param LifecycleEventArgs $eventArgs 
     */
    public function preRemove(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();
        $em     = $eventArgs->getEntityManager();
        $evm    = $eventArgs->getEntityManager()->getEventManager();
        $meta   = $this->_getClassMetadata($eventArgs);

        $reader     = $this->_initAnnotationReader();
        $properties = $this->_getEntityProperties($meta);

        foreach($properties as $property) {

    	    $annotation = $reader->getPropertyAnnotation($property, $this->_namespace . $this->_annotationName);

            if ( $annotation ) {

                if ( ! $this->_isValidType($meta, $property) ) {
                    throw new Exception\InvalidType($property->getName(), $this->_validTypes);
                }

                // set soft delete for entity
                $property->setValue($entity, true);
                
                // block events which can be dangerous
                $blockEvents = array(Events::onFlush, Events::preUpdate);
                $this->_blockEvents($evm, $blockEvents);
                
                // save entity with soft-delete
                $em->flush();
                
                // unblock events
                $this->_unblockEvents($evm);

                // don't delete from db
                $em->detach($entity);
            }
        }
    }
}