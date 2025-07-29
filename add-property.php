<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'agent') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $location = $_POST['location'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $property_type = $_POST['property_type'];
    $bedrooms = $_POST['bedrooms'];
    $bathrooms = $_POST['bathrooms'];
    $amenities = $_POST['amenities'];
    $user_id = $_SESSION['user_id'];

    // Handle multiple image uploads
    $images = [];
    if (!empty($_FILES['images']['name'][0])) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        foreach ($_FILES['images']['name'] as $key => $name) {
            $tmp_name = $_FILES['images']['tmp_name'][$key];
            $target_file = $upload_dir . basename($name);
            if (move_uploaded_file($tmp_name, $target_file)) {
                $images[] = $target_file;
            }
        }
    }
    $images_str = implode(',', $images);

    try {
        $stmt = $pdo->prepare("INSERT INTO properties (user_id, title, description, price, location, city, state, property_type, bedrooms, bathrooms, amenities, images, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'approved')");
        $stmt->execute([$user_id, $title, $description, $price, $location, $city, $state, $property_type, $bedrooms, $bathrooms, $amenities, $images_str]);
        header('Location: dashboard.php');
    } catch (PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Property - Zillow Clone</title>
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
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        }
        h2 {
            color: #333;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        .form-group {
            margin-bottom: 1.2rem;
        }
        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #444;
            font-weight: bold;
        }
        input, select, textarea {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        input:focus, select:focus, textarea:focus {
            border-color: #0078ff;
            outline: none;
        }
        textarea {
            height: 120px;
            resize: vertical;
        }
        button {
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
        button:hover {
            background: #005bb5;
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
            .container {
                margin: 1rem;
                padding: 1rem;
            }
            header h1 {
                font-size: 1.4rem;
            }
            nav a {
                margin: 0 0.5rem;
                font-size: 0.9rem;
            }
        }
    </style>
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
        <h2>Add New Property</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" required></textarea>
            </div>
            <div class="form-group">
                <label for="price">Price ($)</label>
                <input type="number" id="price" name="price" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" id="location" name="location" required>
            </div>
            <div class="form-group">
                <label for="city">City</label>
                <input type="text" id="city" name="city" required>
            </div>
            <div class="form-group">
                <label for="state">State</label>
                <input type="text" id="state" name="state" required>
            </div>
            <div class="form-group">
                <label for="property_type">Property Type</label>
                <select id="property_type" name="property_type" required>
                    <option value="house">House</option>
                    <option value="apartment">Apartment</option>
                    <option value="commercial">Commercial</option>
                </select>
            </div>
            <div class="form-group">
                <label for="bedrooms">Bedrooms</label>
                <input type="number" id="bedrooms" name="bedrooms" required>
            </div>
            <div class="form-group">
                <label for="bathrooms">Bathrooms</label>
                <input type="number" id="bathrooms" name="bathrooms" required>
            </div>
            <div class="form-group">
                <label for="amenities">Amenities (comma-separated)</label>
                <input type="text" id="amenities" name="amenities">
            </div>
            <div class="form-group">
                <label for="images">Images (Multiple)</label>
                <input type="file" id="images" name="images[]" accept="image/*" multiple>
            </div>
            <button type="submit">Add Property</button>
        </form>
    </div>
    <footer>
        <p>© 2025 Zillow Clone. All rights reserved.</p>
    </footer>
</body>
</html>
