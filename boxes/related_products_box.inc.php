<?php
// /*

// +--------------------------------------------------------------------------

// |	related_products.inc.php

// |   ========================================

// |	This module shows related products on product page.

// +--------------------------------------------------------------------------

// */

/* Import classes */

$module_name = "related_products";

require_once('modules'.CC_DS.'3rdparty'.CC_DS.$module_name.CC_DS.'classes'.CC_DS.$module_name.'_controller.php');

Related_Products_Controller::$productId = $_GET['productId'];

	$img_path = $glob['storeURL'] . "/images/uploads/";

	$rel_prods = new XTemplate ('boxes'.CC_DS.$module_name.'.tpl');

	$rel_prods->assign('IMAGE_PATH', $img_path);

	$count = $$module_name->totalCount();

	$products = $$module_name->$module_name;

		for($i=0; $i<=$count-1; $i++):

		$rel_prods->assign('IMG_PATH', $img_path);

		$rel_prods->assign('DATA', $products[$i]);

		$rel_prods->assign('LINK', $$module_name->product_link[$i]);

		$rel_prods->parse( $module_name .'.'. $module_name.'_loop' );

		endfor;

	$rel_prods->parse( $module_name );

	$rel_prods = $rel_prods->text( $module_name );

?>