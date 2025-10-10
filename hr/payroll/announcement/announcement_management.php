<?php
include '../../db.php';

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

if(isset($_POST['add'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    mysqli_query($conn, "INSERT INTO announcements (title, category, content, created_at) VALUES ('$title', '$category', '$content', NOW())");
}

if(isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    mysqli_query($conn, "UPDATE announcements SET title='$title', category='$category', content='$content' WHERE id=$id");
}

if(isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM announcements WHERE id=$id");
}

$announcements = mysqli_query($conn, "SELECT * FROM announcements ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Announcements - Payroll</title>
<link rel="stylesheet" href="announcement.css">
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>
<body>
<div class="overlay">
  <div class="container">
    
    <div class="left-panel">
      <h1>MANAGE ANNOUNCEMENTS</h1>
      <form class="announcement-form" method="POST">
        <input type="hidden" name="id" id="announcement-id">
        <input type="text" name="title" id="announcement-title" placeholder="Announcement Title" required>
        <select name="category" id="announcement-category" required>
          <option value="">Select Category</option>
          <option value="Payroll Reminders">Payroll Reminders</option>
          <option value="HR & Policy Updates">HR & Policy Updates</option>
          <option value="Holidays & Events">Holidays & Events</option>
        </select>
        <textarea name="content" id="announcement-content" placeholder="Announcement Content" required></textarea>
        <div class="form-buttons">
          <a href="http://localhost/hotel/hr/payroll/payroll.php" class="nav-btn">&#8592; Back To Dashboard</a>
          <button type="submit" name="add" id="add-btn">Add Announcement</button>
          <button type="submit" name="update" id="update-btn" style="display:none;">Update Announcement</button>
          <button type="button" id="cancel-btn" style="display:none;">Cancel</button>
        </div>
      </form>
    </div>
    
    <div class="right-panel">
      <div class="announcement-list">
        <?php while($row = mysqli_fetch_assoc($announcements)) { ?>
        <div class="announcement-item">
          <h3><?php echo htmlspecialchars($row['title']); ?></h3>
          <p><em><?php echo htmlspecialchars($row['category']); ?></em></p>
          <p><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
          <div class="actions">
            <button class="edit-btn" onclick="editAnnouncement(<?php echo $row['id']; ?>,'<?php echo addslashes($row['title']); ?>','<?php echo addslashes($row['category']); ?>','<?php echo addslashes($row['content']); ?>')"><i class="fas fa-edit"></i> Edit</button>
            <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete this announcement?')" class="delete-btn"><i class="fas fa-trash-alt"></i> Delete</a>
          </div>
        </div>
        <?php } ?>
      </div>
    </div>

  </div>
</div>

<script>
function editAnnouncement(id, title, category, content) {
  document.getElementById('announcement-id').value = id;
  document.getElementById('announcement-title').value = title;
  document.getElementById('announcement-category').value = category;
  document.getElementById('announcement-content').value = content;
  document.getElementById('add-btn').style.display = 'none';
  document.getElementById('update-btn').style.display = 'inline-block';
  document.getElementById('cancel-btn').style.display = 'inline-block';
}

document.getElementById('cancel-btn').addEventListener('click', function() {
  document.getElementById('announcement-id').value = '';
  document.getElementById('announcement-title').value = '';
  document.getElementById('announcement-category').value = '';
  document.getElementById('announcement-content').value = '';
  document.getElementById('add-btn').style.display = 'inline-block';
  document.getElementById('update-btn').style.display = 'none';
  document.getElementById('cancel-btn').style.display = 'none';
});
</script>
</body>
</html>
