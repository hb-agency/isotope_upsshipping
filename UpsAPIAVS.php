<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  Intelligent Spark 2010
 * @author     Fred Bliss <http://www.intelligentspark.com>
 * @package    UPSAPIShipmentConfirm
 * @license    LGPL
 * @filesource
 */
class UpsAPIAVS extends UpsAPI {
	
	/**
	 * Node name for the root node
	 * 
	 * @var string
	 */
	const NODE_NAME_ROOT_NODE = 'AddressValidationRequest';
	
	/**
	 * Destination (ship to) data
	 * 
	 * Should be in the format:
	 * $destination = array(
	 * 	'name' => '',
	 * 	'attn' => '',
	 * 	'phone' => '1234567890',
	 * 	'address' => array(
	 * 		'street1' => '',
	 * 		'street2' => '',
	 * 		'city' => '',
	 * 		'state' => '**',
	 * 		'zip' => 12345,
	 * 		'country' => '',
	 * 	),
	 * );
	 * 
	 * @access protected
	 * @var array
	 */
	protected $destination = array();
	

	
	/**
	 * Constructor for the Object
	 * 
	 * @access public
	 * @param array $shipment array of shipment data
	 * @param array $shipper array of shipper data
	 * @param array $ship_from array of ship from data
	 * @param array $destination array of destination data
	 */
	public function __construct($destination) {
		parent::__construct();
		// set object properties
		$this->server .='AV';
		$this->destination = $destination;
	} // end function __construct()
	
			/**
	 * Builds the XML used to make the request
	 * 
	 * If $customer_context is an array it should be in the format:
	 * $customer_context = array('Element' => 'Value');
	 * 
	 * @access public
	 * @param array|string $cutomer_context customer data
	 * @return string $return_value request XML
	 */
	public function buildRequest($customer_context = null) {
		/** create DOMDocument objects **/
		$addressconfirm_dom = new DOMDocument('1.0');
		
		
		/** create the ShipmentConfirmRequest element **/
		$addressconfirm_element = $addressconfirm_dom->appendChild(
			new DOMElement('AddressValidationRequest'));
		$addressconfirm_element->setAttributeNode(new DOMAttr('xml:lang', 'en-US'));
		
		// create the child elements
		$request_element = $this->buildRequest_RequestElement(
			$addressconfirm_element, 'AV', null, $customer_context);
		
		$address = $addressconfirm_element->appendChild(new DOMElement('Address'));
		$address = $this->buildRequest_Address($address);

		return parent::buildRequest().$addressconfirm_dom->saveXML();
	}
	
	/**
	 * Builds the destination elements
	 * 
	 * @access protected
	 * @param DOMElement $dom_element
	 * @return DOMElement
	 */
	protected function buildRequest_Address(&$dom_element) {
		
		/** build the address elements children **/
		$dom_element->appendChild(new DOMElement('City',
			$this->destination['city']));
		$dom_element->appendChild(new DOMElement('StateProvinceCode',
			$this->destination['state']));
		$dom_element->appendChild(new DOMElement('PostalCode',
			$this->destination['zip']));
		$dom_element->appendChild(new DOMElement('CountryCode',
			$this->destination['country']));
		
		return $dom_element;
	} // end function buildRequest_Address()

	
	
	/**
	 * Returns the name of the servies response root node
	 * 
	 * @access protected
	 * @return string
	 * 
	 * @todo remove after phps self scope has been fixed
	 */
	protected function getRootNodeName() {
		return self::NODE_NAME_ROOT_NODE;
	} // end function getRootNodeName()



	// Step 1: Generate shipping digest
/*<ShipmentConfirmRequest xml:lang="en-US">
<Request>
<TransactionReference>
<CustomerContext>Customer Comment</CustomerContext>
<XpciVersion/>
</TransactionReference>
<RequestAction>ShipConfirm</RequestAction>
<RequestOption>validate</RequestOption>
</Request>
<LabelSpecification>
<LabelPrintMethod>
<Code>GIF</Code>
<Description>gif file</Description>
</LabelPrintMethod>
<HTTPUserAgent>Mozilla/4.5</HTTPUserAgent>
<LabelImageFormat>
<Code>GIF</Code>
<Description>gif</Description>
</LabelImageFormat>
</LabelSpecification>
<Shipment>
<RateInformation>
<NegotiatedRatesIndicator/>
</RateInformation>
<Description/>
<Shipper>
<Name>TEST</Name>
<PhoneNumber>111-111-1111</PhoneNumber>
<ShipperNumber>SHIPPER NUMBER</ShipperNumber>
<TaxIdentificationNumber>1234567890</TaxIdentificationNumber>
<Address>
<AddressLine1>AIRWAY ROAD SUITE 7</AddressLine1>
<City>SAN DIEGO</City>
<StateProvinceCode>CA</StateProvinceCode>
<PostalCode>92154</PostalCode>
<PostcodeExtendedLow></PostcodeExtendedLow>
<CountryCode>US</CountryCode>
</Address>
</Shipper>
<ShipTo>
<CompanyName>Yats</CompanyName>
<AttentionName>Yats</AttentionName>
<PhoneNumber>123.456.7890</PhoneNumber>
<Address>
<AddressLine1>AIRWAY ROAD SUITE 7</AddressLine1>
<City>SAN DIEGO</City>
<StateProvinceCode>CA</StateProvinceCode>
<PostalCode>92154</PostalCode>
<CountryCode>US</CountryCode>
</Address>
</ShipTo>
<ShipFrom>
<CompanyName>Ship From Company Name</CompanyName>
<AttentionName>Ship From Attn Name</AttentionName>
<PhoneNumber>1234567890</PhoneNumber>
<TaxIdentificationNumber>1234567877</TaxIdentificationNumber>
<Address>
<AddressLine1>AIRWAY ROAD SUITE 7</AddressLine1>
<City>SAN DIEGO</City>
<StateProvinceCode>CA</StateProvinceCode>
<PostalCode>92154</PostalCode>
<CountryCode>US</CountryCode>
</Address>
</ShipFrom>
<PaymentInformation>
<Prepaid>
<BillShipper>
<AccountNumber>SHIPPER NUMBER</AccountNumber>
</BillShipper>
</Prepaid>
</PaymentInformation>
<Service>
<Code>02</Code>
<Description>2nd Day Air</Description>
</Service>
<Package>
<PackagingType>
<Code>02</Code>
<Description>Customer Supplied</Description>
</PackagingType>
<Description>Package Description</Description>
<ReferenceNumber>
<Code>00</Code>
<Value>Package</Value>
</ReferenceNumber>
<PackageWeight>
<UnitOfMeasurement/>
<Weight>60.0</Weight>
</PackageWeight>
<LargePackageIndicator/>
<AdditionalHandling>0</AdditionalHandling>
</Package>
</Shipment>
</ShipmentConfirmRequest>*/
}