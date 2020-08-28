<?php
// -- Control basic requests -- //

// Get requests
function get($url, $cookies){
    // Options
    $initial_options = array(
        CURLOPT_URL => $url,
        CURLOPT_HEADER => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIE => $cookies,
        CURLOPT_CONNECTTIMEOUT => 5
    );
    // Add SSL options
    $options = $initial_options + $GLOBALS["ssloptions"];

    $ch = curl_init();
    curl_setopt_array($ch, $options);
    $response = curl_exec($ch);
    
    $json_data = mb_substr($response, curl_getinfo($ch, CURLINFO_HEADER_SIZE));  
    $result = json_decode(utf8_encode($json_data), true);

    curl_close($ch);
    return $result;
}

// Post requests
function post($url, $data, $cookies){
    // Options
    $initial_options = array(
        CURLOPT_URL => $url,
        CURLOPT_HEADER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($data),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_COOKIE => $cookies,
        CURLOPT_CONNECTTIMEOUT => 5
    );
    // Add SSL options
    $options = $initial_options + $GLOBALS["ssloptions"];

    $ch = curl_init();
    curl_setopt_array($ch, $options);
    $response = curl_exec($ch);
    
    $data = mb_substr($response, curl_getinfo($ch, CURLINFO_HEADER_SIZE));  

    curl_close ($ch);
    return $data;
}
?>
