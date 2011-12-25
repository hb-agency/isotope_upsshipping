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
 * @package    IsotopeUPS
 * @license    LGPL
 * @filesource
 */


/**
 * Parent class for the UpsAPI
 * 
 * @author James I. Armes <jamesiarmes@gmail.com>
 * @package php_ups_api
 */
abstract class UpsAPI extends System{
	/**
	 * Status code for a failed request
	 * 
	 * @var integer
	 */
	const RESPONSE_STATUS_CODE_FAIL = 0;
	
	/**
	 * Status code for a successful request
	 * 
	 * @var integer
	 */
	const RESPONSE_STATUS_CODE_PASS = 1;
	
	/**
	 * Access key provided by UPS
	 * 
	 * @access protected
	 * @var string
	 */
	protected $access_key;
	
	
	/**
	 * Password used to access UPS Systems
	 * 
	 * @access protected
	 * @var string
	 */
	protected $password;
	
	/**
	 * Response from the server as XML
	 * 
	 * @access protected
	 * @var DOMDocument
	 */
	protected $response;
	
	/**
	 * Response from the server as an array
	 * 
	 * @access protected
	 * @var array
	 */
	protected $response_array;
	
	/**
	 * Root Node for the repsonse XML
	 * 
	 * @access protected
	 * @var DOMNode
	 */
	protected $root_node;
	
	/**
	 * UPS Server to send Request to
	 * 
	 * @access protected
	 * @var string
	 */
	protected $server;
	
	/**
	 * Username used to access UPS Systems
	 * 
	 * @access protected
	 * @var string
	 */
	protected $username;
	
	protected $accountNumber;
	
	/**
	 * xpath object for the response XML
	 * 
	 * @access protected
	 * @var DOMXPath
	 */
	protected $xpath;
	
	/**
	 * Sets up the API Object
	 * 
	 * @access public
	 */
	public function __construct() {
		$this->import('Isotope');
		/** Set the Keys on the Object **/
		$this->access_key = $this->Isotope->Config->UpsAccessKey;	
		
		/** Set the username and password on the Object **/
		$this->password = $this->Isotope->Config->UpsPassword;
		$this->username = $this->Isotope->Config->UpsUsername;
		$this->accountNumber = $this->Isotope->Config->UpsAccountNumber;
		$this->server = "https://www".($this->Isotope->Config->UpsMode=='test' ? "cie" : "").".ups.com/ups.app/xml/";
	} // end funciton __construct()
	
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
		// create the access request element
		$access_dom = new DOMDocument('1.0');
		$access_element = $access_dom->appendChild(
			new DOMElement('AccessRequest'));
		$access_element->setAttributeNode(new DOMAttr('xml:lang', 'en-US'));
		
		// create the child elements
		$access_element->appendChild(
			new DOMElement('AccessLicenseNumber', $this->access_key));
		$access_element->appendChild(
			new DOMElement('UserId', $this->username));
		$access_element->appendChild(
			new DOMElement('Password', $this->password));
		
		return $access_dom->saveXML();
	} // end function buildRequest()
	
	/**
	 * Returns the error message(s) from the response
	 * 
	 * @return array
	 */
	public function getError() {
		// iterate over the error messages
		$errors = $this->xpath->query('Response/Error', $this->root_node);
		$return_value = array();
		foreach ($errors as $error) {
			$return_value[] = array(
				'severity' => $this->xpath->query('ErrorSeverity', $error)
					->item(0)->nodeValue,
				'code' => $this->xpath->query('ErrorCode', $error)
					->item(0)->nodeValue,
				'description' => $this->xpath->query('ErrorDescription', $error)
					->item(0)->nodeValue,
				'location' => $this->xpath
					->query('ErrorLocation/ErrorLocationElementName', $error)
					->item(0)->nodeValue,
			); // end $return_value
		} // end for each error message
		
		return $return_value;
	} // end function getError()
	
	/**
	 * Checks to see if a repsonse is an error
	 * 
	 * @access public
	 * @return boolean 
	 */
	public function isError() {
		// check to see if the request failed
		$status = $this->xpath->query('Response/ResponseStatusCode',
			$this->root_node);
		if ($status->item(0)->nodeValue == self::RESPONSE_STATUS_CODE_FAIL) {
			return true;
		} // end if the request failed
		
		return false;
	} // end function isError
	
	/**
	 * Send a request to the UPS Server using xmlrpc
	 * 
	 * @access public
	 * @param string $request_xml XML request from the child objects
	 * buildRequest() method
	 * @param boool $return_raw_xml whether or not to return the raw XML from
	 * the request
	 * 
	 * @todo remove array creation after switching over to xpath
	 */
	public function sendRequest($request_xml, $return_raw_xml = false) {
		//require_once 'XML/Unserializer.php';
		
		// build an array of headers to use for our request
		$headers = array(
			'Method: POST',
			'Connection: Keep-Alive',
			'User-Agent: PHP-SOAP-CURL',
			'Content-Type: text/xml; charset=utf-8',
		); // end $headers
	
		// setup the curl resource
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->server);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $request_xml);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);

		// check to see if the request failed
		if ($response === false) {
			$this->log('UPS Error: ' .curl_error($ch),'UpsAPI sendRequest()',TL_ERROR);
			//throw new UPSException('Curl Error: "'.curl_error($ch).'"');
			return false;
		} // end if the request failed

		// TODO: remove array creation after switching over to xpath
		// create an array from the raw XML data
		//$unserializer = new XML_Unserializer(array('returnResult' => true));
		//$this->response_array = $unserializer->unserialize($response);
		;
				
		return ($return_raw_xml ? $response : $this->xml2array($response));
		
		// build the dom objects
		/*$this->response = new DOMDocument();
		$this->response->loadXML($response);
		$this->xpath = new DOMXPath($this->response);
		$this->root_node = $this->xpath->query(
			'/'.$this->getRootNodeName())->item(0);
		
		// check if we should return the raw XML data
		if ($return_raw_xml) {
			return $response;
		} // end if we should return the raw XML
		
		// return the response as an array
		return $this->response_array;*/
	} // end function sendRequest()
	
	/**
	 * Builds the Request element
	 * 
	 * @access protected
	 * @param DOMElement $dom_element
	 * @param string $action
	 * @param string $option
	 * @param string|array $customer_context
	 * @return DOMElement
	 */
	protected function buildRequest_RequestElement(&$dom_element, $action,
		$option = null, $customer_context = null) {
		// create the child element
		$request = $dom_element->appendChild(
			new DOMElement('Request'));
		
		// create the children of the Request element
		$transaction_element = $request->appendChild(
			new DOMElement('TransactionReference'));
		$request->appendChild(
			new DOMElement('RequestAction', $action));
		
		// check to see if an option was passed in
		if (!empty($option)) {
			$request->appendChild(
				new DOMElement('RequestOption', $option));
		} // end if an option was passed in
		
		// create the children of the TransactionReference element
		$transaction_element->appendChild(
			new DOMElement('XpciVersion', '1.0'));
		
		// check if we have customer data to include
		if (!empty($customer_context)) {
			// check to see if the customer context is an array
			if (is_array($customer_context)) {
				$customer_element = $transaction_element->appendChild(
					new DOMElement('CustomerContext'));

				// iterate over the array of customer data
				foreach ($customer_context as $element => $value) {
					$customer_element->appendChild(
						new DOMElement($element, $value));
				} // end for each customer data
			} // end if the customer data is an array
			else {
				$transaction_element->appendChild(
					new DOMElement('CustomerContext', $customer_context));
			} // end if the customer data is a string
		} // end if we have customer data to include
		
		return $request;
	} // end function buildRequest_RequestElement()
	
	/**
	 * Convert XML to an array
	 */
	protected function xml2array($contents, $get_attributes=1, $priority = 'tag') 
    { 
        if(!$contents) return array(); 
    
        if(!function_exists('xml_parser_create')) { 
            //print "'xml_parser_create()' function not found!"; 
            return array(); 
        } 
    
        //Get the XML parser of PHP - PHP must have this module for the parser to work 
        $parser = xml_parser_create(''); 
        xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss 
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0); 
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1); 
        xml_parse_into_struct($parser, trim($contents), $xml_values); 
        xml_parser_free($parser); 
    
        if(!$xml_values) return;//Hmm... 
    
        //Initializations 
        $xml_array = array(); 
        $parents = array(); 
        $opened_tags = array(); 
        $arr = array(); 
    
        $current = &$xml_array; //Refference 
    
        //Go through the tags. 
        $repeated_tag_index = array();//Multiple tags with same name will be turned into an array 
        foreach($xml_values as $data) { 
            unset($attributes,$value);//Remove existing values, or there will be trouble 
    
            //This command will extract these variables into the foreach scope 
            // tag(string), type(string), level(int), attributes(array). 
            extract($data);//We could use the array by itself, but this cooler. 
    
            $result = array(); 
            $attributes_data = array(); 
             
            if(isset($value)) { 
                if($priority == 'tag') $result = $value; 
                else $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode 
            } 
    
            //Set the attributes too. 
            if(isset($attributes) and $get_attributes) { 
                foreach($attributes as $attr => $val) { 
                    if($priority == 'tag') $attributes_data[$attr] = $val; 
                    else $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr' 
                } 
            } 
    
            //See tag status and do the needed. 
            if($type == "open") {//The starting of the tag '<tag>' 
                $parent[$level-1] = &$current; 
                if(!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag 
                    $current[$tag] = $result; 
                    if($attributes_data) $current[$tag. '_attr'] = $attributes_data; 
                    $repeated_tag_index[$tag.'_'.$level] = 1; 
    
                    $current = &$current[$tag]; 
    
                } else { //There was another element with the same tag name 
    
                    if(isset($current[$tag][0])) {//If there is a 0th element it is already an array 
                        $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result; 
                        $repeated_tag_index[$tag.'_'.$level]++; 
                    } else {//This section will make the value an array if multiple tags with the same name appear together 
                        $current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array
                        $repeated_tag_index[$tag.'_'.$level] = 2; 
                         
                        if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well 
                            $current[$tag]['0_attr'] = $current[$tag.'_attr']; 
                            unset($current[$tag.'_attr']); 
                        } 
    
                    } 
                    $last_item_index = $repeated_tag_index[$tag.'_'.$level]-1; 
                    $current = &$current[$tag][$last_item_index]; 
                } 
    
            } elseif($type == "complete") { //Tags that ends in 1 line '<tag />' 
                //See if the key is already taken. 
                if(!isset($current[$tag])) { //New Key 
                    $current[$tag] = $result; 
                    $repeated_tag_index[$tag.'_'.$level] = 1; 
                    if($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data; 
    
                } else { //If taken, put all things inside a list(array) 
                    if(isset($current[$tag][0]) and is_array($current[$tag])) {//If it is already an array... 
    
                        // ...push the new element into that array. 
                        $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result; 
                         
                        if($priority == 'tag' and $get_attributes and $attributes_data) { 
                            $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data; 
                        } 
                        $repeated_tag_index[$tag.'_'.$level]++; 
    
                    } else { //If it is not an array... 
                        $current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
                        $repeated_tag_index[$tag.'_'.$level] = 1; 
                        if($priority == 'tag' and $get_attributes) { 
                            if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well 
                                 
                                $current[$tag]['0_attr'] = $current[$tag.'_attr']; 
                                unset($current[$tag.'_attr']); 
                            } 
                             
                            if($attributes_data) { 
                                $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data; 
                            } 
                        } 
                        $repeated_tag_index[$tag.'_'.$level]++; //0 and 1 index is already taken 
                    } 
                } 
    
            } elseif($type == 'close') { //End of tag '</tag>' 
                $current = &$parent[$level-1]; 
            } 
        } 
         
        return($xml_array); 
    }
	
	/**
	 * Returns the name of the servies response root node
	 * 
	 * @access protected
	 * @return string
	 * 
	 * @todo remove after phps self scope has been fixed
	 */
	protected abstract function getRootNodeName();
} // end class UpsAPI
