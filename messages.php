
<?php
session_start();

if (empty($_SESSION['admin_id'])) {
    header('Location: admin-login.php');
    exit;
}

require_once __DIR__ . '/config/database.php';

$stmt = $pdo->query(
    "SELECT * FROM contact_messages ORDER BY id DESC"
);

$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAFI - Contact Messages</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<header>
    <div>
        <h1>Contact Messages</h1>
        <p>View messages sent by your customers.</p>
    </div>

    <nav>
        <a href="admin-dashboard.php">Dashboard</a>
        <a href="bookings.php">Bookings</a>
        <a href="admin-logout.php">Log out</a>
    </nav>
</header>

<main>
    <section>
        <h2>All Messages (<?= count($messages) ?>)</h2>

        <?php if (empty($messages)): ?>
            <p>No contact messages have been received yet.</p>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <?php foreach (array_keys($messages[0]) as $column): ?>
                                <th>
                                    <?= htmlspecialchars(
                                        $column,
                                        ENT_QUOTES,
                                        'UTF-8'
                                    ) ?>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($messages as $message): ?>
                            <tr>
                                <?php foreach ($message as $value): ?>
                                    <td>
                                        <?= htmlspecialchars(
                                            (string) ($value ?? ''),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>
                                <?php endforeach; ?>
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
