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
class UpsAPIShippingDigest extends UpsAPI {
	
	/**
	 * Node name for the root node
	 * 
	 * @var string
	 */
	const NODE_NAME_ROOT_NODE = 'ShipmentConfirmRequest';
	
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
	 * Shipment data
	 * 
	 * @access protected
	 * @var array
	 */
	protected $shipment = array();
	
	/**
	 * Ship from data
	 * 
	 * @access protected
	 * @var array
	 */
	protected $ship_from = array();
	
	/**
	 * Shipper data
	 * 
	 * @access protected
	 * @var array
	 */
	protected $shipper = array();
	
	/**
	 * Constructor for the Object
	 * 
	 * @access public
	 * @param array $shipment array of shipment data
	 * @param array $shipper array of shipper data
	 * @param array $ship_from array of ship from data
	 * @param array $destination array of destination data
	 */
	public function __construct($shipment, $shipper, $ship_from, $destination) {
		parent::__construct();
		// set object properties
		$this->server .='ShipConfirm';
		$this->shipment = $shipment;
		$this->shipper = $shipper;
		$this->ship_from = $ship_from;
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
		$shipconfirm_dom = new DOMDocument('1.0');
		
		
		/** create the ShipmentConfirmRequest element **/
		$shipconfirm_element = $shipconfirm_dom->appendChild(
			new DOMElement('ShipmentConfirmRequest'));
		$shipconfirm_element->setAttributeNode(new DOMAttr('xml:lang', 'en-US'));
		
		// create the child elements
		$request_element = $this->buildRequest_RequestElement(
			$shipconfirm_element, 'ShipConfirm', 'nonvalidate', $customer_context);
		
		$shipment = $shipconfirm_element->appendChild(new DOMElement('Shipment'));
		
		$this->buildRequest_Shipper($shipment);
		$this->buildRequest_Destination($shipment);
		$this->buildRequest_ShipFrom($shipment);
		$this->buildRequest_PaymentInformation($shipment);
		$shipment = $this->buildRequest_Shipment($shipment);
		
		$label_specification_element = $shipconfirm_element->appendChild(
			new DOMElement('LabelSpecification'));
			
		$label_print_method_element = $label_specification_element->appendChild(
			new DOMElement('LabelPrintMethod'));
			
		$create = $label_print_method_element->appendChild(
			new DOMElement('Code','GIF'));
				
		$ua = strip_tags($_SERVER['HTTP_USER_AGENT']);
		$ua = preg_replace('/javascript|vbscri?pt|script|applet|alert|document|write|cookie/i', '', $ua);
		
		$create = $label_specification_element->appendChild(
			new DOMElement('HTTPUserAgent',$ua));
		
		$label_image_format_element = $label_specification_element->appendChild(
			new DOMElement('LabelImageFormat'));
			
		$create = $label_image_format_element->appendChild(
			new DOMElement('Code','GIF'));
						
		unset($create);
	
		return parent::buildRequest().$shipconfirm_dom->saveXML();
	}
	
	/**
	 * Builds the destination elements
	 * 
	 * @access protected
	 * @param DOMElement $dom_element
	 * @return DOMElement
	 */
	protected function buildRequest_Destination(&$dom_element) {
		
		/** build the destination element and its children **/
		$destination = $dom_element->appendChild(new DOMElement('ShipTo'));
		$destination->appendChild(new DOMElement('CompanyName',
			$this->destination['name']));
		$destination->appendChild(new DOMElement('PhoneNumber',
			$this->destination['phone']));
		$address = $destination->appendChild(new DOMElement('Address'));
		
		
		/** build the address elements children **/
		$address->appendChild(new DOMElement('AddressLine1',
			$this->destination['street']));
		
		// check to see if there is a second steet line
		if (isset($this->destination['street2']) &&
			!empty($this->destination['street2'])) {
			$address->appendChild(new DOMElement('AddressLine2',
				$this->destination['street2']));
		} // end if there is a second street line
		
		// check to see if there is a third steet line
		if (isset($this->destination['street3']) &&
			!empty($this->destination['street3'])) {
			$address->appendChild(new DOMElement('AddressLine3',
				$this->destination['street3']));
		} // end if there is a second third line
		
		// build the rest of the address
		$address->appendChild(new DOMElement('City',
			$this->destination['city']));
		$address->appendChild(new DOMElement('StateProvinceCode',
			$this->destination['state']));
		$address->appendChild(new DOMElement('PostalCode',
			$this->destination['zip']));
		$address->appendChild(new DOMElement('CountryCode',
			$this->destination['country']));
		
		return $destination;
	} // end function buildRequest_Destination()
	
	/**
	 * Buildes the package elements
	 * 
	 * @access protected
	 * @param DOMElement $dom_element
	 * @param array $package
	 * @return DOMElement
	 * 
	 * @todo determine if the package description is needed
	 */
	protected function buildRequest_Package(&$dom_element, $package) {
		/** build the package and packaging type **/
		$package_element = $dom_element->appendChild(new DOMElement('Package'));
		$packaging_type = $package_element->appendChild(
			new DOMElement('PackagingType'));
		$packaging_type->appendChild(new DOMElement('Code',
			$package['packaging']['code']));
		$packaging_type->appendChild(new DOMElement('Description',
			$package['packaging']['description']));
		
		// TODO: determine if we need this
		if($package['description'])
		$package_element->appendChild(new DOMElement('Description',
			$package['description']));
		
		
		/** build the package weight **/
		$package_weight = $package_element->appendChild(
			new DOMElement('PackageWeight'));
		$units = $package_weight->appendChild(
			new DOMElement('UnitOfMeasurement'));
		$units->appendChild(new DOMElement('Code', $package['units']));
		$package_weight->appendChild(
			new DOMElement('Weight', $package['weight']));
		
		return $package_element;
	} // end function buildRequest_Package()
	
	/**
	 * Builds the service options node
	 * 
	 * @access protected
	 * @param DOMElement $dom_element
	 * @return boolean|DOMElement
	 */
	protected function buildRequest_ServiceOptions(&$dom_element) {
		// build our elements
		$service_options = $dom_element->appendChild(
			new DOMElement('ShipmentServiceOptions'));
		$on_call_air = $service_options->appendChild(
			new DOMElement('OnCallAir'));
		$schedule = $on_call_air->appendChild(new DOMElement('Schedule'));
		
		// check to see if this is a satruday pickup
		if (isset($this->shipment['saturday']['pickup']) &&
			$this->shipment['saturday']['pickup'] !== false) {
			$service_options->appendChild(new DOMElement('SaturdayPickup'));
		} // end if this is a saturday pickup
		
		// check to see if this is a saturday delivery
		if (isset($this->shipment['saturday']['delivery']) &&
			$this->shipment['saturday']['delivery'] !== false) {
			$service_options->appendChild(new DOMElement('SaturdayDelivery'));
		} // end if this is a saturday delivery
		
		// check to see if we have a pickup day
		if (isset($this->shipment['pickup_day'])) {
			$schedule->appendChild(new DOMElement('PickupDay',
				$this->shipment['pickup_day']));
		} // end if we have a pickup day
		
		// check to see if we have a scheduling method
		if (isset($this->shipment['scheduling_method'])) {
			$schedule->appendChild(new DOMElement('Method',
				$this->shipment['scheduling_method']));
		} // end if we have a scheduling method
		
		// check to see if we have on call air options
		if (!$schedule->hasChildNodes()) {
			$service_options->removeChild($on_call_air);
		} // end if we have on call air options
		
		// check to see if we have service options
		if (!$service_options->hasChildNodes()) {
			$dom_element->removeChild($service_options);
			return false;
		} // end if we do not have service options
		
		return $service_options;
	} // end function buildRequest_ServiceOptions()
	
	/**
	 * Builds the ship from elements
	 * 
	 * @access protected
	 * @param DOMElement $dom_element
	 * @return DOMElement
	 */
	protected function buildRequest_ShipFrom(&$dom_element) {
		/** build the destination element and its children **/
		$ship_from = $dom_element->appendChild(new DOMElement('ShipFrom'));
		$ship_from->appendChild(new DOMElement('CompanyName',
			$this->ship_from['name']));
		$ship_from->appendChild(new DOMElement('PhoneNumber',
			$this->ship_from['phone']));
		$address = $ship_from->appendChild(new DOMElement('Address'));
		
		
		/** build the address elements children **/
		$address->appendChild(new DOMElement('AddressLine1',
			$this->ship_from['street']));
		
		// check to see if there is a second steet line
		if (isset($this->ship_from['street2']) &&
			!empty($this->ship_from['street2'])) {
			$address->appendChild(new DOMElement('AddressLine2',
				$this->ship_from['street2']));
		} // end if there is a second street line
		
		// check to see if there is a third steet line
		if (isset($this->ship_from['street3']) &&
			!empty($this->ship_from['street3'])) {
			$address->appendChild(new DOMElement('AddressLine3',
				$this->ship_from['street3']));
		} // end if there is a second third line
		
		// build the rest of the address
		$address->appendChild(new DOMElement('City',
			$this->ship_from['city']));
		$address->appendChild(new DOMElement('StateProvinceCode',
			$this->ship_from['state']));
		$address->appendChild(new DOMElement('PostalCode',
			$this->ship_from['zip']));
		$address->appendChild(new DOMElement('CountryCode',
			$this->ship_from['country']));
		
		return $ship_from;
	} // end function buildRequest_ShipFrom()
	
	protected function buildRequest_PaymentInformation(&$shipment) {
		
		$payment_information = $shipment->appendChild(new DOMElement('PaymentInformation'));
		$prepaid = $payment_information->appendChild(new DOMElement('Prepaid'));
		$billshipper = $prepaid->appendChild(new DOMElement('BillShipper'));

		if($this->Isotope->Config->UpsMode=='test')
		{
			$create = $billshipper->appendChild(new DOMElement('AccountNumber',$this->accountNumber));
		}
		else
		{
			$creditcard = $billshipper->appendChild(new DOMElement('CreditCard'));
			$create = $creditcard->appendChild(new DOMElement('Type','06'));
			$create = $creditcard->appendChild(new DOMElement('Number',4111111111111111));
			$create = $creditcard->appendChild(new DOMElement('ExpirationDate',122015));
		}
		
		return $payment_information;	
	}
	
	/**
	 * Builds the shipment elements
	 * 
	 * @access protected
	 * @param DOMElement $dom_element
	 * @return DOMElement
	 */
	protected function buildRequest_Shipment(&$shipment) {
		
		/** build the shipment node **/
		$service = $shipment->appendChild(new DOMElement('Service'));
		$service->appendChild(new DOMElement('Code',
			$this->shipment['service']));
		
		// iterate over the pacakges to create the package element
		foreach ($this->shipment['packages'] as $package) {
			$this->buildRequest_Package($shipment, $package);
		} // end for each package
		
		$this->buildRequest_ServiceOptions($shipment);
		
		return $shipment;
	} // end function buildRequest_Shipment()
	
	/**
	 * Builds the shipper elements
	 * 
	 * @access protected
	 * @param DOMElement $dom_element
	 * @return DOMElement
	 */
	protected function buildRequest_Shipper(&$dom_element) {
		/** build the destination element and its children **/
		$shipper = $dom_element->appendChild(new DOMElement('Shipper'));
		$shipper->appendChild(new DOMElement('Name',
			$this->shipper['name']));
		$shipper->appendChild(new DOMElement('PhoneNumber',
			$this->shipper['phone']));
		
		// check to see if we have a shipper number
		if (isset($this->shipper['number']) &&
			!empty($this->shipper['number'])) {
			$shipper->appendChild(new DOMElement('ShipperNumber',
				$this->shipper['number']));
		} // end if we have a shipper number
		
		$address = $shipper->appendChild(new DOMElement('Address'));
		
		
		/** build the address elements children **/
		$address->appendChild(new DOMElement('AddressLine1',
			$this->shipper['street']));
		
		// check to see if there is a second steet line
		if (isset($this->shipper['street2']) &&
			!empty($this->shipper['street2'])) {
			$address->appendChild(new DOMElement('AddressLine2',
				$this->shipper['street2']));
		} // end if there is a second street line
		
		// check to see if there is a third steet line
		if (isset($this->shipper['street3']) &&
			!empty($this->shipper['street3'])) {
			$address->appendChild(new DOMElement('AddressLine3',
				$this->shipper['street3']));
		} // end if there is a second third line
		
		// build the rest of the address
		$address->appendChild(new DOMElement('City',
			$this->shipper['city']));
		$address->appendChild(new DOMElement('StateProvinceCode',
			$this->shipper['state']));
		$address->appendChild(new DOMElement('PostalCode',
			$this->shipper['zip']));
		$address->appendChild(new DOMElement('CountryCode',
			$this->shipper['country']));
		
		return $shipper;
	} // end function buildRequest_Shipper()
	
	
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