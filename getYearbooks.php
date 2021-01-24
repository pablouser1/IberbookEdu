<?php
require_once("functions.php");
require_once("headers.php");

require_once("auth.php");

require_once("lang/lang.php");
require_once("classes/yearbooks.php");

$auth = new Auth;
$yearbook = new Yearbooks;

// Loggedin user's yearbook
if (isset($_GET["mode"])) {
    switch ($_GET["mode"]) {
        case "user":
            if ($profileinfo = $auth->isProfileLoggedin()) {
                $useryb = $yearbook->getUserYearbook($profileinfo["schoolid"], $profileinfo["year"]);
                if ($useryb) {
                    $response = [
                        "code" => "C",
                        "data" => $useryb
                    ];
                }
                else {
                    $response = [
                        "code" => "C",
                        "data" => null
                    ];
                }
            }
            else {
                http_response_code(401);
                $response = [
                    "code" => "E",
                    "error" => L::common_needToLogin
                ];
            }
            break;
        case "id":
            $individual = $yearbook->getOne($_GET["id"]);
            $response = [
                "code" => "C",
                "data" => $individual
            ];
            break;
        case "random":
            $random = $yearbook->getRandom();
            $response = [
                "code" => "C",
                "data" => $random
            ];
            break;
        default:
            $response = [
                "code" => "E",
                "error" => "Action not valid"
            ];
    }
}
// Multiple yearbooks
else {
    $sanitized = $yearbook->sanitizeInput($_GET["offset"], $_GET["sort"]);
    if ($sanitized) {
        $yearbooks = $yearbook->getYearbooks($_GET["offset"], $_GET["sort"]);
        if ($yearbooks) {
            $response = [
                "code" => "C",
                "data" => $yearbooks
            ];
        }
        else {
            $response = [
                "code" => "NO-MORE",
                "error" => L::yearbooks_maximum
            ];
        }
    }
    else {
        $response = [
            "code" => "E",
            "error" => L::yearbooks_params
        ];
    }
}
Utils::sendJSON($response);
?>
