<?php
require 'db.php';
session_start();

$user_id = $_SESSION['user_id'];

// Mark as read if button clicked
if (isset($_POST['mark_read'])) {
    $notification_id = $_POST['notification_id'];
    $stmt = $conn->prepare("UPDATE notification SET is_read=1 WHERE notification_id=? AND user_id=?");
    $stmt->bind_param("is", $notification_id, $user_id);
    $stmt->execute();
    header("Location: notifications.php"); // refresh page
    exit();
}

// Fetch all notifications
$stmt = $conn->prepare("SELECT * FROM notification WHERE user_id=? ORDER BY created_at DESC");
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2>Notifications</h2>

<table border="1" cellpadding="10">
<tr>
    <th>Message</th>
    <th>Date</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php while ($row = $result->fetch_assoc()) { ?>
<tr>
    <td><?= $row['message'] ?></td>
    <td><?= $row['created_at'] ?></td>
    <td><?= $row['is_read'] ? 'Read' : 'Unread' ?></td>
    <td>
        <?php if (!$row['is_read']) { ?>
            <form method="POST" style="margin:0;">
                <input type="hidden" name="notification_id" value="<?= $row['notification_id'] ?>">
                <button type="submit" name="mark_read">Mark as Read</button>
            </form>
        <?php } else { ?>
            -
        <?php } ?>
    </td>
</tr>
<?php } ?>
</table>
