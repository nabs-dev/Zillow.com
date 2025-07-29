<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Fetch agent's properties
$stmt = $pdo->prepare("SELECT * FROM properties WHERE user_id = ?");
$stmt->execute([$user_id]);
$my_properties = $stmt->fetchAll();

// Fetch saved properties
$stmt = $pdo->prepare("SELECT p.* FROM properties p JOIN saved_properties sp ON p.id = sp.property_id WHERE sp.user_id = ?");
$stmt->execute([$user_id]);
$saved_properties = $stmt->fetchAll();

// Fetch inquiries for agent's properties
$stmt = $pdo->prepare("SELECT i.*, p.title AS property_title, u.username AS sender FROM inquiries i 
                       JOIN properties p ON i.property_id = p.id 
                       JOIN users u ON i.user_id = u.id 
                       WHERE p.user_id = ? ORDER BY i.created_at DESC");
$stmt->execute([$user_id]);
$inquiries = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Zillow Clone</title>
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
        h2 {
            color: #333;
            margin-bottom: 1rem;
            font-size: 2rem;
            text-align: center;
        }
        .properties, .inquiries {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }
        .property-card, .inquiry-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .property-card:hover, .inquiry-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .property-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }
        .property-card h3, .inquiry-card h3 {
            margin: 1rem;
            font-size: 1.4rem;
            color: #333;
        }
        .property-card p, .inquiry-card p {
            margin: 0 1rem 1rem;
            color: #666;
            font-size: 1rem;
        }
        .property-card a {
            display: block;
            margin: 1rem;
            padding: 0.8rem;
            background: #0078ff;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 8px;
            transition: background 0.3s;
        }
        .property-card a:hover {
            background: #005bb5;
        }
        .btn {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background: #0078ff;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 1rem;
            transition: background 0.3s;
        }
        .btn:hover {
            background: #005bb5;
        }
        .inquiry-card {
            padding: 1rem;
        }
        .inquiry-card .message {
            background: #f9f9f9;
            padding: 1rem;
            border-radius: 8px;
            margin: 0 1rem 1rem;
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
            .property-card img {
                height: 180px;
            }
            nav a {
                margin: 0 0.5rem;
                font-size: 0.9rem;
            }
            h2 {
                font-size: 1.5rem;
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
        <h2>Welcome, <?php echo htmlspecialchars($role); ?>!</h2>
        <?php if ($role == 'agent'): ?>
            <a href="add-property.php" class="btn">Add New Property</a>
            <h2>My Properties</h2>
            <div class="properties">
                <?php if (empty($my_properties)): ?>
                    <p>No properties listed yet.</p>
                <?php else: ?>
                    <?php foreach ($my_properties as $property): ?>
                        <div class="property-card">
                            <img src="<?php 
                                $images = explode(',', $property['images']);
                                echo !empty($images[0]) ? htmlspecialchars($images[0]) : 'https://via.placeholder.com/300x220'; 
                            ?>" alt="Property">
                            <h3><?php echo htmlspecialchars($property['title']); ?></h3>
                            <p><?php echo htmlspecialchars($property['location']); ?> - $<?php echo number_format($property['price'], 2); ?></p>
                            <a href="property-details.php?id=<?php echo $property['id']; ?>">View Details</a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <h2>Inbox (Inquiries)</h2>
            <div class="inquiries">
                <?php if (empty($inquiries)): ?>
                    <p>No inquiries received yet.</p>
                <?php else: ?>
                    <?php foreach ($inquiries as $inquiry): ?>
                        <div class="inquiry-card">
                            <h3>From: <?php echo htmlspecialchars($inquiry['sender']); ?></h3>
                            <p><strong>Property:</strong> <?php echo htmlspecialchars($inquiry['property_title']); ?></p>
                            <p><strong>Sent:</strong> <?php echo $inquiry['created_at']; ?></p>
                            <div class="message">
                                <p><strong>Message:</strong> <?php echo nl2br(htmlspecialchars($inquiry['message'])); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <h2>Saved Properties</h2>
        <div class="properties">
            <?php if (empty($saved_properties)): ?>
                <p>No saved properties yet.</p>
            <?php else: ?>
                <?php foreach ($saved_properties as $property): ?>
                    <div class="property-card">
                        <img src="<?php 
                            $images = explode(',', $property['images']);
                            echo !empty($images[0]) ? htmlspecialchars($images[0]) : 'https://via.placeholder.com/300x220'; 
                        ?>" alt="Property">
                        <h3><?php echo htmlspecialchars($property['title']); ?></h3>
                        <p><?php echo htmlspecialchars($property['location']); ?> - $<?php echo number_format($property['price'], 2); ?></p>
                        <a href="property-details.php?id=<?php echo $property['id']; ?>">View Details</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <footer>
        <p>© 2025 Zillow Clone. All rights reserved.</p>
    </footer>
</body>
</html>
