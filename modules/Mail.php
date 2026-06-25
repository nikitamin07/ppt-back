<?php


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once 'modules/classes/mail/SMTP.php';
require_once 'modules/classes/mail/PHPMailer.php';
require_once 'modules/classes/mail/Exception.php';

class Mail
{
    public $mail;

    public function __construct($username, $password, $host, $post, $sender_email, $sender_name, $charset = 'UTF-8')
    {
        $this -> mail = new PHPMailer;
        $this -> mail -> CharSet = $charset;
        $this -> mail -> isSMTP();
        $this -> mail -> Host = $host;
        $this -> mail -> SMTPAuth   = true;
        $this -> mail -> Username   = $username;
        $this -> mail -> Password   = $password;
        $this -> mail -> SMTPSecure = 'ssl';
        $this -> mail -> Port       = $post;

        $this -> mail->setFrom($sender_email, $sender_name);
    }

    public function setMailBody($subject, $body, $is_html)
    {
        $this -> mail -> isHTML($is_html);
        $this -> mail -> Subject = $subject;
        $this -> mail -> Body    = $body;
    }

    public function setRecipient($recipient)
    {
        $this -> mail->addAddress($recipient);
    }

    public function sendMail()
    {
        try {
            $this -> mail -> send();
            return true;
        } catch (Exception $e) {
            throw new \Exception("Message could not be sent. Mailer Error: {$this -> mail->ErrorInfo}") ;
            return false;
        }
    }

}