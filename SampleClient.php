<?php
error_reporting(E_ALL);
include_once __DIR__ . '/vendor/autoload.php';


use org\wso2\carbon\databridge\commons\thrift\service\general\ThriftEventTransmissionServiceClient;
use publisher\Publisher;
use publisher\Event;




$receiverURL = 'tcp://192.168.1.3:761';//'tcp://10.100.5.198';
$authenticationURL = 'https://localhost:9443';//'ssl://10.100.5.198';
$username = 'admin';
$password = 'admin';

try {

    
    $publisher = new Publisher($receiverURL, $username, $password, $authenticationURL);
    echo $publisher->findStream ( "online_news_stats_1", "1.0.0" );
    
	$streamDefinition = "{ 'name':'test_stream_definition', "
			             ."'version':'5.0.0', "
			             ."'metaData':[{'name':'publisherIP','type':'STRING'}],"
					      ."'payloadData':[ {'name':'message','type':'STRING'},"
							                 ."{'name':'from','type':'STRING'} ] }";
	
    $streamId = $publisher->addStreamDefinition($streamDefinition);
    $event = new Event($streamId, time());
    	
	$metaData = ['1.2.3.4'];
	$payloadData = ['Test Message','Pub'];
	$arbitraryDataMap = ['x'=>'arb1','y'=>'arb2'];
	$event->setStreamId($streamId);
	$event->setMetaData($metaData);
	$event->setPayloadData($payloadData);
	$event->setArbitraryDataMap($arbitraryDataMap);
	
	//$publisher->publish($event);
    	
    
}catch(Exception $e){
    
    var_dump($e);
}
