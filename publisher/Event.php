<?php
namespace publisher;

class Event
{

    private $streamId;

    private $timeStamp;

    private $metaData;

    private $correlationData;

    private $payloadData;

    private $arbitraryDataMap;

    /**
     *
     * @param string $streamId            
     * @param long $timeStamp
     *            timestamp in miliseconds
     * @param array $metaData
     *            meta data as an array. ex: $metaData=[val1,val2];
     * @param array $correlationData
     *            correlation data as an array. ex: $correlationData=[val1,val2];
     * @param array $payloadData
     *            payload data as an array. ex: $payloadData=[val1,val2];
     * @param array $arbitraryDataMap
     *            array with string key value pairs. ex: $arbitraryDataMap=['k1'=>'val1', 'k2'=>'val2']
     */
    public function __construct($streamId = NULL, $timeStamp = NULL, $metaData = NULL, $correlationData = NULL, $payloadData = NULL, $arbitraryDataMap = NULL)
    {
        $this->streamId = $streamId;
        $this->timeStamp = $timeStamp;
        $this->metaData = $metaData;
        $this->correlationData = $correlationData;
        $this->payloadData = $payloadData;
        $this->arbitraryDataMap = $arbitraryDataMap;
    }

    /**
     *
     * @return string
     */
    public function getStreamId()
    {
        return $this->streamId;
    }

    /**
     *
     * @param string $streamId            
     */
    public function setStreamId($streamId)
    {
        $this->streamId = $streamId;
    }

    /**
     * timestamp in miliseconds
     * 
     * @return long
     */
    public function getTimeStamp()
    {
        return $this->timeStamp;
    }

    /**
     * timestamp in miliseconds
     * 
     * @param long $timeStamp            
     */
    public function setTimeStamp($timeStamp)
    {
        $this->timeStamp = $timeStamp;
    }

    /**
     * $metaData meta data as an array.
     * ex: $metaData=[val1,val2];
     *
     * @return array
     */
    public function getMetaData()
    {
        return $this->metaData;
    }

    /**
     * $metaData meta data as an array.
     * ex: $metaData=[val1,val2];
     *
     * @param array $metaData            
     */
    public function setMetaData($metaData)
    {
        $this->metaData = $metaData;
    }

    /**
     * $correlationData correlation data as an array.
     * ex: $correlationData=[val1,val2];
     *
     * @return array
     */
    public function getCorrelationData()
    {
        return $this->correlationData;
    }

    /**
     * $correlationData correlation data as an array.
     * ex: $correlationData=[val1,val2];
     *
     * @param array $correlationData            
     */
    public function setCorrelationData($correlationData)
    {
        $this->correlationData = $correlationData;
    }

    /**
     * $payloadData payload data as an array.
     * ex: $payloadData=[val1,val2];
     *
     * @return array
     */
    public function getPayloadData()
    {
        return $this->payloadData;
    }

    /**
     * $payloadData payload data as an array.
     * ex: $payloadData=[val1,val2];
     *
     * @param array $payloadData            
     */
    public function setPayloadData($payloadData)
    {
        $this->payloadData = $payloadData;
    }

    /**
     * $arbitraryDataMap array with string key value pairs.
     * ex: $arbitraryDataMap=['k1'=>'val1', 'k2'=>'val2']
     *
     * @return array
     */
    public function getArbitraryDataMap()
    {
        return $this->arbitraryDataMap;
    }

    /**
     * $arbitraryDataMap array with string key value pairs.
     * ex: $arbitraryDataMap=['k1'=>'val1', 'k2'=>'val2']
     *
     * @param array $arbitraryDataMap            
     */
    public function setArbitraryDataMap($arbitraryDataMap)
    {
        $this->arbitraryDataMap = $arbitraryDataMap;
    }
}