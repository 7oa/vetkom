<?php

include $_SERVER['DOCUMENT_ROOT'] . '/core/loader/prolog_before.php';

use Core\Main\User,
    Core\Main\DataBase,
    Core\Main\Catalog,
    Core\Main\Template;

$data = $_POST;
$USER_ID = User::getID();
$arUser = User::getByID($USER_ID);
$price_id = $arUser["PRICE"];
$def_price = $arUser["SHOWDEFAULTPRICE"];
$group_det = "false"; //$arUser["PRICEGROUPDETAL"];
$agreement = $arUser["AGREEMENT"];
$USER_EXTERNAL_ID = $arUser["EXTERNAL"];
$type = $data['TYPE'];
$catalog = Catalog::getInstance();
$byID = array('id' => $data["id"], 'price_id' => $price_id);

switch ($data['TYPE']) {
    case 'list':
    	$params = array('id' => $data["id"], 'price_id' => $price_id, 'priceGroupDetal' => $group_det, 'agreement_id' => $agreement, 'Brand' => $data["brand"]);
        $products["ITEMS"] = $catalog->getResult('GetProductList', $params, true);
		$products['ID'] = $data["id"];
		$products['DEF_PRICE'] = $def_price;
        $products['CHECKED'] = $data['checked'];
        //проверка, добавлен ли товар в избранное
        $connect = DataBase::getConnection();
        foreach ($products["ITEMS"] as $key => &$oneProduct) {
			$oneProduct = (array) $oneProduct;


            $pId = $oneProduct['id'];
            $check_favorits = $connect->query("SELECT * FROM `favorits` WHERE `USER_ID` = '$USER_ID' AND `PRODUCT_ID` = '$pId'")->fetchRaw();
            if ($check_favorits == false)
                $oneProduct['favorits'] = 0;
            else
                $oneProduct['favorits'] = $check_favorits['TYPE'];

			//картинки
			if($oneProduct["img_mini"]){
				$img_id = $oneProduct["id"];
				$img = $oneProduct["img_mini"];
				$ext = $oneProduct["img_mini_ext"];
				$oneProduct["img_path"] = $catalog->checkPrevImage($img_id, $img, 'jpg');
			}

        }
		if(!$data['id']) $products["GROUPS"] = "Y";
		Template::includeTemplate('catalog_list', $products);
        break;
	case 'allBrends':
		$brends = $catalog->getResult('GetBrands', array("TypeID"=>$data['id'], "FirstLetter" =>$data['letter']));
		$products["BRENDS"] = $brends["Strings"];
		if($data["letter"]||$data["id"]){
			if ($data["id"]) $products["GROUP_ID"] = $data["id"];
			Template::includeTemplate('brands_let_list', $products);
		}
		else Template::includeTemplate('brands_list', $products);
		break;
    case 'analogs':
        $analogs = $catalog->getResult('GetSimilarProducts', array('id' => $data['id'], 'price_id' => $price_id, 'priceGroupDetal' => $group_det, 'agreement_id' => $agreement));
        if ($analogs == false)
            echo "У выбранного товара нет аналогов";
        else
            Template::includeTemplate('catalog_analogs', $analogs);
        break;
    case 'section':
    	$params = array('id' => $data["id"], 'Input' => '');
        $sections = $catalog->getResult('GetGroupList', $params);
        $arrg = array();
        foreach ($sections as $value) {
            $arrg[$value['name']][0] = $value;
//            $s = $catalog->getResult("GetGroupList", array('id' => $value['id']));
//            $arrg[$value['name']][0] = $value;
//            if (count($s) > 0) {
//                $arrg[$value['name']][1] = $s;
//            }
        }
        Template::includeTemplate('section', $arrg);
        break;
    case 'detail':
        $id = $data["id"];
        $findParams = array('req' => "code", 'value' => $id, 'price_id' => $price_id, 'priceGroupDetal' => $group_det, 'agreement_id' => $agreement);
        $soapCart = $catalog->getResult('GetProductDetails', $byID, true);
        //$find = $catalog->getResult('FindProductsBy', $findParams);
        $detailCard = $soapCart[0];
        //$detailCard['price'] = $find['Products']["price"];
        if (!empty($detailCard['img']))
            $detailCard['img'] = $catalog->checkDetailImage($detailCard);
        $skocks = $catalog->getResult('GetStocksByProduct', array('id' => $id));
        if ($skocks) {
            $detailCard["STOCK"] = $skocks["Stocks"];
        }
        Template::includeTemplate('catalog_item_detail', $detailCard);
        break;
    case 'search':
        $search = array('req' => $data["req"], 'value' => $data["value"], 'price_id' => $price_id, 'priceGroupDetal' => $group_det, 'agreement_id' => $agreement);
        $products["ITEMS"] = $catalog->getResult('FindProductsBy', $search, true);
		foreach ($products["ITEMS"] as $key => &$oneProduct) {
			$oneProduct = (array) $oneProduct;
			//картинки
			if($oneProduct["img_mini"]){
				$img_id = $oneProduct["id"];
				$img = $oneProduct["img_mini"];
				$ext = $oneProduct["img_mini_ext"];
				$oneProduct["img_path"] = $catalog->checkPrevImage($img_id, $img, 'jpg');
			}

		}
        $products["DEF_PRICE"] = $def_price;
        $products["SEARCH"] = "Y";
        $products['CHECKED'] = $data['checked'];
        Template::includeTemplate('catalog_list', $products);
        break;
	case 'searchBrend':
		$search = Catalog::getInstance()->getResult('GetGroupList', array('id' =>'', 'Input' => $data["value"]));
		Template::includeTemplate('group_res', $search);
		break;
    case 'quant':
        $id = $data["id"];
        $skocks = $catalog->getResult('GetStocksByProduct', array('id' => $id));
        if ($skocks) {
            $detailCard["STOCK"] = $skocks["Stocks"];
        }
        Template::includeTemplate('quantity_detail', $skocks);
        break;
    case 'price':
        $params = array('user_id' => $USER_EXTERNAL_ID);
        $print = $catalog->getResult('PrintPrice', $params, true);
        $url = $catalog->checkXLS($print, 'price_'.$USER_EXTERNAL_ID);
        echo $url;
        break;
}
