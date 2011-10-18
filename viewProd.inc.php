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
|	viewProduct.inc.php
|   ========================================
|	Displays the Product in Detail
+--------------------------------------------------------------------------
*/

/*

$prodArray[0]['WSL']==1 checks if it is a "while stocks last" product
$prodArray[0]['WSL']==2 checks if it is a non-stocked product

*/

if(!defined('CC_INI_SET')){ die("Access Denied"); }

// include lang file
$lang = getLang('includes'.CC_DS.'content'.CC_DS.'viewProd.inc.php');

// query database
$_GET['productId'] = (int)sanitizeVar($_GET['productId']);

$query = "SELECT I.*, C.cat_name, C.cat_father_id FROM ".$glob['dbprefix']."CubeCart_inventory AS I LEFT JOIN ".$glob['dbprefix']."CubeCart_category AS C ON I.cat_id = C.cat_id WHERE I.disabled='0' AND I.productId = ".$db->mySQLSafe($_GET['productId']);

$prodArray = $db->select($query);

$view_prod = new XTemplate ('content'.CC_DS.'viewProd.tpl');

require('modules'.CC_DS.'3rdparty'.CC_DS.'Capitalizer'.CC_DS.'classes'.CC_DS.'capitalizer.php');
require('modules'.CC_DS.'3rdparty'.CC_DS.'printProduct'.CC_DS.'classes'.CC_DS.'printProductClass.php');

if ($prodArray) {

	$meta['metaDescription'] = substr(strip_tags($prodArray[0]['description']), 0, 165);

	if($config['seftags']){

		// get the native language for the category name
		$resultForeign = $db->select("SELECT cat_master_id as cat_id, cat_name FROM ".$glob['dbprefix']."CubeCart_cats_lang WHERE cat_lang = '".LANG_FOLDER."' AND cat_master_id = ".$db->mySQLSafe($prodArray[0]['cat_id']));

		if($resultForeign){
			$prodArray[0]['cat_name'] = $resultForeign[0]['cat_name'];
		}

		// get alternate language title for this product
		$sefval = '';
		$sefLangProdTitle = $prodArray[0]['name'];
		if (($sefval = prodAltLang($prodArray[0]['productId'])) !== false){
			// change the titles as they make more sense to the user if they are in their native languages
			// however to the search engine they will always be in the default language regardless as they can't change languages
			$sefLangProdTitle = $sefval['name'];
			// don't worry about description it will always be in the default language regardless as search
			// engines can't change languages
		}

		// create title and metas
		$prevDirSymbol = $config['dirSymbol'];
		$config['dirSymbol'] = ' - ';

		if($config['sefprodnamefirst']){
			$meta['siteTitle'] = $sefLangProdTitle.$config['dirSymbol'].getCatDir($prodArray[0]['cat_name'],$prodArray[0]['cat_father_id'], $prodArray[0]['cat_id'], false, true, FALSE);
		} else {
			$meta['siteTitle'] = getCatDir($prodArray[0]['cat_name'],$prodArray[0]['cat_father_id'], $prodArray[0]['cat_id'], false, true).$config['dirSymbol'].$sefLangProdTitle;
		}
		$config['dirSymbol'] = $prevDirSymbol;
		$meta['sefSiteTitle'] = $prodArray[0]['prod_metatitle'];
		$meta['sefSiteTitle'] = $phrases->capitalize($meta['sefSiteTitle']);
		$meta['sefSiteDesc'] = $prodArray[0]['prod_metadesc'] ? $prodArray[0]['prod_metadesc'] : $meta['metaDescription'];
		$meta['sefSiteKeywords'] = $prodArray[0]['prod_metakeywords'];
	} else {
		$meta['siteTitle'] = $config['siteTitle'].' - '.$prodArray[0]['name'];
	}

	$val = '';

	if(($val = prodAltLang($prodArray[0]['productId'])) !== false) {

		$prodArray[0]['name'] = $val['name'];
		$prodArray[0]['description'] = $val['description'];

	}

	// update amount of views
	$upPop['popularity'] = 'popularity+1';
	$db->update($glob['dbprefix'].'CubeCart_inventory',$upPop,'productId = '.$db->mySQLSafe($_GET['productId']));

	if(isset($_GET['notice']) && $_GET['notice']==1) {
		$view_prod->assign('LANG_OPTS_NOTICE',$lang['viewProd']['opts_notice']);
		$view_prod->parse('view_prod.prod_true.opts_notice');
	}

	$view_prod->assign('LANG_PRODTITLE', $lang['viewProd']['product']);
	$view_prod->assign('LANG_PRODINFO',$lang['viewProd']['product_info']);
	$view_prod->assign('LANG_FIRST_TO_REVIEW',$lang['viewProd']['first_to_review']);
	$view_prod->assign('LANG_PRICE',$lang['viewProd']['price']);
	$view_prod->assign('LANG_PRODCODE',$lang['viewProd']['product_code']);
	$view_prod->assign('LANG_TELLFRIEND',$lang['viewProd']['tellafriend']);
	$view_prod->assign('LANG_READ_REVIEWS',$lang['viewProd']['read_reviews']);
	$view_prod->assign('LANG_WRITE_REVIEWS',$lang['viewProd']['write_reviews']);
	
	$product_name = $prodArray[0]['name'];
	
	// $after_symbol_lowercase_letter = '/(\s[A-Z][a-z]\s?\b)|(\s\W[a-z])/';
			
	// $product_name = preg_replace_callback($after_symbol_lowercase_letter, create_function('$matches','return strtoupper($matches[0]);'),$product_name);

	$product_name = $phrases->capitalize($product_name);
	
	$view_prod->assign('TXT_PRODTITLE', $product_name);
	
	
	$view_prod->assign('TXT_DESCRIPTION',$prodArray[0]['description']);
	## START product brands mod by KissMyCart.net -->
	require("modules".CC_DS."3rdparty".CC_DS."KissMyCart_Brands_Mod".CC_DS."viewProd.inc.php"); 
	## END product brands mod by KissMyCart.net -->
	
	$filename = 'videos/' . $prodArray[0]['productCode'] . '.mov'; 

	if (file_exists($filename)) {
	 
	 $productVideo = $productVideo . '<a class="video_icon" href="' . $filename . '" target="_blank">
	 <img src="skins/KitaBlue/styleImages/video_icon.jpg" border="0" alt="Last Modified: ' . 
	 date("F, Y", $last_modified) . '" /></a>';
	 
	 $productVideo = $productVideo . '<a class="video_text" href="' . $filename . '" 
	 target="_blank" class="txtDefault">Product Video</a>';
	 
	 $view_prod->assign("PRODUCT_VIDEO",$productVideo);
	
	}
	
	require('modules'.CC_DS.'3rdparty'.CC_DS.'product_pdf'.CC_DS.'productPDF.php');
	$product_pdf = new productPDF($prodArray[0]['productCode']);
	$pdf_links = $product_pdf->generate_catalogue_links();
	// var_dump( $product_pdf->check_symbols( $prodArray[0]['productCode'] ) );
	$view_prod->assign("PRODUCT_PDF",$pdf_links);
	$view_prod->assign("PRODUCT_PDF_CLASS", 'productPDF');

	$excluded = array('add'=>1,'quan'=>1,'notice'=>1,'added'=>1);
	$view_prod->assign('CURRENT_URL', currentPage($excluded));
	
	if ($prodArray[0]['unitDescription']) {
	
	$lowercase_unit_desc = strtolower($prodArray[0]['unitDescription']);
	
	if (preg_match('[^pk.]', $lowercase_unit_desc)) {
	
	$output = preg_replace('[^pk]', 'Pack of ', $lowercase_unit_desc);	
	
	$view_prod->assign('UNIT_DESCRIPTION', " per " . $output);
	
	}
	
	else {
	
	$output = preg_replace('[^pk]', 'Pack', $lowercase_unit_desc);	
	
	$view_prod->assign('UNIT_DESCRIPTION', " per " . $output);
	
	
	}
	

	if (!salePrice($prodArray[0]['price'], $prodArray[0]['sale_price']) || !$config['saleMode']) {
		$view_prod->assign('TXT_PRICE_VIEW', priceFormat($prodArray[0]['price'],true));
	} else {
		$view_prod->assign('TXT_PRICE_VIEW',"<span class='txtOldPrice'>".priceFormat($prodArray[0]['price'],true).'</span>');
	}

	if (salePrice($prodArray[0]['price'], $prodArray[0]['sale_price'])) {
	if($prodArray[0]['sale_price'] >= 100) {
		
	$view_prod->assign('FREE_DELIVERY_ITEM', '<li class="free_delivery_item">Free UK Mainland Delivery <img class="free_delivery_tick"></li>');

	}

	}

	else {
		
	if($prodArray[0]['price'] >= 100) {
		
	$view_prod->assign('FREE_DELIVERY_ITEM', '<li class="free_delivery_item">Free UK Mainland Delivery <img class="free_delivery_tick"></li>');

	}

	}

	$salePrice = salePrice($prodArray[0]['price'], $prodArray[0]['sale_price']);
	$view_prod->assign('TXT_SALE_PRICE_VIEW', priceFormat($salePrice,true));
	$view_prod->assign('TXT_PRODCODE', $prodArray[0]['productCode']);
	$view_prod->assign('LANG_HOME', $lang['viewProd']['home']);
	$view_prod->assign('CURRENT_DIR',getCatDir($prodArray[0]['cat_name'],$prodArray[0]['cat_father_id'], $prodArray[0]['cat_id'],true).$config['dirSymbol'].$product_name);

	$view_prod->assign('LANG_QUAN',$lang['viewProd']['quantity']);

	$view_prod->assign('PRODUCT_ID',$prodArray[0]['productId']);

	if (!empty($prodArray[0]['image'])) {
		$imgRelPath = imgPath($prodArray[0]['image'], false, 'rel');
		$view_prod->assign('IMG_SRC', $imgRelPath);
	} else {
		$view_prod->assign('IMG_SRC', 'skins/'. SKIN_FOLDER . '/styleImages/nophoto.gif');
	}

	if ($prodArray[0]['noImages']>0) {
	
		// start mod: Product Images with Javascript Preview, by Estelle
		$pi_mod = fetchDbConfig("Product_Images");
		if ($pi_mod && $pi_mod['status']) {
			$pi_parse_base = "view_prod.prod_true.javascript_gallery";
			include('modules'.CC_DS.'3rdparty'.CC_DS.'Product_Images'.CC_DS.'viewprod1.inc.php');
		}
		else
		
		// end mod: Product Images with Javascript Preview, by Estelle


		if (!isset($config['imgGalleryType']) || !$config['imgGalleryType']) {
			$view_prod->assign('LANG_MORE_IMAGES',$lang['viewProd']['popup_more_images']);
			$view_prod->parse('view_prod.prod_true.popup_gallery');

		} else if ($config['imgGalleryType']) {

			$imgs = $db->select('SELECT `img` FROM '.$glob['dbprefix'].'CubeCart_img_idx WHERE `productId` = '.$db->mySQLsafe($prodArray[0]['productId']).' ORDER BY `id` ASC');
			if ($imgs) {
				foreach ($imgs as $img) {
					$thumbRootPath	= imgPath($img['img'], true, 'root');
					$thumbRelPath	= imgPath($img['img'], true, 'rel');

					if (file_exists($thumbRootPath)) {
						$sizeThumb	= getimagesize($thumbRootPath);
						$view_prod->assign('VALUE_THUMB_SRC', $thumbRelPath);
						$view_prod->assign('VALUE_THUMB_WIDTH', $sizeThumb[0]);

					} else {
						$view_prod->assign('VALUE_THUMB_SRC', imgPath($img['img'], false, 'rel'));
						$view_prod->assign('VALUE_THUMB_WIDTH', $config['gdthumbSize']);
					}

					$view_prod->assign('VALUE_IMG_SRC', imgPath($img['img'], false, 'rel'));
					$view_prod->assign('ALT_THUMB', $lang['viewProd']['thumb_alt']);
					$view_prod->parse('view_prod.prod_true.image_gallery.img_repeat');
				}
				$view_prod->assign('IMAGE_GALLERY', $lang['viewProd']['image_gallery']);
				$view_prod->parse('view_prod.prod_true.image_gallery');
			}
		}
	}

	## Review stars
	$commentQuery = sprintf('SELECT COUNT(`id`) as noComments FROM %sCubeCart_reviews WHERE `approved` = 1 AND `productId` = %d', $glob['dbprefix'], $prodArray[0]['productId']);
	$comments = $db->select($commentQuery);

	if ($comments[0]['noComments'] >= 1) {
		$reviewQuery = 'SELECT COUNT(`id`) AS noReviews, AVG(`rating`) as aveRating FROM `'.$glob['dbprefix'].'CubeCart_reviews` WHERE `type` = 0 AND `approved` = 1 AND `productId` ='.$db->mySQLsafe($prodArray[0]['productId']);
		$review = $db->select($reviewQuery);

		if ($review[0]['noReviews'] >= 1) {

			for ($i=0; $i<5; ++$i) {
				$view_prod->assign('VAL_STAR', starImg($i,$review[0]['aveRating']));
				$view_prod->parse('view_prod.prod_true.reviews_true.review_stars');
			}
			$view_prod->assign('LANG_BASED_ON_X_REVIEWS', sprintf($lang['viewProd']['based_on_x_reviews'], $review[0]['noReviews']));
		}
		$view_prod->parse('view_prod.prod_true.reviews_true');
		$view_prod->parse('view_prod.prod_true.read_reviews');
	} else {

		$view_prod->parse('view_prod.prod_true.reviews_false');
	}
	
	$view_prod->assign('LANG_DIR_LOC', $lang['viewProd']['location']);
	
	// if it is a plywood product
	
	if (!$prodArray[0]['disabled'] && $prodArray[0]['useStockLevel']==2) {
	
	$view_prod->assign('OWN_DELIVERY', $lang['viewProd']['own_delivery']);
	$view_prod->parse('view_prod.prod_true.own_delivery');
	
	}
	
	// if we are not planning to stock that product in the future.
			
	else if ($prodArray[0]['WSL']==1 && $prodArray[0]['stock_level']<1 && $prodArray[0]['useStockLevel'] && !$prodArray[0]['digital']) {
		
	$message = "Item discontinued.";
	$view_prod->assign('TXT_OUTOFSTOCK', $message);
		
	}
	
	else if ($prodArray[0]['WSL']==1 && $prodArray[0]['stock_level']>0 && $prodArray[0]['useStockLevel'] && !$prodArray[0]['digital']) {
		
	$message = "Item discontinued, available while stocks last.<br><br>Current stock level for this item: " . $prodArray[0]['stock_level'];
	$view_prod->assign('TXT_OUTOFSTOCK', $message);
	$view_prod->assign('BTN_ADDBASKET', $lang['viewProd']['add_to_basket']);
	$view_prod->parse('view_prod.prod_true.buy_btn');
		
	}
	
	// if it is a Hafele product
	
	else if ($prodArray[0]['stock_level']<1 && $prodArray[0]['useStockLevel'] == 1 && !$prodArray[0]['digital'] && $prodArray[0]['WSL']==2) {
	
	$message = "Availability: 3-5 days from order";
	$view_prod->assign('TXT_OUTOFSTOCK', $message);
	$view_prod->assign('BTN_ADDBASKET', $lang['viewProd']['add_to_basket']);
	$view_prod->parse('view_prod.prod_true.buy_btn');
		
	}
	
	// if we don't currently have the procuct in stock but will have soon
	
	else if ($prodArray[0]['useStockLevel'] && $prodArray[0]['stock_level'] < 1) {
	
	$message = "Availability: 3-5 days from order";
	$view_prod->assign('TXT_OUTOFSTOCK', $message);
	$view_prod->assign('BTN_ADDBASKET', $lang['viewProd']['add_to_basket']);
	$view_prod->parse('view_prod.prod_true.buy_btn');
	
	}
	
	// if we have this product in stock
	
	else if ($prodArray[0]['useStockLevel'] && $prodArray[0]['stock_level']>0) {
	
	$view_prod->assign('TXT_INSTOCK',$lang['viewProd']['instock']);
	$view_prod->assign('BTN_ADDBASKET', $lang['viewProd']['add_to_basket']);
	$view_prod->parse('view_prod.prod_true.buy_btn');
	
	}
	
	// In any other case, we will show the product as being in stock and allow purchases.
	
	else {
	
	$view_prod->assign('TXT_INSTOCK',$lang['viewProd']['instock']);
	$view_prod->assign('BTN_ADDBASKET', $lang['viewProd']['add_to_basket']);
	$view_prod->parse('view_prod.prod_true.buy_btn');
	
	}
	
	// Add carriage charges link 
	
	$carriageCharges = "<a onClick=\"return false;\"class=\"viewProdCarriageChargesLink\"  href=\"".$glob['storeURL']."/carriage-charges/info_7.html\">Carriage Charges</a>";
	$view_prod->assign('CARRIAGE_CHARGES', $carriageCharges);
	// Add carriage charges link
	
	
	//Print product page
	
	//Assign first gif
	
	$print_img_path['root'] = CC_ROOT_DIR . CC_DS . 'images' . CC_DS . 'uploads' . CC_DS;
	
	$print_img_path['rel'] = $glob['storeURL'] . CC_DS . 'images' . CC_DS. 'uploads' . CC_DS;
	
	$print_img = $prodArray[0]['image'];
	
	$first_gif = $printProduct->generatePath( '.gif', $print_img, $print_img_path );
	
	$view_prod->assign( 'FIRST_GIF', $first_gif );
	
	//Assign first gif
	
	
	//Assign second gif
	
	$second_gif = $printProduct->generatePath( '_1.gif', $print_img, $print_img_path );

	$view_prod->assign( 'SECOND_GIF', $second_gif );
	
	//Assign second gif
	
	
	//Assign second jpg
	
	$second_jpg = $printProduct->generatePath( '_1.jpg', $print_img, $print_img_path );
	
	$view_prod->assign( 'SECOND_JPG', $second_jpg );

	//Assign second jpg
	
	
	//Assign third jpg
	
	$third_jpg = $printProduct->generatePath( '_2.jpg', $print_img, $print_img_path );
	
	$view_prod->assign( 'THIRD_JPG', $third_jpg );
	
	//Assign third jpg
	
	
	$view_prod->assign('PRINT_NAME', urlencode($prodArray[0]['name']));
	$view_prod->assign('PRINT_PRODCODE', urlencode($prodArray[0]['productCode']));
	$view_prod->assign('PRINT_DESCRIPTION', urlencode($prodArray[0]['description']));
	$view_prod->assign('PRINT_IMAGE', urlencode($glob['storeURL']."/images/uploads/".$prodArray[0]['image']));
	$view_prod->assign('PRINT_CATEGORY', $prodArray[0]['cat_name']);
	$view_prod->assign('PRINT_PRICE', urlencode(priceFormat($prodArray[0]['price'],true)));

	$view_prod->parse('view_prod.prod_true.print_product');

	//Print product page
	

	## Build SQL for product options
	$query		= sprintf("SELECT B.*, T.option_name, T.option_type, M.value_name FROM %1\$sCubeCart_options_bot AS B LEFT JOIN %1\$sCubeCart_options_mid AS M ON B.value_id = M.value_id, %1\$sCubeCart_options_top AS T WHERE B.option_id = T.option_id AND B.product = %2\$d ORDER BY T.option_name, M.value_name ASC", $glob['dbprefix'], $_GET['productId']);
	$options	= $db->select($query);

	if ($options) {
		$view_prod->assign('TXT_PROD_OPTIONS', $lang['viewProd']['prod_opts']);
		foreach ($options as $option) {
			if (!empty($option)) {
				if ($option['option_type'] == '0') {
					$selectArray[$option['option_id']][] = $option;
				} else {
					$inputArray[$option['option_id']][] = $option;
				}
			}
		}

		## Select based options
		if ($selectArray) {
			foreach ($selectArray as $option_id => $option) {
				foreach ($option as $values) {
					$view_prod->assign('VAL_OPTION_ID', $values['option_id']);
					$view_prod->assign('VAL_ASSIGN_ID', $values['assign_id']);

					$view_prod->assign('VAL_VALUE_NAME', htmlentities(stripslashes(str_replace(array('&#39;', '&amp;#39;'), "'", utf8_decode($values['value_name'])))));

					if ($values['option_price']>0) {
						$view_prod->assign('VAL_OPT_SIGN', $values['option_symbol']);
						$view_prod->assign('VAL_OPT_PRICE', priceFormat($values['option_price'], true));
						$view_prod->parse('view_prod.prod_true.prod_opts.repeat_options.repeat_values.repeat_price');
					}
					$view_prod->parse('view_prod.prod_true.prod_opts.repeat_options.repeat_values');
				}
				$view_prod->assign('VAL_OPTS_NAME', $option[0]['option_name']);
				$view_prod->parse('view_prod.prod_true.prod_opts.repeat_options');
			}
		}

		## Text-based options
		if ($inputArray) {
			foreach ($inputArray as $option_id => $option) {
				foreach ($option as $value) {

					$view_prod->assign('VAL_VALUE_NAME', htmlentities(stripslashes($value['value_name'])));

					$view_prod->assign('VAL_ASSIGN_ID', $value['assign_id']);
					$view_prod->assign('VAL_OPTION_ID', $value['option_id']);

					if ($value['option_price']>0) {
						$view_prod->assign('VAL_OPT_SIGN', $value['option_symbol']);
						$view_prod->assign('VAL_OPT_PRICE', priceFormat($value['option_price'], true));
						$view_prod->parse('view_prod.prod_true.prod_opts.text_opts.repeat_price');
					}

					if ($value['option_type'] == 2) {
						$view_prod->parse('view_prod.prod_true.prod_opts.text_opts.textarea');
					} else {
						$view_prod->parse('view_prod.prod_true.prod_opts.text_opts.textbox');
					}
				}
				$view_prod->assign('VAL_OPTS_NAME', $option[0]['option_name']);
				$view_prod->parse('view_prod.prod_true.prod_opts.text_opts');
			}
		}
		$view_prod->parse('view_prod.prod_true.prod_opts');
	}

	// start product reviews/comment
	if (isset($_GET['review']) && $_GET['review']=='write' && $prodArray) {

		$reviewResult = false;

		if (isset($_POST['review'])) {

			// start validation
			if($config['floodControl']=='recaptcha') {
				$response = recaptcha_check_answer(	$ini['recaptcha_private_key'],
											$_SERVER['REMOTE_ADDR'],
											$_POST['recaptcha_challenge_field'],
											$_POST['recaptcha_response_field']);
			} elseif($config['floodControl']==1) {
				$spamCode = fetchSpamCode($_POST['ESC'], true);
			}

			if($config['floodControl']=='recaptcha' && !$response->is_valid) {
				$view_prod->assign('VAL_ERROR',$lang['viewProd']['code_error']);
				$view_prod->parse('view_prod.prod_true.write_review.error');
			} elseif ($config['floodControl']==1 && !isset($_POST['review']['spambot']) || ($config['floodControl']==1 && $spamCode['SpamCode']!=strtoupper($_POST['review']['spambot'])) || ($config['floodControl']==1 && get_ip_address()!==$spamCode['userIp'])) {
				$view_prod->assign('VAL_ERROR',$lang['viewProd']['code_error']);
				$view_prod->parse('view_prod.prod_true.write_review.error');

			} elseif (empty($_POST['review']['name']) || empty($_POST['review']['email']) || empty($_POST['review']['title']) || empty($_POST['review']['review'])) {
				$view_prod->assign('VAL_ERROR',$lang['viewProd']['empty_fields']);
				$view_prod->parse('view_prod.prod_true.write_review.error');

			} else if (!validateEmail($_POST['review']['email'])) {
				$view_prod->assign('VAL_ERROR',$lang['viewProd']['invalid_email']);
				$view_prod->parse('view_prod.prod_true.write_review.error');

			} else {
				$data['productId'] = $db->MySQLSafe($_GET['productId']);
				$data['type'] = $db->MySQLSafe($_POST['review']['type']);
				$data['rating'] = $db->MySQLSafe($_POST['review']['rating']);
				$data['name'] = $db->MySQLSafe(htmlentities(stripslashes($_POST['review']['name']),ENT_QUOTES,'UTF-8'));
				$data['email'] = $db->MySQLSafe($_POST['review']['email']);
				$data['title'] = $db->MySQLSafe(htmlentities(stripslashes($_POST['review']['title']),ENT_QUOTES,'UTF-8'));
				$data['review'] = $db->MySQLSafe(htmlentities(stripslashes($_POST['review']['review']),ENT_QUOTES,'UTF-8'));
				/*
				$data['title'] = $db->MySQLSafe(strip_tags(get_magic_quotes_gpc () ? stripslashes($_POST['review']['title']) : $_POST['review']['title']));
				$data['review'] = $db->MySQLSafe(strip_tags(get_magic_quotes_gpc () ? stripslashes($_POST['review']['review']) : $_POST['review']['review']));
				*/
				$data['ip'] = $db->MySQLSafe(get_ip_address());
				$data['time'] = time();

				$reviewResult = $db->insert($glob['dbprefix'].'CubeCart_reviews', $data);
				$reviewId = $db->insertid();

				$view_prod->assign('VAL_SUCCESS',$lang['viewProd']['submit_success']);
				$view_prod->parse('view_prod.prod_true.write_review.success');


				// notify store owner
				require('classes'.CC_DS.'htmlMimeMail'.CC_DS.'htmlMimeMail.php');

				$mail = new htmlMimeMail();

				$lang = getLang('email.inc.php');

				$macroArray = array(

					'AUTHOR_NAME' => sanitizeVar($_POST['review']['name']),
					'AUTHOR_EMAIL' => sanitizeVar($_POST['review']['email']),
					'SENDER_ID' => sanitizeVar(get_ip_address()),
					'PRODUCT_NAME' => sanitizeVar($prodArray[0]['name']),
					'RATING' => sanitizeVar($_POST['review']['rating']),
					'REVIEW_TITLE' => sanitizeVar(strip_tags(stripslashes($_POST['review']['title']))),
					'REVIEW_COPY' => sanitizeVar(strip_tags(stripslashes($_POST['review']['review']))),
					'APPROVE_URL' => $glob['storeURL'].'/'.$glob['adminFile'].'?_g=reviews/index&approved=1&id='.$reviewId,
					'DECLINE_URL' => $glob['storeURL'].'/'.$glob['adminFile'].'?_g=reviews/index&approved=0&id='.$reviewId

				);

				$text = stripslashes(macroSub($lang['email']['new_review_body'],$macroArray));
				unset($macroArray);

				$mail->setText($text);
				$mail->setReturnPath($config['masterEmail']);
				$mail->setFrom(sanitizeVar($_POST['review']['name']).' <'.sanitizeVar($_POST['review']['email']).'>');
				$mail->setSubject($lang['email']['new_review_subject']);
				$mail->setHeader('X-Mailer', 'CubeCart Mailer');
				$send = $mail->send(array($config['masterEmail']), $config['mailMethod']);

			}

		}

		$view_prod->assign('LANG_SUBMIT_REVIEW',$lang['viewProd']['submit_review']);

		if(!$reviewResult) {

			$view_prod->assign('LANG_SUBMIT_REVIEW_COMPLETE',$lang['viewProd']['submit_review_complete']);
			$view_prod->assign('LANG_REVIEW_TYPE',$lang['viewProd']['contrib_type']);
			$view_prod->assign('LANG_REVIEW',$lang['viewProd']['review']);
			$view_prod->assign('LANG_COMMENT',$lang['viewProd']['comment']);
			$view_prod->assign('LANG_RATING',$lang['viewProd']['rating']);
			$view_prod->assign('LANG_SPAMBOT',$lang['viewProd']['spambot']);
			$view_prod->assign('LANG_NAME',$lang['viewProd']['review_name']);
			$view_prod->assign('LANG_EMAIL',$lang['viewProd']['email']);
			$view_prod->assign('LANG_NOT_DISPLAYED',$lang['viewProd']['not_displayed']);
			$view_prod->assign('LANG_TITLE',$lang['viewProd']['title']);
			$view_prod->assign('LANG_DETAILS',$lang['viewProd']['review_details']);

			if(isset($_POST['review']['name']) && isset($_POST['review']['email'])) {
				$view_prod->assign('VAL_REV_NAME',sanitizeVar($_POST['review']['name']));
				$view_prod->assign('VAL_REV_EMAIL',sanitizeVar($_POST['review']['email']));
			} elseif($cc_session->ccUserData['customer_id']>0) {
				$view_prod->assign('VAL_REV_NAME',$cc_session->ccUserData['firstName'].' '.$cc_session->ccUserData['lastName']);
				$view_prod->assign('VAL_REV_EMAIL',$cc_session->ccUserData['email']);
			} else {
				$view_prod->assign('VAL_REV_NAME',$lang['viewProd']['anon']);
			}

			if(isset($_POST['review']) && $_POST['review']['type']==1) {
				$view_prod->assign('VAL_REV_TYPE_C_SELECTED','selected="selected"');
			} else {
				$view_prod->assign('VAL_REV_TYPE_R_SELECTED','selected="selected"');
			}

			if (isset($_POST['review'])) {
				$view_prod->assign('VAL_REV_TITLE',sanitizeVar($_POST['review']['title']));
				$view_prod->assign('VAL_REVIEW',nl2br(sanitizeVar(strip_tags($_POST['review']['review']))));
			}

			$view_prod->assign('VAL_ROOT_REL', $GLOBALS['rootRel']);

			if(isset($_POST['review']['rating']) && $_POST['review']['rating']>0) {

				for($i=0;$i<5;++$i){
					$view_prod->assign('VAL_STAR',starImg($i,$_POST['review']['rating']));
					$view_prod->assign('VAL_STAR_I',$i+1);
					$view_prod->parse('view_prod.prod_true.write_review.form.review_stars');
				}

				$view_prod->assign('VAL_RATING',sanitizeVar($_POST['review']['rating']));
			} else {

				for($i=0;$i<5;++$i){
					$view_prod->assign('VAL_STAR',starImg($i,0));
					$view_prod->assign('VAL_STAR_I',$i+1);
					$view_prod->parse('view_prod.prod_true.write_review.form.review_stars');
				}
				$view_prod->assign('VAL_RATING',0);
			}

			// Start Spam Bot Control
			if($config['floodControl']=='recaptcha') {
				$view_prod->assign('TXT_SPAMBOT', 'Spambot:');
				$recaptcha	= recaptcha_get_html($ini['recaptcha_public_key'], false, detectSSL());
				$view_prod->assign('DISPLAY_RECAPTCHA', $recaptcha);
				$view_prod->parse('view_prod.prod_true.write_review.form.recaptcha');
			} elseif($config['floodControl']) {

				$spamCode = strtoupper(randomPass(5));
				$ESC = createSpamCode($spamCode);

				$imgSpambot = imgSpambot($ESC);

				$view_prod->assign('VAL_ESC',$ESC);
				$view_prod->assign('TXT_SPAMBOT','Spambot:');
				$view_prod->assign('IMG_SPAMBOT',$imgSpambot);
				$view_prod->parse('view_prod.prod_true.write_review.form.spambot');
			}

			$view_prod->parse('view_prod.prod_true.write_review.form');
		}

		$view_prod->parse('view_prod.prod_true.write_review');
	}

	if (isset($_GET['review']) && $_GET['review']=='read' && $prodArray) {

		$page = (isset($_GET['page'])) ? sanitizeVar($_GET['page']) : 0;

		$reviews_query = 'SELECT * FROM `'.$glob['dbprefix'].'CubeCart_reviews` WHERE `approved`=1 AND `productId`='.$db->mySQLsafe($prodArray[0]['productId']).' ORDER BY `time` DESC';
		$reviews = $db->select($reviews_query, 10, $page);
		$totalNoReviews = $db->numrows($reviews_query);

		$reviewsPagination = paginate($totalNoReviews, 10, $page, 'page');

		if ($reviews) {
			$view_prod->assign('VAL_REVIEW_PAGINATION',$reviewsPagination);

			$view_prod->assign('LANG_REVIEWS_AND_COMMENTS',$lang['viewProd']['reviews_and_comments']);
			$view_prod->assign('LANG_BY',$lang['viewProd']['by']);

			for($i = 0, $maxi = count($reviews); $i < $maxi; ++$i) {

				if($reviews[$i]['type']==1){
					$view_prod->assign('LANG_TYPE',$lang['viewProd']['comment_colon']);
				} else{
					$view_prod->assign('LANG_TYPE','');
				}
				$view_prod->assign('VAL_REVIEW_TITLE', $reviews[$i]['title']);
				$view_prod->assign('VAL_REVIEW_NAME', $reviews[$i]['name']);
				$view_prod->assign('VAL_REVIEW_DATE', formatTime($reviews[$i]['time']));
				$view_prod->assign('VAL_REVIEW',$reviews[$i]['review']);

				if($reviews[$i]['type']==0) {
					for($j=0;$j<5;++$j) {
						$view_prod->assign('VAL_REVIEW_STAR',starImg($j,$reviews[$i]['rating']));
						$view_prod->assign('VAL_REVIEW_STAR_I',$j);
						$view_prod->parse('view_prod.prod_true.read_review.reviews_true.review_stars');
					}
				}

				$view_prod->parse('view_prod.prod_true.read_review.reviews_true');
			}

		} else {
			$view_prod->assign('LANG_REVIEWS_AND_COMMENTS',$lang['viewProd']['reviews_and_comments']);
			$view_prod->assign('LANG_NO_REVIEWS_MADE',$lang['viewProd']['no_reviews_made']);
			$view_prod->parse('view_prod.prod_true.read_review.reviews_false');
		}

		$view_prod->parse('view_prod.prod_true.read_review');
	}

	## start customers who bought this also bought...

	// if (version_compare('4.1', $db->serverVersion(), '<=')) {
	/* TOO HEAVY ON MYSQL SERVERS WITH LOADS OF ORDERS
	$query = sprintf("SELECT DISTINCT O.productId, I.name, I.image, I.price, I.sale_price FROM %1\$sCubeCart_order_inv AS O, %1\$sCubeCart_inventory AS I WHERE I.disabled = 0 AND I.productId = O.productId AND O.cart_order_id IN (SELECT DISTINCT cart_order_id FROM %1\$sCubeCart_order_inv WHERE productId = %2\$d) AND O.productId <> %2\$d LIMIT 3;", $glob['dbprefix'], $db->MySQLSafe($_GET['productId'], ''));

	$pastOrders = $db->select($query);
	*/
	
	$cat_id_by_prod_id = 
	'SELECT `cat_id` 
	FROM `'.$glob['dbprefix'].'CubeCart_inventory` 
	WHERE `productId` = '.$db->MySQLSafe($_GET['productId']);
	
	$rel_cat_id = $db->select($cat_id_by_prod_id);
			 
	$query = 'SELECT DISTINCT `cart_order_id` FROM `'.$glob['dbprefix'].'CubeCart_order_inv` WHERE `productId` = '.$db->MySQLSafe($_GET['productId'], '');
	$pastOrders = $db->select($query);
	if($pastOrders) {
		
		foreach($pastOrders as $pastOrder) {
			$query = "SELECT `productId`, `quantity` FROM `".$glob['dbprefix']."CubeCart_order_inv` WHERE `cart_order_id` = '".$pastOrder['cart_order_id']."' AND `productId` <> ".$db->MySQLSafe($_GET['productId'], '');
			$productIds = $db->select($query);
			if($productIds) {
				foreach($productIds as $product){
					## Fix for custom products in inventory with zero product id
					if($product['productId']>0) $related[$product['productId']] += $product['quantity'];
				}
			}
		}


		// if(isset($related) && is_array($related)) {
		// 	arsort($related,SORT_NUMERIC);
		// 	$i = 0;
		// 	$noRelatedProducts = count($related);		
			
		// 	$query = 
		// 	'SELECT `productId`, `image`, `price`, `sale_price`, `name` 
		// 	 FROM `'.$glob['dbprefix'].'CubeCart_inventory` 
		// 	 WHERE `disabled` = 0 
		// 	 AND cat_id = '.$rel_cat_id[0]['cat_id'].' 
		// 	 ORDER BY rand()'; 
			
			
			// AND (';
			// foreach($related as $productId => $salesVolume){
				// $query .= '`productId` = '.$productId;
				// if($i==($noRelatedProducts-1)) {
					// break;
				// } else {
				// $query .= ' OR ';
				// }
				// ++$i;
			// }
		// 	$config['noRelatedProds'] = ((int)$config['noRelatedProds'] > 0) ? $config['noRelatedProds'] : 3;
		// 	$query .= /*') */ ' LIMIT '.$config['noRelatedProds'];
		// 	$relatedProducts = $db->select($query);

		// 	if ($relatedProducts && !isset($_GET['review'])) {
		// 		for ($i = 0, $maxi = count($relatedProducts); $i < $maxi; ++$i) {
		// 			if (($val = prodAltLang($relatedProducts[$i]['productId'])) === true) {
		// 				$relatedProducts[$i]['name'] = $val['name'];
		// 			}
		// 			$thumbRootPath = imgPath($relatedProducts[$i]['image'], 1, 'root');
		// 			$thumbRelPath = imgPath($relatedProducts[$i]['image'], 1, 'rel');

		// 			if (file_exists($thumbRootPath) && !empty($relatedProducts[$i]['image'])) {
		// 				$view_prod->assign('VAL_IMG_SRC',$thumbRelPath);
		// 			} else {
		// 				$view_prod->assign('VAL_IMG_SRC',$GLOBALS['rootRel'].'skins/'. SKIN_FOLDER . '/styleImages/thumb_nophoto.gif');
		// 			}

		// 			if (!salePrice($relatedProducts[$i]['price'], $relatedProducts[$i]['sale_price'])) {
		// 				$view_prod->assign('TXT_PRICE', priceFormat($relatedProducts[$i]['price'], true));
		// 			} else {
		// 				$view_prod->assign('TXT_PRICE','<span class="txtOldPrice">'.priceFormat($relatedProducts[$i]['price'], true).'</span>');
		// 			}
		// 			$salePrice = salePrice($relatedProducts[$i]['price'], $relatedProducts[$i]['sale_price']);
		// 			$view_prod->assign('TXT_SALE_PRICE', priceFormat($salePrice, true));

		// 			$view_prod->assign('VAL_PRODUCT_ID', $relatedProducts[$i]['productId']);
		// 			$view_prod->assign('VAL_PRODUCT_NAME',validHTML($relatedProducts[$i]['name']));
		// 			$view_prod->parse('view_prod.prod_true.related_products.repeat_prods');
		// 		}
		// 		$view_prod->assign('LANG_RELATED_PRODUCTS',$lang['viewProd']['related_products']);
		// 		$view_prod->parse('view_prod.prod_true.related_products');
		// 	}

		// }
	}

	require_once('modules'.CC_DS.'3rdparty'.CC_DS.'related_products'.CC_DS.'boxes'.CC_DS.'related_products_box.inc.php');


	// if(!empty($related_products->related_products)) {

	$view_prod->assign('RELATED_PRODUCTS', $rel_prods);
	$view_prod->parse('view_prod.prod_true.related_products');
	// }

	
// 	else if ($rel_cat_id) {
		
// 	$rel_prods_query = 
// 	'SELECT `productId`, `image`, `price`, `sale_price`, `name` 
// 	FROM `'.$glob['dbprefix'].'CubeCart_inventory` 
// 	WHERE `disabled` = 0 
// 	AND cat_id = '.$rel_cat_id[0]['cat_id'].' AND productId != '.$db->MySQLSafe($_GET['productId']).' LIMIT '.$config['noRelatedProds'].'';

	
// 	$rel_prods = $db->select($rel_prods_query);
	
// 	if($rel_prods) {
	
// 	for ($i = 0, $maxi = count($rel_prods); $i < $maxi; ++$i) {
// 		if (($val = prodAltLang($rel_prods[$i]['productId'])) === true) {
// 			$rel_prods[$i]['name'] = $val['name'];
// 		}

// 		$thumbRootPath = imgPath($rel_prods[$i]['image'], 1, 'root');
// 		$thumbRelPath = imgPath($rel_prods[$i]['image'], 1, 'rel');

// 		if (file_exists($thumbRootPath) && !empty($rel_prods[$i]['image'])) {
// 			$view_prod->assign('VAL_IMG_SRC',$thumbRelPath);
// 		} else {
// 			$view_prod->assign('VAL_IMG_SRC',$GLOBALS['rootRel'].'skins/'. SKIN_FOLDER . '/styleImages/thumb_nophoto.gif');
// 		}

// 		if (!salePrice($rel_prods[$i]['price'], $rel_prods[$i]['sale_price'])) {
// 			$view_prod->assign('TXT_PRICE', priceFormat($rel_prods[$i]['price'], true));
// 		} else {
// 			$view_prod->assign('TXT_PRICE','<span class="txtOldPrice">'.priceFormat($rel_prods[$i]['price'], true).'</span>');
// 		}
// 		$salePrice = salePrice($rel_prods[$i]['price'], $rel_prods[$i]['sale_price']);
// 		$view_prod->assign('TXT_SALE_PRICE', priceFormat($salePrice, true));

// 		$view_prod->assign('VAL_PRODUCT_ID', $rel_prods[$i]['productId']);
// 		$view_prod->assign('VAL_PRODUCT_NAME',validHTML($rel_prods[$i]['name']));
// 		$view_prod->parse('view_prod.prod_true.related_products.repeat_prods');
// 	}
	
// 	$view_prod->assign('LANG_RELATED_PRODUCTS',$lang['viewProd']['related_products']);
// 	$view_prod->parse('view_prod.prod_true.related_products');
		
// 	}

// }
	// }
	$view_prod->parse('view_prod.prod_true');

} else {// end if product array is true
	$view_prod->assign('LANG_PRODUCT_EXPIRED',$lang['viewProd']['prod_not_found']);
	$view_prod->parse('view_prod.prod_false');
}

}

$view_prod->parse('view_prod');
$page_content = $view_prod->text('view_prod');
?>