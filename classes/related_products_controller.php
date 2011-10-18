<?php 
require_once('related_products_model.php');
require_once('modules'.CC_DS.'3rdparty'.CC_DS.'related_products'.CC_DS.'interfaces'.CC_DS.'related_products_interface.php');

class Related_Products_Controller extends Related_Products_Model implements Related_Products_Interface {


	public static $productId;

	public $related_productCodes = array();

	public $cs_product_code_string;

	public $related_products = array();

	public $product_link = array();

	public $errors = array();


	public function __construct() {
		
		if(isset($_GET['_a'])):

			if($_GET['_a'] == "viewProd"):

				$this->init();

			endif;

		endif;

	}

	private function init() {
		
		$this->set_product_id();

		$this->set_related_productCodes_property();

		$this->set_cs_product_codes_string();

		$this->set_related_products();

		$this->create_product_links();
		
	}

	public function set_product_id() {

		if(isset($_GET['productId'])) {
			
			self::$productId = $_GET['productId'];

		}

	}

	public function set_related_productCodes_property() {
		
		$this->related_productCodes = parent::get_related_productCodes( self::$productId );

	}

	public function array_to_string( $array ) {

		if( !is_array( $array ) ) {
			
		throw new Exception( 'related_productCodes property must be an array' );

		}

		else {

		foreach( $array as $rel_prod_codes ):

		$codes[] = $rel_prod_codes['related'];

		endforeach;
			
		$code_string = implode( "','", $codes );

		return $code_string;

		}

	}

	public function set_cs_product_codes_string() {

	try {
		
	$this->cs_product_code_string = $this->array_to_string( $this->related_productCodes );

	}

	catch (Exception $e) {

	$this->log_error( $e->getMessage() ); 

	}

	}

	public function set_related_products() {

	try {
		
	$this->related_products = parent::get_related_products( $this->cs_product_code_string );

	}

	catch( Exception $e ) {
		
	$this->log_error( $e->getMessage() );

	}

	}


	public function log_error( $error ) {
		
	$this->errors[] = $error; 

	}


	public function totalCount() {
		
	return count($this->related_productCodes);

	}

	private function create_product_links() {

	global $glob;
		
		foreach($this->related_products as $key => $product):

			$link[] = $glob['storeURL'];


			try {

			$father = $this->cat_father_by_id($product['cat_father_id']);

			}

			catch( Exception $e ) {
				
			$this->log_error( $e->getMessage() );

			}

			try {

			$grand_father = $this->cat_father_by_id($father[0]['cat_father_id']);

			}

			catch( Exception $e ) {
				
			$this->log_error( $e->getMessage() );

			}



				if($grand_father[0]['cat_name']) {

					$link[] = $grand_father[0]['cat_name'];

				}

				$link[] = $father[0]['cat_name'];

				$link[] = $product['cat_name'];

				$link[] = $product['name'];

				$link[] = "prod_" . $product['productId'] . ".html";

				$link_chain[] = implode('/', $link);

				unset($link);

		endforeach;

	$this->product_link = $link_chain;

	}



}

$related_products = new Related_Products_Controller();
	 
if( !empty( $related_products->errors ) ) {

echo "<pre>";

print_r( $related_products->errors );

echo "</pre>";

}


?>