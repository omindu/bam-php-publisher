<?php
namespace publisher;

// include '../logger/php/Logger.php';

// Logger::configure('../logger/config.xml');
use Thrift\ClassLoader\ThriftClassLoader;
use Thrift\Transport\TSocket;
use Thrift\Transport\TBufferedTransport;
use Thrift\Protocol\TBinaryProtocolAccelerated;
use Thrift\Exception\TTransportException;
use Thrift\Exception\TException;
use org\wso2\carbon\databridge\commons\thrift\service\general\ThriftEventTransmissionServiceClient;

class PubllisherConnector
{

    private $receiverURL;

    private $authenticationURL;

    private $username;

    private $password;

    private $publisherProtocol;

    private $sessionId;

    private $secureClient;

    private $publisherClient;

    private $sessionStartTime;

    private $log;

    private $authenticator;

    /**
     *
     * @param array $receiverURL            
     * @param array $authenticationURL            
     * @param string $username            
     * @param string $password 
     * 
     * @throws ConnectionException
     * @throws NullPointerException        
     */
    public function __construct($receiverURL, $authenticationURL, $username, $password)
    {
        $this->log = \Logger::getLogger(PublisherProperties::getLoggerName());
        $this->receiverURL = $receiverURL;
        $this->authenticationURL = $authenticationURL;
        $this->username = $username;
        $this->password = $password;
        
        $this->createProtocol();
        $this->authenticator = new Authenticator($authenticationURL, $username, $password);
    }

    /**
     *
     * @throws ConnectionException
     * @throws AuthenticationException
     */
    private function connect()
    {
        $this->sessionId = $this->authenticator->Authenticate();
        // $this->sessionId = '94509fe0-3325-4029-875a-1718ce210252';
    }

    /**
     * Creates thrift protoclo for event transmissions via TCP
     * 
     * @throws ConnectionException
     */
    private function createProtocol()
    {
        try {
            $socket = new TSocket($this->buildURL($this->receiverURL), $this->receiverURL['port']);
            $transport = new TBufferedTransport($socket);
            $this->publisherProtocol = new TBinaryProtocolAccelerated($transport);
            $transport->open();
        } catch (TTransportExcept $e) {
            if ($e->getCode() == TTransportException::ALREADY_OPEN) {
                $this->log->warn('Socket already open - ' . $e);
            } else {
                $error = 'Error creating the publisher protocol.';
                $this->log->error($error, $e);
                throw new ConnectionException($error, $e);
            }
        } catch (TException $e) {
            $error = 'Error creating the publisher protocol.';
            $this->log->error($error , $e);
            throw new ConnectionException('Error creating the publisher protocol.', $e);
        }
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
        } elseif (time() - $this->sessionStartTime > PublisherConstants::DEFAULT_SESSION_TIMEOUT_SEC) { // if the session is expired
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
        if (! isset($this->publisherClient)) {
            $this->publisherClient = new ThriftEventTransmissionServiceClient($this->publisherProtocol);
        }
        
        return $this->publisherClient;
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
    private function buildURL($url)
    {
        return $url['scheme'] . '://' . $url['host'];
    }
}