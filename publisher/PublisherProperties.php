<?php
namespace publisher;

class PublisherProperties
{

    private static $verifyPeer = false;

    private static $caFile = null;

    private static $loggerConfigFile = null;

    private static $loggerName = null;

    /**
     * To verify BAM server set 'true'.
     * If true, should provide the server certificate via
     * PublisherProperties::setCaFile($caFile)
     * If the certificate is not provided, default certificate will be used.
     *
     * @param boolean $verifyPeer            
     */
    public static function setVerifyPeer($verifyPeer)
    {
        PublisherProperties::$verifyPeer = $verifyPeer;
    }

    /**
     *
     * @return boolean
     */
    public static function getVerifyPeer()
    {
        return PublisherProperties::$verifyPeer;
    }

    /**
     * set server certificate file absolute path in pem format
     *
     * @param string $caFile            
     */
    public static function setCaFile($caFile)
    {
        PublisherProperties::$caFile = $caFile;
    }

    /**
     *
     * @return string server certificate file absolute path in pem format
     */
    public static function getCaFile()
    {
        return PublisherProperties::$caFile;
    }

    /**
     * set logger configuration xml file path
     *
     * @param string $loggerConfigFile            
     */
    public static function setLoggerConfigFile($loggerConfigFile)
    {
        PublisherProperties::$loggerConfigFile = $loggerConfigFile;
    }

    /**
     *
     * @return string logger configuration xml file path
     */
    public static function getLoggerConfigFile()
    {
        return PublisherProperties::$loggerConfigFile;
    }

    /**
     * 
     * @param string $loggerName
     */
    public static function setLoggerName($loggerName)
    {
        PublisherProperties::$loggerName = $loggerName;
    }

    /**
     * 
     * @return string logger name
     */
    public static function getLoggerName()
    {
        return PublisherProperties::$loggerName;
    }
}