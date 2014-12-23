# PHP Publisher for BAM

The PHP data publisher allows PHP clients to publish data to [WSO2 Business Activity Monitor]. The data can be published to predefined or custom set of data fields. The functionality of the PHP data publisher is analogous to the functionality of a custom java data publisher

The publisher uses [Apache Thrift] to publish data sent by the PHP client to the BAM server. The publisher exposes the client to operations such as defining data streams, searching stream definitions and publishing events.

Read more on data streams and publishing data to WSO2 BAM

* [Data Publisher]
* [Stream Definitions]
* [Creating Custom Data Publishers to BAM/CEP]

## Prerequisites

- PHP 5.5.x or above 
- WSO2 BAM 2.4.1 or above

## Dependancies

- [Apache log4php] v2.3.0
- [Apache Thrift] v0.9

## Getting Started

### Installing the Publisher

The recommended way of installing the Publisher is via [Composer].

The BAM PHP publisher is added to the [Packagist] archive. Therefore the publisher can be installed to a project by including it as a dependency in `Composer.json`. When installing via Composer, log4php and thrift dependencies will be automatically installed to the project.

```json
{
    "require": {
        "omindu/php-publisher": "1.0.0"
    }
}
```

### Sample Publisher Client


```PHP
$receiverURL = 'tcp://10.100.5.198:7761';
$authenticationURL = 'https://localhost:9443';
$username = 'admin';
$password = 'admin';

$verifyPeer = true;
$caFile = '/absolute/path/to/certificate.pem';


try {
    
    //Set configuration properties for the publisher
    $config = new PublisherConfiguration($verifyPeer, $caFile);
    
    //Initializing a Publisher object
    $publisher = new Publisher($receiverURL, $username, $password, $authenticationURL, $config);
    
    //JSON formatted stream definition
	$streamDefinition = "{ 'name':'sample.stream', "
			             ."'version':'1.0.0', "
			             ."'nickName':'Sample Saream Definition'," 
			             ."'description':'This is a description',"    
			             ."'metaData':[{'name':'metaField1','type':'STRING'}],"
			             ."'correlationData':[{'name':'corrField1','type':'STRING'}],"
					     ."'payloadData':[{'name':'payloadField1','type':'STRING'},"
					                       ."{'name':'payloadField2','type':'DOUBLE'},"
					                       ."{'name':'payloadField3','type':'STRING'},"
						                   ."{'name':'payloadField4','type':'INT'} ] }";	
	
	//Adding the strem definition to BAM
	$streamId = $publisher->defineStream($streamDefinition);
	
	//Searching a stream definition
	$streamId =  $publisher->findStream( 'sample.stream', '1.0.0' );
    
    //Initializing an Event object
    $event = new Event($streamId, time());
    
    //Setting up event attributes. The of each array should follow the data type and order of the stream definiiton
	$metaData = ['meta1'];
	$correlationData = ['corr1'];
	$payloadData = ['pay1',pi(),'pay2',888];
	$arbitraryDataMap = ['x'=>'arb1','y'=>'arb2'];
	
	//Adding the attributes to the Event object
	$event->setMetaData($metaData);
	$event->setCorrelationData($correlationData);
	$event->setPayloadData($payloadData);
	$event->setArbitraryDataMap($arbitraryDataMap);	

    //Publish the event to BAM
	$publisher->publish($event);
    	
}catch(Exception $e){
    //To see the exception types supported by the publisher, refer the API section
    print_r($e->getTrace());
}
```

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

#### `class PublisherConfiguration`
```PHP
function __construct($verifyPeer, $caFile)

    @param boolean $verifyPeer - @default false
    @param string $caFile - @default null
    @throws NullPointerException
    
function setVerifyPeer($verifyPeer)   
    @param boolean $verifyPeer

function getVerifyPeer()
    @return boolean $verifyPeer

function setCaFile($caFile)
    @param string $caFile
    
function getCaFile()    
    @return string $caFile
```

#### `class Event`
```PHP
function __construct($streamId, $timeStamp, $metaData , $correlationData, $payloadData , $arbitraryDataMap)

    @param string $streamId - @default null
    @param string $timeStamp - @default null
    @param string $metaData - @default null
    @param array $correlationData - @default null
    @param array $payloadData - @default null
    @param array $arbitraryDataMap - @default null
    
function getStreamId()
    @return string $streamId
    
function setStreamId($streamId)
    @param string $streamId

function getTimeStamp()
    @return long $timeStamp
    
function setTimeStamp($timeStamp)
    @param long $timeStamp
    
function getMetaData()
    @return array $metaData
    
function setMetaData($metaData)
    @param array $metaData
    
function getCorrelationData()
    @return array $correlationData
    
function setCorrelationData($correlationData)
    @param array $correlationData
    
function getPayloadData()
    @return array $payloadData
    
function setPayloadData($payloadData)
    @param array $payloadData
    
function getArbitraryDataMap()
    @return array $arbitraryDataMap
    
function setArbitraryDataMap($arbitraryDataMap)
    @param array $arbitraryDataMap
    
```

## License
[Apache License 2.0]


[WSO2 Business Activity Monitor]:http://wso2.com/products/business-activity-monitor/
[Apache Thrift]:https://thrift.apache.org/
[Apache log4php]:http://logging.apache.org/log4php/index.html
[Composer]:https://getcomposer.org/
[Packagist]:https://packagist.org/
[Data Publisher]:https://docs.wso2.com/display/BAM241/Data+Publisher 
[Stream Definitions]:http://maninda.blogspot.com/2012/10/stream-definitions.html
[Creating Custom Data Publishers to BAM/CEP]:http://wso2.com/library/articles/2012/07/creating-custom-agents-publish-events-bamcep/
[Apache License 2.0]:http://www.apache.org/licenses/LICENSE-2.0
