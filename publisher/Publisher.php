<?php
include 'PublisherConstants.php';
include 'PublisherConnector.php';
include '../logger/php/Logger.php';

Logger::configure('../logger/config.xml');

use org\wso2\carbon\databridge\commons\thrift\exception\ThriftNoStreamDefinitionExistException;
use org\wso2\carbon\databridge\commons\thrift\service\secure\ThriftSecureEventTransmissionServiceClient;
class Publisher {
	
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
	 * @param String $receiverURL
	 *        	ex: tcp://192.168.1.5:7611
	 * @param String $username        	
	 * @param String $password        	
	 * @param String $authenticationURL
	 *        	ex: ssl://192.168.1.5:7711
	 */
	public function __construct($receiverURL, $username, $password, $authenticationURL = NULL) {
		
		$this->log = Logger::getLogger('PublisherLogger');
		if ($receiverURL) {
			$this->setReceiverURL ( $receiverURL );
		} else {
			$this->log->error('Receiver URL cannot be NULL');
			throw new Exception ( "Receiver URL cannot be NULL" );
		}
		
		$this->setAuthenticationURL ( $authenticationURL );
		$this->username = $username;
		$this->password = $password;
		$this->connector = new PubllisherConnector ( $this->receiverURL, $this->authenticationURL, $username, $password );
	}
	
	/**
	 *
	 * @param array $receiverURL        	
	 * @throws Exception
	 */
	private function setReceiverURL($receiverURL) {
		if (strpos ( $receiverURL, 'localhost' ) !== FALSE) {
			str_replace ( 'localhost', '127.0.0.1', $receiverURL );
			$this->log->info('Changing receiver url from \'localhost\' to 127.0.0.1');
		}
		$url = parse_url ( $receiverURL );
		
		if (array_key_exists ( 'host', $url ) && array_key_exists ( 'port', $url )) {
			if (! array_key_exists ( 'scheme', $url )) {
				// using tcp by default
				$url ['scheme'] = 'tcp';
				$this->log->info('Receiver url scheme not defined, Using TCP.');
			}
			
			$this->receiverURL = $url;
		} else {
			$this->log->error("Invalid Receiver URL".$receiverURL.". Receiver URL should be in the form of [tcp|ssl]://[host]:[port]");
			throw new Exception ( "Receiver URL should be in the form of [tcp|ssl]://[host]:[port]" );
		}
	}
	
	/**
	 *
	 * @param array $authenticationURL        	
	 * @throws Exception
	 */
	private function setAuthenticationURL($authenticationURL) {
		if ($authenticationURL) {
			if (strpos ( $authenticationURL, 'localhost' ) !== FALSE) {
				str_replace ( 'localhost', '127.0.0.1', $authenticationURL );
				$this->log->info('Changing authentication url from \'localhost\' to 127.0.0.1');
			}
			$url = parse_url ( $authenticationURL );
			
			if (array_key_exists ( 'host', $url )) {
				if (! array_key_exists ( 'port', $url )) {
					// using default thrift receiver port
					$url ['port'] = PublisherConstants::DEFAULT_RECEIVER_PORT + PublisherConstants::SECURE_RECEIVER_PORT_OFFSET;
					$this->log->info('Authentication url port not defined, Using port:'.$url ['port']);
				}
				if (! array_key_exists ( 'scheme', $url )) {
					// using ssl by default
					$url ['scheme'] = 'ssl';
					$this->log->info('Authentication url scheme not defined, Using SSL.');
				}
				
				$this->authenticationURL = $url;
			} else {
				$this->log->error("Invalid Authentication URL".$authenticationURL.". Authentication URL should be in the form of [ssl]://[host]:[port]");
				throw new Exception ( "Authentication URL should be in the form of [ssl]://[host]:[port]" );
			}
		} else { // if authentication url is not defined construct from receiver url
			$this->authenticationURL = array (
					'scheme' => 'ssl',
					'host' => $this->receiverURL ['host'],
					'port' => $this->receiverURL ['port'] + PublisherConstants::SECURE_RECEIVER_PORT_OFFSET 
			);
			$this->log->info('Authentication url not defined, Using :'.$this->authenticationURL['scheme']."://".$this->authenticationURL['host'].':'.$this->authenticationURL['port']);
		}
	}
	
	/**
	 *
	 * @param string $streamName        	
	 * @param string $streaVersion        	
	 * @return StreamID if exist else FALSE
	 */
	public function findStream($streamName, $streamVersion) {
		try {
			return $this->connector->getPublisherClient ()->findStreamId ( $this->connector->getSessionId (), $streamName, $streamVersion );
		} catch ( ThriftNoStreamDefinitionExistException $e ) {
			$this->log->info('Stream definition'.$streamName.':'.$streamVersion.'not found.');
			return FALSE;
		} catch (ThriftSessionExpiredException $e){
			$this->log->error('Stream definition expired.');
		}
	}
	public function addStreamDefinition($streamDefinision, $streamName, $streamVersion) {
		return $this->connector->getPublisherClient ()->defineStream ( $this->connector->getSessionId (), $streamDefinition );
		
		// $connector = new ThriftSecureEventTransmissionServiceClient($input);
		// $connector->defineStream($sessionId, $streamDefinition);
	}
}