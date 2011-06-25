<?php
/**
 * Twitter adapter for oauth service
 * @author Yaklushin Vasiliy <3a3a3a3@gmail.com>
 */
class SKL_Oauth_Adapter_Twitter extends SKL_Oauth_Adapter_Abstract
{
    /**
     * OAuth version
     */
    protected $_version = 1;

    protected $_config = array(
        'requestTokenUrl'      => 'http://twitter.com/oauth/request_token',
        'userAuthorizationUrl' => 'http://twitter.com/oauth/authorize',
        'accessTokenUrl'       => 'http://twitter.com/oauth/access_token',
        'siteUrl'              => 'http://twitter.com/oauth',
    );

    /**
     * Get user information
     * @return SKL_Oauth_UserProfile
     */
    public function getUserProfile($accessToken)
    {
        $url = 'http://api.twitter.com/1/users/show.xml';

        $userId = $accessToken->getParam('user_id');

        $client = $accessToken->getHttpClient($this->getConfigOptions());
        $client->setUri($url);
        $client->setMethod(Zend_Http_Client::GET);
        $client->setParameterGet('user_id', $userId);

        $request = $client->request();
        
        if ( $request->getStatus() !== 200 ) {
            throw new SKL_Oauth_Exception('Incorrect result of request to url ['.$url.']');
        }
        
        $body = $request->getBody();

        return $this->_createUserProfile($body);
    }

    /**
     * Create user profile from request
     * 
     * @param string $request
     * @return SKL_Oauth_UserProfile
     */
    private function _createUserProfile($request)
    {
        $simpleXML = simplexml_load_string($request);

        $userProfile = new SKL_Oauth_UserProfile();
        $userProfile->setId((int)$simpleXML->id)
                    ->setName((string)$simpleXML->name)
                    ->setServiceName(SKL_Oauth_UserProfile::SERVICE_TWITTER);

        return $userProfile;
    }

    /**
     * Get list of user friends
     *
     * @return SKL_Oauth_Friends
     */
    public function getFriends($accessToken)
    {
        $url = 'http://api.twitter.com/1/friends/ids.xml';

        $userId = $accessToken->getParam('user_id');

        $client = $accessToken->getHttpClient($this->getConfigOptions());
        $client->setUri($url);
        $client->setMethod(Zend_Http_Client::GET);
        $client->setParameterGet('user_id', $userId);
        
        $request = $client->request();

        if ( $request->getStatus() !== 200 ) {
            throw new SKL_Oauth_Exception('Incorrect result of request to url ['.$url.']');
        }

        $body = $request->getBody();

        return $this->_createFriends($body);
    }

    /**
     * Create friends object from request
     *
     * @param string $request
     * @return SKL_Oauth_Friends
     */
    public function _createFriends($request)
    {
        $simpleXML = (array)simplexml_load_string($request);

        $ids = is_array($simpleXML['id']) ? $simpleXML['id'] : array();

        $friends = new SKL_Oauth_Friends();

        /**
         * We get only ids for user friends.
         * I you want to get twitter name, you can create request here:
         * (http://api.twitter.com/1/users/show.json?user_id=12345)
         * 
         * Authorisation is not needed, but twitter has a rate limiting for this requests
         */
        foreach($ids as $id) {
            $friends->addFriend($id, 'Twitter Name [not implemented]');
        }

        return $friends;
    }
}
