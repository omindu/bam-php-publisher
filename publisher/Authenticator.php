<?php
namespace publisher;

include_once __DIR__ . '/../vendor/autoload.php';
use publisher\ConnectionException;
use publisher\AuthenticationException;

class Authenticator
{

    private $username;

    private $password;

    private $authenticationURL;
    
    private $log;

    /**
     *
     * @param array $authenticationURL            
     * @param string $username            
     * @param string $password            
     * @throws NullPointerException
     */
    public function __construct($authenticationURL, $username, $password)
    {
        $this->log = \Logger::getLogger('PublisherLogger');
        
        if (! $authenticationURL) {
            $error = 'Receiver URL cannot be NULL';
            $this->log->error($error);
            throw new NullPointerException($message);
        }
        $this->authenticationURL = $authenticationURL;
        $this->username = $username;
        $this->password = $password;
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
        $credentials = array(
            'username' => $this->username,
            'password' => $this->password
        );
        
        $jsonCredentials = json_encode($credentials);
        
        $curl = curl_init($this->authenticationURLBuilder($this->authenticationURL));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curl, CURLOPT_CAINFO, PublisherConstants::CAFILE_PATH);
        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, 1);
        
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json; charset=UTF-8"
        ));
        curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonCredentials);
        $response = curl_exec($curl);
        $errorStatus = curl_errno($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        echo $errorStatus;
        var_dump(curl_error($curl));
        var_dump($response);
        var_dump(curl_getinfo($curl));
        
        if ($errorStatus !== 0) {
            
            $error = "Error connectiong to secure authentication service. " . curl_error($curl);
            throw new ConnectionException($error);
        } elseif ($statusCode !== 200) {
            $error = "";
            // TODO
            throw new AuthenticationException($error);
        }
        
        curl_close($curl);
        
        // TODO handle response
        $var = explode(' - ', $response);
        
        return $var[1];
    }

    private function authenticationURLBuilder($authenticationURL)
    {
        return $authenticationURL['scheme'] . '://' . $authenticationURL['host'] . ':' . $authenticationURL['port']. PublisherConstants::PUBLISHER_AUTHENTICATION_SERVICE_URL;
    }
}


