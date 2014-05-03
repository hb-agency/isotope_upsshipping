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
 * Table tl_iso_config
 */
$GLOBALS['TL_DCA']['tl_iso_config']['palettes']['__selector__'][] = 'enableUps';
$GLOBALS['TL_DCA']['tl_iso_config']['palettes']['default'] .= ';{ups_legend},enableUps';
$GLOBALS['TL_DCA']['tl_iso_config']['subpalettes']['enableUps'] = 'UpsAccessKey,UpsUsername,UpsPassword,UpsAccountNumber,UpsMode';

/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_iso_config']['fields']['enableUps'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_config']['enableUps'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'					  => array('doNotCopy'=>true, 'submitOnChange'=>true),
	'sql'                     => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_iso_config']['fields']['UpsAccessKey'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_config']['UpsAccessKey'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);
		
$GLOBALS['TL_DCA']['tl_iso_config']['fields']['UpsUsername'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_config']['UpsUsername'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50 clr'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);
		
$GLOBALS['TL_DCA']['tl_iso_config']['fields']['UpsPassword'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_config']['UpsPassword'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'hideInput'=>true),
	'sql'                     => "varchar(255) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_iso_config']['fields']['UpsAccountNumber'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_config']['UpsAccountNumber'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
	'sql'                     => "varchar(255) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_iso_config']['fields']['UpsMode'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_config']['UpsMode'],
	'exclude'                 => true,
	'default'				  => 'test',
	'inputType'               => 'select',
	'options'				  => array('test','live'),
	'eval'					  => array('doNotCopy'=>true, 'tl_class'=>'w50'),
	'reference'				  => &$GLOBALS['TL_LANG']['MSC']['apiMode'],
	'sql'                     => "varchar(8) NOT NULL default ''"
);
