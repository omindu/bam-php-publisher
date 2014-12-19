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
    public function __construct($streamId = NULL, $timeStamp = NULL, $metaData = NULL,
                                $correlationData = NULL, $payloadData = NULL, $arbitraryDataMap = NULL)
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