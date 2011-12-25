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
 * Palettes
 */
$GLOBALS['TL_DCA']['tl_iso_shipping_modules']['palettes']['ups_single']	= '{title_legend},type,name,label;{note_legend:hide},note;{price_legend},price,tax_class;{ups_legend},ups_enabledService;{config_legend},weight_unit,countries,subdivisions,minimum_total,maximum_total,product_types;{expert_legend:hide},guests,protected;{enabled_legend},enabled';
$GLOBALS['TL_DCA']['tl_iso_shipping_modules']['palettes']['ups_multiple']	= '{title_legend},type,name,label;{note_legend:hide},note;{price_legend},price,tax_class;{ups_legend},ups_enabledService;{config_legend},weight_unit,countries,subdivisions,minimum_total,maximum_total,product_types;{expert_legend:hide},guests,protected;{enabled_legend},enabled';


/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_iso_shipping_modules']['ups_enabledService'] = array
(
	'label'					  => &$GLOBALS['TL_LANG']['tl_iso_shipping_modules']['ups_enabledService'],
	'exclude'				  => true,
	'inputType'			  => 'select',
	'options'				  => $GLOBALS['TL_LANG']['tl_iso_shipping_modules']['ups_service'],
	'eval'					  => array('mandatory'=>true)
);
