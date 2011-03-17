<?php
/**
 * @Entity
 * @Table(name="user")
 */
class Application_Model_Entities_User
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $userId;

    /** @Column(type="string", length=50) */
    private $name;


    /** @Column(type="date") */
    private $birthday;

    public function setName ($name)
    {
        $this->name = $name;
        return true;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getBirthday()
    {
        return $this->birthday;
    }

    public function setBirthday($birthday)
    {
        $this->birthday = new DateTime($birthday);
        return true;
    }

}

