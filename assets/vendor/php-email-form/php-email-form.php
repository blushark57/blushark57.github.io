<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../assets/vendor/autoload.php';

class PHP_Email_Form {
    public $to;
    public $from_name;
    public $from_email;
    public $subject;
    public $ajax = false;
    public $smtp = null;
    private $messages = [];

    /**
     * Add a message to the email body.
     *
     * @param string $message The message content.
     * @param string $label The label for the message.
     * @param int $max_length (Optional) Maximum allowed length for the message.
     * @return void
     */
    public function add_message($message, $label, $max_length = null) {
        if ($max_length && strlen($message) > $max_length) {
            $message = substr($message, 0, $max_length) . '...';
        }
        $this->messages[] = "$label: $message";
    }

    /**
     * Sends the email.
     *
     * @return string JSON response with the success or failure message.
     */
    public function send() {
        $body = implode("\n", $this->messages);

        // Use SMTP if defined
        if ($this->smtp) {
            return $this->send_via_smtp($body);
        }

        // Use mail() function as a fallback
        $headers = "From: {$this->from_name} <{$this->from_email}>\r\n";
        $headers .= "Reply-To: {$this->from_email}\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        if (mail($this->to, $this->subject, $body, $headers)) {
            return json_encode(['success' => true, 'message' => 'Email sent successfully!']);
        } else {
            return json_encode(['success' => false, 'message' => 'Failed to send email!']);
        }
    }

    /**
     * Sends email using SMTP configuration.
     *
     * @param string $body The email body.
     * @return string JSON response with the success or failure message.
     */
    private function send_via_smtp($body) {
        if (!$this->smtp) {
            return json_encode(['success' => false, 'message' => 'SMTP configuration is missing.']);
        }

        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = $this->smtp['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->smtp['username'];
            $mail->Password = $this->smtp['password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Use TLS encryption
            $mail->Port = $this->smtp['port'];

            // Set email headers and content
            $mail->setFrom($this->from_email, $this->from_name);
            $mail->addAddress($this->to);
            $mail->Subject = $this->subject;
            $mail->Body = $body;

            // Send the email
            $mail->send();

            return json_encode(['success' => true, 'message' => 'Email sent successfully!']);
        } catch (Exception $e) {
            return json_encode(['success' => false, 'message' => "Mailer Error: {$mail->ErrorInfo}"]);
        }
    }
}

?>
