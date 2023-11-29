<?php

function send_sms($num, $msg) {
    $ci = & get_instance();
    $ci->load->database();
    $sql = "SELECT * FROM `settings` where id = 1";
    $settings = $ci->db->query($sql)->row();
//    $settings = $this->common_model->get_single_data('settings', 1);
    $username = $settings->sms_username;
    $sender_id = $settings->sms_sender_id;
    $password = $settings->sms_password;
    $mobilenumbers = $num;
    $message = $msg; //enter Your Message
    $url = "http://login.smsmoon.com/API/sms.php";
    $message = urlencode($message);
    $ch = curl_init();
    if (!$ch) {
        die("Couldn't initialize a cURL handle");
    }
    $ret = curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "username=$username&password=$password&from=$sender_id&to=$mobilenumbers&msg=$message&type=1&dnd_check=0");
    $ret = curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $curlresponse = curl_exec($ch); // execute
    if (curl_errno($ch))
        if (empty($ret)) {
            return curl_close($ch); // close cURL handler
        } else {
            // print_r($info);
            curl_close($ch); // close cURL handler
            return $info;
        }
}
