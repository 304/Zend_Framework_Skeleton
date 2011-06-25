<?php
/**
 * Linkedin adapter for oauth service
 * @author Yaklushin Vasiliy <3a3a3a3@gmail.com>
 */
class SKL_Oauth_Adapter_Linkedin extends SKL_Oauth_Adapter_Abstract
{
    /**
     * OAuth version
     */
    protected $_version = 1;

    protected $_config = array(
        'version'              => '1.0',
        'requestTokenUrl'      => 'https://api.linkedin.com/uas/oauth/requestToken',
        'userAuthorizationUrl' => 'https://api.linkedin.com/uas/oauth/authorize',
        'accessTokenUrl'       => 'https://api.linkedin.com/uas/oauth/accessToken',
        'siteUrl'              => 'https://api.linkedin.com/uas/oauth',
    );

    /**
     * Get user profile
     *
     * @param Zend_Oauth_Token_Access $accessToken
     * @return SKL_Oauth_UserProfile
     */
    public function getUserProfile($accessToken)
    {
        $url = 'https://api.linkedin.com/v1/people/~:(id,first-name,last-name)';

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
        $parameters = (array)simplexml_load_string($request);

        $id = $parameters['id'];
        $userName = $parameters['first-name'].' '.$parameters['last-name'];

        $userProfile = new SKL_Oauth_UserProfile();
        $userProfile->setId($id)
                    ->setName($userName)
                    ->setServiceName(SKL_Oauth_UserProfile::SERVICE_LINKEDIN);

        return $userProfile;
    }

    /**
     * Get list of user friends
     *
     * @param Zend_Oauth_Token_Access $accessToken
     * @return SKL_Oauth_Friends
     */
    public function getFriends($accessToken)
    {
        $url = 'http://api.linkedin.com/v1/people/~/connections';

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

        $person = (array) $simpleXML->person;

        $friendId   = $person['id'];
        $friendName = $person['first-name'].' '.$person['last-name'];

        $friends = new SKL_Oauth_Friends();
        $friends->addFriend($friendId, $friendName);

        return $friends;
    }
}
