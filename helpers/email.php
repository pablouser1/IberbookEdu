<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require __DIR__.'/../vendor/autoload.php';

require_once("db.php");
class Email {
    private $mail;
    private $path;
    private $db;
    function __construct($config) {
        $this->db = new DB;
        $this->mail = new PHPMailer();
        //Server settings
        //$this->mail->SMTPDebug = SMTP::DEBUG_SERVER; // Enable verbose debug output
        $this->mail->isSMTP(); // Send using SMTP
        $this->mail->Host = $config["host"]; // Set the SMTP server to send through
        $this->mail->SMTPAuth = true; // Enable SMTP authentication
        $this->mail->Username = $config["username"]; // SMTP username
        $this->mail->Password = $config["password"]; // SMTP password
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
        $this->mail->Port = $config["port"]; // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
        $this->mail->setFrom($this->mail->Username, 'IberbookEdu');
        $this->mail->isHTML(true);
        $this->mail->CharSet = 'UTF-8';
    }

    public function getEmails($schoolid, $group) {
        $emails = [];
        $stmt = $this->db->prepare("SELECT email FROM users WHERE schoolid=? AND schoolyear=?");
        $stmt->bind_param("is", $schoolid, $group);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            if (isset($row["email"])) {
                array_push($emails, $row["email"]);
            }
        }
        $stmt->close();
        return $emails;
    }
    // Send email to group when yearbook is generated
    public function sendYearbook($emails, $ybid) {
        $this->mail->Subject = '¡Tu orla está lista!';
        $this->mail->Body = "El administrador de tu grupo ya ha generado tu orla, haz click aquí para verla";
        foreach ($emails as $email) {
            $this->mail->addAddress($email);
            $this->apply();
        }
    }

    private function apply() {
        $this->mail->send();
        $this->mail->clearAddresses();
    }
}
?>
