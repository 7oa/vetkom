<?php

//include $_SERVER['DOCUMENT_ROOT'] . '/core/loader/prolog_before.php';
//use Core\Main\OrderStatus,
//use Core\Main\User;
//$USER_ID = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
$email = filter_input(INPUT_POST, 'email');
$order_id = filter_input(INPUT_POST, 'order_id');
$event = filter_input(INPUT_POST, 'event');
$param = filter_input(INPUT_POST, 'param');
$value = filter_input(INPUT_POST, 'value');
if ($param === 'status' && $event === 'order_change') {
    $mailText = "Заказ № ";
    $mailText .= $order_id . " изменил статус:";
    $mailText .= $value;
    $subject = "Изменен статус заказа";
    $headers = "Content-type: text/html; charset=utf-8 \r\n";
    $headers .= "From: ВТК. СИСОПТ <support@opt.vetkom.ru>\r\n";
    $to = $email;
//if ($result) {
    mail($to, $subject, $mailText, $headers);
}
    //User::sendEmailChangeStatus($email, $order_id, $event, $param, $value, true);
//}

/*

if ($param === 'status' && $event === 'order_change') {
    $parameters = array('filter' => array('USER_ID' => $USER_ID, 'ORDER_ID' => $ORDER_ID));
    $fields = array('USER_ID' => $USER_ID, 'ORDER_ID' => $ORDER_ID, 'STATUS' => $value, 'TYPE'=>'status');
    $dbStatus = OrderStatus::getList($parameters);
    if ($dbStatus->rowsCount() > 0) {
        $arStatus = $dbStatus->fetchRaw();
        $primary = $arStatus['ID'];
        OrderStatus::update($primary, $fields);
    } else
        OrderStatus::add($fields);
}
*/