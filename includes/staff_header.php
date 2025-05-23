<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/header.css" />
    <title>Care Compass Hospitals</title>
  </head>
  <body>
    <header>
      <nav class="nav">
        <a href="../staff/staff_panel.php" class="logo">Care Compass</a>

        <div class="hamburger">
          <span class="line"></span>
          <span class="line"></span>
          <span class="line"></span>
        </div>

        <div class="nav__link hide">
          <a href="../staff/staff_panel.php">Home</a>
          <a href="../staff/manage_laboratory.php">Manage Laboratory</a>
          <a href="../staff/manage_services.php">Manage Services</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="../pages/logout.php">Logout</a>
                <?php else: ?>
                    <a href="../pages/login.php">Login</a>
                    <a href="../pages/register.php">Register</a>
                <?php endif;
            ?>
        </div>
      </nav>
    </header>
  </body>

  <script src="../js/header.js"></script>
</html>

