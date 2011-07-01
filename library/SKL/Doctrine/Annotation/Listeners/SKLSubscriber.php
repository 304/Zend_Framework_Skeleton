<?php

namespace Annotation\Listeners;
use \Doctrine\Common\EventSubscriber,
    \Doctrine\ORM\Events,
    \Doctrine\ORM\Event\LifecycleEventArgs;

/**
 * Behavior basic class
 * @author Yaklushin Vasiliy <3a3a3a3@gmail.com>
 */
abstract class SKLSubscriber implements EventSubscriber
{
    /**
     * Annotation namespace
     * @var string
     */
    protected $_namespace = 'Annotation\Mapping\\';
    
    /**
     * Namespace alias
     * @var string
     */
    protected $_namespaceAlias = 'skl';
    
    /**
     * Annotation name (must set in children classes)
     * @var string
     */
    protected $_annotationName = '';

    /**
     * Valid field types (must set in children classes)
     * @var array
     */
    protected $_validTypes = array();

    /**
     * Array of blocked events
     * @var array
     */
    private $_blockedEvents = array();

    /**
     * Get list of entity properties
     * 
     * @param LifecycleEventArgs $eventArgs
     * @return array
     */
    protected function _getEntityProperties(\Doctrine\ORM\Mapping\ClassMetadata $meta)
    {
        return $meta->getReflectionProperties();
    }

    /**
     * Get class metadata
     * 
     * @param LifecycleEventArgs $eventArgs
     * @return \Doctrine\ORM\Mapping\ClassMetadata
     */
    protected function _getClassMetadata(LifecycleEventArgs $eventArgs)
    {
        $entityClass = get_class($eventArgs->getEntity());

        return $eventArgs->getEntityManager()
                         ->getClassMetadata($entityClass);
    }

    /**
     * Initiatize annotation reader
     * 
     * @return \Doctrine\Common\Annotations\AnnotationReader
     */
    protected function _initAnnotationReader()
    {
        $reader = new \Doctrine\Common\Annotations\AnnotationReader();
        $reader->setAnnotationNamespaceAlias($this->_namespace, $this->_namespaceAlias);

        $reader->setIgnoreNotImportedAnnotations(true);
        $reader->setEnableParsePhpImports(false);
        $reader = new \Doctrine\Common\Annotations\CachedReader(
            new \Doctrine\Common\Annotations\IndexedReader($reader), new \Doctrine\Common\Cache\ArrayCache()
        );        

        // 1. Autoload (but may be slow)
        //	$reader->setAutoloadAnnotations(true);

        $annotationFull = $this->_namespace . $this->_annotationName;
        
        // 2. or explicit loading
        new $annotationFull(array());

        return $reader;
    }
    
    /**
     * Check if field have a correct type
     *
     * @param \Doctrine\ORM\Mapping\ClassMetadata $meta
     * @param \ReflectionProperty $property
     * @return boolean
     */
    protected function _isValidType(\Doctrine\ORM\Mapping\ClassMetadata $meta, \ReflectionProperty $property)
    {
        $mapping = $meta->getFieldMapping($property->getName());

        return $mapping && \in_array($mapping['type'], $this->_validTypes);
    }
    
    /**
     * Block events and remove listeners
     * 
     * @param \Doctrine\Common\EventManager $evm 
     * @param array $events
     */
    protected function _blockEvents(\Doctrine\Common\EventManager $evm, $events)
    {
        if ( ! is_array($events) ) {
            $events = (array) $events;
        }
        
        foreach($events as $event) {
            
            // if event has listeners
            if ( $evm->hasListeners($event) ) {
                
                // add blocked event
                $listeners = $evm->getListeners($event);
                $this->_blockedEvents[$event] = $listeners;
                
                // remove listeners from blocked event
                foreach($listeners as $listener) {
                    $evm->removeEventListener($event, $listener);
                }
            }
        }
    }
    
    /**
     * Restore event listeners
     * 
     * @param \Doctrine\Common\EventManager $evm 
     */
    protected function _unblockEvents(\Doctrine\Common\EventManager $evm)
    {
        if ( $this->_blockedEvents ) {
            
            foreach($this->_blockedEvents as $event => $listeners) {
            
                // restore event listeners 
                foreach($listeners as $listener) {
                    $evm->addEventListener($event, $listener);
                }
            }
            
            // unset blocked events
            $this->_blockedEvents = array();
        }
    }    
}