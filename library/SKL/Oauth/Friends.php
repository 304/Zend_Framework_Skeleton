<?php
/**
 * User's friends in Oauth service
 * @author Yaklushin Vasiliy <3a3a3a3@gmail.com>
 */
class SKL_Oauth_Friends
{
    protected $_friends;

    public function addFriend($friendId, $friendName)
    {
        $this->_friends[$friendId] = $friendName;
    }

    public function getAll()
    {
        return $this->_friends;
    }
}