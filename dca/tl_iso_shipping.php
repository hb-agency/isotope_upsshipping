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
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_iso_shipping']['palettes']['ups']	= '{title_legend},type,name,label;{note_legend:hide},note;{price_legend},price,tax_class;{ups_legend},ups_enabledService;{config_legend},weight_unit,countries,subdivisions,minimum_total,maximum_total,product_types;{expert_legend:hide},guests,protected;{enabled_legend},enabled';


//$GLOBALS['TL_DCA']['tl_iso_shipping']['palettes']['ups_multiple']	= '{title_legend},type,name,label;{note_legend:hide},note;{price_legend},price,tax_class;{ups_legend},ups_enabledService;{config_legend},weight_unit,countries,subdivisions,minimum_total,maximum_total,product_types;{expert_legend:hide},guests,protected;{enabled_legend},enabled';


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_iso_shipping']['fields']['ups_enabledService'] = array
(
	'label'				=> &$GLOBALS['TL_LANG']['tl_iso_shipping']['ups_enabledService'],
	'exclude'			=> true,
	'inputType'			=> 'select',
	'options'			=> $GLOBALS['TL_LANG']['tl_iso_shipping']['ups_service'],
	'eval'				=> array('mandatory'=>true),
	'sql'               => "varchar(255) NOT NULL default ''",
);
