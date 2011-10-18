<?php 

class Related_Products_Model {

	public $related_products;

	public static function get_related_productCodes( $productId = "" ) {

	global $db;
		
		$sql = "SELECT related FROM related_products_idx WHERE productId = " . $productId;

		return $db->select($sql);

	}

	public function get_related_products( $productCodes = "" ) {
		
	global $db;

		$sql = 
		"SELECT i.productId, i.productCode, i.name, i.price, i.sale_price, i.image, 
		i.unitDescription, c.cat_name, c.cat_father_id 
		FROM CubeCart_inventory as i
		JOIN CubeCart_category as c 
		ON i.cat_id = c.cat_id
		WHERE i.productCode IN ('" . $productCodes . "')";

		$related_prods = $db->select($sql);

		if(!$related_prods) {

		throw new Exception('No related products came back from the database. Query: ' . $sql);

		}

		else {

		return $related_prods;

		}
			
	}

	public function cat_father_by_id( $cat_id ) {

	global $db;
		
	$sql = 
	"SELECT cat_father_id, cat_name 
	FROM CubeCart_category 
	WHERE cat_id = $cat_id 
	LIMIT 1";

	if(!$cat_name = $db->select($sql)) {

	throw new Exception('No category in the database with id ' . $cat_id);

	}

	else {

	return $cat_name;

	}

	}

}

?>