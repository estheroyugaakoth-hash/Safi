
<?php
session_start();

require_once __DIR__ . "/config/database.php";

// 1. Accept POST requests only
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: booking.html");
    exit;
}

// 2. Collect and clean submitted form data
$fullName = trim($_POST["fullName"] ?? "");
$email    = trim($_POST["email"] ?? "");
$phone    = trim($_POST["phone"] ?? "");
$service  = $_POST["service"] ?? "";
$date     = $_POST["date"] ?? "";
$time     = $_POST["time"] ?? "";
$address  = trim($_POST["address"] ?? "");
$details  = trim($_POST["details"] ?? "");

// 3. Validate the submitted details
$allowedServices = ["cleaning", "plumbing", "electrical"];
$allowedTimes = ["morning", "afternoon", "evening"];

if (
    strlen($fullName) < 2 ||
    strlen($fullName) > 100 ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    strlen($email) > 150 ||
    strlen($phone) < 9 ||
    strlen($phone) > 16 ||
    !preg_match('/^[0-9+() -]+$/', $phone) ||
    !in_array($service, $allowedServices, true) ||
    !in_array($time, $allowedTimes, true) ||
    $address === "" ||
    strlen($address) > 255 ||
    strlen($details) > 500
) {
    http_response_code(422);
    exit("Invalid booking details. Please return to the form and check your entries.");
}

// 4. Validate the booking date
$dateObject = DateTime::createFromFormat("!Y-m-d", $date);
$dateErrors = DateTime::getLastErrors();
$today = new DateTime("today");

if (
    !$dateObject ||
    ($dateErrors !== false &&
        ($dateErrors["warning_count"] > 0 ||
         $dateErrors["error_count"] > 0)) ||
    $dateObject->format("Y-m-d") !== $date ||
    $dateObject < $today
) {
    http_response_code(422);
    exit("Please select today or a future date.");
}

// 5. Save the booking to MySQL
try {
    $sql = "INSERT INTO bookings
        (full_name, email, phone, booking_date, booking_time,
         service, address, message)
        VALUES
        (:full_name, :email, :phone, :booking_date, :booking_time,
         :service, :address, :message)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":full_name"    => $fullName,
        ":email"        => $email,
        ":phone"        => $phone,
        ":booking_date" => $date,
        ":booking_time" => $time,
        ":service"      => $service,
        ":address"      => $address,
        ":message"      => $details
    ]);

    // 6. Redirect after successful saving
    $_SESSION["booking_success"] =
        "Your booking request has been submitted successfully!";

    header("Location: booking.html?success=1");
    exit;

} catch (PDOException $e) {
    error_log("SAFI booking save failed: " . $e->getMessage());

    http_response_code(500);
    exit("We could not save your booking. Please try again later.");
}
