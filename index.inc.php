<?php
/*
+--------------------------------------------------------------------------
|   CubeCart 4
|   ========================================
|	CubeCart is a registered trade mark of Devellion Limited
|   Copyright Devellion Limited 2010. All rights reserved.
|   Devellion Limited,
|   13 Ducketts Wharf,
|   South Street,
|   Bishops Stortford,
|   HERTFORDSHIRE.
|   CM23 3AR
|   UNITED KINGDOM
|   http://www.devellion.com
|	UK Private Limited Company No. 5323904
|   ========================================
|   Web: http://www.cubecart.com
|   Email: info (at) cubecart (dot) com
|	License Type: CubeCart is NOT Open Source Software and Limitations Apply
|   Licence Info: http://www.cubecart.com/v4-software-license
+--------------------------------------------------------------------------
|	index.inc.php
|   ========================================
|	Main pages of the store
+--------------------------------------------------------------------------
*/
if (!defined('CC_INI_SET')) die("Access Denied");

$body = new XTemplate ('global'.CC_DS.'index.tpl');

if(empty($_GET)) {

$body->assign('RESOLUTION_CHECK', '<script type="text/javascript" src="modules/3rdparty/Homepage_Categories/js/resolutionCheck.js"></script>');

require_once'modules'.CC_DS.'3rdparty'.CC_DS.'Homepage_Categories'.CC_DS.'boxes'.CC_DS.'Homepage_Categories.inc.php';
if($categories->data) {
$body->assign('HOMEPAGE_CATEGORIES',$box_content);
}

}

require_once('modules'.CC_DS.'3rdparty'.CC_DS.'categories'.CC_DS.'boxes'.CC_DS.'category_nav_box.inc.php');
if($categories->data_count > 0) {
$body->assign('CAT_NAV_BOX', $nav_box);
}

require_once('modules'.CC_DS.'3rdparty'.CC_DS.'lock_tech_ranges'.CC_DS.'boxes'.CC_DS.'lock_tech_ranges_box.inc.php');
if(!empty($ranges->range_id)) {
$body->assign('RANGES', $box_content);
}

require_once('modules'.CC_DS.'3rdparty'.CC_DS.'lock_tech_finishes'.CC_DS.'boxes'.CC_DS.'lock_tech_finishes_box.inc.php');

if(!empty($finishes->finish_id)) {

$body->assign('FINISHES', $box_content);
}

## Extra Events
// $extraEvents = '';
// if (isset($_GET['added']) && !empty($_GET['added'])) {
// 	if (!$cc_session->ccUserData['customer_id'] && $config['hide_prices'] == 1) {
// 		## have a break, have a KitKat
// 	} else {
// 		$extraEvents = 'flashBasket(6);';
// 	}
// }
// $body->assign('EXTRA_EVENTS',$extraEvents);

if (isset($_GET['searchStr'])) {
	$body->assign('SEARCHSTR', sanitizeVar($_GET['searchStr']));
} else {
	$body->assign('SEARCHSTR','');
}

//$body->assign('CURRENCY_VER',$currencyVer);

## Incluse langauge config
include('language'.CC_DS.LANG_FOLDER.CC_DS.'config.php');
$body->assign('VAL_ISO',$charsetIso);

/* start mod: Google Analytics - http://cubecart.expandingbrain.com */
/*
$ga_mod = fetchDbConfig("Google_Analytics");
if ($ga_mod && $ga_mod['status_cc4']) {
	include_once 'modules'.CC_DS.'3rdparty'.CC_DS.'Google_Analytics'.CC_DS.'googleAnalytics.inc.php';
	$body->assign("GOOGLE_ANALYTICS", ga_page_tracking_code());
}
*/
/* end mod: Google Analytics - by Estelle */


## START CONTENT BOXES
require_once 'includes'.CC_DS.'boxes'.CC_DS.'searchForm.inc.php';
$body->assign('SEARCH_FORM',$box_content);

//require_once'includes'.CC_DS.'boxes'.CC_DS.'session.inc.php';
//$body->assign('SESSION',$box_content);

// require_once'includes'.CC_DS.'boxes'.CC_DS.'logos.inc.php';
// $body->assign('LOGOS',$box_content);

require_once'includes'.CC_DS.'boxes'.CC_DS.'session2.inc.php';
$body->assign('SESSION2',$box_content);

require_once'modules'.CC_DS.'3rdparty'.CC_DS.'Advanced_Product_Filtering'.CC_DS.'boxes'.CC_DS.'advanced_product_filtering_box.inc.php';
if($apf->product_results->product_results) {
$body->assign('FILTER_BOX',$box_content);
}

// require_once"modules".CC_DS."3rdparty".CC_DS."STP_Slideshow".CC_DS."includes".CC_DS."boxes".CC_DS."preloading_slide.inc.php";
// $body->assign('PRELOADING_SLIDE',$box_content);

// require_once"modules".CC_DS."3rdparty".CC_DS."STP_Slideshow".CC_DS."includes".CC_DS."boxes".CC_DS."stp_slideshow.inc.php";
// $body->assign('STP_SLIDESHOW',$box_content);

require_once'modules'.CC_DS.'3rdparty'.CC_DS.'infoLinks'.CC_DS.'boxes'.CC_DS.'infoLinks.inc.php';
$body->assign('INFO_LINKS',$box_content);

// require_once'includes'.CC_DS.'boxes'.CC_DS.'categories.inc.php';
// $body->assign('CATEGORIES',$box_content);

//require_once'includes'.CC_DS.'boxes'.CC_DS.'randomProd.inc.php';
//$body->assign('RANDOM_PROD',$box_content);

//Javascript Slideshow for CC4 by MarksCarts, http//cc3.biz
// $hp_ss_config = fetchDbConfig("Homepage_Slideshow");
// if ($hp_ss_config['status'] == 1) {
// 	require_once"includes".CC_DS."boxes".CC_DS."slides_js.inc.php";
// 	$body->assign("SLIDES_JS",$box_content);
// 	require_once"modules".CC_DS."3rdparty".CC_DS."Homepage_Slideshow".CC_DS."includes".CC_DS."homepage_slides.inc.php";
// 	$body->assign("HP_SLIDESHOW",$box_content);
// 	}
//Javascript Slideshow for CC4 by MarksCarts, http//cc3.biz

/* Data for basket message popup */
require_once('includes'.CC_DS.'boxes'.CC_DS.'recentlyAddedItems.inc.php');
$body->assign('ADDED_ITEMS',$box_content);
/* Data for basket message popup */

// require_once'includes'.CC_DS.'boxes'.CC_DS.'info.inc.php';
// $body->assign('INFORMATION',$box_content);

//require_once'includes'.CC_DS.'boxes'.CC_DS.'language.inc.php';
//$body->assign('LANGUAGE',$box_content);

//require_once'includes'.CC_DS.'boxes'.CC_DS.'currency.inc.php';
//$body->assign('CURRENCY',$box_content);

require_once'includes'.CC_DS.'boxes'.CC_DS.'shoppingCart.inc.php';
$body->assign('SHOPPING_CART',$box_content);

require_once'modules'.CC_DS.'3rdparty'.CC_DS.'Free_Standard_Delivery_Info'.CC_DS.'boxes'.CC_DS.'free_standard_delivery_info.inc.php';
$body->assign('DELIVERY_INFO_BOX',$box_content);

// require_once'modules'.CC_DS.'3rdparty'.CC_DS.'socialMediaBox'.CC_DS.'includes'.CC_DS.'boxes'.CC_DS.'socialMediaBox.inc.php';
// $body->assign('SOCIAL_MEDIA_BOX',$box_content);

//require_once'includes'.CC_DS.'boxes'.CC_DS.'popularProducts.inc.php';
//$body->assign('POPULAR_PRODUCTS',$box_content);

// require_once'includes'.CC_DS.'boxes'.CC_DS.'saleItems.inc.php';
// $body->assign('SALE_ITEMS',$box_content);

// require_once'includes'.CC_DS.'boxes'.CC_DS.'facebook.inc.php';
// $body->assign('FACEBOOK',$box_content);

require_once'includes'.CC_DS.'boxes'.CC_DS.'mailList.inc.php';
$body->assign('MAIL_LIST',$box_content);

require_once'includes'.CC_DS.'boxes'.CC_DS.'siteDocs.inc.php';
$body->assign('SITE_DOCS',$box_content);

require_once'includes'.CC_DS.'boxes'.CC_DS.'skin.inc.php';
$body->assign('SKIN',$box_content);

//require_once"includes".CC_DS."boxes".CC_DS."Menu1.inc.php";
//$body->assign("MENU1",$box_content);

//require_once"includes".CC_DS."boxes".CC_DS."Menu2.inc.php";
//$body->assign("MENU2",$box_content);

//require_once"includes".CC_DS."boxes".CC_DS."Menu3.inc.php";
//$body->assign("MENU3",$box_content);

//require_once"includes".CC_DS."boxes".CC_DS."Menu4.inc.php";
//$body->assign("MENU4",$box_content);

//require_once"includes".CC_DS."boxes".CC_DS."Menu5.inc.php";
//$body->assign("MENU5",$box_content);

//require_once"includes".CC_DS."boxes".CC_DS."Menu6.inc.php";
//$body->assign("MENU6",$box_content);

## START product brands mod by KissMyCart.net
// require_once"includes".CC_DS."boxes".CC_DS."productbrands.inc.php";
// $body->assign("PRODUCT_BRANDS",$box_content);
## END product brands mod by KissMyCart.net 



## END CONTENT BOXES

## START  MAIN CONTENT
if (!empty($_GET['_a'])) {
	#if ($_GET['_a'] == 'search') $_GET['_a'] = 'viewCat';
	if (file_exists('includes'.CC_DS.'content'.CC_DS.sanitizeVar($_GET['_a']).'.inc.php')) {
		require_once('includes'.CC_DS.'content'.CC_DS.sanitizeVar($_GET['_a']).'.inc.php');
	} else {
		require_once('includes'.CC_DS.'content'.CC_DS.'index.inc.php');
	}
} else {
	require_once('includes'.CC_DS.'content'.CC_DS.'index.inc.php');
}

## END MAIN CONTENT



## START META DATA
if (isset($meta)) {
	$meta['title'] = sefMetaTitle();
	$meta['description'] = sefMetaDesc();
	$meta['keywords'] = sefMetaKeywords();

} else {
	$meta['title'] = str_replace("&#39;","'",$config['siteTitle']);
	$meta['description'] = $config['metaDescription'];
	$meta['keywords'] = $config['metaKeyWords'];
}

$body->assign('META_TITLE', stripslashes($meta['title']));
$body->assign('META_DESC', stripslashes($meta['description']));
$body->assign('META_KEYWORDS', stripslashes($meta['keywords']));
?>