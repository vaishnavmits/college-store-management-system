<?php
session_start();
require_once "../config/db.php";

// Check if admin is logged in
if(!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true){
    header("location: admin_login.php");
    exit;
}

$success = $error = "";

// Handle Add Product
if(isset($_POST['add_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];
    
    $sql = "INSERT INTO products (name, price, quantity) VALUES (?, ?, ?)";
    
    if($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "sdi", $name, $price, $quantity);
        
        if(mysqli_stmt_execute($stmt)) {
            $success = "Product added successfully.";
        } else {
            $error = "Error adding product. Please try again.";
        }
        mysqli_stmt_close($stmt);
    }
}

// Handle Update Stock
if(isset($_POST['update_stock'])) {
    $product_id = $_POST['product_id'];
    $new_quantity = $_POST['new_quantity'];
    
    $sql = "UPDATE products SET quantity = ? WHERE product_id = ?";
    
    if($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "ii", $new_quantity, $product_id);
        
        if(mysqli_stmt_execute($stmt)) {
            $success = "Stock updated successfully.";
        } else {
            $error = "Error updating stock. Please try again.";
        }
        mysqli_stmt_close($stmt);
    }
}

// Get all products
$products_sql = "SELECT * FROM products ORDER BY name";
$products = mysqli_query($conn, $products_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products - Campus Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">Admin Dashboard</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="manage_products.php">Manage Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="view_orders.php">View Orders</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php 
        if(!empty($success)){
            echo '<div class="alert alert-success">' . $success . '</div>';
        }
        if(!empty($error)){
            echo '<div class="alert alert-danger">' . $error . '</div>';
        }
        ?>

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Add New Product</h5>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="form-group mb-3">
                                <label>Product Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="form-group mb-3">
                                <label>Price ($)</label>
                                <input type="number" name="price" class="form-control" step="0.01" min="0" required>
                            </div>
                            <div class="form-group mb-3">
                                <label>Initial Stock</label>
                                <input type="number" name="quantity" class="form-control" min="0" required>
                            </div>
                            <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Current Products</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product ID</th>
                                        <th>Name</th>
                                        <th>Price</th>
                                        <th>Current Stock</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if(mysqli_num_rows($products) > 0) {
                                        while($product = mysqli_fetch_assoc($products)) {
                                            echo "<tr>";
                                            echo "<td>" . htmlspecialchars($product['product_id']) . "</td>";
                                            echo "<td>" . htmlspecialchars($product['name']) . "</td>";
                                            echo "<td>$" . htmlspecialchars($product['price']) . "</td>";
                                            echo "<td>" . htmlspecialchars($product['quantity']) . "</td>";
                                            echo "<td>";
                                            echo "<form method='post' class='d-inline'>";
                                            echo "<input type='hidden' name='product_id' value='" . $product['product_id'] . "'>";
                                            echo "<input type='number' name='new_quantity' class='form-control form-control-sm d-inline' style='width: 80px;' min='0' value='" . $product['quantity'] . "' required>";
                                            echo " <button type='submit' name='update_stock' class='btn btn-primary btn-sm'>Update</button>";
                                            echo "</form>";
                                            echo "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='5' class='text-center'>No products found.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>