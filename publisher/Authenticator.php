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

use publisher\ConnectionException;
use publisher\AuthenticationException;

class Authenticator
{

    private $username;

    private $password;

    private $authenticationURL;

    private $log;

    private $configuration;

    /**
     *
     * @param array $authenticationURL            
     * @param string $username            
     * @param string $password            
     * @throws NullPointerException
     */
    public function __construct(array $authenticationURL, $username, $password, PublisherConfiguration $configuration)
    {
        $this->log = \Logger::getLogger(PublisherConstants::LOGGER_NAME);
        
        if (! $authenticationURL) {
            $error = 'Authentication URL cannot be NULL';
            $this->log->error($error);
            throw new NullPointerException($message);
        }
        $this->authenticationURL = $authenticationURL;
        $this->username = $username;
        $this->password = $password;
        $this->configuration = $configuration;
    }

    /**
     * {@internal Only for the use of PublisherConnector}
     *
     * Authenticate the publisher through BAM JAX-RS authentication service and return the SessionID
     *
     * @throws ConnectionException
     * @throws AuthenticationException
     * @return string Session ID as a string
     */
    public function Authenticate()
    {
        $curl = curl_init($this->authenticationURLBuilder($this->authenticationURL));
        
        if ($this->configuration->getVerifyPeer()) {
            if ($this->log->isDebugEnabled()) {
                $this->log->debug("Peer verification enabled");
            }
            
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
            
            if ($this->configuration->getCaFile()) {
                
                if ($this->log->isDebugEnabled()) {
                    $this->log->debug("Setting CA file to: " . $this->configuration->getCaFile());
                }
                curl_setopt($curl, CURLOPT_CAINFO, $this->configuration->getCaFile());
            } else {
                $this->log->warn("CA File not set. Using default value: " . PublisherConstants::CAFILE_PATH);
                curl_setopt($curl, CURLOPT_CAINFO, PublisherConstants::CAFILE_PATH);
            }
        } else {
            $this->log->info("Peer verification disabled");
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        }
        
        curl_setopt($curl, CURLOPT_USERPWD, $this->username . ':' . $this->password);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($curl);
        
        $errorStatus = curl_errno($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        
        // echo $errorStatus;
        // var_dump(curl_error($curl));
        // var_dump($response);
        // var_dump(curl_getinfo($curl));
        
        if ($errorStatus !== 0) {
            
            $error = "Error connecting to secure authentication service. " . curl_error($curl);
            $this->log->error($error);
            throw new ConnectionException($error);
        } elseif ($statusCode !== 200) {
            $error = "Authentication error.\nError Code: " . $statusCode . "\n" . $response;
            $this->log->error($error);
            throw new AuthenticationException($error);
        }
        
        $sessionID = base64_decode($response);
        curl_close($curl);
        
        return $sessionID;
    }

    private function authenticationURLBuilder($authenticationURL)
    {
        return $authenticationURL['scheme'] . PublisherConstants::URL_SCHEME_AND_HOST_SEPERATOR . $authenticationURL['host'] . PublisherConstants::URL_HOST_AND_PORT_SEPERATOR . $authenticationURL['port'] . PublisherConstants::PUBLISHER_AUTHENTICATION_SERVICE_URL;
    }
}


