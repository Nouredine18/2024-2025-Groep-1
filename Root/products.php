<?php
include 'connect.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schoenen Wijns</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        .header {
            background-color: #000;
            color: white;
            text-align: center;
            padding: 20px 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            font-size: 30px;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .container {
            display: flex;
            justify-content: space-between;
            margin: 20px;
            flex-wrap: wrap;
        }

        .sidebar {
            width: 250px;
            background-color: #fff;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar h3 {
            font-size: 22px;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .sidebar label {
            font-size: 14px;
            font-weight: 500;
        }

        .sidebar input, .sidebar select, .sidebar button {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border-radius: 8px;
            border: 1px solid #ddd;
            font-size: 14px;
            box-sizing: border-box;
        }

        .sidebar button {
            background-color: #111;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .sidebar button:hover {
            background-color: #333;
        }

        .products {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
            flex-grow: 1;
            padding-left: 20px;
        }

        .product-card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            max-width: 260px;
            text-align: center;
            height: 280px;
        }

        .product-card:hover {
            transform: scale(1.05);
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.15);
        }

        .product-card img {
            width: 100%;
            height: 170px;
            object-fit: cover;
            border-bottom: 2px solid #f5f5f5;
        }

        .product-card .info {
            padding: 10px 15px;
            overflow: hidden;
        }

        .product-card h4 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .product-card p {
            font-size: 16px;
            color: #111;
            font-weight: 500;
        }

        /* Footer styles */
        .footer {
            background-color: #111;
            color: white;
            text-align: center;
            padding: 15px;
            position: fixed;
            width: 100%;
            bottom: 0;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>

    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h3>Filter Options</h3>
            <form method="GET" action="products.php">
                <label for="search">Search:</label>
                <input type="text" name="search" placeholder="Search for shoes..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">

                <label for="price-range">Price Range:</label>
                <input type="number" name="min_price" placeholder="Min Price" step="0.01" value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>">
                <input type="number" name="max_price" placeholder="Max Price" step="0.01" value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>">

                <label for="merk">Brand:</label>
                <select name="merk">
                    <option value="">All Brands</option>
                    <?php
                    $brandQuery = "SELECT DISTINCT merk FROM Products";
                    $brands = $conn->query($brandQuery);
                    while ($brand = $brands->fetch_assoc()) {
                        $selected = ($_GET['merk'] ?? '') == $brand['merk'] ? 'selected' : '';
                        echo "<option value=\"" . htmlspecialchars($brand['merk']) . "\" $selected>" . htmlspecialchars($brand['merk']) . "</option>";
                    }
                    ?>
                </select>

                <label for="kleur">Color:</label>
                <select name="kleur">
                    <option value="">All Colors</option>
                    <?php
                    $colorQuery = "SELECT DISTINCT kleur FROM ProductVariant";
                    $colors = $conn->query($colorQuery);
                    while ($color = $colors->fetch_assoc()) {
                        $selected = ($_GET['kleur'] ?? '') == $color['kleur'] ? 'selected' : '';
                        echo "<option value=\"" . htmlspecialchars($color['kleur']) . "\" $selected>" . htmlspecialchars($color['kleur']) . "</option>";
                    }
                    ?>
                </select>

                <label for="maat">Size:</label>
                <select name="maat">
                    <option value="">All Sizes</option>
                    <?php
                    for ($i = 30; $i <= 50; $i++) {
                        $selected = ($_GET['maat'] ?? '') == $i ? 'selected' : '';
                        echo "<option value=\"$i\" $selected>$i</option>";
                    }
                    ?>
                </select>

                <label for="sort">Sort By:</label>
                <select name="sort">
                    <option value="lowest" <?= ($_GET['sort'] ?? '') == 'lowest' ? 'selected' : '' ?>>Price: Low to High</option>
                    <option value="highest" <?= ($_GET['sort'] ?? '') == 'highest' ? 'selected' : '' ?>>Price: High to Low</option>
                    <option value="popularity_asc" <?= ($_GET['sort'] ?? '') == 'popularity_asc' ? 'selected' : '' ?>>Popularity: Least to Most</option>
                    <option value="popularity_desc" <?= ($_GET['sort'] ?? '') == 'popularity_desc' ? 'selected' : '' ?>>Popularity: Most to Least</option>
                </select>

                <button type="submit">Apply Filters</button>
            </form>
        </div>

        <!-- Products -->
        <div class="products">
            <?php
            // Initialize filter parameters
            $params = [];
            $types = "";

            // Base query to fetch product details
            $query = "SELECT DISTINCT p.artikelnr, p.naam, p.prijs, p.directory, p.popularity 
                      FROM Products p
                      LEFT JOIN ProductVariant pv ON p.artikelnr = pv.artikelnr";
            $queryConditions = [];

            if (!empty($_GET['search'])) {
                $queryConditions[] = "p.naam LIKE ?";
                $params[] = '%' . $_GET['search'] . '%';
                $types .= 's';
            }

            if (!empty($_GET['min_price'])) {
                $queryConditions[] = "p.prijs >= ?";
                $params[] = $_GET['min_price'];
                $types .= 'd';
            }
            if (!empty($_GET['max_price'])) {
                $queryConditions[] = "p.prijs <= ?";
                $params[] = $_GET['max_price'];
                $types .= 'd';
            }

            if (!empty($_GET['kleur'])) {
                $queryConditions[] = "pv.kleur = ?";
                $params[] = $_GET['kleur'];
                $types .= 's';
            }

            if (!empty($_GET['maat'])) {
                $queryConditions[] = "pv.maat = ?";
                $params[] = $_GET['maat'];
                $types .= 'i';
            }

            if (!empty($_GET['merk'])) {
                $queryConditions[] = "p.merk = ?";
                $params[] = $_GET['merk'];
                $types .= 's';
            }

            if (!empty($queryConditions)) {
                $query .= " WHERE " . implode(" AND ", $queryConditions);
            }

            // Apply sorting options
            if (!empty($_GET['sort'])) {
                $sortOptions = [
                    'lowest' => "ORDER BY p.prijs ASC",
                    'highest' => "ORDER BY p.prijs DESC",
                    'popularity_asc' => "ORDER BY p.popularity ASC",
                    'popularity_desc' => "ORDER BY p.popularity DESC"
                ];
                $query .= " " . $sortOptions[$_GET['sort']];
            }

            $stmt = $conn->prepare($query);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();

            // Display products if they have matching variants
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="product-card">';
                    echo '<a href="info_product.php?artikelnr=' . htmlspecialchars($row['artikelnr']) . '">';
                    echo '<img src="directory/' . htmlspecialchars($row['directory']) . '" alt="' . htmlspecialchars($row['naam']) . '">';
                    echo '</a>';
                    echo '<div class="info">';
                    echo '<h4>' . htmlspecialchars($row['naam']) . '</h4>';
                    echo '<p>€' . number_format($row['prijs'], 2) . '</p>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p>No products found for the selected filters.</p>';
            }
            ?>
        </div>
    </div>

    <div class="footer">
        <p>&copy; 2024 Schoenen Wijns - All Rights Reserved</p>
    </div>
</body>
</html>
