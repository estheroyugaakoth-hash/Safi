
<?php
session_start();

if (empty($_SESSION['admin_id'])) {
    header('Location: admin-login.php');
    exit;
}

require_once __DIR__ . '/config/database.php';

// Count all bookings
$bookingCount = (int) $pdo
    ->query("SELECT COUNT(*) FROM bookings")
    ->fetchColumn();

// Count all contact messages
$messageCount = (int) $pdo
    ->query("SELECT COUNT(*) FROM contact_messages")
    ->fetchColumn();

// Get the latest bookings
$bookings = $pdo->query(
    "SELECT * FROM bookings ORDER BY id DESC LIMIT 5"
)->fetchAll();

// Get the latest contact messages
$messages = $pdo->query(
    "SELECT * FROM contact_messages ORDER BY id DESC LIMIT 5"
)->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAFI Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">

    
</head>
<body>


<header>
    <div>
        <h1>SAFI Admin Dashboard</h1>
        <p>Welcome, <?= htmlspecialchars(
            $_SESSION['admin_username'],
            ENT_QUOTES,
            'UTF-8'
        ) ?>!</p>
    </div>

    <nav>
        <a href="admin-dashboard.php">Dashboard</a>
        <a href="bookings.php">Manage Bookings</a>
        <a href="messages.php">Contact Messages</a>
        <a href="admin-logout.php">Log out</a>
    </nav>
</header>


<div class="summary">
    <div class="card">
        <h2>Total Bookings</h2>
        <p class="number"><?= $bookingCount ?></p>
    </div>

    <div class="card">
        <h2>Contact Messages</h2>
        <p class="number"><?= $messageCount ?></p>
    </div>
</div>

<section>
    <h2>Recent Bookings</h2>

    <?php if (!$bookings): ?>
        <p>No bookings have been submitted yet.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <?php foreach (array_keys($bookings[0]) as $column): ?>
                            <th><?= htmlspecialchars($column, ENT_QUOTES, 'UTF-8') ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <?php foreach ($booking as $value): ?>
                                <td><?= htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

<section>
    <h2>Recent Contact Messages</h2>

    <?php if (!$messages): ?>
        <p>No contact messages have been submitted yet.</p>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <?php foreach (array_keys($messages[0]) as $column): ?>
                            <th><?= htmlspecialchars($column, ENT_QUOTES, 'UTF-8') ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($messages as $message): ?>
                        <tr>
                            <?php foreach ($message as $value): ?>
                                <td><?= htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>

</body>
</html>
