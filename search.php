<?php
require 'db.php';
session_start();

$filters = [];
$params = [];

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!empty($_GET['city'])) {
        $filters[] = "city LIKE ?";
        $params[] = "%" . $_GET['city'] . "%";
    }
    if (!empty($_GET['min_price'])) {
        $filters[] = "price >= ?";
        $params[] = $_GET['min_price'];
    }
    if (!empty($_GET['max_price'])) {
        $filters[] = "price <= ?";
        $params[] = $_GET['max_price'];
    }
    if (!empty($_GET['property_type']) && $_GET['property_type'] != 'any') {
        $filters[] = "property_type = ?";
        $params[] = $_GET['property_type'];
    }
    if (!empty($_GET['bedrooms'])) {
        $filters[] = "bedrooms >= ?";
        $params[] = $_GET['bedrooms'];
    }
    if (!empty($_GET['amenities'])) {
        $filters[] = "amenities LIKE ?";
        $params[] = "%" . $_GET['amenities'] . "%";
    }
}

$query = "SELECT * FROM properties WHERE status = 'approved'";
if (!empty($filters)) {
    $query .= " AND " . implode(" AND ", $filters);
}
$query .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$properties = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Properties - Zillow Clone</title>
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
        .search-form {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
            margin-bottom: 2rem;
        }
        .search-form h2 {
            color: #333;
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 1.8rem;
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
        input, select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        input:focus, select:focus {
            border-color: #0078ff;
            outline: none;
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
            .search-form {
                padding: 1rem;
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
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>
    <div class="container">
        <div class="search-form">
            <h2>Search Properties</h2>
            <form method="GET">
                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city" value="<?php echo isset($_GET['city']) ? htmlspecialchars($_GET['city']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="min_price">Min Price ($)</label>
                    <input type="number" id="min_price" name="min_price" value="<?php echo isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="max_price">Max Price ($)</label>
                    <input type="number" id="max_price" name="max_price" value="<?php echo isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="property_type">Property Type</label>
                    <select id="property_type" name="property_type">
                        <option value="any" <?php echo !isset($_GET['property_type']) || $_GET['property_type'] == 'any' ? 'selected' : ''; ?>>Any</option>
                        <option value="house" <?php echo isset($_GET['property_type']) && $_GET['property_type'] == 'house' ? 'selected' : ''; ?>>House</option>
                        <option value="apartment" <?php echo isset($_GET['property_type']) && $_GET['property_type'] == 'apartment' ? 'selected' : ''; ?>>Apartment</option>
                        <option value="commercial" <?php echo isset($_GET['property_type']) && $_GET['property_type'] == 'commercial' ? 'selected' : ''; ?>>Commercial</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="bedrooms">Min Bedrooms</label>
                    <input type="number" id="bedrooms" name="bedrooms" value="<?php echo isset($_GET['bedrooms']) ? htmlspecialchars($_GET['bedrooms']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="amenities">Amenities (e.g., pool, garage)</label>
                    <input type="text" id="amenities" name="amenities" value="<?php echo isset($_GET['amenities']) ? htmlspecialchars($_GET['amenities']) : ''; ?>">
                </div>
                <button type="submit">Search</button>
            </form>
        </div>
        <h2>Search Results</h2>
        <div class="properties">
            <?php if (empty($properties)): ?>
                <p>No properties found matching your criteria.</p>
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
