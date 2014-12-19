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

use Thrift\ClassLoader\ThriftClassLoader;
use Thrift\Transport\TSocket;
use Thrift\Transport\TBufferedTransport;
use Thrift\Protocol\TBinaryProtocolAccelerated;
use Thrift\Exception\TTransportException;
use Thrift\Exception\TException;
use org\wso2\carbon\databridge\commons\thrift\service\general\ThriftEventTransmissionServiceClient;
use Thrift\Transport\THttpClient;
use Thrift\Protocol\TCompactProtocol;
use org\wso2\carbon\databridge\commons\thrift\service\secure\ThriftSecureEventTransmissionServiceClient;
use org\wso2\carbon\databridge\commons\thrift\exception\ThriftAuthenticationException;

class PubllisherConnector
{

    private $receiverURL;

    private $authenticationURL;

    private $username;

    private $password;

    private $publisherProtocol;

    private $secureProtocol;

    private $sessionId;

    private $secureClient;

    private $publisherClient;

    private $sessionStartTime;

    private $log;

    private $authenticator;

    private $configuration;

    /**
     *
     * @internal
     *
     * @param array $receiverURL            
     * @param array $authenticationURL            
     * @param string $username            
     * @param string $password            
     *
     * @throws ConnectionException
     * @throws NullPointerException
     */
    public function __construct(array $receiverURL, array $authenticationURL, $username, $password, PublisherConfiguration $configuration)
    {
        $this->log = \Logger::getLogger(PublisherConstants::LOGGER_NAME);
        $this->receiverURL = $receiverURL;
        $this->authenticationURL = $authenticationURL;
        $this->username = $username;
        $this->password = $password;
        $this->configuration = $configuration;
        
        if ($this->receiverURL['scheme'] == 'tcp') {
            $this->createProtocol();
            $this->authenticator = new Authenticator($authenticationURL, $username, $password, $configuration);
        } elseif ($this->receiverURL['scheme'] == 'https') {
            $this->createSecureProtcol();
        }
    }

    /**
     *
     * @throws ConnectionException
     * @throws AuthenticationException
     */
    private function connect()
    {
        if ($this->receiverURL['scheme'] == 'tcp') {
            $this->sessionId = $this->authenticator->Authenticate();
        } elseif ($this->receiverURL['scheme'] == 'https') {
            try {
                $this->secureClient = new ThriftSecureEventTransmissionServiceClient($this->secureProtocol);
                $this->sessionId = $this->secureClient->connect($this->username, $this->password);
            } catch (ThriftAuthenticationException $e) {
                $error = "Error while authenticating the publisher. ".$e->getMessage();
                $this->log->error($error, $e);
                throw new AuthenticationException($error, $e);
            } catch (TTransportException $e) {
                $error = "Error connecting to secure authentication service at ".$this->buildURLWithPort($this->authenticationURL);
                $this->log->error($error, $e);
                throw new ConnectionException($error, $e);
            }
        }
    }

    /**
     * Creates thrift protoclo for event transmissions via TCP
     *
     * @throws ConnectionException
     */
    private function createProtocol()
    {
        try {
            
            $socket = new TSocket($this->buildURLWithoutPort($this->receiverURL), $this->receiverURL['port']);
            $transport = new TBufferedTransport($socket);
            $this->publisherProtocol = new TBinaryProtocolAccelerated($transport);
            $transport->open();
        } catch (TTransportExcept $e) {
            if ($e->getCode() == TTransportException::ALREADY_OPEN) {
                $this->log->warn('Socket already open - ' . $e);
            } else {
                $error = 'Error creating the publisher protocol with ' 
                                    . $socket->getHost() 
                                    . PublisherConstants::URL_HOST_AND_PORT_SEPERATOR 
                                    . $socket->getPort();
                $this->log->error($error, $e);
                throw new ConnectionException($error, $e);
            }
        } catch (TException $e) {
            $error = 'Error creating the publisher protocol with ' . $socket->getHost() 
                       . PublisherConstants::URL_HOST_AND_PORT_SEPERATOR . $socket->getPort();
            $this->log->error($error, $e);
            throw new ConnectionException($error, $e);
        }
    }

    /**
     */
    private function createSecureProtcol()
    {
        $transport = new THttpClient($this->authenticationURL['host'], 
                                    $this->authenticationURL['port'], 
                                    PublisherConstants::THRIFT_SECURE_EVENT_TRANSMISSION_SERVLET_URI, 
                                    $this->authenticationURL['scheme']);
        
        $this->secureProtocol = new TCompactProtocol($transport);
    }

    /**
     * Return the session ID for the current active session
     *
     * @return Session ID as a string upon successful connection
     *        
     * @throws ConnectionException
     * @throws AuthenticationException
     */
    public function getSessionId()
    {
        if (! isset($this->sessionId)) {
            $this->connect();
            $this->sessionStartTime = time();
        } elseif (time() - $this->sessionStartTime > PublisherConstants::DEFAULT_SESSION_TIMEOUT_SEC) { 
            // if the session is expired
            $this->log->info('Default session time expired. Reconnecting...');
            $this->connect();
            $this->log->info('Conection established');
            $this->sessionStartTime = time();
        }
        return $this->sessionId;
    }

    /**
     * Returns an active event transmisson service client
     *
     * @return ThriftSecureEventTransmissionServiceClient
     */
    public function getPublisherClient()
    {
        if ($this->receiverURL['scheme'] == 'tcp') {
            if (! isset($this->publisherClient)) {
                $this->publisherClient = new ThriftEventTransmissionServiceClient($this->publisherProtocol);
            }
            
            return $this->publisherClient;
        } elseif ($this->receiverURL['scheme'] == 'https') {
            if (! isset($this->publisherClient)) {
                $this->publisherClient = new ThriftSecureEventTransmissionServiceClient($this->secureProtocol);
            }
            
            return $this->publisherClient;
        }
    }

    /**
     * Returns an active secure event transmisson service client
     *
     * @return ThriftSecureEventTransmissionServiceClient
     */
    public function getSecureClient()
    {
        if (! isset($this->secureClient)) {
            $this->secureClient = new ThriftSecureEventTransmissionServiceClient($this->secureProtocol);
        }
        
        return $this->secureClient;
    }

    /**
     * Used to obtain the session ID in case of a ThriftSessionExpireException
     *
     * @throws ConnectionException
     * @throws AuthenticationException
     */
    public function reconnect()
    {
        $this->log->info('Session expired. Reconnecting...');
        $this->connect();
        $this->sessionStartTime = time();
    }

    /**
     *
     * Build a URL from array components
     *
     * @param string $url            
     * @return string
     */
    private function buildURLWithoutPort($url)
    {
        return $url['scheme'] . PublisherConstants::URL_SCHEME_AND_HOST_SEPERATOR . $url['host'];
    }

    private function buildURLWithPort($url)
    {
        return $url['scheme'] . PublisherConstants::URL_SCHEME_AND_HOST_SEPERATOR . $url['host']
                 . PublisherConstants::URL_HOST_AND_PORT_SEPERATOR . $url['port'];
    }
}