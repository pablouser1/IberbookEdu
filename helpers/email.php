<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require __DIR__.'/../vendor/autoload.php';

require_once(__DIR__."/db.php");
class Email {
    private $mail;
    private $path;
    private $db;
    function __construct($config) {
        $this->db = new DB;
        $this->mail = new PHPMailer();
        //Server settings
        //$this->mail->SMTPDebug = SMTP::DEBUG_SERVER; // Enable verbose debug output

        // SMTP Config
        $this->mail->isSMTP(); // Send using SMTP
        $this->mail->SMTPAuth = true; // Enable SMTP authentication
        $this->mail->SMTPKeepAlive = true;
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

        // Login
        $this->mail->Host = $config["host"]; // Set the SMTP server to send through
        $this->mail->Username = $config["username"]; // SMTP username
        $this->mail->Password = $config["password"]; // SMTP password
        $this->mail->Port = $config["port"]; // SMTP Port

        // Misc
        $this->mail->setFrom($config["username"], 'IberbookEdu');
        $this->mail->addReplyTo($config["username"], 'IberbookEdu');
        $this->mail->isHTML(true);
        $this->mail->CharSet = 'UTF-8';
    }

    public function getEmails($schoolid, $group) {
        $users = [];
        $emails = [];
        $stmt = $this->db->prepare("SELECT userid FROM profiles WHERE schoolid=? AND schoolyear=?");
        $stmt->bind_param("is", $schoolid, $group);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        $stmt->close();

        foreach ($users as $user) {
            $stmt = $this->db->prepare("SELECT fullname, email FROM users WHERE id=?");
            $stmt->bind_param("i", $user["userid"]);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            array_push($emails, $row);
            $stmt->close();
        }

        return $emails;
    }
    // Send email to group when yearbook is generated
    public function sendYearbook($users, $ybid) {
        $this->mail->Subject = 'Your yearbook is ready!';
        $body = file_get_contents(__DIR__."/templates/email/yearbook.html");
        $this->mail->AltBody = 'To view the message, please use an HTML compatible email viewer!';
        foreach ($users as $user) {
            $customBody = str_replace("%fullname%", $user["fullname"], $body);
            $this->mail->msgHTML($customBody);
            $this->mail->addAddress($user["email"], $user["fullname"]);
            $this->mail->send();
            $this->mail->clearAddresses();
        }
    }
}
?>
