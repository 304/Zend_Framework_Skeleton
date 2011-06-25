<?php
/**
 * SKL Oauth storage interface
 * 
 */
interface SKL_Oauth_Storage_Interface
{
    /**
     * Returns true if and only if storage is empty
     *
     * @throws SKL_Oauth_Storage_Exception If it is impossible to determine whether storage is empty
     * @return boolean
     */
    public function isEmpty();

    /**
     * Returns the contents of storage
     *
     * Behavior is undefined when storage is empty.
     *
     * @throws SKL_Oauth_Storage_Exception If reading contents from storage is impossible
     * @return mixed
     */
    public function read();

    /**
     * Writes $contents to storage
     *
     * @param  mixed $contents
     * @throws SKL_Oauth_Storage_Exception If writing $contents to storage is impossible
     * @return void
     */
    public function write($contents);

    /**
     * Clears contents from storage
     *
     * @throws SKL_Oauth_Storage_Exception If clearing contents from storage is impossible
     * @return void
     */
    public function clear();
}
