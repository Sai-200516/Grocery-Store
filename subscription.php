<?php
require 'dbcon.php';
session_start();

if (!isset($_SESSION['USER_ID']) || empty($_SESSION['USER_ID'])) {
    header("Location: login.php");
    exit();
}

// Check if cart exists and has items
if(!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo '<script>alert("Your cart is empty. Please add items to your cart first.");
    window.location.href = "index.php";</script>';
    exit();
}

// Debug cart contents
echo '<pre>';
print_r($_SESSION['cart']);
echo '</pre>';

$cart_items = $_SESSION['cart'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Subscription | Grocery Store</title>
    <?php include 'header.php'; ?>

    <div class="w3l_banner_nav_right">
        <div class="privacy about">
            <h3>Setup Your <span>Subscription</span></h3>
            
            <form action="checkout.php" method="post">
                <div class="checkout-right">
                    <h4>Select items for subscription: <span><?=count($cart_items)?> Products</span></h4>
                    <table class="timetable_sub">
                        <thead>
                            <tr>
                                <th>Select</th>
                                <th>Product</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th>Delivery Interval</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total = 0;
                            foreach ($cart_items as $pid => $item) {
                                $result = $conn->query("SELECT * FROM `product` WHERE `pid` = $pid");
                                if ($result && $row = $result->fetch_assoc()) {
                                    $subtotal = $row['price'] * $item['quantity'];
                                    $total += $subtotal;
                            ?>
                            <tr class="rem<?=$pid?>">
                                <td>
                                    <input type="checkbox" name="subscribe_items[]" value="<?=$pid?>" checked>
                                </td>
                                <td>
                                    <img src="<?=$row['pic']?>" alt="<?=$row['name']?>" style="width: 100px;">
                                </td>
                                <td><?=ucwords($row['name'])?></td>
                                <td>₹<?=$row['price']?></td>
                                <td>
                                    <div class="quantity">
                                        <input type="number" name="quantity_<?=$pid?>" value="<?=$item['quantity']?>" min="1" class="form-control">
                                    </div>
                                </td>
                                <td>₹<?=$subtotal?></td>
                                <td>
                                    <select name="interval_<?=$pid?>" class="form-control" required>
                                        <option value="">Select Interval</option>
                                        <option value="3">Every 3 Days</option>
                                        <option value="4">Every 4 Days</option>
                                        <option value="7">Weekly</option>
                                        <option value="10">Every 10 Days</option>
                                        <option value="14">Every 2 Weeks</option>
                                        <option value="30">Monthly</option>
                                    </select>
                                </td>
                            </tr>
                            <?php 
                                }
                            } 
                            ?>
                        </tbody>
                    </table>

                    <div class="subscription-summary">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="subscription-info">
                                    <h4>Subscription Benefits</h4>
                                    <ul>
                                        <li><i class="fa fa-check"></i> Regular automated delivery</li>
                                        <li><i class="fa fa-check"></i> Skip or cancel anytime</li>
                                        <li><i class="fa fa-check"></i> Free delivery on all subscriptions</li>
                                        <li><i class="fa fa-check"></i> Save time and never run out</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="subscription-total">
                                    <h4>Order Summary</h4>
                                    <table class="table">
                                        <tr>
                                            <td>Subtotal:</td>
                                            <td>₹<?=$total?></td>
                                        </tr>
                                        <tr>
                                            <td>Delivery:</td>
                                            <td>FREE</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total:</strong></td>
                                            <td><strong>₹<?=$total?></strong></td>
                                        </tr>
                                    </table>
                                    <input type="hidden" name="total_amount" value="<?=$total?>">
                                    <div class="delivery-confirmation" style="margin-bottom: 15px;"></div>
                                    <button type="submit" class="btn btn-primary btn-lg btn-block" id="checkoutBtn">Proceed to Checkout</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="clearfix"></div>

    <style>
        .subscription-info {
            padding: 20px;
            background: #f9f9f9;
            border-radius: 5px;
            margin: 20px 0;
        }
        .subscription-info ul {
            list-style: none;
            padding: 0;
        }
        .subscription-info li {
            margin: 10px 0;
        }
        .subscription-info i {
            color: #84c639;
            margin-right: 10px;
        }
        .subscription-total {
            padding: 20px;
            background: #f9f9f9;
            border-radius: 5px;
            margin: 20px 0;
        }
        .quantity input {
            width: 60px;
            text-align: center;
        }
        .delivery-confirmation {
            padding: 10px;
            background: #e8f5e9;
            border-radius: 4px;
            display: none;
        }
    </style>

    <?php include 'footer.php'; ?>

    <script>
        $(document).ready(function() {
            $('input[type="number"]').on('change', function() {
                updateTotals();
            });

            function updateTotals() {
                let total = 0;
                $('input[type="number"]').each(function() {
                    const quantity = $(this).val();
                    const price = parseFloat($(this).closest('tr').find('td:eq(3)').text().replace('₹', ''));
                    const subtotal = quantity * price;
                    $(this).closest('tr').find('td:eq(5)').text('₹' + subtotal);
                    total += subtotal;
                });
                $('.subscription-total td:last').text('₹' + total);
                $('input[name="total_amount"]').val(total);
            }

            $('#checkoutBtn').click(function(e) {
                e.preventDefault();
                let intervals = [];
                $('select[name^="interval_"]').each(function() {
                    if ($(this).val()) {
                        let productName = $(this).closest('tr').find('td:eq(2)').text();
                        let interval = $(this).find('option:selected').text();
                        intervals.push(productName + ': ' + interval);
                    }
                });

                if (intervals.length > 0) {
                    $('.delivery-confirmation').html('Confirming subscription delivery intervals:<br>' + 
                        intervals.join('<br>')).show();
                    
                    setTimeout(function() {
                        $('form').submit();
                    }, 2000);
                } else {
                    alert('Please select delivery intervals for your subscribed items');
                }
            });
        });
    </script>
</body>
</html>
