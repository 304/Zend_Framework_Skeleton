<?php
/**
 * Implementation for second version of OAuth protocol
 * @author Yaklushin Vasiliy <3a3a3a3@gmail.com>
 */
class SKL_Oauth_Version_Two implements SKL_Oauth_Version_Interface
{
    /** @var SKL_Oauth_Adapter_Abstract */
    protected $_adapter = null;

    protected $_accessToken = null;

    public function __construct(SKL_Oauth_Adapter_Abstract $adapter)
    {
        $this->_adapter = $adapter;
    }

    /**
     * Send user to Oauth service authorization
     */
    public function authorize()
    {
        $config = $this->_adapter->getConfigOptions();
        $url = $config['requestTokenUrl'];

        $query = http_build_query(array(
            'client_id'    => $config['consumerKey'],
            'redirect_uri' => $config['callbackUrl'],
        ));

        header('Location: ' . $url . '?' . $query);

        exit(1);
    }

    /**
     * Request Access Token from Oauth service
     *
     * @param array $queryData GET data returned in user's redirect from Provider
     * @return string
     */
    public function requestAccessToken($queryData)
    {
        $config = $this->_adapter->getConfigOptions();

        $query = http_build_query(array(
                    'client_id'     => $config['consumerKey'],
                    'redirect_uri'  => $config['callbackUrl'],
                    'client_secret' => $config['consumerSecret'],
                    'code'          => $queryData['code'],
        ));

        $accessTokenUrl = $config['accessTokenUrl'];

        $this->_accessToken = file_get_contents($accessTokenUrl . '?' . $query);

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
