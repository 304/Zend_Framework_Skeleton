<?php
namespace Annotation\Listeners;
use \Doctrine\Common\EventSubscriber,
    \Doctrine\ORM\Events,
    \Doctrine\ORM\Event\LifecycleEventArgs,
    \Doctrine\ORM\Event\PreUpdateEventArgs,
    Annotation\Exception;

/**
 * Timestampable behavior
 * @author Yaklushin Vasiliy <3a3a3a3@gmail.com>
 */
class Timestampable extends SKLSubscriber
{
    /**
     * Annotation name
     * @var string
     */
    protected $_annotationName = 'Timestampable';

    /**
     * Valid timestampable types
     * @var array
     */
    protected $_validTypes = array(
        'date',
        'time',
        'datetime',
        'timestamp',
    );
    
    /**
     * Accepted events
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(Events::prePersist, Events::onFlush);
    }

    /**
     * Pre insert event
     *
     * @param LifecycleEventArgs $eventArgs
     */
    public function prePersist(LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();
        $meta = $this->_getClassMetadata($eventArgs);

        $reader     = $this->_initAnnotationReader();
        $properties = $this->_getEntityProperties($meta);

        foreach($properties as $property) {

    	    $annotation = $reader->getPropertyAnnotation($property, $this->_namespace . 'Timestampable');
            
            if ( $annotation ) {

                if ( ! $this->_isValidType($meta, $property) ) {
                    throw new Exception\InvalidType($property->getName(), $this->_validTypes);
                }

                if ( $annotation->on == 'create' ) {
                    $this->_setCreateDate($property, $entity);
                }
            }
        }
    }
    
    /**
     * On-save event
     * 
     * @param \Doctrine\ORM\Event\OnFlushEventArgs $eventArgs 
     */
    public function onFlush(\Doctrine\ORM\Event\OnFlushEventArgs $eventArgs)
    {
        $em  = $eventArgs->getEntityManager();
        
        $uow = $em->getUnitOfWork();
        
        $reader = $this->_initAnnotationReader();
        
        // get all updates events
        foreach ($uow->getScheduledEntityUpdates() AS $entity) {
        
            $entityClass = get_class($entity);
            $meta = $em->getClassMetadata($entityClass);
            
            $properties = $this->_getEntityProperties($meta);
            
            foreach($properties as $property) {

                $annotation = $reader->getPropertyAnnotation($property, $this->_namespace . $this->_annotationName);

                if ( $annotation ) {

                    if ( ! $this->_isValidType($meta, $property) ) {
                        throw new Exception\InvalidType($property->getName(), $this->_validTypes);
                    }

                    if ( $annotation->on == 'update' ) {

                       $this->_setUpdateDate($property, $entity);
                       
                       // set change set
                       $uow->recomputeSingleEntityChangeSet($meta, $entity);
                    }
                }
            }
        }
    }

    /**
     * Timestampable for create
     *
     * @param object $object
     */
    protected function _setCreateDate($property, $object)
    {
        if ( ! $property->getValue($object) ) {
            $property->setValue($object, new \DateTime('now'));
        }
    }

    /**
     * Timestampable for update
     *
     * @param object $object
     */
    protected function _setUpdateDate($property, $object)
    {
        $property->setValue($object, new \DateTime('now'));
    }
}
