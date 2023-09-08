<?php
include_once 'src/Api.php';

$params = [
//    'card_exp_month' => '02',//DECLINE
];

$payment = new Api($params);
$response = $payment->sendPost();
$payment->checkResponse($response);