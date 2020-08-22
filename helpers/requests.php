<?php
// -- Control basic requests -- //

// Get requests
function get($url, $cookies){
    $options = array(
        'http' => array(
            'method'  => 'GET',
            'header'  => array(
                "Content-type: application/json",
                "Cookie: ".array_keys($cookies)[0]."=".$cookies[array_keys($cookies)[0]],
                "Cookie: ".array_keys($cookies)[1]."=".$cookies[array_keys($cookies)[1]]
            )
        ),
        "ssl"=>$GLOBALS["ssloptions"],
    );
    $context = stream_context_create($options);
    return(json_decode(utf8_encode(file_get_contents($url, false, $context)), true));
}

// Post requests
function post($url, $data, $cookies){
    $options = array(
        'http' => array(
            'method'  => 'POST',
            'content' => http_build_query($data),
            'header'  => array(
                "Content-Type: application/x-www-form-urlencoded",
                "Cookie: ".array_keys($cookies)[0]."=".$cookies[array_keys($cookies)[0]],
                "Cookie: ".array_keys($cookies)[1]."=".$cookies[array_keys($cookies)[1]]
            )
        ),
        "ssl"=>$GLOBALS["ssloptions"],
    );
    $context = stream_context_create($options);
    return(file_get_contents($url, false, $context));
}
?>
