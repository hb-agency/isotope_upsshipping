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


class ModuleUPSRatesAndService extends ModuleIsotope
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_iso_upsratesandservice';

	protected $strFormId = 'iso_mod_ups_rates_and_service';
	
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

			$objTemplate->wildcard = '### ISOTOPE ECOMMERCE: UPS RATES AND SERVICE ###';
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
		$this->Template->addressWidget = $this->generateAddressWidget();		
		$this->Template->action = ampersand($this->Environment->request, ENCODE_AMPERSANDS);
		$this->Template->formId = $this->strFormId;
		$this->Template->formSubmit = $this->strFormId;
		$this->Template->enctype = 'application/x-www-form-urlencoded';
		$this->Template->slabel = $GLOBALS['TL_LANG']['MSC']['submitTimeInTransit'];
	}
	
	protected function generateAddressWidget()
	{
		if(FE_USER_LOGGED_IN)
		{
			$objAddress = $this->Database->execute("SELECT * FROM tl_iso_addresses WHERE pid={$this->User->id} AND store_id={$this->Isotope->Config->store_id} ORDER BY isDefaultBilling DESC");
	
			while( $objAddress->next() )
			{
				if (is_array($arrCountries) && !in_array($objAddress->country, $arrCountries))
					continue;
	
				$arrOptions[] = array
				(
					'value'		=> $objAddress->id,
					'label'		=> $this->Isotope->generateAddressString($objAddress->row(), $this->Isotope->Config->shipping_fields),
				);
			}	
		}
		$intDefaultValue = strlen($arrAddress['id']) ? $arrAddress['id'] : -1;

		if (count($arrOptions))
		{
			$strClass = $GLOBALS['TL_FFL']['radio'];

			$arrData = array('id'=>$field, 'name'=>$field, 'mandatory'=>true);

			$objWidget = new $strClass($arrData);
			$objWidget->options = $arrOptions;
			$objWidget->value = $intDefaultValue;
			$objWidget->onclick = "Isotope.toggleAddressFields(this, '" . $field . "_new');";
			$objWidget->storeValues = true;
			$objWidget->tableless = true;

			// Validate input
			if ($this->Input->post('FORM_SUBMIT') == $this->strFormId)
			{
				$objWidget->validate();

				if ($objWidget->hasErrors())
				{
					$this->doNotSubmit = true;
				}
				else
				{
					$intSelectedAddress = $objWidget->value;
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
		}

		if (strlen($_SESSION['CHECKOUT_DATA'][$field]['id']))
		{
			//$this->Isotope->Cart->$field = $_SESSION['CHECKOUT_DATA'][$field]['id'];
		}
		elseif (!FE_USER_LOGGED_IN)
		{

		//	$this->doNotSubmit = true;
		}


		$strBuffer .= '<div id="' . $field . '_new" class="address_new"' . (((!FE_USER_LOGGED_IN && $field == 'shipping_address') || $objWidget->value == 0) ? '>' : ' style="display:none">');
		$strBuffer .= '<span>' . $this->generateAddressWidgets('shipping_address', count($arrOptions)) . '</span>';
		$strBuffer .= '</div>';

		return $strBuffer;
	}


	/**
	 * Generate the current step widgets.
	 * strResourceTable is used either to load a DCA or else to gather settings related to a given DCA.
	 *
	 * @todo <table...> was in a template, but I don't get why we need to define the table here?
	 */
	protected function generateAddressWidgets($strAddressType, $intOptions)
	{
		$arrBuffer = array();

		$this->loadLanguageFile('tl_iso_addresses');
		$this->loadDataContainer('tl_iso_addresses');

		foreach($this->Isotope->Config->shipping_fields as $field)
		{
			if($field['value']=='subdivision')
				$blnSubdivisionEnabled = $field['enabled'];	
		}
		//$arrFields = $this->Isotope->Config->shipping_fields;
		$arrFields[] = array('enabled'=>true,'value'=>'city');
		$arrFields[] = array('enabled'=>true,'value'=>'postal');
		$arrFields[] = array('enabled'=>$blnSubdivisionEnabled,'value'=>'subdivision');
		$arrFields[] = array('enabled'=>true,'value'=>'country');
		$arrDefault = $this->Isotope->Cart->shipping_address;

		if ($arrDefault['id'] == -1)
			$arrDefault = array();

		foreach( $arrFields as $field )
		{
			$arrData = $GLOBALS['TL_DCA']['tl_iso_addresses']['fields'][$field['value']];

			if (!is_array($arrData) || !$arrData['eval']['feEditable'] || !$field['enabled'] || ($arrData['eval']['membersOnly'] && !FE_USER_LOGGED_IN))
				continue;

			$strClass = $GLOBALS['TL_FFL'][$arrData['inputType']];

			// Continue if the class is not defined
			if (!$this->classFileExists($strClass))
				continue;

			// Special field "country"
			if ($field['value'] == 'country')
			{
				$arrCountries = ($strAddressType == 'shipping_address' ? $this->Isotope->Config->shipping_countries : $this->Isotope->Config->_countries);

				$arrData['options'] = array_values(array_intersect($arrData['options'], $arrCountries));
				$arrData['default'] = $this->Isotope->Config->country;
			}

			// Special field type "conditionalselect"
			elseif (strlen($arrData['eval']['conditionField']))
			{
				$arrData['eval']['conditionField'] = 'shipping_address_' . $arrData['eval']['conditionField'];
			}

			// Special fields "isDefaultBilling" & "isDefault"
			elseif ($field['value'] == 'isDefault' && $strAddressType == 'shipping_address' && $intOptions < 3)
			{
				$arrDefault[$field['value']] = '1';
			}

			$i = count($arrBuffer);

			$objWidget = new $strClass($this->prepareForWidget($arrData, $strAddressType . '_' . $field['value'], (strlen($this->arrDestination[$field['value']]) ? $this->arrDestination[$field['value']] : $arrDefault[$field['value']])));

			$objWidget->mandatory = $field['mandatory'] ? true : false;
			$objWidget->required = $objWidget->mandatory;
			$objWidget->tableless = $this->tableless;
			$objWidget->label = $field['label'] ? $this->Isotope->translate($field['label']) : $objWidget->label;
			$objWidget->storeValues = true;
			$objWidget->rowClass = 'row_'.$i . (($i == 0) ? ' row_first' : '') . ((($i % 2) == 0) ? ' even' : ' odd');

			// Validate input
			if ($this->Input->post('FORM_SUBMIT') == $this->strFormId && ($this->Input->post($strAddressType) === '0' || $this->Input->post($strAddressType) == ''))
			{
				$objWidget->validate();

				$varValue = $objWidget->value;

				// Convert date formats into timestamps
				if (strlen($varValue) && in_array($arrData['eval']['rgxp'], array('date', 'time', 'datim')))
				{
					$objDate = new Date($varValue, $GLOBALS['TL_CONFIG'][$arrData['eval']['rgxp'] . 'Format']);
					$varValue = $objDate->tstamp;
				}

				// Do not submit if there are errors
				if ($objWidget->hasErrors())
				{
					$this->doNotSubmit = true;
				}

				// Store current value
				elseif ($objWidget->submitInput())
				{
					$arrAddress[$field['value']] = $varValue;
				}
			}
			elseif ($this->Input->post($strAddressType) === '0' || $this->Input->post($strAddressType) == '')
			{
				$this->Input->setPost($objWidget->name, $objWidget->value);

				$objValidator = clone $objWidget;
				$objValidator->validate();

				if ($objValidator->hasErrors())
				{
					$this->doNotSubmit = true;
				}
			}

			$arrBuffer[] = $objWidget->parse();
		}

		// Add row_last class to the last widget
		array_pop($arrBuffer);
		$objWidget->rowClass = 'row_'.$i . (($i == 0) ? ' row_first' : '') . ' row_last' . ((($i % 2) == 0) ? ' even' : ' odd');
		$arrBuffer[] = $objWidget->parse();

		// Validate input
		if ($this->Input->post('FORM_SUBMIT') == $this->strFormId && !$this->doNotSubmit && is_array($arrAddress) && count($arrAddress))
		{
			$arrResponse = $this->processRequest($arrAddress);
			$arrTransit = $arrResponse['transit'];
			$arrRates = $arrResponse['rates'];
			
			$arrAddress['id'] = 0;
			$this->Template->serviceTitleLabel = $GLOBALS['TL_LANG']['MSC']['serviceTitleLabel'];
			$this->Template->guaranteedLabel = $GLOBALS['TL_LANG']['MSC']['guaranteedLabel'];
			$this->Template->estimatedArrivalLabel = $GLOBALS['TL_LANG']['MSC']['estimatedArrivalLabel'];
			$this->Template->pickupDatimLabel = $GLOBALS['TL_LANG']['MSC']['pickupDatimLabel'];
			$this->Template->businessTransitDaysLabel = $GLOBALS['TL_LANG']['MSC']['businessTransitDaysLabel'];
			$this->Template->serviceResults = $arrTransit['TimeInTransitResponse']['TransitResponse']['ServiceSummary'];
			$this->Template->upsDisclaimer = $arrTransit['TimeInTransitResponse']['TransitResponse']['Disclaimer'];
		}

		if (is_array($this->arrDestination) && $this->arrDestination['id'] === 0)
		{
			$this->Isotope->Cart->shipping_address = $this->arrDestination;
		}

		if ($this->tableless)
		{
			return implode('', $arrBuffer);
		}

		return '<table cellspacing="0" cellpadding="0" summary="Form fields">
' . implode('', $arrBuffer) . '
</table>';
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

