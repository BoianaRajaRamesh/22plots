<?php

function push_notification($title, $text, $token, $screen = '', $required_id = '') {
    $data = array(
        "to" => $token,
        "priority" => 'high',
        "data" => array(
            "title" => $title,
            "body" => $text,
            'sound' => 'notification_tone',
            "icon" => "ic_app_only_icon",
            "screen" => $screen,
            "required_id" => $required_id,
        )
    );

    $data_string = json_encode($data);

    $headers = array(
        'Authorization: key=' . FIREBASE_ACCESS_KEY,
        'Content-Type: application/json'
    );

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

    $result = curl_exec($ch);

    curl_close($ch);

    $arr = array(
        'status' => "valid",
        'data' => strip_tags($result),
        'response' => $data
    );

    //echo json_encode($arr, JSON_PRETTY_PRINT);
    return $arr;
}

function multi_push_notification($data) {


    $data_string = json_encode($data);

    $headers = array(
        'Authorization: key=' . FIREBASE_ACCESS_KEY,
        'Content-Type: application/json'
    );

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);

    $result = curl_exec($ch);

    curl_close($ch);

    $arr = array(
        'status' => "valid",
        'data' => strip_tags($result),
        'response' => $data
    );

    //echo json_encode($arr, JSON_PRETTY_PRINT);
    return $arr;
}
