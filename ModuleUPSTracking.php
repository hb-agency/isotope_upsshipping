<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * TYPOlight Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
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
 * @copyright  Winans Creative 2009, Intelligent Spark 2010, iserv.ch GmbH 2010
 * @author     Fred Bliss <fred.bliss@intelligentspark.com>
 * @author     Andreas Schempp <andreas@schempp.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */


class ModuleUPSTracking extends ModuleIsotope
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_iso_upstracking';

	protected $strFormId = 'iso_mod_ups_tracking';
	
	/** 
	 * Destination
	 * @var array
	 */
	protected $arrDestination = array();
	
	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### ISOTOPE ECOMMERCE: UPS TRACKING ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = $this->Environment->script.'?do=modules&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}
		
		return parent::generate();
	}


	public function generateAjax()
	{
		return '';
	}


	/**
	 * Generate module
	 */
	protected function compile()
	{		
		$this->Template->trackingWidget = $this->generateTrackingWidget();		
		
		$arrResponse = array();
		$arrTrackingNumbers = array();
	
		
		if($this->Input->post('FORM_SUBMIT')==$this->strFormId && !$this->doNotSubmit)
		{
			if($this->Input->post('tracking_number') || $this->Input->get('tracking_number'))
			{
				$arrTrackingNumbers = preg_split("/[\s,]+/",$this->Input->post('tracking_number'));
			}
		}
		elseif($this->Input->get('tracking_number'))
		{
			$arrTrackingNumbers[] = $this->Input->get('tracking_number');
		}
		
		if(count($arrTrackingNumbers))
		{
			foreach($arrTrackingNumbers as $tracking)
			{
				$tracking = new UpsAPITracking($tracking,array());
				$xml = $tracking->buildRequest();	
				$arrResult = $tracking->sendRequest($xml);
									
				$arrResponses[] = $this->parseResponse($arrResult['TrackResponse']);
			}	
		}
	
		$this->Template->datimLabel = $GLOBALS['TL_LANG']['MSC']['upsTrackDatimLabel'];
		$this->Template->activityLabel = $GLOBALS['TL_LANG']['MSC']['upsTrackActivityLabel'];
		$this->Template->locationLabel = $GLOBALS['TL_LANG']['MSC']['upsTrackLocationLabel'];
		$this->Template->detailsLabel = $GLOBALS['TL_LANG']['MSC']['upsTrackDetailsLabel'];
		$this->Template->action = ampersand($this->Environment->request, ENCODE_AMPERSANDS);
		$this->Template->formId = $this->strFormId;
		$this->Template->formSubmit = $this->strFormId;
		$this->Template->enctype = 'application/x-www-form-urlencoded';
		$this->Template->slabel = $GLOBALS['TL_LANG']['MSC']['submitTracking'];
		$this->Template->trackingNumberLabel = $GLOBALS['TL_LANG']['MSC']['trackingNumberLabel'];
		$this->Template->trackingResults = $arrResponses;
	}
	
	/** 
	 * Simplify the nightmarish bloatteriffic UPS response
	 */
	protected function parseResponse($arrResponse)
	{
		
		$arrReturn = array();
		
		switch($arrResponse['Response']['ResponseStatusCode'])
		{
			case '0':
				$arrReturn['general_info']['tracking_number'] = $this->Input->post('tracking_number');
				$arrReturn = array
				(
					'status'		=> 'error',
					'description'	=> $arrResponse['Response']['Error']['ErrorDescription']
				);
				break;
			default:
				$arrActivity = array();							
				$arrActivities = array();
				$arrAddressData= array();
								
				$arrActivity = $arrResponse['Shipment']['Package']['Activity'];
				
				$arrAddressData = $this->parseStupidUPSAddress($arrResponse['Shipment']['ShipTo']['Address']);
				
				$arrReturn['status'] = 'success';
				$arrReturn['general_info']['tracking_number'] = $arrResponse['Shipment']['Package']['TrackingNumber'];
				$arrReturn['general_info']['destination'] = $this->Isotope->generateAddressString($arrAddressData); 
				$arrReturn['general_info']['service'] = $arrResponse['Shipment']['Service']['Description'];
				$arrReturn['general_info']['weight'] = $arrResponse['Shipment']['ShipmentWeight']['Weight'] . ' ' . $arrResponse['Shipment']['ShipmentWeight']['UnitOfMeasurement']['Code'];
				
				foreach($arrActivity as $row)
				{
					$arrAddressData = $this->parseStupidUPSAddress($row['ActivityLocation']['Address']);
				
					$arrActivities[] = array
					(
						'datim'			=> $this->parseStupidUPSDatim($row['Date'],$row['Time']),
						'activity'		=> $row['Status']['StatusType']['Description'],
						'location'		=> $this->Isotope->generateAddressString($arrAddressData),
						'details'		=> $row['ActivityLocation']['Description']					
					);			
				}
				
				if(count($arrActivities))
				{
					$arrReturn['activity'] = $arrActivities;
					
					$arrReturn['general_info']['ship_date'] = $arrActivities[0]['datim'];
					$arrReturn['general_info']['delivery_date'] = $arrActivities[count($arrActivities)-1]['datim'];		
				}
				break;
		}
		
		return $arrReturn;
		
	}
	
	protected function parseStupidUPSAddress($arrData)
	{
		$arrAddressData['city'] = $arrData['City'];
		$arrAddressData['subdivision'] = $arrData['StateProvinceCode'];
		$arrAddressData['country'] = strtolower($arrData['CountryCode']);	
		
		return $arrAddressData;
	}
	
	/**
	 * Function to handle the stupid date & time strings they return 
	 */
	protected function parseStupidUPSDatim($strDate,$strTime='000000')
	{
		//YYYYMMDD // 00:00:00
		$intDatim = mktime((int)substr($strTime,0,2),(int)substr($strTime,2,2),(int)substr($strTime,4,2),(int)substr($strDate,4,2),(int)substr($strDate,6,2),(int)substr($strDate,0,4));
		
		//24HR
		
		return $this->parseDate($GLOBALS['TL_CONFIG']['datimFormat'],$intDatim);	
	}
	
	
	protected function generateTrackingWidget()
	{
			$strClass = $GLOBALS['TL_FFL']['textarea'];

			$arrData = array('id'=>'tracking_number', 'name'=>'tracking_number', 'mandatory'=>true);

			$objWidget = new $strClass($arrData);
			$objWidget->tableless = true;

			// Validate input
			if ($this->Input->post('FORM_SUBMIT') == $this->strFormId)
			{
				$objWidget->validate();

				if ($objWidget->hasErrors())
				{
					$this->doNotSubmit = true;
				}
			}
			elseif ($objWidget->value != '')
			{
				$this->Input->setPost($objWidget->name, $objWidget->value);

				$objValidator = clone $objWidget;
				$objValidator->validate();

				if ($objValidator->hasErrors())
				{
					$this->doNotSubmit = true;
				}
			}
			
			$strBuffer .= $objWidget->parse();
		

		return $strBuffer;
	}


	
	protected function processRequest($arrAddress)
	{				
		//Origin Address//
		$arrOrigin['city']		= ($this->Isotope->Config->city ? $this->Isotope->Config->city : NULL);
		
		
		if($this->Isotope->Config->subdivision)
		{
			$arrState = explode('-',$this->Isotope->Config->subdivision);
			$arrOrigin['state']		= $arrState[1];
			$arrOrigin['country']	= $arrState[0];
		}
		else
		{
			$arrOrigin['country']	= $arrState[0];
		}
				
		if($this->Isotope->Config->postal)
		{
			$arrOrigin['zip']	= $this->Isotope->Config->postal;
		}
		//END Origin Address//
		
		//Destination Address//				
		$arrFieldMap = array
		(
			'name' => $arrAddress['firstname'].' '.$arrAddress['lastname'],
			'street' => $arrAddress['street_1'],
			'city' => $arrAddress['city']
		);
	
		if($arrAddress['subdivision'])
		{
			$arrState = explode("-",$arrAddress['subdivision']);
			$arrFieldMap['state'] = $arrState[1];
			$arrFieldMap['country'] = $arrState[0];
		}
		else
		{	
			$arrFieldMap['country'] = strtoupper($arrAddress['country']);
		}
		
		if($arrAddress['postal'])
			$arrFieldMap['zip'] = $arrAddress['postal'];
		
		//END Destination Address//
		
		//Package Info//
		$fltWeight = $this->Isotope->Cart->getShippingWeight('lb');
		
		$arrData = array(
			'pickup_date' => date('Ymd',time()+86400),	//to figure for a shippign time cutoff as store config value.
			'invoice' => array(
				'currency_code' => $this->Isotope->Config->currency,
				'monetary_value' => $this->Isotope->Cart->subTotal,
			), // end pickup_date
			'weight' => array(
				'unit_of_measure' => array(
					'code' => 'LBS',
					'desc' => 'Pounds',
				), // end unit_of_measure
				'weight' => ($fltWeight>0 ? ceil($fltWeight) : 3)
			), // end weight
		); // end $data	
		//END Package Info//
		
		$strIds = implode(",",deserialize($this->iso_shipping_modules,true));

		if(!$strIds)
		{
			$this->Template->message = $GLOBALS['TL_LANG']['MSC']['noEnabledUpsServices'];
			return array();
		}
		
		$objUPSService = $this->Database->prepare("SELECT ups_enabledservice FROM tl_iso_shipping_modules WHERE id IN($strIds) AND type='ups' OR type='multi_ups'")->limit(1)->execute();
		
		if(!$objUPSService->numRows)
		{
			$this->Template->message = $GLOBALS['TL_LANG']['MSC']['noEnabledUpsServices'];
			return array();
		}
		
		//Service Info//
		$arrShipment['pickup_type'] = (string)$objUPSService->ups_enabledservice;	//default one-time but should be configurable.
		$arrShipment['service'] = (string)$objUPSService->ups_enabledservice; //ground
		$arrShipment['packages'][] = array
				(
					'packaging'		=> array
					(
						'code'			=> '02',	//counter
						'description'	=> ''
					),
					'description'	=> '',
					'units'			=> 'LBS',
					'weight'		=> ($fltWeight>0 ? ceil($fltWeight) : 3),

				);
		
		$arrReturn = array();
		
		//END Service Info/
		$objUpsTimeRequest = new UpsAPITimeInTransit($arrOrigin, $arrFieldMap, $arrData);

		$xmlTime = $objUpsTimeRequest->buildRequest();

		$arrReturn['transit'] = $objUpsTimeRequest->sendRequest($xmlTime);

		//collect rate codes for the rate values
		foreach($arrReturn['transit']['TimeInTransitResponse']['TransitResponse']['ServiceSummary'] as $service)
		{
			$arrCodes[] = $service['Service']['Code'];
		}
		
		$objUpsRateRequest = new UpsAPIRatesAndService($arrShipment, $arrOrigin, $arrOrigin, $arrFieldMap);		
			
		$xmlRate = $objUpsRateRequest->buildRequest();
				
		
		// check the output type
		
	
		$arrReturn['rates']	= $objUpsRateRequest->sendRequest($xmlRate);
	
		return $arrReturn;
	}
}

