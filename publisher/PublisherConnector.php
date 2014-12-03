<?php
include __DIR__ . '/../lib/Thrift/ClassLoader/ThriftClassLoader.php';

use Thrift\ClassLoader\ThriftClassLoader;

$loader = new ThriftClassLoader ();
$loader->registerNamespace ( 'Thrift', __DIR__ . '/../lib' );
$loader->register ();

include __DIR__ . '/../gen-php/org/wso2/carbon/databridge/commons/thrift/exception/Types.php';
include __DIR__ . '/../gen-php/org/wso2/carbon/databridge/commons/thrift/service/general/ThriftEventTransmissionService.php';
include __DIR__ . '/../gen-php/org/wso2/carbon/databridge/commons/thrift/service/secure/ThriftSecureEventTransmissionService.php';
include '../logger/php/Logger.php';

Logger::configure('../logger/config.xml');

use Thrift\Transport\TSocket;
use Thrift\Transport\TBufferedTransport;
use Thrift\Protocol\TBinaryProtocolAccelerated;
use org\wso2\carbon\databridge\commons\thrift\service\secure\ThriftSecureEventTransmissionServiceClient;
use Thrift\Exception\TTransportException;
class PubllisherConnector {
	private $receiverURL;
	private $authenticationURL;
	private $username;
	private $password;
	private $isSinglePublishURL;
	private $secureProtocol;
	private $publisherProtocol;
	private $sessionId;
	private $secureClient;
	private $publisherClient;
	private $sessionStartTime;
	private $log;
	
	/**
	 *
	 * @param array $receiverURL        	
	 * @param array $authenticationURL        	
	 * @param string $username        	
	 * @param string $password        	
	 */
	public function __construct($receiverURL, $authenticationURL, $username, $password) {
		$this->log = Logger::getLogger('PublisherLogger');
		$this->receiverURL = $receiverURL;
		$this->authenticationURL = $authenticationURL;
		$this->username = $username;
		$this->password = $password;
		
		if ($receiverURL == $authenticationURL) {
			$this->isSinglePublishURL = TRUE;
		} else {
			$this->isSinglePublishURL = FALSE;
		}
		
		$this->createProtocol ();
	}
	
	/**
	 * Authenticate publisher via secure connection
	 */
	private function connect() {
		try {
			$this->secureClient = new ThriftSecureEventTransmissionServiceClient ( $this->secureProtocol );
			$this->sessionId = $this->secureClient->connect ( $this->username, $this->password );
		}catch(Exception $e){
			$this->log->error('Error connecting the secure client - '.$e);
			$this->sessionId = 'd50c08c4-ecf2-42db-abba-519c1f20f026';
		}
		
	}
	
	/**
	 * Creates thrift protoclos for secure ad normal transmissions
	 */
	private function createProtocol() {
		try {
			
			$this->log->info('creating secure protocole');
			$secureSocket = new TSocket ( $this->buildURL ( $this->authenticationURL ), $this->authenticationURL ['port'] );
			$secureTransport = new TBufferedTransport ( $secureSocket );
			$this->secureProtocol = new TBinaryProtocolAccelerated ( $secureTransport );
			$secureTransport->open ();
		} catch ( TTransportException $e ) {
			
			if ($e->getCode () == TTransportException::ALREADY_OPEN) {
				$this->log->warn('Socket already open - '.$e);
			} else {
				$this->log->error('Error creating the secure protocol - '.$e);
				throw $e;
			}
		}
		
		if ($this->isSinglePublishURL) {
			$this->publisherProtocol = &$this->secureProtocol;
		} else {
			
			try {
				$socket = new TSocket ( $this->buildURL ( $this->receiverURL ), $this->receiverURL ['port'] );
				$transport = new TBufferedTransport ( $socket );
				$this->publisherProtocol = new TBinaryProtocolAccelerated ( $transport );
				$transport->open ();
			} catch ( TTransportExcept $e ) {
				if ($e->getCode () == TTransportException::ALREADY_OPEN) {
					$this->log->warn('Socket already open - '.$e);
				} else {
					$this->log->error('Error creating the publisher protocol - '.$e);
					throw $e;
				}
			}
		}
	}
	
	/**
	 * Return the session ID for the current active session
	 *
	 * @return Session ID as a string upon successful connection
	 */
	public function getSessionId() {
		if (! isset ( $this->sessionId )) {
			$this->connect ();
			$this->sessionStartTime = time ();
		} else if (time () - $this->sessionStartTime > PublisherConstants::DEFAULT_SESSION_TIMEOUT_SEC) { // if the session is expired
			$this->log->info('Default session time expired. Reconnecting...');
			$this->connect ();
			$this->log->info('Conection established');
			$this->sessionStartTime = time ();
		}
		return $this->sessionId;
	}
	
	/**
	 * Returns an active event transmisson service client
	 *
	 * @return ThriftSecureEventTransmissionServiceClient
	 */
	public function getPublisherClient() {
		if (! isset ( $this->publisherClient )) {
			$this->publisherClient = new ThriftSecureEventTransmissionServiceClient ( $this->publisherProtocol);
		}
		
		return $this->publisherClient;
	}
	
	/**
	 *
	 * Build a URL from array components
	 *
	 * @param string $url        	
	 * @return string
	 */
	private function buildURL($url) {
		return $url ['scheme'] . '://' . $url ['host'];
	}
}