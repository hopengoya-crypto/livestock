<?php
$page_title = 'Livestock Vaccination Reminder System';
include '../includes/header.php';
?>
<nav class="siderbar">
  <div class="logo-menu">
    <h3 class="logo-text">Farmer Dashboard</h3>
    <i class="bx bx-menu-toggle-btn"></i>
  </div>
<ul class="list">
    <li class="list-item">
        <a href="farmer-dashboard.php">
        <span class="link-name">Dashboard</span>
        </a>
        <a href="livestock.php">
        <span class="link-name">Register Livestock</span>
        </a>
        <a href="select.php">
        <span class="link-name">Select Veterinarian</span>
        </a>
        <a href="request.php">
          <span class="link-name">Request Schedule</span>
        </a>
        <a href="update.php">
        <span class="link-name">Update Information</span>
        </a>
        <a href="records.php">
        <span class="link-name">View Records</span>
        </a>
        <a href="logout.php">
        <span class="link-name">Logout</span>
        </a>
    </li>
</ul>
</nav>
<?php include '../includes/footer.php'; ?>