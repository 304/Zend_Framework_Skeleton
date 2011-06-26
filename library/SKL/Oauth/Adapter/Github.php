<?php
/**
 * Github adapter for oauth service
 * @author Yaklushin Vasiliy <3a3a3a3@gmail.com>
 */
class SKL_Oauth_Adapter_Github extends SKL_Oauth_Adapter_Abstract
{
    /**
     * OAuth version
     */
    protected $_version = 2;

    protected $_config = array(
        'version'              => '2.0',
        'requestTokenUrl'      => 'https://github.com/login/oauth/authorize',
        'userAuthorizationUrl' => 'https://github.com/login/oauth/authorize',
        'accessTokenUrl'       => 'https://github.com/login/oauth/access_token',
        'siteUrl'              => 'https://github.com/login/oauth',
    );

    /**
     * Get user information
     *
     * @return SKL_Oauth_UserProfile
     */
    public function getUserProfile($accessToken)
    {
        $url = 'http://github.com/api/v2/json/user/show';

        $content = file_get_contents($url.'?'.$accessToken);

        $decodedContent = json_decode($content);

        if (is_null($decodedContent) ) {
            throw new SKL_Oauth_Exception('Cannot decode json response from url ['.$url.']');
        }

        return $this->_createProfile($decodedContent->user);
    }

    /**
     * Create user profile from request
     *
     * @param stdClass $request
     * @return SKL_Oauth_UserProfile
     */
    private function _createProfile($request)
    {
        $userProfile = new SKL_Oauth_UserProfile();

        $userProfile->setId($request->id);
        $userProfile->setName($request->login);
        $userProfile->setServiceName(SKL_Oauth_UserProfile::SERVICE_GITHUB);

        return $userProfile;
    }

    /**
     * Get list of user friends 
     * 
     * @return SKL_Oauth_Friends
     */
    public function getFriends($accessToken)
    {
        $userProfile = $this->getUserProfile($accessToken);
        
        $url = 'http://github.com/api/v2/json/user/show/'.$userProfile->getName().'/following';

        $content = file_get_contents($url.'?'.$accessToken);

        $decodedContent = json_decode($content);

        if (is_null($decodedContent) ) {
            throw new SKL_Oauth_Exception('Cannot decode json response from url ['.$url.']');
        }

        return $this->_createFriends($decodedContent->users);
        
    }
    
    /**
     * Create friends object from request
     *
     * @param array $request
     * @return SKL_Oauth_Friends
     */
    private function _createFriends($request)
    {
        $friends = new SKL_Oauth_Friends();

        foreach($request as $key => $friendName) {
            $friends->addFriend($key, $friendName);
        }

        return $friends;
    }    
}
