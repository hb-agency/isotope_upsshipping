<?php

/**
 * UPS Integration for Isotope eCommerce for Contao Open Source CMS
 *
 * Copyright (C) 2014 HB Agency
 *
 * @package    Isotope_UPSShipping
 * @link       http://www.hbagency.com
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */
 

/**
 * Frontend modules
 */
//$GLOBALS['FE_MOD']['isotope']['iso_upsratesandservice'] = 'ModuleUPSRatesAndService';
//$GLOBALS['FE_MOD']['isotope']['iso_upstracking'] = 'ModuleUPSTracking';


/**
 * Shipping methods
 */
\Isotope\Model\Shipping::registerModelType('ups', 'HBAgency\Model\Shipping\UPS');
