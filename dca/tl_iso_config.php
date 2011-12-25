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
	'eval'						=> array('doNotCopy'=>true, 'submitOnChange'=>true),
);

$GLOBALS['TL_DCA']['tl_iso_config']['fields']['UpsAccessKey'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_config']['UpsAccessKey'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
);
		
$GLOBALS['TL_DCA']['tl_iso_config']['fields']['UpsUsername'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_config']['UpsUsername'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'tl_class'=>'w50 clr'),
);
		
$GLOBALS['TL_DCA']['tl_iso_config']['fields']['UpsPassword'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_config']['UpsPassword'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('mandatory'=>true, 'maxlength'=>255, 'hideInput'=>true),
);

$GLOBALS['TL_DCA']['tl_iso_config']['fields']['UpsAccountNumber'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_config']['UpsAccountNumber'],
	'exclude'                 => true,
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>255, 'tl_class'=>'w50'),
);

$GLOBALS['TL_DCA']['tl_iso_config']['fields']['UpsMode'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_iso_config']['UpsMode'],
	'exclude'                 => true,
	'default'				  => 'test',
	'inputType'               => 'select',
	'options'				  => array('test','live'),
	'eval'					  => array('doNotCopy'=>true, 'tl_class'=>'w50'),
	'reference'				  => &$GLOBALS['TL_LANG']['MSC']['apiMode']
);
