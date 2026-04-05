<?php
session_start();
require_once "../config/db.php";

// Check if admin is logged in
if(!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true){
    header("location: admin_login.php");
    exit;
}

// Get total number of orders
$orders_count = mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE status != 'Cancelled'")->fetch_assoc()['total'];

// Get total number of print orders
$print_orders_count = mysqli_query($conn, "SELECT COUNT(*) as total FROM printing_orders WHERE status != 'Cancelled'")->fetch_assoc()['total'];

// Get total number of products
$products_count = mysqli_query($conn, "SELECT COUNT(*) as total FROM products")->fetch_assoc()['total'];

// Get total number of students
$students_count = mysqli_query($conn, "SELECT COUNT(*) as total FROM students")->fetch_assoc()['total'];

// Get recent orders
$recent_orders_sql = "SELECT o.*, p.name as product_name, s.name as student_name 
                     FROM orders o 
                     JOIN products p ON o.product_id = p.product_id 
                     JOIN students s ON o.student_id = s.student_id 
                     ORDER BY o.date DESC LIMIT 5";
$recent_orders = mysqli_query($conn, $recent_orders_sql);

// Get low stock products (less than 10 items)
$low_stock_sql = "SELECT * FROM products WHERE quantity < 10 ORDER BY quantity ASC";
$low_stock = mysqli_query($conn, $low_stock_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Campus Store</title>
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
                        <a class="nav-link" href="manage_products.php">Manage Products</a>
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
        <div class="dashboard-stats">
            <div class="stat-card">
                <h3><?php echo $orders_count; ?></h3>
                <p>Active Orders</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $print_orders_count; ?></h3>
                <p>Print Orders</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $products_count; ?></h3>
                <p>Total Products</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $students_count; ?></h3>
                <p>Registered Students</p>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Recent Orders</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Student</th>
                                        <th>Product</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if(mysqli_num_rows($recent_orders) > 0) {
                                        while($order = mysqli_fetch_assoc($recent_orders)) {
                                            echo "<tr>";
                                            echo "<td>" . htmlspecialchars($order['order_id']) . "</td>";
                                            echo "<td>" . htmlspecialchars($order['student_name']) . "</td>";
                                            echo "<td>" . htmlspecialchars($order['product_name']) . "</td>";
                                            echo "<td>$" . htmlspecialchars($order['total_price']) . "</td>";
                                            echo "<td>" . htmlspecialchars($order['status']) . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='5' class='text-center'>No recent orders.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Low Stock Alert</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        if(mysqli_num_rows($low_stock) > 0) {
                            while($product = mysqli_fetch_assoc($low_stock)) {
                                echo '<div class="alert ' . ($product['quantity'] < 5 ? 'alert-danger' : 'alert-warning') . '">';
                                echo '<strong>' . htmlspecialchars($product['name']) . '</strong><br>';
                                echo 'Remaining: ' . htmlspecialchars($product['quantity']) . ' units';
                                echo '</div>';
                            }
                        } else {
                            echo '<p class="text-success">All products are well-stocked.</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>