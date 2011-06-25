<?php
/**
 * Interface for Oauth versions
 * @author Yaklushin Vasiliy <3a3a3a3@gmail.com>
 */
interface SKL_Oauth_Version_Interface
{
    public function authorize();
    public function requestAccessToken($queryData);
}