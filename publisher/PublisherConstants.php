<?php
namespace publisher;


define("capath", __DIR__.'/resources/wso2.pem');
define("configpath",__DIR__.'/resources/config.xml');
class PublisherConstants
{
    
    
    const DEFAULT_BAM_SECURE_PORT = 9443;

    const DEFAULT_THRIFT_RECEIVER_PORT = 7611;

    const DEFAULT_SESSION_TIMEOUT_SEC = 1700;

    const PUBLISHER_AUTHENTICATION_SERVICE_URL = '/com.test/services/test';
    
    const CAFILE_PATH = capath;
    
    const LOG4J_CONFIG_FILE_PATH = configpath;
    
    const LOGGER_NAME = 'PublisherLogger';
}