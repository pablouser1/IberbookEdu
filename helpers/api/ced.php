<?php
require_once(__DIR__. "/../config.php");
require_once(__DIR__. "/requests.php");
class Api {
    // -- Initial vars -- //
    private $req;
    private $db;
    private $base_url;
    public $type;
    private $cookies;

    // -- Base functions -- //
    function __construct() {
        // Class from requests.php
        $this->req = new req();
        $this->db = new DB();
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

    // -- Common functions -- //
    function settype($type) {
        $this->type = $type;
        switch ($type) {
            case "students":
            case "tutorlegal":
                $this->base_url = $GLOBALS["base_url"].'pasendroid';
            break;
            case "teachers":
                $this->base_url = $GLOBALS["base_url"].'senecadroid';
            break;
            default:
                die("Tipo de usuario no válido");
        }
    }

    // Login user
    function login($username, $password, $type){
        // Initial config
        $useragent = "IberbookEdu Testing";
        $this->settype($type);
        $data = array('p' => '{"version":"11.10.0"}', 'USUARIO' => $username, 'CLAVE' => $password);
        // Options
        $url = "{$this->base_url}/login";
        $initial_options = array(
            CURLOPT_URL => $url,
            CURLOPT_HEADER => true,
            CURLOPT_USERAGENT => $useragent,
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
                "code" => "CURL",
                "error" => "Ha habido un error al conectarse con los servidores remotos"
            );
        }
        $json_data = mb_substr($response, curl_getinfo($ch, CURLINFO_HEADER_SIZE));  
        $result = json_decode($json_data, true);
        curl_close($ch);
        if ($result["ESTADO"]["CODIGO"] != "C"){
            return array(
                "code" => $result["ESTADO"]["CODIGO"],
                "error" => $result["ESTADO"]["DESCRIPCION"]
            );
        }
        // Get cookies
        $cookies = "";
        preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $response, $matches);
        foreach($matches[1] as $item) {
            $cookies .= "{$item};";
        }
        $this->req->setcookies($cookies);

        return [
            "code" => $result["ESTADO"]["CODIGO"],
            "error" => null
        ];
    }

    // Get basic data from user
    function getinfo(){
        $url = "{$this->base_url}/infoSesion";
        $info = $this->req->get($url);
        // Save common user info to array
        switch($this->type){
            // -- Alumno -- //
            case 'students':
                // Get Pic
                $datapic = array('X_MATRICULA' => $info["RESULTADO"][0]["MATRICULAS"][0]["X_MATRICULA"], 'ANCHO' => 64, 'ALTO' => 64);
                $photo = $this->getpicstudent($datapic);
                // Get school id and name
                $datacentro = array("X_CENTRO" => $info["RESULTADO"][0]["MATRICULAS"][0]["X_CENTRO"]);
                $infocentro = $this->getcentrostudent($datacentro);
                // Set user info
                $userinfo = [
                    "iduser" => $info["RESULTADO"][0]["MATRICULAS"][0]["X_MATRICULA"],
                    "nameuser" => $info["RESULTADO"][0]["USUARIO"],
                    "typeuser" => "students",
                    "yearuser" => $info["RESULTADO"][0]["MATRICULAS"][0]["UNIDAD"],
                    "photouser" => $photo,
                    "idcentro" => $infocentro["idcentro"],
                    "namecentro" => $infocentro["namecentro"]
                ];
                break;
            // -- Tutor legal -- //
            case 'tutorlegal':
                // Set user info
                $children = array();
                foreach($info["RESULTADO"][0]["HIJOS"] as $child){
                    // Picture
                    $datapic = array('X_MATRICULA' => $child["MATRICULAS"][0]["X_MATRICULA"], "ANCHO" => 64, "ALTO" => 64);
                    $child["FOTO"] = $this->getpicstudent($datapic);
                    // Check if student is allowed
                    $datacentro = array("X_CENTRO" => $child["MATRICULAS"][0]["X_CENTRO"]);
                    $infocentro = $this->getcentrostudent($datacentro);
                    $sql = "SELECT `id` FROM `schools` WHERE id=$infocentro[idcentro]";
                    $result = $this->db->query($sql);
                    // If student is allowed, include him in array
                    if ($result !== false && $result->num_rows == 1 && preg_match("/(4º\sESO)|(2º\sBCT)|(6.)P/", $child["MATRICULAS"][0]["UNIDAD"])) {
                        // Merge child info with school info
                        $children[] = $child + $infocentro;
                    }
                }
                $userinfo = [
                    "nameuser" => $info["RESULTADO"][0]["USUARIO"],
                    "typeuser" => "tutor",
                    "children" => $children
                ];
                break;
            // -- Profesor -- //
            case 'teachers':
                $idteacher = $this->getidteacher();
                // Only one school
                if (!isset($info["RESULTADO"][0]["CENTROS"])) {
                    $schoolid = $info["RESULTADO"][0]["C_CODIGO"];
                    $school = $this->getallteacher($info["RESULTADO"][0]);
                    if ($school) {
                        $finalschools[$schoolid] = $school;
                    }
                    else {
                        $finalschools = [];
                    }
                }
                // Multiple schools
                else {
                    $finalschools = [];
                    foreach($info["RESULTADO"][0]["CENTROS"] as $centro) {
                        $schoolid = $centro["C_CODIGO"];
                        $data = ["X_CENTRO" => $centro["X_CENTRO"], "C_PERFIL" => "P"];
                        // Get school info
                        $this->changeschoolteachers($data);
                        $school = $this->getallteacher($centro);
                        if ($school) {
                            $finalschools[$schoolid] = $school;
                        }
                    }
                }

                $userinfo = array(
                    "iduser" => $idteacher,
                    "nameuser" => $info["RESULTADO"][0]["USUARIO"],
                    "typeuser" => "teachers",
                    "schools" => $finalschools
                );
            break;
        }
        return $userinfo;
    }

    // -- Students only -- //
    // Get school id
    function getcentrostudent($data){
        $url = "{$this->base_url}/datosCentro";
        $response = json_decode(utf8_encode($this->req->post($url, $data)), true);
        return array(
            "idcentro" => $response["RESULTADO"][0]["DATOS"][0][1],
            "namecentro" => $response["RESULTADO"][0]["DATOS"][2][1]
        );
    }

    // Get pic of student
    function getpicstudent($data){
        $url = "{$this->base_url}/imageAlumno";
        return base64_encode($this->req->post($url, $data));
    }

    // -- Teachers only -- //
    // Get id of teacher
    function getidteacher(){
        $url = "{$this->base_url}/getDatosUsuario";
        $response = json_decode(utf8_encode($this->req->post($url, [])), true);
        return $response["RESULTADO"][0]["DATOS"][0]["C_NUMIDE"]; // Teacher's id
    }
    
    function getallteacher($centro) {
        $schoolinfo = [];
        // Set array with only allowed schools
        $stmt = $this->db->prepare("SELECT id FROM schools WHERE id=?");
        $stmt->bind_param("i", $centro["C_CODIGO"]);
        $stmt->execute();
        $stmt->store_result();
        if($stmt->num_rows == 1) {
            $groups = $this->getgroupsteachers();
            while ($stmt->fetch()) {
                // Set basic school info
                $schoolinfo = [
                    "name" => $centro["CENTRO"],
                    "id" => $centro["C_CODIGO"],
                ];
            }
            $stmt->close();
            // Set groups info
            if(empty($groups)){
                return null;
            }
            else {
                $schoolinfo["groups"] = $groups;
            }
        }
        return $schoolinfo;
    }

    // Change between schools (if needed)
    function changeschoolteachers($data){
        $url = "{$this->base_url}/setCentro";
        $response = json_decode(utf8_encode($this->req->post($url, $data)), true);
        if ($response["ESTADO"]["CODIGO"] == "C") return true;
        else return false;
    }

    function getgroupsteachers(){
        $url = $GLOBALS["base_url"]."senecadroid/getGrupos";
        $response = json_decode(utf8_encode($this->req->post($url, [])), true);
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
            $grupos = $this->super_unique($grupos_repeated, "name");
            return array_values($grupos);
        }
        else {
            return [];
        }
    }
}
?>
