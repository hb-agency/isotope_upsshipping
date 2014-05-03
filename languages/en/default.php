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
 * Shipping methods
 */
$GLOBALS['TL_LANG']['MODEL']['tl_iso_shipping.ups'] = array('UPS Live Rates &amp; Service shipping', 'This is the default shipping method for regular shipping.');

/**
 * Miscellaneous
 */

$GLOBALS['TL_LANG']['MSC']['labelLabel'] = 'Shipping Label';
$GLOBALS['TL_LANG']['MSC']['trackingNumberLabel'] = 'Tracking Number';
$GLOBALS['TL_LANG']['MSC']['submitTimeInTransit'] = 'Submit';
$GLOBALS['TL_LANG']['MSC']['submitTracking'] = 'Track';
$GLOBALS['TL_LANG']['MSC']['serviceTitleLabel'] = 'Service';
$GLOBALS['TL_LANG']['MSC']['guaranteedLabel'] = 'Guaranteed';
$GLOBALS['TL_LANG']['MSC']['estimatedArrivalLabel'] = 'Est. Arrival';
$GLOBALS['TL_LANG']['MSC']['pickupDatimLabel'] = 'Pickup Cutoff';
$GLOBALS['TL_LANG']['MSC']['businessTransitDaysLabel'] = '# of Transit Days';
$GLOBALS['TL_LANG']['MSC']['upsTrackDatimLabel'] = 'Date/Time';
$GLOBALS['TL_LANG']['MSC']['upsTrackActivityLabel'] = 'Activity';
$GLOBALS['TL_LANG']['MSC']['upsTrackLocationLabel'] = 'Location';
$GLOBALS['TL_LANG']['MSC']['upsTrackDetailsLabel'] = 'Details';
$GLOBALS['TL_LANG']['MSC']['printShippingLabel'] = 'Print Shipping Label';
$GLOBALS['TL_LANG']['MSC']['noEnabledUpsServices'] = 'No UPS services have been enabled for this store.';

/**
 * Reference
 */
$GLOBALS['TL_LANG']['MSC']['apiMode']['test']		= 'Test Mode';
$GLOBALS['TL_LANG']['MSC']['apiMode']['live']		= 'Live Mode';