<?php

namespace publisher;

class PublisherConfiguration{
    
    
    private  $verifyPeer = false;
    
    private  $caFile = null;
    

    public function __construct($verifyPeer = false, $caFile = null){
        if ($verifyPeer) {
            $this->verifyPeer = $verifyPeer;
            if ($caFile) {
                $this->caFile = $caFile;
            }else{
                \Logger::configure(PublisherConstants::LOG4J_CONFIG_FILE_PATH);
                $log = \Logger::getLogger(PublisherConstants::LOGGER_NAME);
                $error = '$caFile cannot be null when $verifyPeer is \'true\'. Please set the $caFile ';
                $log->error($error);
                throw new NullPointerException($error);
            }
        }

    }
    
    /**
     * To verify BAM server set 'true'.
     * If true, should provide the server certificate via
     * $publisherConfiguration->setCaFile($caFile)
     * If the certificate is not provided, default certificate will be used.
     *
     * @param boolean $verifyPeer
     */
    public function setVerifyPeer($verifyPeer)
    {
        $this->verifyPeer = $verifyPeer;
    }
    
    /**
     *
     * @return boolean
     */
    public  function getVerifyPeer()
    {
        return $this->verifyPeer;
    }
    
    /**
     * set server certificate file absolute path in pem format
     *
     * @param string $caFile
     */
    public  function setCaFile($caFile)
    {
        $this->caFile = $caFile;
    }
    
    /**
     *
     * @return string server certificate file absolute path in pem format
     */
    public  function getCaFile()
    {
        return $this->caFile;
    }
    
}