# PHP Publisher for BAM

The PHP data publisher allows PHP clients to publish data to [WSO2 Business Activity Monitor]. The data can be published to predefined or custom set of data fields. The functionality of the PHP data publisher is analogous to the functionality of a custom java data publisher

The publisher uses [Apache Thrift] to publish data sent by the PHP client to the BAM server. The publisher exposes the client to operations such as defining data streams, searching stream definitions and publishing events.

## Prerequisites

- PHP 5.5.x or above 
- WSO2 BAM 2.4.1 or above

## Dependancies

- [Apache log4php] v2.3.0
- [Apache Thrift] v0.9

## API

#### `class Publisher`
```PHP
function __construct($receiverURL, $username, $password, $authenticationURL)

    @param string $receiverURL
    @param string $username
    @param string $password
    @param string $authenticationURL - @default null
  
function findStreamId($streamName, $streamVersion)
    
    @param string $streamName
    @param string $streamVersion
    @return string $streamId
    @throws NoStreamDefinitionExistException
    
function defineStream($streamDefinision)

    @param string $streamDefinision
    @return string $streamId
    @throws StreamDefinitionException
    @throws DifferentStreamDefinitionAlreadyDefinedException
    @throws MalformedStreamDefinitionException
    
function publish($event)
    @param Event $event
    @throws UnknownEventAttributeException    
```

## Getting Started

### Installing the Publisher

The recommended way of installing the Publisher is via [Composer].

The BAM PHP publisher is added to the [Packagist] archive. Therefore the publisher can be installed to a project by including it as a dependancy in `Composer.json`. When installing via Composer, log4php and thrift dependancies will be automatically installed to the project.

```json
{
    "require": {
        "omindu/php-publisher": "dev-master"
    }
}
```

[WSO2 Business Activity Monitor]:http://wso2.com/products/business-activity-monitor/
[Apache Thrift]:https://thrift.apache.org/
[Apache log4php]:http://logging.apache.org/log4php/index.html
[Composer]:https://getcomposer.org/
[Packagist]:https://packagist.org/
