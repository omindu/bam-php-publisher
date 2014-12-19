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

\Logger::configure(PublisherConstants::LOG4J_CONFIG_FILE_PATH);
use org\wso2\carbon\databridge\commons\thrift\exception\ThriftNoStreamDefinitionExistException;
use org\wso2\carbon\databridge\commons\thrift\service\secure\ThriftSecureEventTransmissionServiceClient;
use org\wso2\carbon\databridge\commons\thrift\exception\ThriftSessionExpiredException;
use org\wso2\carbon\databridge\commons\thrift\exception\ThriftDifferentStreamDefinitionAlreadyDefinedException;
use org\wso2\carbon\databridge\commons\thrift\exception\ThriftMalformedStreamDefinitionException;
use org\wso2\carbon\databridge\commons\thrift\exception\ThriftStreamDefinitionException;
use publisher\NullPointerException;
use publisher\MalformedURLException;
use org\wso2\carbon\databridge\commons\thrift\exception\ThriftUndefinedEventTypeException;

class Publisher
{

    /**
     * Array consisting Receiver URL scheme, host, port
     *
     * @var array
     */
    private $receiverURL;

    /**
     * Array consisting Authentication URL scheme, host, port
     *
     * @var array
     */
    private $authenticationURL;

    /**
     * Authentication username
     *
     * @var String
     */
    private $username;

    /**
     * Authentication username
     *
     * @var String
     */
    private $password;

    /**
     *
     * @var PublisherConnector
     */
    private $connector;

    /**
     *
     * @var log4j Logger
     */
    private $log;

    /**
     *
     * @var PublisherConfiguration
     */
    private $configuration;

    private static $RECEIVER_URL = 1;

    private static $AUTHENTICATION_URL = 2;

    /**
     *
     * @param String $receiverURL
     *            thrift TCP url and port
     *            ex: tcp://192.168.1.1:7611
     * @param String $username            
     * @param String $password            
     * @param String $authenticationURL
     *            BAM secure server
     *            ex: https://192.168.1.1:9443
     *            
     * @throws NullPointerException
     * @throws ConnectionException
     */
    public function __construct($receiverURL, $username, $password, $authenticationURL = NULL, 
                                PublisherConfiguration $configuration = null)
    {
        $this->configureLogger();
        
        if (! $configuration) {
            $configuration = new PublisherConfiguration();
        }
        
        if ($receiverURL) {
            $this->receiverURL = $this->validateURLs($receiverURL, Publisher::$RECEIVER_URL);
        } else {
            $error = 'Receiver URL cannot be NULL';
            $this->log->error($error);
            throw new NullPointerException($error);
        }
        
        $this->authenticationURL = $this->validateURLs($authenticationURL, Publisher::$AUTHENTICATION_URL);
        $this->username = $username;
        $this->password = $password;
        $this->connector = new PubllisherConnector($this->receiverURL, $this->authenticationURL, 
                                            $username, $password, $configuration);
    }

    private function configureLogger()
    {
        
        $this->log = \Logger::getLogger(PublisherConstants::LOGGER_NAME);
    }

    private function validateURLs($referenceURL, $urlType)
    {
        
        if (! $referenceURL && $urlType == Publisher::$RECEIVER_URL) {
            $error = 'Receiver URL cannot be NULL';
            $this->log->error($error);
            throw new NullPointerException($error);
        } elseif (! $referenceURL && $urlType == Publisher::$AUTHENTICATION_URL) { 
            // if authentication url is not defined construct from receiver url
            
            if ($this->receiverURL['scheme']== 'https' ) {
                $url = $this->receiverURL;
            }else{
                $url = array(
                    'scheme' => 'https',
                    'host' => $this->receiverURL['host'],
                    'port' => PublisherConstants::DEFAULT_BAM_SECURE_PORT
                );
            }
            $this->log->info('BAM secure server url not defined, Using :' . $url['scheme'] 
                            . PublisherConstants::URL_SCHEME_AND_HOST_SEPERATOR . $url['host'] 
                            . PublisherConstants::URL_HOST_AND_PORT_SEPERATOR . $url['port']);
            return $url;
        }
        
        $url = parse_url($referenceURL);
        
        /**
         * parse_url() seperates a url string into parts such as scheme, host, port, path and stores in an assosiative array
         * $array('scheme' => 'tcp',
         *         'host' => '192.168.1.1',
         *         'port' => '7611'
         *         'path' => '/path')
         * 
         * if the url only has an ip, the parse_url() method identifies the IP as a path.
         * 
         */
        if (isset($url['path']) && ! isset($url['host'])) {
            
            /**
             * Checking if the path is an IP
             */
            if (filter_var($url['path'], FILTER_VALIDATE_IP) | $url['path'] == 'localhost') {
                $url['host'] = $url['path'];
                unset($url['path']);
            } else {
                $error = 'Invalid URL '.$url;
                $this->log->error($error);
                throw new MalformedURLException($error);
            }
        }
        
        if (array_key_exists('host', $url)) {
            if (! array_key_exists('scheme', $url)) {
                
                if ($urlType == Publisher::$RECEIVER_URL) {
                    $url['scheme'] = 'tcp';
                    $this->log->warn('Receiver url scheme not defined, Using TCP.');
                } elseif ($urlType == Publisher::$AUTHENTICATION_URL) {
                    // using https by default
                    $url['scheme'] = 'https';
                    $this->log->info('BAM secure server url scheme not defined, Using https.');
                }
            } else {
                if ($urlType == Publisher::$RECEIVER_URL && ($url['scheme'] != 'tcp' && $url['scheme'] != 'https')) {
                    $this->log->error('Unsupported URL scheme ' . $url['scheme'] . ' Switching to');
                }
                if ($urlType == Publisher::$AUTHENTICATION_URL && $url['scheme'] != 'https') {
                    //TODO
                    $this->log->warn();
                }
            }
            if (! array_key_exists('port', $url)) {
                if ($urlType == Publisher::$RECEIVER_URL) {
                    // using default thrift receiver port
                    if ($url['scheme'] == 'tcp') {
                        $url['port'] = PublisherConstants::DEFAULT_THRIFT_RECEIVER_PORT;;
                    }elseif ($url['scheme'] == 'https') {
                            $url['port'] = PublisherConstants::DEFAULT_BAM_SECURE_PORT;
                        
                    }
                    
                    $this->log->warn('Receiver url port not defined, Using port: ' . $url['port']);
                } elseif ($urlType == Publisher::$AUTHENTICATION_URL) {
                    // using default BAM https port
                    $url['port'] = PublisherConstants::DEFAULT_BAM_SECURE_PORT;
                    $this->log->warn('BAM secure server port not defined, Using port: ' . $url['port']);
                }
            }
            
            if (! array_key_exists('port', $url)) {
                // using default thrift receiver port
                $url['port'] = PublisherConstants::DEFAULT_THRIFT_RECEIVER_PORT;
                $this->log->warn('Receiver url port not defined, Using port: ' . $url['port']);
            }
            
            return  $url;
        } else {
            
            $error = '';
            if ($urlType == Publisher::$RECEIVER_URL) {
                $error = "Invalid Receiver URL '" . $referenceURL . "'. Receiver URL should be in the form of tcp://[host]:[port]";
            } else 
                if ($urlType == Publisher::$AUTHENTICATION_URL) {
                    $error = "Invalid BAM secure server URL: " . $referenceURL 
                            . ". The URL should be in the form of https://[host]:[port]";
                }
            $this->log->error($error);
            throw new MalformedURLException($error);
        }
    }


    /**
     * Search a stream definition, given the stream name and the version
     *
     * @param string $streamName            
     * @param string $streaVersion            
     * @return string StreamID if exist else FALSE
     *        
     * @throws NoStreamDefinitionExistException
     */
    public function findStreamId($streamName, $streamVersion)
    {
        try {
            return $this->connector->getPublisherClient()->findStreamId($this->connector->getSessionId(), 
                                                                        $streamName, $streamVersion);
        } catch (ThriftNoStreamDefinitionExistException $e) {
            $error = 'Stream definition: ' . $streamName . ':' . $streamVersion . ' not found';
            $this->log->error($error, $e);
            throw new NoStreamDefinitionExistException('Stream definition: ' . $streamName . ':' 
                                                        . $streamVersion . ' not found', $e);
        } catch (ThriftSessionExpiredException $e) {
            $this->log->error('Session expired.', $e);
            $this->connector->reconnect();
            $this->findStream($streamName, $streamVersion);
        }
    }

    /**
     * Adds a stream definition to BAM
     *
     * @param string $streamDefinision            
     * @return string stream ID upon successfull insertion
     *        
     * @throws StreamDefinitionException
     * @throws DifferentStreamDefinitionAlreadyDefinedException
     * @throws MalformedStreamDefinitionException
     */
    public function defineStream($streamDefinision)
    {
        try {
            return $this->connector->getPublisherClient()->defineStream($this->connector->getSessionId(),
                                                                        $streamDefinision);
        } catch (ThriftDifferentStreamDefinitionAlreadyDefinedException $e) {
            $error = 'A different stream definition already exist!';
            $this->log->error($error, $e);
            throw new DifferentStreamDefinitionAlreadyDefinedException($error, $e);
        } catch (ThriftMalformedStreamDefinitionException $e) {
            $error = 'Malformed stream definition!';
            $this->log->error($error, $e);
            throw new MalformedStreamDefinitionException($error, $e);
        } catch (ThriftStreamDefinitionException $e) {
            $error = 'Error adding the stream definition!';
            $this->log->error($error, $e);
            throw new StreamDefinitionException($error, $e);
        } catch (ThriftSessionExpiredException $e) {
            $this->log->error('Session expired.', $e);
            $this->connector->reconnect();
            $this->addStreamDefinition($streamDefinision);
        }
    }

    /**
     * Publish event to BAM server
     *
     * @param Event $event            
     * @throws UnknownEventAttributeException
     */
    public function publish($event)
    {
        $eventBundle = ThriftEventConverter::covertToThriftBundle($event, $this->connector->getSessionId());
        try {
            $this->connector->getPublisherClient()->publish($eventBundle);
        } catch (ThriftUndefinedEventTypeException $e) {
            
            $error = 'Error publishing the event to stream definition!';
            $this->log->error($error, $e);
            throw new EventPublishException($error);
        } catch (ThriftSessionExpiredException $e) {
            $this->log->error('Session expired.', $e);
            $this->connector->reconnect();
            $this->publish($event);
        }
    }
    
    
}