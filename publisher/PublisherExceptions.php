<?php
namespace publisher;

use \Exception;

class MalformedURLException extends Exception
{

    public function __construct($message, $error = null)
    {
        parent::__construct($message, null, $error);
    }
}

class NullPointerException extends Exception
{

    public function __construct($message, $error = null)
    {
        parent::__construct($message, null, $error);
    }
}

class StreamDefinitionException extends Exception
{

    public function __construct($message, $error = null)
    {
        parent::__construct($message, null, $error);
    }
}

class ConnectionException extends Exception
{

    public function __construct($message, $error = null)
    {
        parent::__construct($message, null, $error);
    }
}

class UnknownAttributeException extends Exception
{

    public function __construct($message, $error = null)
    {
        parent::__construct($message, null, $error);
    }
}
