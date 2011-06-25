<?php
/**
 * Facebook adapter for oauth service
 * @author Yaklushin Vasiliy <3a3a3a3@gmail.com>
 */
class SKL_Oauth_Adapter_Facebook extends SKL_Oauth_Adapter_Abstract
{
    /**
     * OAuth version
     */
    protected $_version = 2;

    protected $_config = array(
        'version'              => '2.0',
        'requestTokenUrl'      => 'https://graph.facebook.com/oauth/authorize',
        'userAuthorizationUrl' => 'https://graph.facebook.com/oauth/authorize',
        'accessTokenUrl'       => 'https://graph.facebook.com/oauth/access_token',
        'siteUrl'              => 'https://graph.facebook.com/oauth',
    );

    /**
     * Get user information
     *
     * @return SKL_Oauth_UserProfile
     */
    public function getUserProfile($accessToken)
    {
        $url = 'https://graph.facebook.com/me';

        $content = file_get_contents($url.'?'.$accessToken);

        $decodedContent = json_decode($content);

        if (is_null($decodedContent) ) {
            throw new SKL_Oauth_Exception('Cannot decode json response from url ['.$url.']');
        }

        // check for facebook error
        if ( isset($decodedContent->error) ) {
            throw new SKL_Oauth_Exception($decodedContent->error->message);
        }

        return $this->_createProfile($decodedContent);
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
        $userProfile->setName($request->name);
        $userProfile->setServiceName(SKL_Oauth_UserProfile::SERVICE_FACEBOOK);

        return $userProfile;
    }

    /**
     * Get list of user friends
     * 
     * @return SKL_Oauth_Friends
     */
    public function getFriends($accessToken)
    {
        $url = 'https://graph.facebook.com/me/friends';

        $content = file_get_contents($url.'?'.$accessToken);

        $decodedContent = json_decode($content);

        if (is_null($decodedContent) ) {
            throw new SKL_Oauth_Exception('Cannot decode json response from url ['.$url.']');
        }

        // check for facebook error
        if ( isset($decodedContent->error) ) {
            throw new SKL_Oauth_Exception($decodedContent->error->message);
        }

        return $this->_createFriends($decodedContent);
    }

    /**
     * Create friends object from request
     *
     * @param stdClass $request
     * @return SKL_Oauth_Friends
     */
    private function _createFriends($request)
    {
        $friends = new SKL_Oauth_Friends();

        foreach($request->data as $friend) {
            $friends->addFriend($friend->id, $friend->name);
        }

        return $friends;
    }
}
