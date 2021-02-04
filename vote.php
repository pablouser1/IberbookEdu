<?php
require_once("functions.php");
require_once("headers.php");

require_once("auth.php");
require_once("helpers/db.php");
require_once("lang/lang.php");
require_once("classes/yearbooks.php");
$auth = new Auth;

class Vote {
    private $db;
    function __construct() {
        $this->db = new DB;
    }

    // Check if user can vote
    public function checkifValid($yearbook, $profileinfo) {
        $acyear = date("Y",strtotime("-1 year"))."-".date("Y");
        if ($yearbook["schoolid"] == $profileinfo["schoolid"] && $yearbook["schoolyear"] == $profileinfo["year"]) {
            // Allow votes from the same group but different acyear
            if ($acyear !== $yearbook["acyear"]) {
                return true;
            }
            return false;
        }
        else {
            return true;
        }
    }

    // Check if user already voted and change vote if that's the case
    public function checkIfAlreadyVoted($ybid, $userid) {
        $stmt = $this->db->prepare("SELECT voted from users WHERE id=?");
        $stmt->bind_param("i", $userid);
        $stmt->execute();
        $stmt->store_result();
        // Get profile id
        $stmt->bind_result($uservote);
        $stmt->fetch();
        $stmt->close();
        if (!isset($uservote)) {
            return false;
        }
        // Voted to another yearbook before
        elseif ($uservote != $ybid) {
            $this->removeVote($uservote);
            return "DIFFERENT-YEARBOOK";
        }
        // Is trying to vote to same yearbook
        else {
            return "SAME-YEARBOOK";
        }
    }

    // Add 1 vote to yearbook and set voted to yb id
    public function addVote($ybid) {
        $stmt = $this->db->prepare("UPDATE yearbooks SET votes = votes + 1 WHERE id=?");
        $stmt->bind_param("i", $ybid);
        $stmt->execute();
        $stmt->close();
    }

    // Remove 1 vote from yearbook
    public function removeVote($ybid) {
        $stmt = $this->db->prepare("UPDATE yearbooks SET votes = votes - 1 WHERE id=?");
        $stmt->bind_param("i", $ybid);
        $stmt->execute();
        $stmt->close();
    }

    public function setVoteToUser($ybid, $userid) {
        $stmt = $this->db->prepare("UPDATE users SET voted =? WHERE id=?");
        $stmt->bind_param("ii", $ybid, $userid);
        $stmt->execute();
        $stmt->close();
    }
}
$userinfo = $auth->isUserLoggedin();
$profileinfo = $auth->isProfileLoggedin();

if ($userinfo && $profileinfo) {
    $vote = new Vote;
    $yearbookClass = new Yearbooks;
    if (isset($_POST["id"])) {
        $yearbook = $yearbookClass->getOne($_POST["id"]);
        if ($yearbook) {
            $ybid = $yearbook["id"];
            if ($vote->checkifValid($yearbook, $profileinfo)) {
                $alreadyvoted = $vote->checkIfAlreadyVoted($ybid, $userinfo["id"]);
                if ($alreadyvoted === "SAME-YEARBOOK") {
                    $response = [
                        "code" => "E",
                        "error" => L::vote_alreadyVoted
                    ];
                }
                else {
                    $vote->addVote($ybid);
                    $vote->setVoteToUser($ybid, $userinfo["id"]);
                    $response = [
                        "code" => "C"
                    ];
                }
            }
            else {
                $response = [
                    "code" => "E",
                    "error" => "Not valid"
                ];
            }
        }
        else {
            $response = [
                "code" => "E",
                "error" => L::vote_notExist
            ];
        }
    }
    else {

    }
}
else {
    $response = [
        "code" => "E",
        "error" => L::common_needToLogin
    ];
}
Utils::sendJSON($response);
?>
