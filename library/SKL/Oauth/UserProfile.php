<?php
/**
 * User profile in Oauth service
 * @author Yaklushin Vasiliy <3a3a3a3@gmail.com>
 */
class SKL_Oauth_UserProfile
{
    protected $_id;
    protected $_name;
    protected $_email;
    protected $_serviceName;

    /**
     * Types of services
     */
    const SERVICE_TWITTER  = 'twitter';
    const SERVICE_LINKEDIN = 'linkedin';
    const SERVICE_FACEBOOK = 'facebook';
    const SERVICE_YAHOO    = 'yahoo';

    protected $_services = array(
        self::SERVICE_TWITTER,
        self::SERVICE_LINKEDIN,
        self::SERVICE_FACEBOOK,
        self::SERVICE_YAHOO,
    );

    /**
     * Set Id
     * @param string $id
     * @return SKL_Oauth_UserProfile
     */
    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }

    /**
     * Get Id
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Set name
     * @param string $name
     * @return SKL_Oauth_UserProfile
     */
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Set email
     * @param string $email
     * @return SKL_Oauth_UserProfile
     */
    public function setEmail($email)
    {
        $this->_email = $email;
        return $this;
    }

    /**
     * Get email
     * @return string
     */
    public function getEmail()
    {
        return $this->_email;
    }

    /**
     *
     * @param string $serviceName
     * @throw InvalidArgumentException
     * @return SKL_Oauth_UserProfile
     */
    public function setServiceName($serviceName)
    {
        if ( ! in_array($serviceName, $this->_services) ) {
            throw new InvalidArgumentException('Try to set undefined service name = ['.$serviceName.']');
        }

        $this->_serviceName = $serviceName;
        return $this;
    }

    /**
     * Get service name
     * @return string
     */
    public function getServiceName()
    {
        return $this->_serviceName;
    }
}