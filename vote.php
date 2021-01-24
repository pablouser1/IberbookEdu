<?php
require_once("functions.php");
require_once("headers.php");

require_once("auth.php");
require_once("helpers/db.php");
require_once("lang/lang.php");
require_once("classes/yearbooks.php");
$auth = new Auth;

class Vote {
    private $conn;
    private $userinfo;
    private $yearbooks;
    function __construct($userinfo) {
        $this->db = new DB;
        $this->userinfo = $userinfo;
        $this->yearbooks = new Yearbooks;
    }

    public function start($id) {
        $yearbook = $this->yearbooks->getOne($id);
        if ($yearbook) {
            if ($this->checkifValid($yearbook)) {
                $alreadyvoted = $this->checkIfAlreadyVoted($id);
                if ($alreadyvoted === "SAME-YEARBOOK") {
                    $response = [
                        "code" => "E",
                        "error" => L::vote_alreadyVoted
                    ];
                }
                else {
                    $this->addVote($id);
                    $this->setVoteToUser($id);
                    $response = [
                        "code" => "C"
                    ];
                }
            }
            else {
                $response = [
                    "code" => "E",
                    "error" => L::vote_notYourself
                ];
            }
        }
        else {
            $response = [
                "code" => "E",
                "error" => L::vote_notExist
            ];
        }
        return $response;
    }

    // Check if user can vote
    private function checkifValid($yearbook) {
        $acyear = date("Y",strtotime("-1 year"))."-".date("Y");
        if ($yearbook["schoolid"] == $this->userinfo["schoolid"] && $yearbook["schoolyear"] == $this->userinfo["year"]) {
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
    private function checkIfAlreadyVoted($ybid) {
        $stmt = $this->db->prepare("SELECT voted from users WHERE id=?");
        $stmt->bind_param("i", $this->userinfo["id"]);
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
    private function addVote($ybid) {
        $stmt = $this->db->prepare("UPDATE yearbooks SET votes = votes + 1 WHERE id=?");
        $stmt->bind_param("i", $ybid);
        $stmt->execute();
        $stmt->close();
    }

    // Remove 1 vote from yearbook
    private function removeVote($ybid) {
        $stmt = $this->db->prepare("UPDATE yearbooks SET votes = votes - 1 WHERE id=?");
        $stmt->bind_param("i", $ybid);
        $stmt->execute();
        $stmt->close();
    }

    private function setVoteToUser($ybid) {
        $stmt = $this->db->prepare("UPDATE users SET voted =? WHERE id=?");
        $stmt->bind_param("ii", $ybid, $this->userinfo["id"]);
        $stmt->execute();
        $stmt->close();
    }
}

if ($userinfo = $auth->isUserLoggedin()) {
    $vote = new Vote($userinfo);
    $response = $vote->start($_POST["id"]);
}
else {
    $response = [
        "code" => "E",
        "error" => L::common_needToLogin
    ];
}
Utils::sendJSON($response);
?>
