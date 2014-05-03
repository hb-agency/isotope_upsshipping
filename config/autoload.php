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
 * Register PSR-0 namespace
 */
NamespaceClassLoader::add('HBAgency', 'system/modules/isotope_upsshipping/library');
NamespaceClassLoader::add('UPS', 'system/modules/isotope_upsshipping/vendor/php-ups-api/lib');


/**
 * Register classes outside the namespace folder
 */
NamespaceClassLoader::addClassMap(array
(
    
));