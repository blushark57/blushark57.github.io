<?php
// Include the library
require_once '../assets/vendor/php-email-form/php-email-form.php';



// Replace with your real receiving email address
$receiving_email_address = 'arbaz_shaikh@cms.co.in';

// Check if form data exists
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contact = new PHP_Email_Form;
    $contact->ajax = true;

    $contact->to = $receiving_email_address;
    $contact->from_name = $_POST['name'];
    $contact->from_email = $_POST['email'];
    $contact->subject = $_POST['subject'];

    // Add form messages
    $contact->add_message($_POST['name'], 'From');
    $contact->add_message($_POST['email'], 'Email');
    $contact->add_message($_POST['message'], 'Message', 10);

    // Uncomment if using SMTP

    $contact->smtp = [
        'host' => 'smtp.gmail.com',
        'username' => 'arbaz57@gmail.com',
        'password' => 'xhca kybk qvsl sgrl',
        'port' => 587
    ];


    echo $contact->send();
} else {
    die('Invalid request method.');
}
?>
