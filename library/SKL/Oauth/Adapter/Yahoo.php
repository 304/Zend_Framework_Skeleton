<?php
/**
 * Yahoo adapter for oauth service
 * @author Yaklushin Vasiliy <3a3a3a3@gmail.com>
 */
class SKL_Oauth_Adapter_Yahoo extends SKL_Oauth_Adapter_Abstract
{
    /**
     * OAuth version
     */
    protected $_version = 1;

    protected $_config = array(
        'requestTokenUrl'      => 'https://api.login.yahoo.com/oauth/v2/get_request_token',
        'accessTokenUrl'       => 'https://api.login.yahoo.com/oauth/v2/get_token',
        'userAuthorizationUrl' => 'https://api.login.yahoo.com/oauth/v2/request_auth',
    );

    /**
     * Get user information
     * @param Zend_Oauth_Token_Access $accessToken
     * @return SKL_Oauth_UserProfile
     */
    public function getUserProfile($accessToken)
    {
        $guid = $accessToken->getParam('xoauth_yahoo_guid');

        $url = 'http://social.yahooapis.com/v1/user/'.$guid.'/profile';

        $client = $accessToken->getHttpClient($this->getConfigOptions());
        $client->setUri($url);
        $client->setMethod(Zend_Http_Client::GET);
        
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
        $userProfile->setId((string)$simpleXML->guid)
                    ->setName((string)$simpleXML->nickname)
                    ->setEmail((string) $simpleXML->emails->handle)
                    ->setServiceName(SKL_Oauth_UserProfile::SERVICE_YAHOO);

        return $userProfile;
    }

    /**
     * Get list of user friends
     *
     * @return SKL_Oauth_Friends
     */
    public function getFriends($accessToken)
    {
        $guid = $accessToken->getParam('xoauth_yahoo_guid');

        $url = 'http://social.yahooapis.com/v1/user/'.$guid.'/contacts';

        $client = $accessToken->getHttpClient($this->getConfigOptions());
        $client->setUri($url);
        $client->setMethod(Zend_Http_Client::GET);
        
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
    private function _createFriends($request)
    {
        $simpleXML = simplexml_load_string($request);

        $friends = new SKL_Oauth_Friends();

        if ( isset($simpleXML->contact) ) {
            foreach($simpleXML->contact->fields as $contact) {
                $friends->addFriend((string)$contact->id, (string)$contact->value);
            }
        }

        return $friends;
    }
}
