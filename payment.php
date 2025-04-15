<!--
author: W3layouts
author URL: http://w3layouts.com
License: Creative Commons Attribution 3.0 Unported
License URL: http://creativecommons.org/licenses/by/3.0/
-->
<!DOCTYPE html>
<html>
<head>
<title>Grocery Store | Checkout</title>
</head>
<body>
<?php include 'header.php'?>

<div class="w3l_banner_nav_right">
    <!-- payment -->
    <div class="privacy about">
        <h3>Pay<span>ment</span></h3>

        <div class="checkout-right">
            <!--Horizontal Tab-->
            <div id="parentHorizontalTab">
			<ul class="resp-tabs-list hor_1">
    <li>Cash on delivery (COD)</li>
    <li>Credit/Debit</li>
    <li>Netbanking</li>
    <li>Paypal Account</li>
    <li>Subscription</li> <!-- New tab added -->
</ul>

                <div class="resp-tabs-container hor_1">
                    <div>
                        <div class="vertical_post check_box_agile">
                            <h5>COD</h5>
                            <div class="checkbox">                             
                                <div class="check_box_one cashon_delivery">
                                    <label class="anim">
                                        <input type="checkbox" class="checkbox">
                                        <span> We also accept Credit/Debit card on delivery. Please Check with the agent.</span> 
                                    </label> 
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="vertical_post check_box_agile">
                            <h5>Subscription Payment Details</h5>
                            <form action="checkout.php" method="post">
                                <div class="subscription_details">
                                    <p>Your subscription will be automatically renewed based on your selected intervals.</p>
                                    <ul>
                                        <li>First payment: Cash on Delivery</li>
                                        <li>Subsequent payments: Cash on Delivery for each delivery</li>
                                        <li>You can cancel or modify your subscription anytime</li>
                                    </ul>
                                    <div class="subscription_agreement">
                                        <label class="anim">
                                            <input type="checkbox" class="checkbox" name="agree_terms" required>
                                            <span>I agree to the subscription terms and automatic renewal</span>
                                        </label>
                                    </div>
                                    <button class="btn btn-primary submit" style="margin-top: 15px;" type="submit" name="confirm_subscription">Confirm Subscription</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div>
                        <div class="vertical_post check_box_agile">
                            <h5>COD</h5>
                            <div class="checkbox">								
                                <div class="check_box_one cashon_delivery">
                                    <label class="anim">
                                        <input type="checkbox" class="checkbox">
                                        <span> We also accept Credit/Debit card on delivery. Please Check with the agent.</span> 
                                    </label> 
                                </div>
                            </div>
                        </div>
                        <div class="controls">
                            <label class="control-label">Expiration Date</label>
                            <input class="expiration-month-and-year form-control" type="text" name="expiration-month-and-year" placeholder="MM / YY">
                        </div>
                        <button class="submit"><span>Make a payment </span></button>
                    </div>

                    <div>
                        <div class="vertical_post">
                            <form action="#" method="post">
                                <h5>Select From Popular Banks</h5>
                                <div class="swit-radio">								
                                    <div class="check_box_one"><div class="radio_one"><label><input type="radio" name="radio" checked=""><i></i>Syndicate Bank</label></div></div>
                                    <div class="check_box_one"><div class="radio_one"><label><input type="radio" name="radio"><i></i>Bank of Baroda</label></div></div>
                                    <div class="check_box_one"><div class="radio_one"><label><input type="radio" name="radio"><i></i>Canara Bank</label></div></div>	
                                    <div class="check_box_one"><div class="radio_one"><label><input type="radio" name="radio"><i></i>ICICI Bank</label></div></div>	
                                    <div class="check_box_one"><div class="radio_one"><label><input type="radio" name="radio"><i></i>State Bank Of India</label></div></div>		
                                    <div class="clearfix"></div>
                                </div>
                                <h5>Or SELECT OTHER BANK</h5>
                                <div class="section_room_pay">
                                    <select class="year">
                                        <option value="">=== Other Banks ===</option>
                                        <option value="ALB-NA">Allahabad Bank NetBanking</option>
                                        <option value="ADB-NA">Andhra Bank</option>
                                        <option value="BBK-NA">Bank of Bahrain and Kuwait NetBanking</option>
                                        <option value="BBC-NA">Bank of Baroda Corporate NetBanking</option>
                                        <option value="BBR-NA">Bank of Baroda Retail NetBanking</option>
                                        <option value="BOI-NA">Bank of India NetBanking</option>
                                        <option value="BOM-NA">Bank of Maharashtra NetBanking</option>
                                        <!-- Add more options as needed -->
                                    </select>
                                </div>
                                <input type="submit" value="PAY NOW">
                            </form>
                        </div>
                    </div>

                    <div>
                        <div id="tab4" class="tab-grid" style="display: block;">
                            <div class="row">
                                <div class="col-md-6">
                                    <img class="pp-img" src="images/paypal.png" alt="PayPal">
                                    <p>Important: You will be redirected to PayPal's website to securely complete your payment.</p>
                                    <a class="btn btn-primary">Checkout via Paypal</a>	
                                </div>
                                <div class="col-md-6">
                                    <form class="cc-form">
                                        <div class="clearfix">
                                            <div class="form-group form-group-cc-number">
                                                <label>Card Number</label>
                                                <input class="form-control" placeholder="xxxx xxxx xxxx xxxx" type="text">
                                            </div>
                                            <div class="form-group form-group-cc-cvc">
                                                <label>CVV</label>
                                                <input class="form-control" placeholder="xxxx" type="text">
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
                                        <div class="checkbox checkbox-small">
                                            <label><input class="i-check" type="checkbox" checked="">Add to My Cards</label>
                                        </div>
                                        <input class="btn btn-primary submit" type="submit" value="Proceed Payment">
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
    <div class="vertical_post check_box_agile">
        <h5>Subscription-Based Payment</h5>
        <p>This payment method allows automatic order generation and delivery on a recurring basis.</p>
        <ul>
            <li>First payment via Cash on Delivery</li>
            <li>Subsequent deliveries will be handled automatically</li>
            <li>You can cancel anytime from the dashboard</li>
        </ul>
        <form action="checkout.php" method="post">
            <label class="anim">
                <input type="checkbox" name="agree_subscription" required>
                <span>I agree to start a subscription and accept the terms</span>
            </label><br><br>
            <button type="submit" class="btn btn-primary" name="confirm_subscription">Start Subscription</button>
        </form>
    </div>
</div>

                </div> <!-- .resp-tabs-container -->
            </div> <!-- #parentHorizontalTab -->
        </div> <!-- .checkout-right -->

    </div> <!-- .privacy about -->
</div> <!-- .w3l_banner_nav_right -->

<div class="clearfix"></div>

<?php include 'footer.php'?>
</body>
</html>

<style>
    .subscription_details {
        padding: 15px;
        background: #f9f9f9;
        border-radius: 5px;
        margin: 10px 0;
    }
    .subscription_details ul {
        list-style: disc;
        padding-left: 20px;
        margin: 15px 0;
    }
    .subscription_details li {
        margin: 8px 0;
        color: #666;
    }
    .subscription_agreement {
        margin-top: 20px;
        padding-top: 15px;
        border-top: 1px solid #eee;
    }
</style>

<script>
    $(document).ready(function() {
        $('#parentHorizontalTab').easyResponsiveTabs({
            type: 'default',
            width: 'auto',
            fit: true,
            closed: 'accordion',
            activate: function(event) {
                var $tab = $(this);
                var $info = $('#tabInfo');
                var $name = $('span', $info);
                $name.text($tab.text());
                $info.show();
            }
        });
    });
</script>
