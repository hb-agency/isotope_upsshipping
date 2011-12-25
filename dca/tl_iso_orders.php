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
 * @author     Christian de la Haye <service@delahaye.de>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 */

$GLOBALS['TL_DCA']['tl_iso_orders']['palettes']['default'] .= ';{ups_legend},ups_tracking_number';

$GLOBALS['TL_DCA']['tl_iso_orders']['fields']['ups_tracking_number'] = array
(
	'label'					=> &$GLOBALS['TL_LANG']['tl_iso_orders']['ups_tracking_number'],
	'input_field_callback'	=> array('tl_iso_orders_ups','createTrackingLink')
);

class tl_iso_orders_ups extends Backend
{
	public function createTrackingLink($dc, $xlabel)
	{
		return '<div class="ups_tracking_number">'.($dc->activeRecord->ups_tracking_number ? $dc->activeRecord->ups_tracking_number : '<no shipment has been created>').'</div>';
	}
}