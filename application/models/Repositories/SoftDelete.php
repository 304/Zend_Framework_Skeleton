<?php

namespace Repositories;

/**
 * Soft delete repository prototype
 */
class SoftDelete extends \Doctrine\ORM\EntityRepository
{
    public function find($id, $lockMode = LockMode::NONE, $lockVersion = null) 
    {
        return $this->findOneBy(array('id' => $id));
    }
    
    public function findOneBy(array $criteria)
    {
        return parent::findOneBy($this->_softDeleteCriteria($criteria));
    }
    
    public function findAll()
    {
        return $this->findBy(array());
    }
    
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return parent::findBy($this->_softDeleteCriteria($criteria), $orderBy, $limit, $offset);
    }
    
    protected function _softDeleteCriteria(array $criteria)
    {
        if ( ! isset($criteria['deleted']) ) {
            $criteria['deleted'] = true;
        }
        
        return $criteria;
    }
}