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

$GLOBALS['TL_DCA']['tl_iso_product_collection']['palettes']['default'] .= ';{ups_legend},ups_tracking_number';

$GLOBALS['TL_DCA']['tl_iso_product_collection']['fields']['ups_tracking_number'] = array
(
	'label'					=> &$GLOBALS['TL_LANG']['tl_iso_product_collection']['ups_tracking_number'],
	'input_field_callback'	=> array('tl_iso_product_collection_ups','createTrackingLink'),
	'sql'                   => "varchar(255) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_iso_product_collection']['fields']['ups_label'] = array
(
	'label'					=> &$GLOBALS['TL_LANG']['tl_iso_product_collection']['ups_label'],
	'sql'                   => "blob NULL"
);

class tl_iso_product_collection_ups extends Backend
{
	public function createTrackingLink($dc, $xlabel)
	{
		return '<div class="ups_tracking_number">'.($dc->activeRecord->ups_tracking_number ? $dc->activeRecord->ups_tracking_number : '<no shipment has been created>').'</div>';
	}
}