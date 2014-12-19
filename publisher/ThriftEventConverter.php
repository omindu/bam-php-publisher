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

use org\wso2\carbon\databridge\commons\thrift\data\ThriftEventBundle;
use publisher\UnknownEventAttributeException;

class ThriftEventConverter
{

    /**
     *
     * Converts an Event object to a ThriftEventBundle object
     *
     * @param Event $event            
     * @param string $sessionId            
     * @param \org\wso2\carbon\databridge\commons\thrift\data\ThriftEventBundle $eventBundle            
     * @return \org\wso2\carbon\databridge\commons\thrift\data\ThriftEventBundle
     * @throws UnknownEventAttributeException
     */
    public static function covertToThriftBundle($event, $sessionId, $thiftEventBundle = NULL)
    {
        
        if (! $thiftEventBundle) {
            $thiftEventBundle = new ThriftEventBundle();
            $thiftEventBundle->sessionId = $sessionId;
            $thiftEventBundle->eventNum = 0;
        }
        
        $thiftEventBundle->eventNum = $thiftEventBundle->eventNum + 1;
        $thiftEventBundle->stringAttributeList[] = $event->getStreamId();
        $thiftEventBundle->longAttributeList[] = $event->getTimeStamp();
        $thiftEventBundle = ThriftEventConverter::assignAttribute($thiftEventBundle, $event->getMetaData());
        $thiftEventBundle = ThriftEventConverter::assignAttribute($thiftEventBundle, $event->getCorrelationData());
        $thiftEventBundle = ThriftEventConverter::assignAttribute($thiftEventBundle, $event->getPayloadData());
        $thiftEventBundle = ThriftEventConverter::assignMap($thiftEventBundle, $event->getArbitraryDataMap());
        
        return $thiftEventBundle;
    }

    /**
     * Adds attributes to a ThriftEventBundle object
     *
     * @param \org\wso2\carbon\databridge\commons\thrift\data\ThriftEventBundle $thiftEventBundle            
     * @param array $attributes            
     * @return \org\wso2\carbon\databridge\commons\thrift\data\ThriftEventBundle
     * @throws UnknownEventAttributeException::
     */
    private static function assignAttribute($thiftEventBundle, $attributes)
    {
        
        $log = \Logger::getLogger ( PublisherConstants::LOGGER_NAME );
        if ($attributes) {
            foreach ($attributes as $value) {
                
                if (is_string($value)) {
                    $thiftEventBundle->stringAttributeList[] = $value;
                } elseif (is_int($value)) {
                    $thiftEventBundle->intAttributeList[] = $value;
                } elseif (is_long($value)) {
                    $thiftEventBundle->longAttributeList[] = $value;
                } elseif (is_float($value)) {
                    $thiftEventBundle->doubleAttributeList[] = $value;
                } elseif (is_double($value)) {
                    $thiftEventBundle->doubleAttributeList[] = $value;
                } elseif (is_bool($value)) {
                    $thiftEventBundle->boolAttributeList[] = $value;
                } elseif (is_null($value)) {
                    $thiftEventBundle->stringAttributeList[] = '_null';
                } else {
                    $error = 'Unknown attribute type ' . $value;
                    $log->error ( $error);
                    throw new UnknownEventAttributeException($error);
                }
            }
        }
        
        return $thiftEventBundle;
    }

    /**
     * Adds an arbitary data map to a ThriftEventBundle object
     *
     * @param \org\wso2\carbon\databridge\commons\thrift\data\ThriftEventBundle $thiftEventBundle            
     * @param array $arbitraryDataMap            
     * @return \org\wso2\carbon\databridge\commons\thrift\data\ThriftEventBundle
     */
    private static function assignMap($thiftEventBundle, $arbitraryDataMap)
    {
        
        if ($arbitraryDataMap) {
            $thiftEventBundle->arbitraryDataMapMap = array(
                $arbitraryDataMap
            );
        }
        
        return $thiftEventBundle;
    }
}