<?php

include $_SERVER['DOCUMENT_ROOT'] . '/core/loader/prolog_before.php';

use Core\Main\User,
    Core\Main\Order,
    Core\Main\DataBase,
    Core\Main\Basket,
    Core\Main\Payment,
    Core\Main\Samples,
    Core\Main\Template;

$data = $_POST;
$USER_ID = User::getID();
$arUser = User::getByID($USER_ID);
$USER_EXTERNAL_ID = $arUser["EXTERNAL"];
$def_price = $arUser["SHOWDEFAULTPRICE"];
$order = Order::getInstance();
$payment = Payment::getInstance();
$date = strtotime($data["date"]);
$orderID = $data["number"];
$orderParams = array('number' => $orderID, 'date' => $date);
$type = $data['TYPE'];
$shipType = $data['shipType'];
$shipAddress = $data['shipAddress'];
$shipmentCompany = $data['shipmentCompany'];
$comment = $data['comment'];
switch ($type) {
    case 'add':
    	//echo '<pre>'; print_r($data);echo "</pre>";
		$desDate = $data['desDate'];
		$refresh = false;
		$orderNum = date('Y-m-d');
		$orderDate = date('Y-m-d');
		$orderGUID = '';
		if($data['refresh']){
			$orderNum = $data['orderNum'];
			//$orderDate = date('Y-m-d', strtotime($data['orderDate']));
			$orderGUID = $data['orderGUID'];
			$refresh = true;
		}
		$comment.= "\n Желаемая дата выполнения заказа: ".$desDate;
        $params = array('filter' => array('USER_ID' => $USER_ID));
        $dbBacket = Basket::getList($params);
        $backet = $dbBacket->fetchAll();
        $orderPrice = 0;
        foreach ($backet as $arProducts) {
            $products[] = array(
                'id' => $arProducts["PRODUCT_ID"],
                'art' => $arProducts["ART"],
                'name' => $arProducts["NAME"],
                'quantity' => $arProducts["QUANTITY"],
                'price' => $arProducts["PRICE"],
                'sum' => $arProducts["PRICE"] * $arProducts["QUANTITY"]
            );
            $orderPrice += ($arProducts["PRICE"] * $arProducts["QUANTITY"]);
        }
        $orderFields = array(
            'number' => $orderNum,
            'date' => $orderDate,
            'user_id' => $USER_EXTERNAL_ID,
            'sum' => $orderPrice,
            'status' => 'added',
            'strings' => $products,
            'paymentPerc' => '',
            'shipmentPerc' => '',
            'debtPerc' => '',
            'shipmentDate' => '0001-01-01',
            'state' => '',
            'shipmentType' => $shipType,
            'shipmentAddress' => $shipAddress,
            'shipmentCompany' => $shipmentCompany,
            'comment' => $comment,
            'guid' => $orderGUID
        );
        $addFields = array('user_id' => $USER_EXTERNAL_ID, 'order' => $orderFields, 'refresh' => $refresh);
        //echo "<pre>"; print_r($addFields); echo "</pre>";
        $result = $order->getResult('AddOrder', $addFields);
        if ($result[0] == "Документ оформлен!") {
			if($refresh){
				$connect = DataBase::getConnection();
				$connect->query("DELETE FROM `order_edit` WHERE `USER_ID` = '$USER_ID'");
			}
            if(!$refresh) User::sendEmail($arUser, $backet, $result[1], true);

            foreach ($backet as $arProducts) {
                Basket::delete($arProducts['ID']);
            }
        }
        echo $result[0];
        break;
    case 'list':
        $from = date('Y-m-d', strtotime($data["dfrom"]));
        $to = date('Y-m-d', strtotime($data["dto"]));

        $params = array('user_id' => $USER_EXTERNAL_ID, 'date1' => $from, 'date2' => $to);
        $list = $order->getResult('GetOrderList', $params);
        krsort($list);
        foreach ($list as &$arResult) {
            if ($arResult['shipmentPerc'] == "100")
                $arResult['status'] = "Отгружено";
            else {
                switch ($arResult['status']) {
                    case "":
                        $arResult['status'] = "Обрабатывается менеджером";
                        break;
                    case "На сборке":
                    case "Собрано частично":
                        $arResult['status'] = "Заказ комплектуется";
                        break;
                    case "Собрано полностью":
                    case "Отгружено в отдел/Завершено":
                        $arResult['status'] = "Готов к отгрузке";
                        break;
                    case "РАСФОРМИРОВАН":
                    case "Расформирован":
                        $arResult['status'] = "Отменен";
                        break;
                }
            }
        }
        Template::includeTemplate('order_list', $list);
        break;
    case 'detail':
        $guid = $data["guid"];
        $orderPrice = 0;
        $res = '';
        $detail = $order->getResult('GetOrder', $orderParams);
        $detail['number'] = $orderID;
        $detail['date'] = $data["date"];
        switch ($detail['shipmentType']) {
            case 0:
                $detail['shipmentType'] = "Самовывоз";
                break;
            case 1:
                $detail['shipmentType'] = "До клиента";
                break;
            case 2:
                $detail['shipmentType'] = "Силами перевозчика";
                break;
        }
        $detail["DEF_PRICE"] = $def_price;
        $docs = $payment->getResult('GetRelatedDocByOrder', array('guid' => $guid));
        if ($docs) {
            $detail["DOCS"] = $docs["Documents"];
        }
        //проверка, добавлен ли товар в избранное
        $connect = DataBase::getConnection();
        foreach ($detail["strings"] as $key => &$oneProduct) {
            $pId = $oneProduct['id'];
            $check_favorits = $connect->query("SELECT * FROM `favorits` WHERE `USER_ID` = '$USER_ID' AND `PRODUCT_ID` = '$pId'")->fetchRaw();
            if ($check_favorits == false)
                $oneProduct['favorits'] = 0;
            else
                $oneProduct['favorits'] = $check_favorits['TYPE'];
        }
		$detail["guid"] = $guid;
        Template::includeTemplate('order_detail', $detail);
        break;
    case 'repeate':
        $detail = $order->getResult('GetOrder', $orderParams);
        //$search = array('#RESULT#', '#PRODUCT_ID#','#ART#', '#NAME#', '#FORMATED_PRICE#', '#QUANTITY#', '#FORMATED_SUM#');
        foreach ($detail['strings'] as $option):
            $product = array(
                "PRODUCT_ID" => $option["id"],
                "ART" => $option["art"],
                "NAME" => $option["name"],
                "PRICE" => $option["price"],
                "QUANTITY" => $option["quantity"],
                'USER_ID' => $USER_ID,
            );
            $item = Basket::addItemByProduct($product);
        endforeach;
        $basket = Basket::getInstance(false);
        Template::includeTemplate('basket_items', $basket);
        break;
    case 'edit':
        $detail = $order->getResult('GetOrder', $orderParams);
        $ORDER_NUM = $detail["number"];
        $ORDER_DATE = $detail["date"];
		$ORDER_GUID = $data["guid"];
		$SHIP_TYPE = $detail["shipmentType"];
		$SHIP_ADDR = $detail["shipmentAddress"];
		$COMMENT = $detail["comment"];
        $userBasket=Basket::getItems($USER_ID);
        if($userBasket){
            foreach($userBasket as $arItems){
                Basket::delete($arItems["ID"]);
            }
        }
        foreach ($detail['strings'] as $option):
            $product = array(
                "PRODUCT_ID" => $option["id"],
                "ART" => $option["art"],
                "NAME" => $option["name"],
                "PRICE" => $option["price"],
                "QUANTITY" => $option["quantity"],
                'USER_ID' => $USER_ID,
            );
            $item = Basket::addItemByProduct($product);
        endforeach;
        $basket = Basket::getInstance(false);
        $connect = DataBase::getConnection();
        $isedit = $connect->query("SELECT * FROM `order_edit` WHERE `USER_ID` = '$USER_ID'")->fetchRaw();
        if(!empty($isedit)){
            $connect->query("UPDATE `order_edit` SET ORDER_NUM='$ORDER_NUM', ORDER_DATE='$ORDER_DATE', ORDER_GUID='$ORDER_GUID', SHIP_TYPE='$SHIP_TYPE', SHIP_ADDR='$SHIP_ADDR', COMMEN='$COMMENT' WHERE ID='$isedit[ID]'");
        }
        else{
            $connect->query("INSERT INTO `order_edit` (USER_ID, ORDER_NUM, ORDER_DATE, ORDER_GUID, SHIP_TYPE, SHIP_ADDR, COMMEN) VALUES ('$USER_ID','$ORDER_NUM','$ORDER_DATE','$ORDER_GUID','$SHIP_TYPE','$SHIP_ADDR','$COMMENT')");
        }
		//echo "<pre>"; print_r($detail); echo "</pre>";
		$basket->orderDate = $ORDER_DATE;
		$basket->orderGuid = $ORDER_GUID;
		$basket->orderNum = $ORDER_NUM;
		$basket->shipType = $SHIP_TYPE;
		$basket->shipAddr = $SHIP_ADDR;
		$basket->comment = $COMMENT;
        Template::includeTemplate('basket_index', $basket);
        break;
    case 'samples':
        $sname = $data['sname'];
        $detail = $order->getResult('GetOrder', $orderParams);
        //$search = array('#RESULT#', '#PRODUCT_ID#','#ART#', '#NAME#', '#FORMATED_PRICE#', '#QUANTITY#', '#FORMATED_SUM#');
        $id = Samples::checkSample($sname);
        if ($id == false)
            echo "Error";
        else {
            foreach ($detail['strings'] as $arItems) {
                $sampleItem = array(
                    "ROOT" => $id,
                    "PRODUCT_ID" => $arItems["id"],
                    "S_NAME" => $sname,
                    "ART" => $arItems["art"],
                    "NAME" => $arItems["name"],
                    "QUANTITY" => $arItems["quantity"],
                    "USER_ID" => $USER_ID
                );
                $result = Samples::addItemByProduct($sampleItem);
            }
        }
        break;
    case 'orderPrint':
        //$data["frm"] = '"paymentInvoice';
        $params = array('number' => $data["number"], 'date' => date('Y-m-d', strtotime($data["date"])), 'type' => $data["frm"]);
        $print = $order->getResult('printOrder', $params, true);
        $url = $order->checkPDF($print, $data["number"]);
        echo $url;
        //echo "/{$order->docFolder}/$print";
        break;
    case 'docPrint':
        $params = array('name' => $data["name"], 'guid' => $data["guid"], 'type' => $data["type"]);
        $print = $payment->getResult('PrintDocument', $params, true);
        $url = $payment->checkPDF($print, $data["guid"]);
        echo $url;
        //echo "/{$order->docFolder}/$print";
        break;
    case 'detailPayment':
        $params = array('name' => $data["name"], 'guid' => $data["guid"]);
        $arResult = $payment->getResult('GetDocumentDetails', $params);
        Template::includeTemplate('detailpayments', $arResult);
        break;
    case 'paymentList':
        $from = date('Y-m-d', strtotime($data["dfrom"]));
        $to = date('Y-m-d', strtotime($data["dto"]));
        $params = array('user_id' => $USER_EXTERNAL_ID, 'date1' => $from, 'date2' => $to);
        $docs = $payment->getResult('GetMutualPayments', $params);
        if ($docs['Documents'])
            krsort($docs['Documents']);
        Template::includeTemplate('docs_list', $docs);
        break;
}