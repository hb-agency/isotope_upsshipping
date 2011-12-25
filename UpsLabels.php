<?php
function USPSLabel() {

// This script was written by Mark Sanborn at http://www.marksanborn.net
// If this script benefits you are your business please consider a donation
// You can donate at http://www.marksanborn.net/donate.

// ========== CHANGE THESE VALUES TO MATCH YOUR OWN ===========

$userName = ''; // Your USPS Username
$FromName = '';
$FromAddress2 = '';
$FromCity = '';
$FromState = '';
$FromZip5 = '';

$ToName = '';
$ToAddress2 = '';
$ToCity = '';
$ToState = '';
$ToZip5 = '';

$weightOunces = 5;

// =============== DON'T CHANGE BELOW THIS LINE ===============

$url = "https://Secure.ShippingAPIs.com/ShippingAPI.dll";
$ch = curl_init();

// set the target url
curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_HEADER, 1);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

// parameters to post
curl_setopt($ch, CURLOPT_POST, 1);

$data = "API=DeliveryConfirmationV3&XML=<DeliveryConfirmationV3.0Request USERID=\"$userName\">
<Option>1</Option>
<ImageParameters />
<FromName>$FromName</FromName>
<FromFirm />
<FromAddress1 />
<FromAddress2>$FromAddress2</FromAddress2>
<FromCity>$FromCity</FromCity>
<FromState>$FromState</FromState>
<FromZip5>$FromZip5</FromZip5>
<FromZip4 />
<ToName>$ToName</ToName>
<ToFirm />
<ToAddress1 />
<ToAddress2>$ToAddress2</ToAddress2>
<ToCity>$ToCity</ToCity>
<ToState>$ToState</ToState>
<ToZip5>$ToZip5</ToZip5>
<ToZip4 />
<WeightInOunces>$weightOunces</WeightInOunces>
<ServiceType>Priority</ServiceType>
<POZipCode />
<ImageType>PDF</ImageType>
<LabelDate />
</DeliveryConfirmationV3.0Request>";

// send the POST values to USPS
curl_setopt($ch, CURLOPT_POSTFIELDS,$data);

$result=curl_exec ($ch);
$data = strstr($result, '<?');
// echo '<!-- '. $data. ' -->'; // Uncomment to show XML in comments

$xmlParser = new uspsxmlParser();
$fromUSPS = $xmlParser->xmlparser($data);
$fromUSPS = $xmlParser->getData();

curl_close($ch);
return $fromUSPS;
}

class uspsxmlParser {

var $params = array(); //Stores the object representation of XML data
var $root = NULL;
var $global_index = -1;
var $fold = false;

/* Constructor for the class
* Takes in XML data as input( do not include the <xml> tag
*/
function xmlparser($input, $xmlParams=array(XML_OPTION_CASE_FOLDING => 0)) {
    $xmlp = xml_parser_create();
        foreach($xmlParams as $opt => $optVal) {
            switch( $opt ) {
            case XML_OPTION_CASE_FOLDING:
                $this->fold = $optVal;
            break;
            default:
            break;
            }
            xml_parser_set_option($xmlp, $opt, $optVal);
    }

    if(xml_parse_into_struct($xmlp, $input, $vals, $index)) {
        $this->root = $this->_foldCase($vals[0]['tag']);
        $this->params = $this->xml2ary($vals);
    }
    xml_parser_free($xmlp);
}

function _foldCase($arg) {
    return( $this->fold ? strtoupper($arg) : $arg);
}

/*
 * Credits for the structure of this function
 * http://mysrc.blogspot.com/2007/02/php-xml-to-array-and-backwards.html
 *
 * Adapted by Ropu - 05/23/2007
 *
*/

function xml2ary($vals) {

    $mnary=array();
    $ary=&$mnary;
    foreach ($vals as $r) {
        $t=$r['tag'];
        if ($r['type']=='open') {
            if (isset($ary[$t]) && !empty($ary[$t])) {
                if (isset($ary[$t][0])){
                    $ary[$t][]=array();
                } else {
                    $ary[$t]=array($ary[$t], array());
                }
                $cv=&$ary[$t][count($ary[$t])-1];
            } else {
                $cv=&$ary[$t];
            }
            $cv=array();
            if (isset($r['attributes'])) {
                foreach ($r['attributes'] as $k=>$v) {
                $cv[$k]=$v;
                }
            }

            $cv['_p']=&$ary;
            $ary=&$cv;

            } else if ($r['type']=='complete') {
                if (isset($ary[$t]) && !empty($ary[$t])) { // same as open
                    if (isset($ary[$t][0])) {
                        $ary[$t][]=array();
                    } else {
                        $ary[$t]=array($ary[$t], array());
                    }
                $cv=&$ary[$t][count($ary[$t])-1];
            } else {
                $cv=&$ary[$t];
            }
            if (isset($r['attributes'])) {
                foreach ($r['attributes'] as $k=>$v) {
                    $cv[$k]=$v;
                }
            }
            $cv['VALUE'] = (isset($r['value']) ? $r['value'] : '');

            } elseif ($r['type']=='close') {
                $ary=&$ary['_p'];
            }
    }

    $this->_del_p($mnary);
    return $mnary;
}

// _Internal: Remove recursion in result array
function _del_p(&$ary) {
    foreach ($ary as $k=>$v) {
	if ($k==='_p') {
          unset($ary[$k]);
        }
        else if(is_array($ary[$k])) {
          $this->_del_p($ary[$k]);
        }
    }
}

/* Returns the root of the XML data */
function GetRoot() {
  return $this->root;
}

/* Returns the array representing the XML data */
function GetData() {
  return $this->params;
}
}

/***********
 * UPS Label Generation Code
 *

require('USPSLabel.php');

echo '<pre>'; print_r(USPSLabel()); echo '</pre>';
$USPSResponse = USPSLabel();
$USPSLabel = $USPSResponse['DeliveryConfirmationV3.0Response']['DeliveryConfirmationLabel']['VALUE'];
*/