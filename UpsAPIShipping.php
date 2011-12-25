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
 * @package    UPSAPIShipmentAccept
 * @license    LGPL
 * @filesource
 */
class UpsAPIShipping extends UpsAPI {
	
	/**
	 * Node name for the root node
	 * 
	 * @var string
	 */
	const NODE_NAME_ROOT_NODE = 'ShipmentAcceptRequest';
	
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
	 * @param array $desination array of destination data
	 */
	public function __construct($shipment, $shipper, $ship_from, $destination) {
		
		parent::__construct();
		$this->import('Isotope');
		// set object properties
		$this->server .= "ShipAccept";
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
		$shipaccept_dom = new DOMDocument('1.0');
		
		
		/** create the ShipmentAcceptRequest element **/
		$shipaccept_element = $shipaccept_dom->appendChild(
			new DOMElement('ShipmentAcceptRequest'));
		$shipaccept_element->setAttributeNode(new DOMAttr('xml:lang', 'en-US'));
		
		// create the child elements
		$request_element = $this->buildRequest_RequestElement(
			$shipaccept_element, 'ShipAccept', 1, $customer_context);
			
		//echo var_dump(array($this->shipment, $this->shipper, $this->ship_from, $this->destination));

		$objUPSShippingDigest = new UpsAPIShippingDigest($this->shipment, $this->shipper, $this->ship_from, $this->destination);
		
		$xmlShipDigest = $objUPSShippingDigest->buildRequest();
	
		$arrResponse = $objUPSShippingDigest->sendRequest($xmlShipDigest);
		
		if((int)$arrResponse['ShipmentConfirmResponse']['Response']['ResponseStatusCode']==1)
		{
			//tracking - save to order
			$create = $shipaccept_element->appendChild(
			new DOMElement('ShipmentDigest',$arrResponse['ShipmentConfirmResponse']['ShipmentDigest']));
		}
		else
		{
			$this->log(sprintf('Error in shipping digest: %s',$arrResponse['ShipmentConfirmResponse']['Response']['ResponseStatusDescription']), 'ShippingUPS backendInterface()', TL_ERROR);		
		}
		
		return parent::buildRequest().$shipaccept_dom->saveXML();
	} // end function buildRequest()

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





 // SHIP ACCEPT REQUEST
/*$xmlRequest1='<?xml version="1.0″ encoding="ISO-8859-1″?>
<AccessRequest>
<AccessLicenseNumber>ACCESS LICENCE NUMBER</AccessLicenseNumber>
<UserId>UPS USERNAME</UserId>
<Password>UPS PASSWORD</Password>
</AccessRequest>
<?xml version="1.0″ encoding="ISO-8859-1″?>
<ShipmentAcceptRequest>
<Request>
<TransactionReference>
<CustomerContext>Customer Comment</CustomerContext>
</TransactionReference>
<RequestAction>ShipAccept</RequestAction>
<RequestOption>1</RequestOption>
</Request>
<ShipmentDigest>SHIPMENT DIGEST</ShipmentDigest>
</ShipmentAcceptRequest>
';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://wwwcie.ups.com/ups.app/xml/ShipAccept");
// uncomment the next line if you get curl error 60: error setting certificate verify locations
//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
// uncommenting the next line is most likely not necessary in case of error 60
// curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlRequest1);
curl_setopt($ch, CURLOPT_TIMEOUT, 3600);

//if ($this->logfile) {
//   error_log("UPS REQUEST: " . $xmlRequest . "\n", 3, $this->logfile);
//}
$xmlResponse = curl_exec ($ch); // SHIP ACCEPT RESPONSE
//echo curl_errno($ch);

$xml = $xmlResponse;

preg_match_all( "/\<ShipmentAcceptResponse\>(.*?)\<\/ShipmentAcceptResponse\>/s",
$xml, $bookblocks );

foreach( $bookblocks[1] as $block )
{
preg_match_all( "/\<GraphicImage\>(.*?)\<\/GraphicImage\>/",
$block, $author ); // GET LABEL

preg_match_all( "/\<TrackingNumber\>(.*?)\<\/TrackingNumber\>/",
$block, $tracking ); // GET TRACKING NUMBER
//echo( $author[1][0]."\n" );
}

echo '<img src="data:image/gif;base64,'. $author[1][0]. '"/>';*/
}
