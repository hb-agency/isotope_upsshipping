-- **********************************************************
-- *                                                        *
-- * IMPORTANT NOTE                                         *
-- *                                                        *
-- * Do not import this file manually but use the TYPOlight *
-- * install tool to create and maintain database tables!   *
-- *                                                        *
-- **********************************************************


-- --------------------------------------------------------

--
-- Table `tl_iso_orders`
--

CREATE TABLE `tl_iso_orders` (
  `ups_tracking_number` varchar(255) NOT NULL default '',
  `ups_label` blob NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table `tl_iso_config`
--

CREATE TABLE `tl_iso_config` (
  `enableUps` char(1) NOT NULL default '',
  `UpsAccessKey` varchar(255) NOT NULL default '',
  `UpsUsername` varchar(255) NOT NULL default '',
  `UpsPassword` varchar(255) NOT NULL default '', 
  `UpsAccountNumber` varchar(255) NOT NULL default '',
  `UpsMode` varchar(8) NOT NULL default ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table `tl_iso_addresses`
--

CREATE TABLE `tl_iso_addresses` (
  `address_classification` varchar(64) NOT NULL default '',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;