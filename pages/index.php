<?php
session_start();
require_once '../config/db_connect.php';
include '../includes/header.php';
?>

<link rel="stylesheet" href="../css/index.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap">
<script src="../js/main.js" defer></script>

<div class="hero-section">
    <div class="container">
        <h1>Care Compass Hospitals</h1>
        <p>Your trusted healthcare partner with locations in Kandy, Colombo, and Kurunegala</p>
    </div>
</div>
</br>
<body>


<div class="welcome-section">
    <h3>WELCOME TO</h3>
    <h1>Care Compass Hospitals</h1>
    <p>
        Care Compass Hospitals is your trusted healthcare partner in Sri Lanka, offering state-of-the-art medical
        facilities and unparalleled patient care. With locations in Kandy, Colombo, and Kurunegala, we are committed
        to revolutionizing the healthcare experience with advanced treatments and compassionate service.
    </p>
    <a href="../pages/contact.php" class="btn-about">ABOUT US</a>
</div>


<div class="services-section">
    <div class="services-content">
        <h3>SERVICES</h3>
        <h2>ENSURING THE BEST IN THE INDUSTRY</h2>
        <p>
            Care Compass Hospitals is committed to providing compassionate care and excellent service
            that transcends conventional healthcare.
        </p>
        <a href="services.php" class="btn-services">SERVICES</a>
    </div>
    <div class="services-image">
        <img src="../uploads/images/service.jpg" alt="Services Image">
    </div>
</div>


<div class="slideshow-container">
    <div class="slide fade">
      <img src="../uploads/slider/1.png" alt="Slide 1">
      <div class="caption">Free Medical Check-up</div>
    </div>
    <div class="slide fade">
      <img src="../uploads/slider/2.png" alt="Slide 2">
      <div class="caption">Care Compass Hospitals</div>
    </div>
    <div class="slide fade">
      <img src="../uploads/slider/3.png" alt="Slide 3">
      <div class="caption">Professional Doctors</div>
    </div>
    <!-- Next and Previous buttons -->
    <a class="prev" onclick="changeSlide(-1)">&#10094;</a>
    <a class="next" onclick="changeSlide(1)">&#10095;</a>
  </div>


  <div class="lab-facilities-section">
    <div class="lab-facilities-content">
        <h3>LAB FACILITIES</h3>
        <h2>STATE-OF-THE-ART MEDICAL LABORATORIES</h2>
        <p>
            At Care Compass Hospitals, our laboratories are equipped with the latest technology to
            ensure precise diagnostics and efficient medical testing, setting a benchmark for quality care.
        </p>
        <a href="lab-facilities.php" class="btn-lab-facilities">LEARN MORE</a>
    </div>
    <div class="lab-facilities-image">
        <img src="../uploads/images/lab.jpg" alt="Lab Facilities Image">
    </div>
</div>


</div>
<?php include '../includes/footer.php'; ?>

</body>



