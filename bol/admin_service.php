<?php

class SPODOAUTH2CONNECT_BOL_AdminService extends SPODOAUTH2CONNECT_BOL_Service
{
    private static $classInstance;

    /**
     * Returns class instance
     *
     * @return SPODOAUTH2CONNECT_BOL_AdminService
     */
    public static function getInstance() {
        if ( null === self::$classInstance )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    public static function configureApplication() {
   
        return true;
    }


}