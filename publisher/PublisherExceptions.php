<?php
namespace publisher;

use \Exception;

class PublisherException extends Exception
{

    public static $MALFORMED_URL_EXCEPTION = 1;

    public static $NULL_POINTER_EXCEPTION = 2;

    public static $STREAM_DEFINITION_EXCEPTION = 3;

    public static $CONNECTION_EXCEPTION = 4;

    public static $UNKNOWN_ATTRIBUTE_EXCEPTION = 5;

    public static $AUTHENTICATION_EXCEPTION = 6;

    private static $log = null;

    public function __construct($message, $errorCode = null, $error = null)
    {
        
        parent::__construct($message, $errorCode, $error);
        PublisherException::logError($message, $errorCode, $error) ;
    }

    private static function logError($message, $errorCode, $error)
    {
        if (! PublisherException::$log) {
            PublisherException::$log = \Logger::getLogger(PublisherConstants::LOGGER_NAME);
        }
        
        if ($error) {
            PublisherException::$log->error($message . ' ' . $error);
        } else {
            PublisherException::$log->error($message);
        }
    }
}

class MalformedURLException extends PublisherException
{

    public function __construct($message, $error = null)
    {
        parent::__construct($message, PublisherException::$MALFORMED_URL_EXCEPTION, $error);
    }
}

class NullPointerException extends PublisherException
{

    public function __construct($message, $error = null)
    {
        parent::__construct($message, PublisherException::$NULL_POINTER_EXCEPTION, $error);
    }
}

class StreamDefinitionException extends PublisherException
{

    public function __construct($message, $error = null)
    {
        parent::__construct($message, PublisherException::$STREAM_DEFINITION_EXCEPTION, $error);
    }
}

class ConnectionException extends PublisherException
{

    public function __construct($message, $error = null)
    {
        parent::__construct($message, PublisherException::$CONNECTION_EXCEPTION, $error);
    }
}

class UnknownAttributeException extends PublisherException
{

    public function __construct($message, $error = null)
    {
        parent::__construct($message, PublisherException::$UNKNOWN_ATTRIBUTE_EXCEPTION, $error);
    }
}

class AuthenticationException extends PublisherException
{

    public function __construct($message, $error = null)
    {
        parent::__construct($message, PublisherException::$AUTHENTICATION_EXCEPTION, $error);
    }
}
