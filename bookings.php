
<?php
session_start();

if (empty($_SESSION['admin_id'])) {
    header('Location: admin-login.php');
    exit;
}

require_once __DIR__ . '/config/database.php';

// Create a CSRF token to protect status updates
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';

    if (!is_string($token) ||
        !hash_equals($_SESSION['csrf_token'], $token)) {
        $error = 'Invalid request. Please refresh and try again.';
    } else {
        $bookingId = filter_input(
            INPUT_POST,
            'booking_id',
            FILTER_VALIDATE_INT
        );

        $status = $_POST['status'] ?? '';

        $allowedStatuses = [
            'Pending',
            'Confirmed',
            'Cancelled'
        ];

        if (!$bookingId || !in_array($status, $allowedStatuses, true)) {
            $error = 'Invalid booking or status selected.';
        } else {
            $stmt = $pdo->prepare(
                "UPDATE bookings SET status = :status WHERE id = :id"
            );

            $stmt->execute([
                'status' => $status,
                'id' => $bookingId
            ]);

            if ($stmt->rowCount() > 0) {
                $success = 'Booking status updated successfully.';
            } else {
                $check = $pdo->prepare(
                    "SELECT id FROM bookings WHERE id = :id"
                );
                $check->execute(['id' => $bookingId]);

                if ($check->fetch()) {
                    $success = 'The booking already has that status.';
                } else {
                    $error = 'Booking not found.';
                }
            }
        }
    }
}

// Fetch all bookings
$stmt = $pdo->query(
    "SELECT * FROM bookings ORDER BY id DESC"
);

$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAFI - Manage Bookings</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header>
    <div>
        <h1>Manage Bookings</h1>
        <p>View and update customer booking requests.</p>
    </div>

    <nav>
        <a href="admin-dashboard.php">Dashboard</a>
        <a href="messages.php">Contact Messages</a>
        <a href="admin-logout.php">Log out</a>
    </nav>
</header>

<main>
    <section>
        <h2>All Bookings (<?= count($bookings) ?>)</h2>

        <?php if ($success !== ''): ?>
            <p role="status" style="color: green;">
                <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
            </p>
        <?php endif; ?>

        <?php if ($error !== ''): ?>
            <p role="alert" style="color: #b42318;">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </p>
        <?php endif; ?>

        <?php if (empty($bookings)): ?>
            <p>No bookings have been submitted yet.</p>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <?php foreach (array_keys($bookings[0]) as $column): ?>
                                <th>
                                    <?= htmlspecialchars(
                                        $column,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </th>
                            <?php endforeach; ?>
                            <th>Manage Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <?php foreach ($booking as $value): ?>
                                    <td>
                                        <?= htmlspecialchars(
                                            (string) ($value ?? ''),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>
                                <?php endforeach; ?>

                                <td>
                                    <form method="POST" action="bookings.php">
                                        <input
                                            type="hidden"
                                            name="csrf_token"
                                            value="<?= htmlspecialchars(
                                                $_SESSION['csrf_token'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>"
                                        >

                                        <input
                                            type="hidden"
                                            name="booking_id"
                                            value="<?= (int) $booking['id'] ?>"
                                        >

                                        <label
                                            for="status-<?= (int) $booking['id'] ?>"
                                        >
                                            Status for booking
                                            <?= (int) $booking['id'] ?>
                                        </label>

                                        <select
                                            id="status-<?= (int) $booking['id'] ?>"
                                            name="status"
                                            required
                                        >
                                            <?php foreach (
                                                ['Pending', 'Confirmed', 'Cancelled']
                                                as $option
                                            ): ?>
                                                <option
                                                    value="<?= $option ?>"
                                                    <?= $booking['status'] === $option
                                                        ? 'selected'
                                                        : '' ?>
                                                >
                                                    <?= $option ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>

                                        <button type="submit">
                                            Update Status
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <p><a href="admin-dashboard.php">Back to Dashboard</a></p>
    </section>
</main>

</body>
</html>
