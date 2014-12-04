<?php
use org\wso2\carbon\databridge\commons\thrift\data\ThriftEventBundle;
class ThriftEventConverter {
	
	/**
	 * 
	 * Converts an Event object to a ThriftEventBundle object
	 * 
	 * @param Event $event
	 * @param string $sessionId
	 * @param \org\wso2\carbon\databridge\commons\thrift\data\ThriftEventBundle $eventBundle
	 * @return \org\wso2\carbon\databridge\commons\thrift\data\ThriftEventBundle
	 */
	public static function covertToThriftBundle($event, $sessionId, $eventBundle = NULL) {
		$thiftEventBundle = new ThriftEventBundle (); // /<-remove
		
		$event = new Event (); // <-remove
		
		if (! $thiftEventBundle) {
			$thiftEventBundle = new ThriftEventBundle ();
			$thiftEventBundle->sessionId = $sessionId;
			$thiftEventBundle->eventNum = 0;
		} else {
			
			$thiftEventBundle->eventNum = $thiftEventBundle->eventNum + 1;
			$thiftEventBundle->stringAttributeList [] = $event->getStreamId ();
			$thiftEventBundle = ThriftEventConverter::assignAttribute ( $thiftEventBundle, $event->getMetaData () );
			$thiftEventBundle = ThriftEventConverter::assignAttribute ( $thiftEventBundle, $event->getCorrelationData () );
			$thiftEventBundle = ThriftEventConverter::assignAttribute ( $thiftEventBundle, $event->getPayloadData () );
			$thiftEventBundle = ThriftEventConverter::assignMap ( $thiftEventBundle, $event->getArbitraryDataMap () );
		}
		
		return $thiftEventBundle;
	}
	
	/**
	 * Adds attributes to a ThriftEventBundle object
	 * 
	 * @param \org\wso2\carbon\databridge\commons\thrift\data\ThriftEventBundle $thiftEventBundle
	 * @param array $attributes
	 * @return \org\wso2\carbon\databridge\commons\thrift\data\ThriftEventBundle
	 */
	private static function assignAttribute($thiftEventBundle, $attributes) {
		$thiftEventBundle = new ThriftEventBundle (); // /<-remove
		$log = Logger::getLogger ( 'PublisherLogger' );
		if (! $attributes) {
			foreach ( $attributes as $value ) {
				
				if (is_string ( $value )) {
					$thiftEventBundle->stringAttributeList [] = $value;
				} else if (is_int ( $value )) {
					$thiftEventBundle->intAttributeList [] = $value;
				} else if (is_long ( $value )) {
					$thiftEventBundle->longAttributeList [] = $value;
				} else if (is_float ( $value )) {
					$thiftEventBundle->doubleAttributeList [] = $value;
				} else if (is_double ( $value )) {
					$thiftEventBundle->doubleAttributeList [] = $value;
				} else if (is_bool ( $value )) {
					$thiftEventBundle->boolAttributeList [] = $value;
				} else if (is_null ( $value )) {
					$thiftEventBundle->stringAttributeList [] = '_null';
				} else {
					$log->error ( 'Unknown attribute type ' . $value );
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
	private static function assignMap($thiftEventBundle, $arbitraryDataMap) {
		$thiftEventBundle = new ThriftEventBundle ( $thiftEventBundle, $attributes ); // /<-remove
		
		if ($arbitraryDataMap) {
			$thiftEventBundle->arbitraryDataMapMap = array (
					$thiftEventBundle->eventNum,
					$arbitraryDataMap 
			);
		}
		
		return $thiftEventBundle;
	}
}