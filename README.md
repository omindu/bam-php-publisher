# PHP Publisher for BAM

The PHP data publisher allows PHP clients to publish data to [WSO2 Business Activity Monitor]. The data can be published to predefined or custom set of data fields. The functionality of the PHP data publisher is analogous to the functionality of a custom java data publisher

The publisher uses [Apache Thrift] to publish data sent by the PHP client to the BAM server. The publisher exposes the client to operations such as defining data streams, searching stream definitions and publishing events.

## Prerequisites

- PHP 5.5.x with curl
- WSO2 BAM 2.4.1

## Dependancies

- [Apache log4php] v2.3.0
- [Apache Thrift] v0.9

## Getting Started

### Installing the Publisher




[WSO2 Business Activity Monitor]:http://wso2.com/products/business-activity-monitor/
[Apache Thrift]:https://thrift.apache.org/
[Apache log4php]:http://logging.apache.org/log4php/index.html
