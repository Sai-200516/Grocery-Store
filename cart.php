<?php
session_start();
require_once 'dbcon.php';

// Initialize cart if not exists
if(!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

// Handle POST requests for adding items to cart
if(isset($_POST['pid']) && isset($_POST['quantity'])) {
    $pid = $_POST['pid'];
    $quantity = $_POST['quantity'];
    $_SESSION['cart'][$pid] = array('quantity' => $quantity);
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cart</title>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="w3l_banner_nav_right">
        <div class="privacy about">
            <h3>Shopping <span>Cart</span></h3>
            
            <div class="checkout-right">
                <table class="timetable_sub">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Remove</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total = 0;
                        if(!empty($_SESSION['cart'])) {
                            foreach($_SESSION['cart'] as $pid => $item) {
                                $query = "SELECT * FROM product WHERE pid = $pid";
                                $result = $conn->query($query);
                                if($row = $result->fetch_assoc()) {
                                    $subtotal = $row['price'] * $item['quantity'];
                                    $total += $subtotal;
                        ?>
                        <tr>
                            <td><img src="<?= $row['pic'] ?>" alt="<?= $row['name'] ?>" width="100"></td>
                            <td><?= $row['name'] ?></td>
                            <td>₹<?= $row['price'] ?></td>
                            <td>
                                <input type="number" value="<?= $item['quantity'] ?>" min="1" 
                                       class="quantity-input" data-pid="<?= $pid ?>">
                            </td>
                            <td>₹<?= $subtotal ?></td>
                            <td>
                                <button class="remove-item" data-pid="<?= $pid ?>">×</button>
                            </td>
                        </tr>
                        <?php
                                }
                            }
                        }
                        ?>
                    </tbody>
                </table>

                <div class="cart-footer">
                    <div class="cart-total">
                        <h4>Cart Total: ₹<?= $total ?></h4>
                    </div>
                    <div class="cart-buttons">
                        <a href="index.php" class="btn btn-primary">Continue Shopping</a>
                        <?php if(!empty($_SESSION['cart'])) { ?>
                            <a href="subscription.php" class="btn btn-success">Setup Subscription</a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    .cart-footer {
        margin-top: 20px;
        padding: 20px;
        background: #f9f9f9;
        border-radius: 5px;
    }
    .cart-buttons {
        margin-top: 15px;
    }
    .remove-item {
        background: red;
        color: white;
        border: none;
        border-radius: 50%;
        padding: 5px 10px;
        cursor: pointer;
    }
    .quantity-input {
        width: 60px;
        text-align: center;
    }
    </style>

    <script>
    $(document).ready(function() {
        // Update quantity
        $('.quantity-input').change(function() {
            var pid = $(this).data('pid');
            var quantity = $(this).val();
            $.post('update_cart.php', {
                pid: pid,
                quantity: quantity
            }, function() {
                location.reload();
            });
        });

        // Remove item
        $('.remove-item').click(function() {
            var pid = $(this).data('pid');
            $.post('remove_from_cart.php', {
                pid: pid
            }, function() {
                location.reload();
            });
        });
    });
    </script>

    <?php include 'footer.php'; ?>
</body>
</html>