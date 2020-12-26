<?php
require_once("functions.php");
require_once("headers.php");

require_once("auth.php");
require_once("helpers/db.php");

$auth = new Auth;

class Vote {
    private $conn;
    private $userinfo;
    function __construct($userinfo) {
        $this->db = new DB;
        $this->userinfo = $userinfo;
    }

    public function start($id) {
        $yearbook = $this->getYearbookInfo($id);
        if ($yearbook) {
            if ($this->checkifvalid($yearbook)) {
                $this->checkIfAlreadyVoted($id);
                $this->addVote($id);
                $this->setVoteToUser($id);
                $response = [
                    "code" => "C"
                ];
            }
            else {
                $response = [
                    "code" => "E",
                    "error" => "No puedes votar a tu propio grupo"
                ];
            }
        }
        else {
            $response = [
                "code" => "E",
                "error" => "Esa orla no existe"
            ];
        }
        return $response;
    }

    private function getYearbookInfo($id) {
        $stmt = $this->db->prepare("SELECT schoolid, schoolyear FROM yearbooks WHERE id=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $yearbook = $result->fetch_assoc();
            $stmt->close();
            return $yearbook;
        }
        else {
            return false;
        }
    }

    // Check if user can vote
    private function checkifvalid($yearbook) {
        if ($yearbook["schoolid"] == $this->userinfo["schoolid"] && $yearbook["schoolyear"] == $this->userinfo["year"]) {
            return false;
        }
        else {
            return true;
        }
    }

    // Check if user already voted and change vote if that's the case
    private function checkIfAlreadyVoted($ybid) {
        $stmt = $this->db->prepare("SELECT votes from users WHERE id=?");
        $stmt->bind_param("i", $this->userinfo["id"]);
        $stmt->execute();
        $stmt->store_result();
        // Get profile id
        $stmt->bind_result($uservote);
        $stmt->fetch();
        $stmt->close();
        if ($uservote !== $ybid) {
            $this->removeVote($uservote);
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
        $stmt = $this->db->prepare("UPDATE users SET votes =? WHERE id=?");
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
        "error" => "Necesitas inciar sesión para votar"
    ];
}
sendJSON($response);
?>
