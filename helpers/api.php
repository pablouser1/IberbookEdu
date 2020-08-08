<?php
require_once("api_config.php");

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

// Login user
function login($username, $password, $type){
    if ($type === "alumno" || $type === "tutorlegal"){
        $url = $GLOBALS["base_url"].'pasendroid/login';
        $data = array('p' => '{"version":"11.10.0"}', 'USUARIO' => $username, 'CLAVE' => $password);
    }
    if ($type === "profesor") {
        $url = $GLOBALS["base_url"]."senecadroid/login";
        $data = array('p' => '{"version":"11.2.9"}', 'USUARIO' => $username, 'CLAVE' => $password);
    }
    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        ),
        "ssl"=>$GLOBALS["ssloptions"],
    );
    $context = stream_context_create($options);
    $result = json_decode(file_get_contents($url, false, $context), true);
    if ($result["ESTADO"]["CODIGO"] != "C"){
        return array(
            "cookies" => null,
            "error" => "Hubo un error al iniciar sesión:<br>".$result["ESTADO"]["DESCRIPCION"]
        );
    }
    // Get cookies
    $cookies = array();
    foreach ($http_response_header as $hdr) {
        if (preg_match('/^Set-Cookie:\s*([^;]+)/', $hdr, $matches)) {
            urldecode(parse_str($matches[1], $tmp));
            $cookies += $tmp;
        }
    }
    $cookies["SenecaP"] = str_replace(' ', '+', $cookies["SenecaP"]); // PHP converts "+" to spaces, undo that
    return array(
        "cookies" => $cookies,
        "error" => null
    );

}

// Get basic data from user
function getinfo($cookies, $type){
    $_SESSION["cookies"] = $cookies;
    if ($type == "alumno" || $type == "tutorlegal"){
        $url = $GLOBALS["base_url"].'pasendroid/infoSesion';
    }
    elseif ($type == "profesor") {
        $url = $GLOBALS["base_url"]."senecadroid/infoSesion";
    }
    $info = get($url, $cookies);
    // Save common user info to array
    switch($type){
        case 'alumno':
            // Get Pic
            $datapic = array('X_MATRICULA' => $info["RESULTADO"][0]["MATRICULAS"][0]["X_MATRICULA"]);
            $photo = getpicstudent($cookies, $datapic);
            // Set user info
            $userinfo = array(
                "iduser" => $info["RESULTADO"][0]["MATRICULAS"][0]["X_MATRICULA"],
                "nameuser" => $info["RESULTADO"][0]["USUARIO"],
                "typeuser" => $info["RESULTADO"][0]["C_PERFIL"],
                "yearuser" => $info["RESULTADO"][0]["MATRICULAS"][0]["UNIDAD"],
                "photouser" => $photo,
                "idcentro" => $info["RESULTADO"][0]["MATRICULAS"][0]["X_CENTRO"],
                "namecentro" => $info["RESULTADO"][0]["MATRICULAS"][0]["DENOMINACION"],
            );
            break;
        case 'tutorlegal':
            // Set user info
            $userinfo = array(
                "nameuser" => $info["RESULTADO"][0]["USUARIO"],
                "typeuser" => $info["RESULTADO"][0]["C_PERFIL"],
                "children" => $info["RESULTADO"][0]["HIJOS"],
            );
            break;
        case 'profesor':
            // Set user info
            $idteacher = getidteacher($cookies);
            $userinfo = array(
                "iduser" => $idteacher,
                "nameuser" => $info["RESULTADO"][0]["USUARIO"],
                "typeuser" => $info["RESULTADO"][0]["C_PERFIL"],
            );
            foreach ($info["RESULTADO"][0]["CENTROS"] as $id => $centro){
                $userinfo["centros"][$id]["id"] = $centro["X_CENTRO"];
                $userinfo["centros"][$id]["name"] = $centro["CENTRO"];
            }
            break;
    }
    return $userinfo;
}

// Get pic of student
function getpicstudent($cookies, $data){
    $url = $GLOBALS["base_url"].'pasendroid/imageAlumno';
    return base64_encode(post($url, $data, $cookies));
}

// Get id of teacher
function getidteacher($cookies){
    $url = "https://seneca.juntadeandalucia.es/seneca/jsp/senecadroid/getDatosUsuario";
    $cafile = "helpers/cert/juntadeandalucia-es-chain.pem";
    $response = json_decode(utf8_encode(post($url, [], $cookies)), true);
    return $response["RESULTADO"][0]["DATOS"][0]["C_NUMIDE"]; // Teacher's id
}

function getgroupsteachers($cookies){
    $url = "https://seneca.juntadeandalucia.es/seneca/jsp/senecadroid/getGrupos";
    $response = json_decode(utf8_encode(post($url, [], $cookies)), true);
    // Get each course, split all groups and if there are any 4º ESO or 2º BCT, add it to array
    foreach($response["RESULTADO"] as $id => $grupo){
        $grupos_split[] = str_split($grupo["UNIDADES"], 10);
        foreach($grupos_split[$id] as $nameid => $name){
            if(strpos($name, "4º ESO") !== false || strpos($name, "2º BCT") !== false){
                $grupos[$id]["name"] = trim($name);
                $grupos[$id]["subject"] = $grupo["MATERIAS"];
            }
        }
    }

    // https://stackoverflow.com/a/10514539, eliminates duplicates in array
    function super_unique($array,$key)
    {
       $temp_array = [];
       foreach ($array as &$v) {
           if (!isset($temp_array[$v[$key]]))
           $temp_array[$v[$key]] =& $v;
       }
       $array = array_values($temp_array);
       return $array;

    }

    $grupos = super_unique($grupos, "name");
    return array_values($grupos);
}
?>