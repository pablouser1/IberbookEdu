<?php
require_once("config.php");
require_once("requests.php");

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
    // Options
    $initial_options = array(
        CURLOPT_URL => $url,
        CURLOPT_HEADER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($data),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 5
    );
    $options = $initial_options + $GLOBALS["ssloptions"];

    $ch = curl_init();
    curl_setopt_array($ch, $options);
    // Get headers and JSON data
    $response = curl_exec($ch);
    // Check if any errors (timeouts...)
    if(curl_error($ch))
    {
        return array(
            "cookies" => null,
            "error" => curl_error($ch)
        );
    }
    $json_data = mb_substr($response, curl_getinfo($ch, CURLINFO_HEADER_SIZE));  
    $result = json_decode($json_data, true);
    curl_close($ch);
    if ($result["ESTADO"]["CODIGO"] != "C"){
        return array(
            "cookies" => null,
            "error" => $result["ESTADO"]["DESCRIPCION"]
        );
    }
    // Get cookies
    $cookies = "";
    preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $response, $matches);
    foreach($matches[1] as $item) {
        $cookies .= $item.";";
    }

    return [
        "cookies" => $cookies,
        "error" => null
    ];

}

// Get basic data from user
function getinfo($cookies, $type){
    $_SESSION["cookies"] = $cookies;
    if ($type == "alumno" || $type == "tutorlegal"){
        $url = $GLOBALS["base_url"].'pasendroid/infoSesion';
        $typeuser = "students";
    }
    elseif ($type == "profesor") {
        $url = $GLOBALS["base_url"]."senecadroid/infoSesion";
        $typeuser = "teachers";
    }
    $_SESSION["typeuser"] = $typeuser;
    $info = get($url, $cookies);
    // Save common user info to array
    switch($type){
        // -- Alumno -- //
        case 'alumno':
            // Get Pic
            $datapic = array('X_MATRICULA' => $info["RESULTADO"][0]["MATRICULAS"][0]["X_MATRICULA"], 'ANCHO' => 64, 'ALTO' => 64);
            $photo = getpicstudent($cookies, $datapic);
            // Get school id and name
            $datacentro = array("X_CENTRO" => $info["RESULTADO"][0]["MATRICULAS"][0]["X_CENTRO"]);
            $infocentro = getcentrostudent($cookies, $datacentro);

            if (empty($info["RESULTADO"][0]["MATRICULAS"][0]["UNIDAD"])) {
                $userinfo = [
                    "error" => "No tienes un grupo asignado"
                ];
            }
            else {
                // Set user info
                $userinfo = [
                    "iduser" => $info["RESULTADO"][0]["MATRICULAS"][0]["X_MATRICULA"],
                    "nameuser" => $info["RESULTADO"][0]["USUARIO"],
                    "typeuser" => $info["RESULTADO"][0]["C_PERFIL"],
                    "yearuser" => $info["RESULTADO"][0]["MATRICULAS"][0]["UNIDAD"],
                    "photouser" => $photo,
                    "idcentro" => $infocentro["idcentro"],
                    "namecentro" => $infocentro["namecentro"]
                ];                
            }
            break;
        // -- Tutor legal -- //
        case 'tutorlegal':
            // Set user info
            $userinfo = array(
                "nameuser" => $info["RESULTADO"][0]["USUARIO"],
                "typeuser" => $info["RESULTADO"][0]["C_PERFIL"],
                "children" => $info["RESULTADO"][0]["HIJOS"],
            );
            break;
        // -- Profesor -- //
        case 'profesor':
            // Set user info
            $idteacher = getidteacher($cookies);
            $userinfo = array(
                "iduser" => $idteacher,
                "nameuser" => $info["RESULTADO"][0]["USUARIO"],
                "typeuser" => $info["RESULTADO"][0]["C_PERFIL"],
            );
            // TODO, check if needed
            if(!isset($info["RESULTADO"][0]["CENTROS"]) || empty($info["RESULTADO"][0]["CENTROS"])) {
                $id = $info["RESULTADO"][0]["C_CODIGO"];
                $userinfo["centros"][$id] = [
                    "id" => $info["RESULTADO"][0]["C_CODIGO"],
                    "name" => $info["RESULTADO"][0]["CENTRO"],
                    "X_CENTRO" => null
                ];
            }
            else {
                foreach ($info["RESULTADO"][0]["CENTROS"] as $centro){
                    $id = $centro["C_CODIGO"];
                    $userinfo["centros"][$id] = [
                        "id" => $id,
                        "name" => $centro["CENTRO"],
                        "X_CENTRO" => $centro["X_CENTRO"]
                    ];
                }
            }
            break;
    }
    return $userinfo;
}

// Get school id
function getcentrostudent($cookies, $data){
    $url = $GLOBALS["base_url"].'pasendroid/datosCentro';
    $response = json_decode(utf8_encode(post($url, $data, $cookies)), true);
    return array(
        "idcentro" => $response["RESULTADO"][0]["DATOS"][0][1],
        "namecentro" => $response["RESULTADO"][0]["DATOS"][2][1]
    );
}

// Get pic of student
function getpicstudent($cookies, $data){
    $url = $GLOBALS["base_url"].'pasendroid/imageAlumno';
    return base64_encode(post($url, $data, $cookies));
}

// Get id of teacher
function getidteacher($cookies){
    $url = $GLOBALS["base_url"]."senecadroid/getDatosUsuario";
    $response = json_decode(utf8_encode(post($url, [], $cookies)), true);
    return $response["RESULTADO"][0]["DATOS"][0]["C_NUMIDE"]; // Teacher's id
}

// Change between schools (if needed)
function changeschoolteachers($cookies, $data){
    $url = $GLOBALS["base_url"]."senecadroid/setCentro";
    $response = json_decode(utf8_encode(post($url, $data, $cookies)), true);
    if ($response["ESTADO"]["CODIGO"] == "C") return true;
    else return false;
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

function getgroupsteachers($cookies){
    $url = $GLOBALS["base_url"]."senecadroid/getGrupos";
    $response = json_decode(utf8_encode(post($url, [], $cookies)), true);
    // Get each course, split all groups and if there are any 4º ESO, 2º BCT, 6 Primaria add it to array
    foreach($response["RESULTADO"] as $id => $grupo){
        preg_match_all("/(4º\sESO)\s.|(2º\sBCT)\s.|(6.)P/", $grupo["UNIDADES"], $tempgrupo);
        foreach ($tempgrupo[0] as $temp) {
            $grupos_repeated[] = [
                "name" => $temp,
                "subject" => $grupo["MATERIAS"]
            ];
        }
    }

    if(isset($grupos_repeated)) {
        // Sort
        $grupos = super_unique($grupos_repeated, "name");
        return array_values($grupos);
    }
    else {
        return [];
    }
}
?>
