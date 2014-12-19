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

define("capath", __DIR__ . '/resources/wso2.pem');
define("configpath", __DIR__ . '/resources/config.xml');

class PublisherConstants
{

    const DEFAULT_BAM_SECURE_PORT = 9443;

    const DEFAULT_THRIFT_RECEIVER_PORT = 7611;

    const DEFAULT_SESSION_TIMEOUT_SEC = 1700;

    const PUBLISHER_AUTHENTICATION_SERVICE_URL = '/restauthenticator/getsessionid';

    const THRIFT_SECURE_EVENT_TRANSMISSION_SERVLET_URI = '/securedThriftReceiver';

    const CAFILE_PATH = capath;

    const LOG4J_CONFIG_FILE_PATH = configpath;

    const LOGGER_NAME = 'PublisherLogger';

    const URL_SCHEME_AND_HOST_SEPERATOR = '://';

    const URL_HOST_AND_PORT_SEPERATOR = ':';
}