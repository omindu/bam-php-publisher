<?php


error_reporting(E_ALL);

include '../publisher/Publisher.php';

$receiverURL = 'tcp://10.100.5.198:7611';
$authenticationURL = 'ssl://10.100.5.198:7711';
$username = 'admin';
$password = 'admin';

try {
$publisher = new Publisher($receiverURL, $username, $password,$authenticationURL);

echo $publisher->findStream("online_news_stats_1", "1.0.0");

}catch (Exception $e){
	//var_dump($e);
}