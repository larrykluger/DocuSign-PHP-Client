<?php
/*
 * Copyright 2013 DocuSign Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

require_once 'DocuSign_Service.php';
require_once 'DocuSign_Resource.php';

class DocuSign_ConnectService extends DocuSign_Service {

	public $connect;

	/**
	* Constructs the internal representation of the DocuSign Connect service.
	*
	* @param DocuSign_Client $client
	*/
	public function __construct(DocuSign_Client $client) {
		parent::__construct($client);
		$this->connect = new DocuSign_ConnectResource($this);
	}
}

class DocuSign_ConnectResource extends DocuSign_Resource {

	public function __construct(DocuSign_Service $service) {
		parent::__construct($service);
	}
	
	private function setURL($tail) {
		$this->url = 'https://' . $this->client->getEnvironment() . '.docusign.net/restapi/' . $this->client->getVersion() . $tail;
	}

	private function rmNulls($data) {
		# remove nulls and empty arrays
		$cleaned = array();
		foreach ($data as $key => $value) {
			if ((!is_array($value) && $value !== null) || (is_array($value) &&  count($value) !== 0)) {
				# It's good
				$cleaned[$key] = $value;
			}
		}
		return $cleaned;
	}

	private function setBooleans($data, $elements) {
		foreach ($data as $key => &$value) {
			if (in_array ($key, $elements)) {
				$value = (bool)$value;
			}
		}
		return $data;
	}
	
	private function setCSVs($data, $elements) {
		# Set specific elements to be comma separated text instead of arrays
		foreach ($data as $key => &$value) {
			if (in_array ($key, $elements)) {
				# $value is currently an array
				$value = implode(",", $value);
			}
		}
		return $data;
	}

	public function getConnectConfiguration($accountId) {
		$this->setURL('/accounts/' . $accountId . '/connect');
		return $this->curl->makeRequest($this->url, 'GET', $this->client->getHeaders(), array(), null);	
	}

	public function getConnectConfigurationByID($accountId, $connectID) {
		$this->setURL('/accounts/' . $accountId . '/connect/' . $connectID);
		return $this->curl->makeRequest($this->url, 'GET', $this->client->getHeaders(), array(), null);	
	}

	public function createConnectConfiguration(	
		$accountId, # string	Account Id
		$params = array()){
		# params is an associative array holding the parameters. Most are optional.
		# Valid keys:
		# urlToPublishTo, # Required. string	Client's incoming webhook url
		# allUsers,	# boolean	Track events initiated by all users.
		# allowEnvelopePublish, # boolean	Enables users to publish processed events.
		# enableLog, # boolean	Enables logging on prcoessed events. Log only maintains the last 100 events.
		# envelopeEvents, # array list of 'Envelope' related events to track. Events: Sent, Delivered, Signed, Completed, Declined, Voided
		# includeDocuments, # boolean	Include envelope documents
		# includeSenderAccountasCustomField, # boolean	Include sender account as Custom Field.
		# includeTimeZoneInformation, # boolean	Include time zone information.
		# name, # string	name of the connection
		# recipientEvents, # array list of 'Recipient' related events to track. Events: Sent, AutoResponsed(Delivery Failed), Delivered, Completed, Declined, AuthenticationFailure
		# requiresAcknowledgement, # boolean	true or false
		# signMessagewithX509Certificate,	# boolean	Signs message with an X509 certificate.
		# soapNamespace, # string	Soap method namespace. Required if useSoapInterface is true.
		# useSoapInterface, # boolean	Set to true if the urlToPublishTo is a SOAP endpoint
		# userIds # array list of user Id's. Required if allUsers is false

		$this->setURL('/accounts/' . $accountId . '/connect');

		$data = $params;
		$data = $this->setBooleans($data, array(
			'allUsers',
			'allowEnvelopePublish', 
			'enableLog',
			'includeDocuments', 
			'includeSenderAccountasCustomField', 
			'includeTimeZoneInformation', 
			'requiresAcknowledgement',
			'signMessagewithX509Certificate',
			'useSoapInterface'
			));
		$data = $this->setCSVs($data, array(
			'envelopeEvents',
			'recipientEvents',
			'userIds'
			));

		return $this->curl->makeRequest($this->url, 'POST', $this->client->getHeaders(), array(), json_encode($data));
	}
	
	public function updateConnectConfiguration(	
		$accountId, # string	Account Id
		$connectId, # string	Connection Id
		$params = array()){
		# params is an associative array holding the parameters. All are optional.
		# Valid keys:
		# urlToPublishTo, # string	Client's incoming webhook url
		# allUsers,	# boolean	Track events initiated by all users.
		# allowEnvelopePublish, # boolean	Enables users to publish processed events.
		# enableLog, # boolean	Enables logging on prcoessed events. Log only maintains the last 100 events.
		# envelopeEvents, # array list of 'Envelope' related events to track. Events: Sent, Delivered, Signed, Completed, Declined, Voided
		# includeDocuments, # boolean	Include envelope documents
		# includeSenderAccountasCustomField, # boolean	Include sender account as Custom Field.
		# includeTimeZoneInformation, # boolean	Include time zone information.
		# name, # string	name of the connection
		# recipientEvents, # array list of 'Recipient' related events to track. Events: Sent, AutoResponsed(Delivery Failed), Delivered, Completed, Declined, AuthenticationFailure
		# requiresAcknowledgement, # boolean	true or false
		# signMessagewithX509Certificate,	# boolean	Signs message with an X509 certificate.
		# soapNamespace, # string	Soap method namespace. Required if useSoapInterface is true.
		# useSoapInterface, # boolean	Set to true if the urlToPublishTo is a SOAP endpoint
		# userIds # array list of user Id's. Required if allUsers is false

		$this->setURL('/accounts/' . $accountId . '/connect');

		$data = $params;
		$data['connectId'] = $connectId;
		$data = $this->setBooleans($data, array(
			'allUsers',
			'allowEnvelopePublish', 
			'enableLog',
			'includeDocuments', 
			'includeSenderAccountasCustomField', 
			'includeTimeZoneInformation', 
			'requiresAcknowledgement',
			'signMessagewithX509Certificate',
			'useSoapInterface'
			));
		$data = $this->setCSVs($data, array(
			'envelopeEvents',
			'recipientEvents',
			'userIds'
			));

		return $this->curl->makeRequest($this->url, 'PUT', $this->client->getHeaders(), array(), json_encode($data));
	}

	public function deleteConnectConfiguration(	
		$accountId, # string	Account Id
		$connectId	# string	Connection Id
		){
		$this->setURL('/accounts/' . $accountId . '/connect/' . $connectId);
		return $this->curl->makeRequest($this->url, 'DELETE', $this->client->getHeaders(), array(), null);
	}
}

?>
