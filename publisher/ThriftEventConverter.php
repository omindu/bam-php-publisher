<?php
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
        // $thiftEventBundle = new ThriftEventBundle (); // /<-remove
        
        // $event = new Event (); // <-remove
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
        // $thiftEventBundle = new ThriftEventBundle (); // /<-remove
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
        // $thiftEventBundle = new ThriftEventBundle ( $thiftEventBundle, $attributes ); // /<-remove
        if ($arbitraryDataMap) {
            $thiftEventBundle->arbitraryDataMapMap = array(
                $arbitraryDataMap
            );
        }
        
        return $thiftEventBundle;
    }
}