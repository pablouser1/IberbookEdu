<?php
require_once("headers.php");
require_once("functions.php");
require_once("auth.php");
require_once("classes/profiles.php");

// Get all profiles of user
$auth = new Auth;
if ($userinfo = $auth->isUserLoggedin()) {
    $profilesMng = new Profiles;
    if (isset($_GET["action"])) {
        switch ($_GET["action"]) {
            case "set":
                if (isset($_POST["schoolindex"], $_POST["groupindex"])) {
                    // Get school and group index
                    $schoolindex = $_POST["schoolindex"];
                    $groupindex = $_POST["groupindex"];

                    // Get arrays
                    $school = (array) $userinfo["schools"][$schoolindex];
                    $group = (array) $school["groups"][$groupindex];

                    // If both exists, change profile
                    if ($school && $group) {
                        $profile = $profilesMng->changeProfile($userinfo["id"], $school, $group);
                    }
                    if ($profile) {
                        $response = [
                            "code" => "C",
                            "data" => [
                                "profileinfo" => $profile
                            ]
                        ];
                    }
                    else {
                        $response = [
                            "code" => "E",
                            "error" => "Unknown error while changing profile"
                        ];
                    }
                }
                else {
                    $response = [
                        "code" => "E",
                        "error" => "Not enough info provided"
                    ];
                }
                break;
            default:
                $response = [
                    "code" => "E",
                    "error" => "Action not valid"
                ];
        }
    }
}
else {
    http_response_code(401);
    $response = [
        "code" => "E",
        "error" => "Not logged in"
    ];
}
Utils::sendJSON($response);
?>
