<?php
/*
 * Copyright (c) 2014, WSO2 Inc. (http://www.wso2.org) All Rights Reserved.
 *
 * WSO2 Inc. licenses this file to you under the Apache License,
 * Version 2.0 (the "License"); you may not use this file except
 * in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied. See the License for the
 * specific language governing permissions and limitations
 * under the License.
 */
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