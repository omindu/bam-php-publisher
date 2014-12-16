<?php
error_reporting(E_ALL);
include_once __DIR__ . '/vendor/autoload.php';


use org\wso2\carbon\databridge\commons\thrift\service\general\ThriftEventTransmissionServiceClient;
use publisher\Publisher;
use publisher\Event;
use publisher\PublisherConfiguration;
use publisher\PublisherConstants;




$receiverURL = 'tcp://10.100.5.198:7611';;
$authenticationURL = 'https://localhost:9443';
$username = 'admin';
$password = 'admin';

try {

    $configuration = new PublisherConfiguration(true, PublisherConstants::CAFILE_PATH);
    $publisher = new Publisher($receiverURL, $username, $password, $authenticationURL, $configuration);
    
    echo $publisher->findStream ( "online_news_stats", "1.0.0" );
    
	$streamDefinition = "{ 'name':'test_stream_definition', "
			             ."'version':'2.0.3', "
			             ."'metaData':[{'name':'publisherIP','type':'STRING'}],"
					     ."'payloadData':[ {'name':'message','type':'STRING'},"
							             ."{'name':'from','type':'STRING'} ] }";
	
    $streamId = $publisher->addStreamDefinition($streamDefinition);
    $event = new Event($streamId, time());
    	
	$metaData = ['1.2.3.4'];
	$payloadData = ['Test Message','Pub'];
	$arbitraryDataMap = ['x'=>'arb1','y'=>'arb2'];
	$event->setMetaData($metaData);
	$event->setPayloadData($payloadData);
	$event->setArbitraryDataMap($arbitraryDataMap);	

	$publisher->publish($event);
    	
    
}catch(Exception $e){
    echo $e->getMessage();
}
