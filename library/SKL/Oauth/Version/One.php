<?php
/**
 * Implementation for first version of OAuth protocol
 * @author Yaklushin Vasiliy <3a3a3a3@gmail.com>
 */
class SKL_Oauth_Version_One implements SKL_Oauth_Version_Interface
{
    /** @var SKL_Oauth_Adapter_Abstract */
    protected $_adapter = null;

    /** @var Zend_Oauth_Consumer */
    protected $_oauth = null;

    /** @var Zend_Oauth_Token_Request */
    protected $_requestToken = null;

    /** @var Zend_Oauth_Token_Access */
    protected $_accessToken = null;

    public function __construct(SKL_Oauth_Adapter_Abstract $adapter)
    {
        $this->_adapter = $adapter;
        $this->_oauth   = new Zend_Oauth_Consumer($adapter->getConfigOptions());
    }

    /**
     * Send user to Oauth service authorization
     */
    public function authorize()
    {
        $this->_requestToken = $this->_oauth->getRequestToken();

        $redirectUrl = $this->_oauth->getRedirectUrl();
        header('Location: ' . $redirectUrl);
    }

    /**
     * Request Access Token from Oauth service
     * 
     * @param array $queryData GET data returned in user's redirect from Provider
     * @return Zend_Oauth_Token_Access
     */
    public function requestAccessToken($queryData)
    {
        $this->_accessToken = $this->_oauth->getAccessToken($queryData, $this->_requestToken);

        return $this->_accessToken;
    }

    /**
     * Get user profile
     *
     * @return SKL_Oauth_UserProfile
     */
    public function getUserProfile()
    {
        return $this->_adapter->getUserProfile($this->_accessToken);
    }

    /**
     * Get user friends
     *
     * @return SKL_Oauth_Friends
     */
    public function getFriends()
    {
        return $this->_adapter->getFriends($this->_accessToken);
    }
}