<?
$sh_param = array(
	"exceptions" => 1,
	"trace" => 1,
	'login'    =>    'wsdl',
	'password'    =>    'wsdl');

$client = new SoapClient("http://213.141.157.162:1331/db_erp/ws/ssok_srv.1cws?wsdl",$sh_param);

ini_set("soap.wsdl_cache_enabled", "0");

$res = $client->GetProductsTypes(array('Input' => ''));
$res2 = $client->GetProductsTypes(array('Input' => 'd'));

$brends = $client->GetBrands(array('TypeID' => '164eb2b0-9259-11db-9032-0016171cc02f'));
echo '<pre>'; print_r($brends);echo '</pre>';