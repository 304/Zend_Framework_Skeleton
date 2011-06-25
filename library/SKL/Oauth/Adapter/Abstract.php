<?php
/**
 * Abstract adapter for oauth service
 * @author Yaklushin Vasiliy <3a3a3a3@gmail.com>
 */
abstract class SKL_Oauth_Adapter_Abstract
{
    /**
     * OAuth version
     */
    protected $_version = 1;

    /**
     * Adapter config options
     * @var array
     */
    protected $_config = array();

    public function __construct($options = null)
    {
        if ( isset($options) && is_array($options) ) {
            $this->_config = array_merge($this->_config, $options);
        }
    }

    /**
     * Get config options
     * @return array
     */
    public function getConfigOptions()
    {
        return $this->_config;
    }

    /**
     * Get Oauth version
     * @return int
     */
    public function getVersion()
    {
        return $this->_version;
    }

    /**
     * Get user information
     * @return SKL_Oauth_UserProfile
     */
    abstract public function getUserProfile($accessToken);

    /**
     * Get list of user friends
     * @return SKL_Oauth_Friends
     */
    abstract public function getFriends($accessToken);
}
