<?php
session_start();
require 'dbcon.php';

// Initialize variables
$products = [];
$cart = [];
$total = 0;

if (!isset($_SESSION['products']) || empty($_SESSION['products'])) {
    if (empty($_POST)) {
        header('Location: index.php');
        exit;
    }
    
    // Process POST data
    foreach ($_POST as $key => $val) {
        $array = explode('_', $key);
        
        if (count($array) > 1) {
            $i = array_pop($array);
        } else {
            $i = $array[0];
        }

        $key = implode('_', $array);

        if (is_numeric($i)) {
            $products[$i][$key] = $val;
        } else {
            $cart[$key] = $val;
        }
    }

    $total = $cart['total'];
    $_SESSION['products'] = $products;
    $_SESSION['total'] = $total;
} else {
    $products = $_SESSION['products'];
    $total = $_SESSION['total'];
}

// Process order if user is logged in
if (isset($_SESSION['USER_ID']) && !empty($_SESSION['USER_ID'])) {
    $uid = $_SESSION['USER_ID'];
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Save order
        $stmt = $conn->prepare("INSERT INTO `ord`(`uid`, `total`) VALUES (?, ?)");
        $stmt->bind_param("id", $uid, $total);
        $stmt->execute();
        
        // Get order ID
        $oid = $conn->insert_id;
        
        // Save order items
        foreach ($products as $pid => $product) {
            $stmt = $conn->prepare("INSERT INTO `order_items`(`oid`, `pid`, `quantity`, `amount`, `subtotal`) 
                                  VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iiidd", $oid, $pid, $product['quantity'], $product['amount'], $product['subtotal']);
            $stmt->execute();
        }
        
        // Save payment info
        $paymentType = isset($_POST['payment_type']) ? $_POST['payment_type'] : 'COD';
        $stmt = $conn->prepare("INSERT INTO `payment`(`total_amount`, `payment_type`, `oid`, `uid`) 
                              VALUES (?, ?, ?, ?)");
        $stmt->bind_param("dsii", $total, $paymentType, $oid, $uid);
        $stmt->execute();
        
        // Save subscription if selected
        if (isset($_POST['is_subscription']) && $_POST['is_subscription'] == '1') {
            foreach ($products as $pid => $product) {
                $interval = isset($_POST['interval_'.$pid]) ? (int)$_POST['interval_'.$pid] : 7;
                $stmt = $conn->prepare("INSERT INTO `subscriptions`(`uid`, `pid`, `oid`, `quantity`, `delivery_interval`) 
                                      VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("iiiid", $uid, $pid, $oid, $product['quantity'], $interval);
                $stmt->execute();
            }
        }
        
        $conn->commit();
        
    } catch (Exception $e) {
        $conn->rollback();
        die("Error processing order: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Checkout | Grocery Store</title>
    <?php include 'header.php'; ?>
    <style>
        .subscription-item {
            padding: 15px;
            margin: 10px 0;
            background: #f8f8f8;
            border-radius: 5px;
        }
        .subscription-terms {
            margin-top: 20px;
            padding: 15px;
            background: #f0f0f0;
            border-radius: 5px;
        }
        .subscription-terms ul {
            list-style: disc;
            padding-left: 20px;
            margin: 15px 0;
        }
        .subscription-confirm {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }
        .tab-content {
            padding: 20px;
            background: #fff;
            border: 1px solid #ddd;
            border-top: none;
        }
    </style>
</head>
<body>
    <!-- banner -->
    <div class="banner">
        <div class="w3l_banner_nav_right">
            <!-- payment -->
            <div class="privacy about">
                <h3>Pay<span>ment</span></h3>
                <div class="checkout-right">
                    <?php if (!isset($_SESSION['USER_ID']) || empty($_SESSION['USER_ID'])): ?>
                        <div class="col-md-12 address_form_agile">
                            <section class="creditly-wrapper wthree, w3_agileits_wrapper" style="margin-top: 35px">
                                <div class="information-wrapper">
                                    <a href="login.php?page=checkout">
                                        <button class="submit check_out btn-block">Login To Continue</button>
                                    </a>
                                </div>
                            </section>
                        </div>
                        <div class="clearfix"></div>
                    <?php else: ?>
                        <div class="col-md-12 address_form_agile">
                            <section class="creditly-wrapper wthree, w3_agileits_wrapper" style="margin-top: 35px">
                                <div class="information-wrapper">
                                    <button class="submit check_out btn-block">Your order has been placed</button>
                                </div>
                            </section>
                        </div>
                        <div class="clearfix"></div>
                        
                        <!-- Horizontal Tabs -->
                        <div id="parentHorizontalTab">
                            <ul class="resp-tabs-list hor_1">
                                <li>Cash on delivery (COD)</li>
                                <li>Credit/Debit</li>
                                <li>Netbanking</li>
                                <li>Paypal Account</li>
                                <li>Subscription</li>
                            </ul>
                            <div class="resp-tabs-container hor_1">
                                <!-- COD Tab -->
                                <div>
                                    <div class="vertical_post check_box_agile">
                                        <div class="checkbox">
                                            <div class="check_box_one cashon_delivery">
                                                <form method="post">
                                                    <input type="hidden" name="payment_type" value="COD">
                                                    <label class="anim">
                                                        <input type="radio" name="payment_method" value="cod" checked>
                                                        <span>Cash On Delivery</span> 
                                                    </label>
                                                    <button type="submit" class="btn btn-primary" style="margin-top: 20px;">Confirm Order</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Credit/Debit Tab -->
                                <div>
                                    <form action="#" method="post" class="creditly-card-form agileinfo_form">
                                        <input type="hidden" name="payment_type" value="CREDIT">
                                        <section class="creditly-wrapper wthree, w3_agileits_wrapper">
                                            <div class="credit-card-wrapper">
                                                <div class="first-row form-group">
                                                    <div class="controls">
                                                        <label class="control-label">Name on Card</label>
                                                        <input class="billing-address-name form-control" type="text" name="name" placeholder="John Smith" required>
                                                    </div>
                                                    <div class="w3_agileits_card_number_grids">
                                                        <div class="w3_agileits_card_number_grid_left">
                                                            <div class="controls">
                                                                <label class="control-label">Card Number</label>
                                                                <input class="number credit-card-number form-control" type="text" name="number"
                                                                      inputmode="numeric" autocomplete="cc-number" placeholder="•••• •••• •••• ••••" required>
                                                            </div>
                                                        </div>
                                                        <div class="w3_agileits_card_number_grid_right">
                                                            <div class="controls">
                                                                <label class="control-label">CVV</label>
                                                                <input class="security-code form-control" type="text" name="security-code"
                                                                      inputmode="numeric" placeholder="•••" required>
                                                            </div>
                                                        </div>
                                                        <div class="clear"> </div>
                                                    </div>
                                                    <div class="controls">
                                                        <label class="control-label">Expiration Date</label>
                                                        <input class="expiration-month-and-year form-control" type="text" name="expiration" placeholder="MM/YY" required>
                                                    </div>
                                                </div>
                                                <button type="submit" class="submit"><span>Make a payment</span></button>
                                            </div>
                                        </section>
                                    </form>
                                </div>
                                
                                <!-- Netbanking Tab -->
                                <div>
                                    <form method="post">
                                        <input type="hidden" name="payment_type" value="NETBANKING">
                                        <h5>Select From Popular Banks</h5>
                                        <div class="swit-radio">								
                                            <div class="check_box_one"> 
                                                <div class="radio_one"> 
                                                    <label><input type="radio" name="bank" value="syndicate" checked><i></i>Syndicate Bank</label> 
                                                </div>
                                            </div>
                                            <div class="check_box_one"> 
                                                <div class="radio_one"> 
                                                    <label><input type="radio" name="bank" value="baroda"><i></i>Bank of Baroda</label> 
                                                </div>
                                            </div>
                                            <div class="check_box_one"> 
                                                <div class="radio_one"> 
                                                    <label><input type="radio" name="bank" value="canara"><i></i>Canara Bank</label> 
                                                </div>
                                            </div>	
                                            <div class="check_box_one"> 
                                                <div class="radio_one"> 
                                                    <label><input type="radio" name="bank" value="icici"><i></i>ICICI Bank</label> 
                                                </div>
                                            </div>	
                                            <div class="check_box_one"> 
                                                <div class="radio_one"> 
                                                    <label><input type="radio" name="bank" value="sbi"><i></i>State Bank Of India</label> 
                                                </div>
                                            </div>		
                                            <div class="clearfix"></div>
                                        </div>
                                        <h5>Or SELECT OTHER BANK</h5>
                                        <div class="section_room_pay">
                                            <select class="year" name="other_bank">
                                                <option value="">=== Other Banks ===</option>
                                                <option value="allahabad">Allahabad Bank NetBanking</option>
                                                <option value="andhra">Andhra Bank</option>
                                                <!-- More bank options -->
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary" style="margin-top: 20px;">Pay Now</button>
                                    </form>
                                </div>
                                
                                <!-- PayPal Tab -->
                                <div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <img class="pp-img" src="images/paypal.png" alt="PayPal">
                                            <p>Important: You will be redirected to PayPal's website to securely complete your payment.</p>
                                            <form method="post">
                                                <input type="hidden" name="payment_type" value="PAYPAL">
                                                <button type="submit" class="btn btn-primary">Checkout via Paypal</button>
                                            </form>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="cc-form">
                                                <div class="clearfix">
                                                    <div class="form-group form-group-cc-number">
                                                        <label>Card Number</label>
                                                        <input class="form-control" placeholder="xxxx xxxx xxxx xxxx" type="text">
                                                    </div>
                                                    <div class="form-group form-group-cc-cvc">
                                                        <label>CVV</label>
                                                        <input class="form-control" placeholder="xxx" type="text">
                                                    </div>
                                                </div>
                                                <div class="clearfix">
                                                    <div class="form-group form-group-cc-name">
                                                        <label>Card Holder Name</label>
                                                        <input class="form-control" type="text">
                                                    </div>
                                                    <div class="form-group form-group-cc-date">
                                                        <label>Valid Thru</label>
                                                        <input class="form-control" placeholder="mm/yy" type="text">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Subscription Tab -->
                                <div>
                                    <form method="post">
                                        <input type="hidden" name="is_subscription" value="1">
                                        <input type="hidden" name="payment_type" value="COD">
                                        
                                        <div class="subscription-summary">
                                            <h4>Subscription Summary</h4>
                                            <?php
                                            if(isset($_SESSION['products']) && !empty($_SESSION['products'])) {
                                                foreach($_SESSION['products'] as $pid => $product) {
                                                    $query = "SELECT * FROM product WHERE pid = $pid";
                                                    $result = $conn->query($query);
                                                    if($row = $result->fetch_assoc()) {
                                            ?>
                                                <div class="subscription-item">
                                                    <h5><?= htmlspecialchars($row['name']) ?></h5>
                                                    <div class="form-group">
                                                        <label>Delivery Interval (days):</label>
                                                        <select name="interval_<?= $pid ?>" class="form-control">
                                                            <option value="7">Weekly (7 days)</option>
                                                            <option value="14">Bi-Weekly (14 days)</option>
                                                            <option value="30">Monthly (30 days)</option>
                                                        </select>
                                                    </div>
                                                    <p>Quantity: <?= $product['quantity'] ?></p>
                                                </div>
                                            <?php
                                                    }
                                                }
                                            } else {
                                                echo '<p class="alert alert-warning">No items in cart for subscription.</p>';
                                            }
                                            ?>
                                        </div>
                                        
                                        <div class="subscription-terms">
                                            <h4>Subscription Terms</h4>
                                            <ul>
                                                <li>Your subscription will start from the next delivery</li>
                                                <li>Payment will be collected on delivery</li>
                                                <li>You can modify or cancel your subscription anytime</li>
                                                <li>First delivery will be processed immediately</li>
                                            </ul>
                                            <div class="subscription-confirm">
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" name="agree_terms" required>
                                                        I agree to the subscription terms and automatic deliveries
                                                    </label>
                                                </div>
                                                <button type="submit" class="btn btn-success" style="margin-top: 15px;">Confirm Subscription</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <!-- //payment -->
        </div>
        <div class="clearfix"></div>
    </div>
    <!-- //banner -->

    <?php include 'footer.php'; ?>

    <!-- JavaScript Libraries -->
    <script src="js/easyResponsiveTabs.js"></script>
    <script src="js/creditly.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize horizontal tabs
            $('#parentHorizontalTab').easyResponsiveTabs({
                type: 'default',
                width: 'auto',
                fit: true,
                tabidentify: 'hor_1'
            });

            // Initialize credit card form
            var creditly = Creditly.initialize(
                '.creditly-wrapper .expiration-month-and-year',
                '.creditly-wrapper .credit-card-number',
                '.creditly-wrapper .security-code',
                '.creditly-wrapper .card-type'
            );

            $(".creditly-card-form .submit").click(function(e) {
                e.preventDefault();
                var output = creditly.validate();
                if (output) {
                    this.form.submit();
                }
            });
        });
    </script>
</body>
</html>