<?php
// Start session and include required files at the very top
session_start();
require_once '../config/db_connect.php';
include '../includes/header.php';
// Initialize variables
$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = $_POST['subject'] ?? '';
    $message_content = $_POST['message'] ?? '';
    $user_id = $_SESSION['user_id'] ?? null;

    try {
        $stmt = $pdo->prepare("INSERT INTO queries (user_id, subject, message) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $subject, $message_content]);
        $message = "Your message has been sent successfully! We'll get back to you soon.";
        $messageType = "success";
    } catch(PDOException $e) {
        $message = "Something went wrong. Please try again later.";
        $messageType = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Our Medical Center</title>
    <style>
        :root {
            --primary: #2b6cb0;
            --secondary: #4299e1;
            --accent: #ebf8ff;
            --text: #2d3748;
            --light: #f7fafc;
            --success: #48bb78;
            --error: #f56565;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f8fafc;
            color: var(--text);
            line-height: 1.6;
        }

        .page-header {
            background: linear-gradient(rgba(44, 82, 130, 0.7), rgba(44, 82, 130, 0.7)),
            url('../uploads/images/hospital-hero.jpg.jpg') center/cover;
            color: white;
            padding: 60px 20px;
            text-align: center;
            margin-bottom: 40px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .page-header h1 {
            font-size: 2.5em;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .page-header p {
            font-size: 1.1em;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }

        .location-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .location-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .location-image {
            height: 200px;
            background: var(--primary);
            position: relative;
            overflow: hidden;
        }

        .location-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.9;
        }

        .location-info {
            padding: 25px;
        }

        .location-info h3 {
            color: var(--primary);
            margin-bottom: 15px;
            font-size: 1.4em;
        }

        .contact-detail {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            color: var(--text);
        }

        .contact-detail i {
            margin-right: 10px;
            color: var(--secondary);
        }

        .form-section {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 50px;
        }

        .form-section h2 {
            color: var(--primary);
            margin-bottom: 30px;
            text-align: center;
            font-size: 2em;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--text);
            font-weight: 500;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1em;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--secondary);
        }

        .submit-btn {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 1.1em;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            width: 100%;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(66, 153, 225, 0.3);
        }

        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            display: none;
            z-index: 1000;
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .success {
            background: var(--success);
            box-shadow: 0 4px 12px rgba(72, 187, 120, 0.2);
        }

        .error {
            background: var(--error);
            box-shadow: 0 4px 12px rgba(245, 101, 101, 0.2);
        }

        @media (max-width: 768px) {
            .page-header {
                padding: 40px 20px;
            }

            .page-header h1 {
                font-size: 2em;
            }

            .form-section {
                padding: 25px;
            }
        }
    </style>
</head>
<body>
    <div class="page-header">
        <h1>Contact Our Medical Center</h1>
        <p>We're here to help you with any questions or concerns. Reach out to our team at any of our locations.</p>
    </div>

    <div class="container">
        <div class="contact-grid">
            <div class="location-card">
                <div class="location-image">
                    <img src="../uploads/images/Colombo.jpg" alt="Colombo Branch">
                </div>
                <div class="location-info">
                    <h3>Colombo Branch</h3>
                    <div class="contact-detail">
                        <i>📍</i>
                        <p>456 Health Avenue, Colombo 07</p>
                    </div>
                    <div class="contact-detail">
                        <i>📞</i>
                        <p>011-2345678</p>
                    </div>
                    <div class="contact-detail">
                        <i>⏰</i>
                        <p>Open 24/7</p>
                    </div>
                </div>
            </div>

            <div class="location-card">
                <div class="location-image">
                    <img src="../uploads/images/Kandy.jpg" alt="Kandy Branch">
                </div>
                <div class="location-info">
                    <h3>Kandy Branch</h3>
                    <div class="contact-detail">
                        <i>📍</i>
                        <p>123 Hospital Road, Kandy</p>
                    </div>
                    <div class="contact-detail">
                        <i>📞</i>
                        <p>081-2234567</p>
                    </div>
                    <div class="contact-detail">
                        <i>⏰</i>
                        <p>8:00 AM - 10:00 PM</p>
                    </div>
                </div>
            </div>

            <div class="location-card">
                <div class="location-image">
                    <img src="../uploads/images/Kurunegala.jpg" alt="Kurunegala Branch">
                </div>
                <div class="location-info">
                    <h3>Kurunegala Branch</h3>
                    <div class="contact-detail">
                        <i>📍</i>
                        <p>789 Medical Street, Kurunegala</p>
                    </div>
                    <div class="contact-detail">
                        <i>📞</i>
                        <p>037-2234567</p>
                    </div>
                    <div class="contact-detail">
                        <i>⏰</i>
                        <p>8:00 AM - 8:00 PM</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h2>Send Us a Message</h2>
            <form id="contactForm" method="POST">
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" required 
                           placeholder="What would you like to discuss?">
                </div>

                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="5" required
                              placeholder="Tell us how we can help you..."></textarea>
                </div>

                <button type="submit" class="submit-btn">Send Message</button>
            </form>
        </div>
    </div>

    <div id="notification" class="notification"></div>

    <script>
    document.getElementById('contactForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(() => {
            showNotification('Your message has been sent successfully! We\'ll get back to you soon.', 'success');
            this.reset();
        })
        .catch(() => {
            showNotification('Something went wrong. Please try again later.', 'error');
        });
    });

    function showNotification(message, type) {
        const notification = document.getElementById('notification');
        notification.textContent = message;
        notification.className = `notification ${type}`;
        notification.style.display = 'block';
        
        setTimeout(() => {
            notification.style.display = 'none';
        }, 4000);
    }

    <?php if ($message): ?>
        showNotification('<?php echo $message; ?>', '<?php echo $messageType; ?>');
    <?php endif; ?>
    </script>

<?php include '../includes/footer.php'; ?>
</body>
</html>