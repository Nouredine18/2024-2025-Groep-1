<?php
include 'connect.php';
session_start();

// Check if the popup was shown in the last hour
if (!isset($_SESSION['last_popup']) || (time() - $_SESSION['last_popup']) > 1) {
    $_SESSION['show_popup'] = true;
    $_SESSION['last_popup'] = time();
} else {
    $_SESSION['show_popup'] = false;
}

// Threshold voor nieuwe producten (laatste 30 dagen)
$newProductThreshold = new DateTime('-30 days');

// Check if the user is logged in and is a customer
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Query to find the most purchased brand by the user
    $brandQuery = "
        SELECT p.merk, COUNT(bp.artikelnr) as count
        
        FROM BoughtProducts bp
        JOIN Products p ON bp.artikelnr = p.artikelnr
        WHERE bp.user_id = ?
        GROUP BY p.merk
        ORDER BY count DESC
        LIMIT 1
    ";
    $stmt = $conn->prepare($brandQuery);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $brandResult = $stmt->get_result();
    $mostPurchasedBrand = $brandResult->fetch_assoc()['merk'] ?? null;

    // Fetch products of the most purchased brand
    if ($mostPurchasedBrand) {
        $recommendedQuery = "
            SELECT artikelnr, naam, prijs, directory, created_at
            FROM Products
            WHERE merk = ?
            LIMIT 5
        ";
        $stmt = $conn->prepare($recommendedQuery);
        $stmt->bind_param('s', $mostPurchasedBrand);
        $stmt->execute();
        $recommendedResult = $stmt->get_result();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schoenen Wijns</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        /* General styles */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Header styles */
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

        /* Container for content */
        .container {
            flex: 1;
            display: flex;
            justify-content: space-between;
            margin: 20px;
            flex-wrap: wrap;
        }

        /* Footer styles */
        .footer {
            background-color: #000;
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: auto;
        }

        /* Sidebar styles */
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

        /* Product display styles */
        .products {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: flex-start;
            align-items: flex-start;
            flex-grow: 1;
        }

        .product-card {
            flex: 1 1 calc(20% - 15px); /* Zorgt ervoor dat producten naast elkaar staan, 5 per rij */
            max-width: 220px; /* Voorkomt te brede items */
            height: auto; /* Zorgt ervoor dat de hoogte zich aanpast aan de inhoud */
            position: relative; /* Voor het positioneren van het "Nieuw" label */
        }

        .product-card img {
            width: 100%;
            height: 170px;
            object-fit: cover;
            border-bottom: 2px solid #f5f5f5;
        }

        .product-card:hover {
            transform: scale(1.05);
            box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.15);
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
            text-overflow: ellipsis; /* Ensures product names don't overflow */
        }

        .product-card p {
            font-size: 16px;
            color: #111;
            font-weight: 500;
        }

        /* Footer styles */
        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            z-index: 1000;
            text-align: center;
        }

        .popup h2 {
            margin-top: 0;
        }

        .popup .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
        }

        .popup-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        /* Recommended section styles */
        .recommended {
            width: 100%;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .recommended h3 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .recommended .products {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
        }

        /* Add some custom styles for the new product label */
        .new-label {
            background-color: red;
            color: white;
            font-weight: bold;
            padding: 5px 10px;
            position: absolute;
            top: 10px;
            right: 10px;
            border-radius: 5px;
            z-index: 1;
        }

        .discount-label {
            background-color: #ff0000;
            color: #ffffff;
            padding: 2px 6px;
            font-weight: bold;
        }
        .discount-price {
            color: #ff0000;
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>
    <?php include 'popups.php'; ?> <!-- Include advertisement section -->

    <?php if ($_SESSION['show_popup']): ?>
        <div class="popup-overlay"></div>
        <div class="popup">
            <button class="close-btn">&times;</button>
            <h2>Special Offer!</h2>
            <p><?= htmlspecialchars($popupMessage) ?></p>
        </div>
    <?php endif; ?>

    <div class="container">
        <!-- Recommended Products Section -->
        <?php if (isset($recommendedResult) && $recommendedResult->num_rows > 0): ?>
            <div class="recommended">
                <h3>Recommended for You</h3>
                <div class="products">
                    <?php while ($row = $recommendedResult->fetch_assoc()): ?>
                        <div class="product-card">
                            <a href="info_product.php?artikelnr=<?= htmlspecialchars($row['artikelnr']) ?>">
                                <img src="directory/<?= htmlspecialchars($row['directory']) ?>" alt="<?= htmlspecialchars($row['naam']) ?>">
                            </a>
                            <div class="info">
                                <h4><?= htmlspecialchars($row['naam']) ?></h4>
                                <p>€<?= number_format($row['prijs'], 2) ?></p>
                            </div>
                            <?php
                            $createdAt = new DateTime($row['created_at']);
                            if ($createdAt > $newProductThreshold): ?>
                                <span class="new-label">NEW</span>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

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
                    <option value="newest" <?= ($_GET['sort'] ?? '') == 'newest' ? 'selected' : '' ?>>Newest Arrivals</option>
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
            $query = "SELECT DISTINCT p.artikelnr, p.naam, p.prijs, p.directory, p.popularity, p.created_at, p.discount 
                      FROM Products p
                      LEFT JOIN ProductVariant pv ON p.artikelnr = pv.artikelnr";

            // Apply filters for product variants (size, color, brand, price, etc.)
            $queryConditions = [];

            // Apply search filter if needed
            if (!empty($_GET['search'])) {
                $queryConditions[] = "p.naam LIKE ?";
                $params[] = '%' . $_GET['search'] . '%';
                $types .= 's';
            }

            // Apply price range filter if needed
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

            // Apply filters for variants (size, color)
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

            // Apply brand filter
            if (!empty($_GET['merk'])) {
                $queryConditions[] = "p.merk = ?";
                $params[] = $_GET['merk'];
                $types .= 's';
            }

            // Combine conditions for WHERE clause
            if (!empty($queryConditions)) {
                $query .= " WHERE " . implode(" AND ", $queryConditions);
            }

            // Apply sorting options
            if (!empty($_GET['sort'])) {
                $sortOptions = [
                    'lowest' => "ORDER BY p.prijs ASC",
                    'highest' => "ORDER BY p.prijs DESC",
                    'popularity_asc' => "ORDER BY p.popularity ASC",
                    'popularity_desc' => "ORDER BY p.popularity DESC",
                    'newest' => "ORDER BY p.created_at DESC"
                ];
                $query .= " " . $sortOptions[$_GET['sort']];
            }

            // Prioritize products with discounts
            $query .= " ORDER BY p.discount DESC, p.created_at DESC";

            // Prepare and execute the query
            $stmt = $conn->prepare($query);
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $result = $stmt->get_result();

            // Determine the threshold for new products (e.g., products added in the last 30 days)
            $newProductThreshold = new DateTime('-30 days');

            // Display products if they have matching variants
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="product-card">';
                    echo '<a href="info_product.php?artikelnr=' . htmlspecialchars($row['artikelnr']) . '">';
                    echo '<img src="directory/' . htmlspecialchars($row['directory']) . '" alt="' . htmlspecialchars($row['naam']) . '">';
                    echo '</a>';
                    echo '<div class="info">';
                    echo '<h4>' . htmlspecialchars($row['naam']) . '</h4>';

                    // Calculate and display the discounted price if applicable
                    $price = $row['prijs'];
                    if ($row['discount'] > 0) {
                        $discountedPrice = $price - ($price * ($row['discount'] / 100));
                        echo '<p><span style="text-decoration: line-through;">€' . number_format($price, 2) . '</span> <span class="discount-price">€' . number_format($discountedPrice, 2) . '</span></p>';
                        echo '<span class="discount-label">DISCOUNT</span>';
                    } else {
                        echo '<p>€' . number_format($price, 2) . '</p>';
                    }

                    // Check if the product is new (added in the last 30 days)
                    $createdAt = new DateTime($row['created_at']);
                    if ($createdAt > $newProductThreshold) {
                        echo '<span class="new-label">NEW</span>';
                    }

                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p>No products found for the selected filters.</p>';
            }
            ?>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const popup = document.querySelector('.popup');
            const overlay = document.querySelector('.popup-overlay');
            const closeBtn = document.querySelector('.popup .close-btn');

            if (popup && overlay) {
                popup.style.display = 'block';
                overlay.style.display = 'block';

                closeBtn.addEventListener('click', function() {
                    popup.style.display = 'none';
                    overlay.style.display = 'none';
                });
            }
        });
    </script>
    <?php include('footer.php'); ?>

</body>
</html>