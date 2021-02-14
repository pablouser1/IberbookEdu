<?php
require_once(__DIR__."/../helpers/db.php");
require_once(__DIR__."/../classes/users.php");
class Messages {
    private $db;
    private $users;
    function __construct() {
        $this->db = new DB;
        $this->users = new Users;
    }

    public function getMessages($id, $offset) {
        $messages = [];
        $stmt = $this->db->prepare("SELECT id, `from`, content, `sent` FROM messages WHERE `to`=? ORDER BY `sent` LIMIT 10 OFFSET $offset");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $messages[] = [
                    "id" => $row["id"],
                    "from" => $this->users->getName($row["from"]),
                    "sent" => $row["sent"],
                    "content" => $row["content"],
                ];
            }
            $stmt->close();
            return $messages;
        }
        else {
            return false;
        }
    }

    public function sendMessage($senderId, $recieverId, $message) {
        $sanitizedMessage = nl2br(htmlspecialchars($message));
        $stmt = $this->db->prepare("INSERT INTO messages (`from`, `to`, content) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $senderId, $recieverId, $sanitizedMessage);
        if ($stmt->execute()) {
            return true;
        }
        else {
            return false;
        }
    }
}
?>
