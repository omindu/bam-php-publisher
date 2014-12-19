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

use \Exception;

class PublisherException extends Exception
{

    public static $MALFORMED_URL_EXCEPTION = 1;

    public static $NULL_POINTER_EXCEPTION = 2;

    public static $STREAM_DEFINITION_EXCEPTION = 3;

    public static $CONNECTION_EXCEPTION = 4;

    public static $UNKNOWN_ATTRIBUTE_EXCEPTION = 5;

    public static $AUTHENTICATION_EXCEPTION = 6;
    
    public static $EVENT_PUBLISH_EXCEPTION = 7;
    
    public static $MALFORMED_STREAM_DEFINITION_EXCEPTION = 8;
    
    public static $DIFFERENT_STREAM_DEFINITION_ALREADY_DEFINED_EXCEPTION = 9;
    
    public static $NO_STREAM_DEFINITION_EXCEPTION = 10;



    public function __construct($message, $errorCode = null, $error = null)
    {
        
        parent::__construct($message, $errorCode, $error);
        
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

class DifferentStreamDefinitionAlreadyDefinedException extends PublisherException{

    public function __construct($message, $error = null)
    {
        parent::__construct($message, PublisherException::$DIFFERENT_STREAM_DEFINITION_ALREADY_DEFINED_EXCEPTION, $error);
    }
}
 
class MalformedStreamDefinitionException extends PublisherException{

    public function __construct($message, $error = null)
    {
        parent::__construct($message, PublisherException::$MALFORMED_STREAM_DEFINITION_EXCEPTION, $error);
    }
}

class NoStreamDefinitionExistException extends PublisherException{

    public function __construct($message, $error = null)
    {
        parent::__construct($message, PublisherException::$NO_STREAM_DEFINITION_EXCEPTION, $error);
    }
}


class ConnectionException extends PublisherException
{

    public function __construct($message, $error = null)
    {
        parent::__construct($message, PublisherException::$CONNECTION_EXCEPTION, $error);
    }
}

class UnknownEventAttributeException extends PublisherException
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

class EventPublishException extends PublisherException
{

    public function __construct($message, $error = null)
    {
        parent::__construct($message, PublisherException::$EVENT_PUBLISH_EXCEPTION, $error);
    }
}
