<?
$sh_param = array(
	"exceptions" => 1,
	"trace" => 1,
	'login'    =>    'wsdl',
	'password'    =>    'wsdl');

$client = new SoapClient("http://213.141.157.162:1331/db_erp/ws/ssok_srv.1cws?wsdl",$sh_param);

ini_set("soap.wsdl_cache_enabled", "0");

/*$res = $client->GetProductsTypes(array('Input' => ''));
$res2 = $client->GetProductsTypes(array('Input' => 'd'));*/
//$params = array('TypeID' => '', 'FirstLetter' => 'A');
//$params = array('id' => '164eb291-9259-11db-9032-0016171cc02f', 'price_id' => '96cb233d-7bb0-11e5-b1b8-005056c00008', 'priceGroupDetal' => false, 'agreement_id' => '96cb233d-7bb0-11e5-b1b8-005056c00008', 'Brand' => '');
//$brends = $client->GetBrands($params);
$params_i = array('id' => '', 'price_id' => '151a87ab-d640-11e6-80e8-0cc47ac4f649', 'priceGroupDetal' => false, 'agreement_id' => '151a87ab-d640-11e6-80e8-0cc47ac4f649', 'Brand' => 'Gefest');
$items = $client->GetProductList($params_i);

//echo '<pre>'; print_r($params);echo '</pre>';
//echo '<pre>'; print_r($brends);echo '</pre>';
echo '<pre>'; print_r($items);echo '</pre>';