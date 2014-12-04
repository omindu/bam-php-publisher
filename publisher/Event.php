<?php


class Event {
	
	private $streamId;
	private $timeStamp;
	private $metaData;
	private $metaData;
	private $correlationData;
	private $payloadData;
	private $arbitraryDataMap;
	
	
	
	public function __construct($streamId = NULL, $timeStamp = NULL, $metaData = NULL, $correlationData = NULL, $payloadData = NULL, $arbitraryDataMap = NULL) {
	
		$this->streamId = $streamId;
		$this->timeStamp =$timeStamp;
		$this->metaData = $metaData;
		$this->correlationData = $correlationData;
		$this->payloadData = $payloadData;
		$this->arbitraryDataMap = $arbitraryDataMap;
	
	
	}
	
	
	public function getStreamId(){
		return $this->streamId;
	}
	
	public function setStreamId($streamId){
		$this->streamId = $streamId;
	}
	
	public function getTimeStamp(){
		return $this->timeStamp;
	}
	
	public function setTimeStamp($timeStamp){
		$this->timeStamp = $timeStamp;
	}
	
	public function getMetaData(){
		return $this->metaData;
	}
	
	public function setMetaData($metaData){
		$this->metaData = $metaData;
	}
	
	public function getMetaData(){
		return $this->metaData;
	}
	
	public function setMetaData($metaData){
		$this->metaData = $metaData;
	}
	
	public function getCorrelationData(){
		return $this->correlationData;
	}
	
	public function setCorrelationData($correlationData){
		$this->correlationData = $correlationData;
	}
	
	public function getPayloadData(){
		return $this->payloadData;
	}
	
	public function setPayloadData($payloadData){
		$this->payloadData = $payloadData;
	}
	
	public function getArbitraryDataMap(){
		return $this->arbitraryDataMap;
	}
	
	public function setArbitraryDataMap($arbitraryDataMap){
		$this->arbitraryDataMap = $arbitraryDataMap;
	}
	
}