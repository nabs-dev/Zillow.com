<?php
require 'db.php';
session_start();

$stmt = $pdo->query("SELECT * FROM properties ORDER BY created_at DESC LIMIT 6");
$properties = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zillow Clone - Home</title>
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
        .hero {
            background: url('https://images.unsplash.com/photo-1518780664697-55e3ad937233') no-repeat center/cover;
            height: 500px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            text-shadow: 0 0 8px rgba(0,0,0,0.7);
        }
        .hero h2 {
            font-size: 3rem;
            margin: 0;
        }
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }
        h2 {
            color: #333;
            text-align: center;
            margin-bottom: 2rem;
            font-size: 2rem;
        }
        .properties {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }
        .property-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .property-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .property-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }
        .property-card h3 {
            margin: 1rem;
            font-size: 1.4rem;
            color: #333;
        }
        .property-card p {
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
            .hero h2 {
                font-size: 2rem;
            }
            .property-card img {
                height: 180px;
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
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="dashboard.php">Dashboard</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="signup.php">Sign Up</a>
            <?php endif; ?>
        </nav>
    </header>
    <div class="hero">
        <h2>Find Your Dream Home Today</h2>
    </div>
    <div class="container">
        <h2>Featured Properties</h2>
        <div class="properties">
            <?php if (empty($properties)): ?>
                <p>No properties available.</p>
            <?php else: ?>
                <?php foreach ($properties as $property): ?>
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
