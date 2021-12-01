<?php

// ver 1.0 - 05.03.21

ini_set('max_execution_time', '1700');
set_time_limit(1700);


header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE');
header('Access-Control-Allow-Headers: application/json');
header('Content-Type: application/json; charset=utf-8');

http_response_code(200);


$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, TRUE); //convert JSON into array


if ($input["sender"] == NULL || $input["sender"] == 'undefined') {
    $result["error"]["code"] = 422;
    $result["error"]["description"]["sender"][] = "sender is missing";
    $result["error"]["message"] = "Unprocessable entity";
}
if ($input["email"] == NULL || $input["email"] == 'undefined') {
    $result["error"]["code"] = 422;
    $result["error"]["description"]["emails"][] = "email is missing";
    $result["error"]["message"] = "Unprocessable entity";
}
if ($result["error"]["code"] == 422) {
    http_response_code(422);
    echo json_encode($result);
    exit;
}

$code = mt_rand(100000, 999999);

$header = "From: Verification <".$input["sender"].">\r\n"
    ."Content-type: text/html; charset=utf-8\r\n"
    ."X-Mailer: PHP mail script by \"Mufik Soft\"";
$body = "Verification Code: ".$code;


$mail = mail( $input["email"], mb_encode_mimeheader ("Verification", 'utf-8'), $body, $header);
if ($mail == true) {
    $result["state"] = true;
    $result["code"] = $code;
} else {
    $result["state"] = false;
    $result["message"] = "failed send code";
}

echo json_encode($result);