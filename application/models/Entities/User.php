<?php
namespace Entities;
/**
 * @Entity(repositoryClass="\Repositories\User")
 * @Table(name="user")
 */
class User
{
    /**
     * @Id @Column(type="integer", name="id")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }
}