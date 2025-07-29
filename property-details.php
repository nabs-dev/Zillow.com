<?php
require 'db.php';
session_start();

if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$property_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM properties WHERE id = ?");
$stmt->execute([$property_id]);
$property = $stmt->fetch();

if (!$property) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $message = trim($_POST['message']);
    $user_id = $_SESSION['user_id'];

    if (!empty($message)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO inquiries (user_id, property_id, message) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $property_id, $message]);
            $success = "Inquiry sent successfully! The agent will review your message.";
        } catch (PDOException $e) {
            $error = "Error sending inquiry: " . $e->getMessage();
        }
    } else {
        $error = "Please enter a message.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Property Details - Zillow Clone</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }
        header {
            background: #0078ff;
            color: white;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        header h1 {
            margin: 0;
            font-size: 1.8rem;
        }
        nav a {
            color: white;
            margin: 0 1rem;
            text-decoration: none;
            font-size: 1rem;
            transition: color 0.3s;
        }
        nav a:hover {
            color: #e0e0e0;
        }
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        .property-details {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        }
        .carousel {
            position: relative;
            max-width: 100%;
            overflow: hidden;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }
        .carousel img {
            width: 100%;
            max-height: 500px;
            object-fit: cover;
            display: none;
        }
        .carousel img.active {
            display: block;
        }
        .carousel .prev, .carousel .next {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0,0,0,0.5);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            cursor: pointer;
            font-size: 1.2rem;
        }
        .carousel .prev {
            left: 10px;
        }
        .carousel .next {
            right: 10px;
        }
        .property-details h2 {
            color: #333;
            margin-bottom: 1rem;
            font-size: 2rem;
        }
        .property-details p {
            color: #555;
            margin-bottom: 0.8rem;
            font-size: 1rem;
        }
        .btn {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background: #0078ff;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            margin: 0.5rem 0;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #005bb5;
        }
        .inquiry-form {
            margin-top: 2rem;
            padding: 1.5rem;
            background: #f9f9f9;
            border-radius: 10px;
        }
        .inquiry-form h3 {
            color: #333;
            margin-bottom: 1rem;
        }
        .inquiry-form textarea {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1rem;
            height: 120px;
            resize: vertical;
        }
        .inquiry-form button {
            width: 100%;
            padding: 0.9rem;
            background: #0078ff;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background 0.3s;
        }
        .inquiry-form button:hover {
            background: #005bb5;
        }
        .success {
            color: #2e7d32;
            text-align: center;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        .error {
            color: #d32f2f;
            text-align: center;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        footer {
            background: #222;
            color: white;
            text-align: center;
            padding: 1rem;
            margin-top: 2rem;
            position: relative;
            bottom: 0;
            width: 100%;
        }
        @media (max-width: 768px) {
            .property-details {
                padding: 1rem;
            }
            .carousel img {
                max-height: 300px;
            }
            .property-details h2 {
                font-size: 1.5rem;
            }
            nav a {
                margin: 0 0.5rem;
                font-size: 0.9rem;
            }
        }
    </style>
    <script>
        function redirectToSaveProperty(id) {
            window.location.href = 'save-property.php?id=' + id;
        }

        // Carousel functionality
        document.addEventListener('DOMContentLoaded', () => {
            const images = document.querySelectorAll('.carousel img');
            let currentIndex = 0;

            if (images.length > 0) {
                images[currentIndex].classList.add('active');
            }

            document.querySelector('.carousel .next')?.addEventListener('click', () => {
                images[currentIndex].classList.remove('active');
                currentIndex = (currentIndex + 1) % images.length;
                images[currentIndex].classList.add('active');
            });

            document.querySelector('.carousel .prev')?.addEventListener('click', () => {
                images[currentIndex].classList.remove('active');
                currentIndex = (currentIndex - 1 + images.length) % images.length;
                images[currentIndex].classList.add('active');
            });
        });
    </script>
</head>
<body>
    <header>
        <h1>Zillow Clone</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="search.php">Search</a>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>
    <div class="container">
        <div class="property-details">
            <div class="carousel">
                <?php
                $images = explode(',', $property['images']);
                if (empty($images[0])) {
                    echo '<img src="https://via.placeholder.com/600x400" alt="Property">';
                } else {
                    foreach ($images as $image) {
                        echo '<img src="' . htmlspecialchars($image) . '" alt="Property">';
                    }
                }
                ?>
                <?php if (count($images) > 1 || !empty($images[0])): ?>
                    <button class="prev">❮</button>
                    <button class="next">❯</button>
                <?php endif; ?>
            </div>
            <h2><?php echo htmlspecialchars($property['title']); ?></h2>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($property['location']); ?>, <?php echo htmlspecialchars($property['city']); ?>, <?php echo htmlspecialchars($property['state']); ?></p>
            <p><strong>Price:</strong> $<?php echo number_format($property['price'], 2); ?></p>
            <p><strong>Type:</strong> <?php echo ucfirst($property['property_type']); ?></p>
            <p><strong>Bedrooms:</strong> <?php echo $property['bedrooms']; ?></p>
            <p><strong>Bathrooms:</strong> <?php echo $property['bathrooms']; ?></p>
            <p><strong>Amenities:</strong> <?php echo htmlspecialchars($property['amenities'] ?: 'None'); ?></p>
            <p><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($property['description'])); ?></p>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="#" class="btn" onclick="redirectToSaveProperty(<?php echo $property['id']; ?>)">Save Property</a>
                <div class="inquiry-form">
                    <h3>Send Inquiry</h3>
                    <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
                    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
                    <form method="POST">
                        <textarea name="message" placeholder="Your message to the agent..." required></textarea>
                        <button type="submit">Send Inquiry</button>
                    </form>
                </div>
            <?php else: ?>
                <p><a href="login.php" class="btn">Login</a> to save or send an inquiry.</p>
            <?php endif; ?>
        </div>
    </div>
    <footer>
        <p>© 2025 Zillow Clone. All rights reserved.</p>
    </footer>
</body>
</html>
