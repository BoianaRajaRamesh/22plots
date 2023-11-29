<?php

//to get the current page url
function curPageURL()
{
    $pageURL = 'http';
    // if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}

if (strpos(curPageURL(), "/api/")) {
    ob_start(function ($json) {

        function json_validator($data = NULL)
        {
            if (!empty($data)) {
                json_decode($data);
                return (json_last_error() === JSON_ERROR_NONE);
            }

            return false;
        }

        if (json_validator($json)) {
            /* $response_arry = json_decode(str_replace('null', '""', $json));
              if (json_last_error_msg() == "Syntax error") {
              $response_arry = json_decode(str_replace('"null"', '""', $arr));
              }
              return json_encode($response_arry, JSON_PRETTY_PRINT); */
            return json_encode(json_decode($json), JSON_PRETTY_PRINT);
        } else {
            return ob_get_contents();
        }
    });
}

function sms_gateway_enabled()
{
    $CI = &get_instance();
    return $CI->parent_model->get_result('name,status', 'utilities', ['status' => 'YES']);
}

function email_enabled()
{
    $CI = &get_instance();
    return $CI->parent_model->get_result('name,status', 'utilities', ['status' => 'YES']);
}

function get_ads($ad_size_id, $no_of_ads_to_display = 1, $image_class_name = 'img-responsive')
{
    $CI = &get_instance();
    switch ($ad_size_id) {
        case 1: //Means get 250 x 250 square ads
            return $CI->parent_model->get_result('', 'ads', ['display_size' => $ad_size_id]);
            break;
        case 2: //Means get 200 x 200 small square ads
            return $CI->parent_model->get_result('', 'ads', ['display_size' => $ad_size_id]);
            break;
        case 3: //Means get 468 x 60 banner
            return $CI->parent_model->get_result('', 'ads', ['display_size' => $ad_size_id]);
            break;
        case 4: //Means get 728 x 90 Leaderboard
            return $CI->parent_model->get_result('', 'ads', ['display_size' => $ad_size_id]);
            break;
        case 5: //Means get 300 x 250 inline rectangle
            return $CI->parent_model->get_result('', 'ads', ['display_size' => $ad_size_id]);
            break;
        case 6: //Means get 336 x 280 large rectangle
            return $CI->parent_model->get_result('', 'ads', ['display_size' => $ad_size_id]);
            break;
        case 7: //Means get 120 x 600 skyscraper
            return $CI->parent_model->get_result('', 'ads', ['display_size' => $ad_size_id]);
            break;
        case 8: //Means get 160 x 600 wide skyscraper
            return $CI->parent_model->get_result('', 'ads', ['display_size' => $ad_size_id]);
            break;
        default:
            break;
    }
    return false;
}

function forward_to_view()
{
    return;
}

function how_many_days($start_date, $end_date)
{
    $today = date('d-m-Y', strtotime($start_date));

    //$exp = date('d-m-Y',strtotime($end_date)); //query result form database
    $exp = date('d-m-Y', strtotime($end_date)); //query result form database

    $expDate = date_create($exp);
    $todayDate = date_create($today);
    $diff = date_diff($todayDate, $expDate);
    if ($diff->format("%R%a") > 0) {
        //echo "active";
    } else {
        //echo "inactive";
    }
    //echo "Remaining Days " . $diff->format("%R%a days");
    return $diff->format("%R%a");
}

function how_many_days_with_out_sign($start_date, $end_date)
{
    $today = date('d-m-Y', strtotime($start_date));

    //$exp = date('d-m-Y',strtotime($end_date)); //query result form database
    $exp = date('d-m-Y', strtotime($end_date)); //query result form database

    $expDate = date_create($exp);
    $todayDate = date_create($today);
    $diff = date_diff($todayDate, $expDate);
    if ($diff->format("%R%a") > 0) {
        //echo "active";
    } else {
        //echo "inactive";
    }
    //echo "Remaining Days " . $diff->format("%R%a days");
    return $diff->format("%a");
}

function how_many_days_left($end_date)
{
    $today = date('d-m-Y', time());

    //$exp = date('d-m-Y',strtotime($end_date)); //query result form database
    $exp = date('d-m-Y', $end_date); //query result form database

    $expDate = date_create($exp);
    $todayDate = date_create($today);
    $diff = date_diff($todayDate, $expDate);
    if ($diff->format("%R%a") > 0) {
        //echo "active";
    } else {
        //echo "inactive";
    }
    //echo "Remaining Days ".$diff->format("%R%a days");
    if ($diff->format("%R%a") == 1) {
        return singular($diff->format("%R%a days"));
    }
    return $diff->format("%a days");
}

function how_many_days_left_count($end_date)
{
    $today = date('d-m-Y', time());

    //$exp = date('d-m-Y',strtotime($end_date)); //query result form database
    $exp = date('d-m-Y', $end_date); //query result form database

    $expDate = date_create($exp);
    $todayDate = date_create($today);
    $diff = date_diff($todayDate, $expDate);
    if ($diff->format("%R%a") > 0) {
        //echo "active";
    } else {
        //echo "inactive";
    }
    //echo "Remaining Days ".$diff->format("%R%a days");
    return $diff->format("%R%a");
}

function get_mystrtotime($mydate)
{
    $dmy = $mydate;
    list($day, $month, $year) = explode("/", $dmy);

    $ymd = "$year-$month-$day";

    return strtotime($ymd);
}

function get_tiny_url($url)
{
    $ch = curl_init();
    $timeout = 10;
    curl_setopt($ch, CURLOPT_URL, 'https://tinyurl.com/api-create.php?url=' . $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function generateRandomNumber($length = 10)
{
    $characters = '0123456789';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function time_slots($from, $to, $interval)
{
    $arr = array();
    $start = strtotime($from);
    $in = $interval * 60;
    //echo date('h:i A',strtotime("+$interval minutes", strtotime($to))); die;
    //$end = strtotime($to) - $in;
    $end = strtotime("+$interval minutes", strtotime($to)) - $in;
    $time = strtotime("+0 minutes", $start);
    //echo date('h:i A', $time); echo "<br />";
    $arr[] = date('h:i A', $time);

    for ($i = $start; $i < $end; $i = $i + $in) {
        if ($i >= $start && $i <= $end - $in) {
            $time = strtotime("+" . $interval . " minutes", $i);
            $arr[] = date('h:i A', $time);
            //echo date('h:i A', $time); echo "<br />";
        }
    }
    return $arr;
}

function left_pad($number)
{
    return (($number < 10 && $number >= 0) ? '0' : '') + $number;
}

function convert_minutes_to_string($minutes)
{
    $sign = '';
    if ($minutes < 0) {
        $sign = '-';
    }
    $hours = left_pad(floor(abs($minutes) / 60));
    $minutes = left_pad(abs($minutes) % 60);
    return $sign . $hours . 'hrs ' . $minutes . 'min';
}

function get_time_difference($start_time, $end_time)
{
    $firstTime = strtotime($start_time);
    $lastTime = strtotime($end_time);
    $timeDiff = $lastTime - $firstTime;
    return ($timeDiff / 60) / 60;
}

function time_elapsed_string($datetime, $full = false)
{
    date_default_timezone_set("Asia/Kolkata");
    $today = time();
    $createdday = $datetime;
    $datediff = abs($today - $createdday);
    $difftext = "";
    $years = floor($datediff / (365 * 60 * 60 * 24));
    $months = floor(($datediff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
    $days = floor(($datediff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
    $hours = floor($datediff / 3600);
    $minutes = floor($datediff / 60);
    $seconds = floor($datediff);
    //year checker
    if ($difftext == "") {
        if ($years > 1)
            $difftext = $years . " years ago";
        elseif ($years == 1)
            $difftext = $years . " year ago";
    }
    //month checker
    if ($difftext == "") {
        if ($months > 1)
            $difftext = $months . " months ago";
        elseif ($months == 1)
            $difftext = $months . " month ago";
    }
    //month checker
    if ($difftext == "") {
        if ($days > 1)
            $difftext = $days . " days ago";
        elseif ($days == 1)
            $difftext = $days . " day ago";
    }
    //hour checker
    if ($difftext == "") {
        if ($hours > 1)
            $difftext = $hours . " hrs ago";
        elseif ($hours == 1)
            $difftext = $hours . " hr ago";
    }
    //minutes checker
    if ($difftext == "") {
        if ($minutes > 1)
            $difftext = $minutes . " mins ago";
        elseif ($minutes == 1)
            $difftext = $minutes . " min ago";
    }
    //seconds checker
    if ($difftext == "") {
        if ($seconds > 1)
            $difftext = $seconds . " sec ago";
        elseif ($seconds == 1)
            $difftext = $seconds . " secd ago";
    }
    return $difftext;
}

function remaining_time($datetime, $full = false)
{
    date_default_timezone_set("Asia/Kolkata");
    $today = time();
    $createdday = $datetime;
    $datediff = abs($today - $createdday);
    $difftext = "";
    $years = floor($datediff / (365 * 60 * 60 * 24));
    $months = floor(($datediff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
    $days = floor(($datediff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
    $hours = floor($datediff / 3600);
    $minutes = floor($datediff / 60);
    $seconds = floor($datediff);
    //year checker
    if ($difftext == "") {
        if ($years > 1)
            $difftext = $years . " years left";
        elseif ($years == 1)
            $difftext = $years . " year left";
    }
    //month checker
    if ($difftext == "") {
        if ($months > 1)
            $difftext = $months . " months left";
        elseif ($months == 1)
            $difftext = $months . " month left";
    }
    //month checker
    if ($difftext == "") {
        if ($days > 1)
            $difftext = $days . " days left";
        elseif ($days == 1)
            $difftext = $days . " day left";
    }
    //hour checker
    if ($difftext == "") {
        if ($hours > 1)
            $difftext = $hours . " hrs left";
        elseif ($hours == 1)
            $difftext = $hours . " hr left";
    }
    //minutes checker
    if ($difftext == "") {
        if ($minutes > 1)
            $difftext = $minutes . " mins left";
        elseif ($minutes == 1)
            $difftext = $minutes . " min left";
    }
    //seconds checker
    if ($difftext == "") {
        if ($seconds > 1)
            $difftext = $seconds . " secs left";
        elseif ($seconds == 1)
            $difftext = $seconds . " secd left";
    }
    return $difftext;
}

function calculateTimeDifferenceInMinutes($dateTime1, $dateTime2)
{
    // Create DateTime objects for the input dates
    $date1 = new DateTime($dateTime1);
    $date2 = new DateTime($dateTime2);

    // Check if dateTime1 is less than dateTime2
    if ($date1 < $date2) {
        return 0; // Return 0 minutes if dateTime1 is less than dateTime2
    }

    // Calculate the difference between two dates
    $interval = $date1->diff($date2);

    // Calculate the total minutes in the difference
    $minutes = $interval->days * 24 * 60;
    $minutes += $interval->h * 60;
    $minutes += $interval->i;

    return $minutes;
}

function generateFancyPassword()
{
    $rotations = rand(1, 2);
    if ($rotations == 1) {
        $password = rand(1121, 9999);
    } else if ($rotations == 2) {
        $password = rand(1, 9) * 11;
        $password .= rand(1, 9) * 11;
    }
    return $password;
}

function show_forbidden()
{
    $arr = array(
        'status' => "invalid",
        "error_type" => "invalid_login",
        "title" => "Invalid Login",
        "message" => "You are not authorised for this request please contact us from help page",
        "data" => [
            "message" => "You are not authorised for this request please contact us from help page"
        ]
    );
    echo json_encode($arr);
    exit();
}

function get_ip_location_details($ip_address)
{
    $URL = "http://ip-api.com/json/$ip_address?fields=520191";
    $ch = curl_init();
    //curl_setopt($ch, CURLOPT_URL, "http://ip-api.com/json/$ip?fields=520191");
    curl_setopt($ch, CURLOPT_URL, $URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $contents = curl_exec($ch);
    $response = json_decode($contents);
    curl_close($ch);
    $result = (object) [
        "city" => isset($response->city) ? $response->city : null,
        "region_name" => isset($response->regionName) ? $response->regionName : null,
        "country_name" => isset($response->country) ? $response->country : null,
        "time_zone" => isset($response->timezone) ? $response->timezone : null
    ];
    //print_r($result);
    //die;
    return $result;
}

function replaceNullValueWithEmptyString(&$value)
{
    $value = $value === null || $value === NULL ? "" : $value;
}

function is_url_exist($url)
{

    $options = array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HEADER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_ENCODING => "",
        CURLOPT_AUTOREFERER => true,
        CURLOPT_CONNECTTIMEOUT => 120,
        CURLOPT_TIMEOUT => 120,
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_NOBODY => true,
        CURLOPT_SSL_VERIFYPEER => false
    );

    $ch = curl_init();
    curl_setopt_array($ch, $options);
    $response = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    /*
      if ($httpCode != 200) {
      echo "Return code is {$httpCode} \n"
      . curl_error($ch);
      } else {
      echo "<pre>" . htmlspecialchars($response) . "</pre>";
      }
     */
    curl_close($ch);

    if ($code == 200) {
        $status = true;
    } else {
        $status = false;
    }
    return $status;
}

function count_down_timer($mysql_date_time_format = null)
{
    if (!isset($mysql_date_time_format)) {
        return "";
    }
    $count_down_date = strtotime($mysql_date_time_format);
    $now = time();

    // Find the distance between now and the count down date
    $distance = ($count_down_date - $now) * 1000;

    // Time calculations for days, hours, minutes and seconds
    $days = floor($distance / (1000 * 60 * 60 * 24));
    $hours = floor(($distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    $minutes = floor(($distance % (1000 * 60 * 60)) / (1000 * 60));
    $seconds = floor(($distance % (1000 * 60)) / 1000);

    if ($distance < 0) {
        return "EXPIRED";
    }

    if ($days > 0) {
        $display_time = $days . "days " . $hours . "hrs " . $minutes . "m " . $seconds . "s ";
    } else {
        $display_time = $hours . "hrs " . $minutes . "m " . $seconds . "s ";
    }

    return $display_time;
}

function validate_age($date_of_birth, $restriction_years = 18)
{
    //$date_of_birth  give format should d-m-Y

    $date = date_create_from_format('d-m-Y', $date_of_birth);
    $selected_date = date_format($date, 'Y-m-d');

    // $then will first be a string-date
    $date_of_birth = strtotime($selected_date);
    //The age to be over, over +18
    $min = strtotime("+$restriction_years years", $date_of_birth);
    if (time() < $min) {
        return false;
    } else {
        return true;
    }
}

function number_shorten($number, $precision = 3, $divisors = null)
{

    // Setup default $divisors if not provided
    if (!isset($divisors)) {
        $divisors = array(
            pow(1000, 0) => '', // 1000^0 == 1
            pow(1000, 1) => 'K', // Thousand
            pow(1000, 2) => 'M', // Million
            pow(1000, 3) => 'B', // Billion
            pow(1000, 4) => 'T', // Trillion
            pow(1000, 5) => 'Qa', // Quadrillion
            pow(1000, 6) => 'Qi', // Quintillion
        );
    }

    // Loop through each $divisor and find the
    // lowest amount that matches
    foreach ($divisors as $divisor => $shorthand) {
        if (abs($number) < ($divisor * 1000)) {
            // We found a match!
            break;
        }
    }

    // We found our match, or there were no matches.
    // Either way, use the last defined value for $divisor.
    //return number_format($number / $divisor, $precision) . $shorthand;
    // We found our match, or there were no matches.
    // Either way, use the last defined value for $divisor.
    $responde = number_format($number / $divisor, $precision);

    $whole = floor($responde);

    $fraction = $responde - $whole;
    if ($fraction > .00) {
        return number_format((float) $responde, 1, '.', '') . $shorthand;
    } else {
        return number_format((float) $responde, 0, '.', '') . $shorthand;
    }
}
