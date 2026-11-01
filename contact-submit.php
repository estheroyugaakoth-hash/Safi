
<?php
session_start();

require_once __DIR__ . "/config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: contact.html");
    exit;
}

// Collect form data
$fullName = trim($_POST["fullName"] ?? "");
$email    = trim($_POST["email"] ?? "");
$subject  = trim($_POST["subject"] ?? "");
$message  = trim($_POST["message"] ?? "");

// Validate form data
if (
    strlen($fullName) < 2 ||
    strlen($fullName) > 100 ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    strlen($email) > 150 ||
    $subject === "" ||
    strlen($subject) > 200 ||
    $message === "" ||
    strlen($message) > 2000
) {
    http_response_code(422);
    exit("Please check your name, email, subject, and message.");
}

try {
    $sql = "INSERT INTO contact_messages
            (full_name, email, subject, message, status)
            VALUES
            (:full_name, :email, :subject, :message, :status)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":full_name" => $fullName,
        ":email"     => $email,
        ":subject"   => $subject,
        ":message"   => $message,
        ":status"    => "new"
    ]);

    $_SESSION["contact_success"] =
        "Thank you! Your message has been sent successfully.";

    header("Location: contact.html?success=1");
    exit;

} catch (PDOException $e) {
    error_log("SAFI contact save failed: " . $e->getMessage());

    http_response_code(500);
    exit("We could not save your message. Please try again later.");
}
