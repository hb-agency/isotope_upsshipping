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