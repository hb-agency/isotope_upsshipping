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
 * Fields
 */
 
$GLOBALS['TL_LANG']['tl_iso_config']['enableUps']			= array('Enable UPS API','Enable the UPS Shipping Tools API.');
$GLOBALS['TL_LANG']['tl_iso_config']['UpsAccessKey']		= array('Access Key','Please provide the access key supplied by UPS.');
$GLOBALS['TL_LANG']['tl_iso_config']['UpsUsername']			= array('User name','Please provide your UPS account user name.');
$GLOBALS['TL_LANG']['tl_iso_config']['UpsPassword']			= array('Password','Please provide your UPS API password.');
$GLOBALS['TL_LANG']['tl_iso_config']['UpsAccountNumber']	= array('Account number','Please provide your UPS Account number (for shipping/label generation. To obtain, log into My UPS and then click "Account Summary". The value is listed as a UPS Account Number.)');
$GLOBALS['TL_LANG']['tl_iso_config']['UpsMode']				= array('UPS API Mode','Use test to avoid real shipping requests!');

/** 
 * Legends
 */
$GLOBALS['TL_LANG']['tl_iso_config']['ups_legend']		= 'UPS API';