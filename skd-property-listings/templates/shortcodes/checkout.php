<?php
// Calculate GST
$gst_amount = 0;
if (!$plan->is_free) {
    if ($plan->add_gst_rate) {
        if ($plan->gst_type === 'flat') {
            $gst_amount = $plan->gst_rate;
        } elseif ($plan->gst_type === 'percentage') {
            $gst_amount = ($plan->price * $plan->gst_rate) / 100;
        }
    }
}
$total_price = $plan->is_free ? 0 : ($plan->price + $gst_amount);
?>

<section class="section4">
    <div class="container">
        <div class="checkoutOuter">
            <div class="checkoutInner">

                <p class="msgText">Your order details are given below. Please review it and click on Proceed to Payment to complete this order.</p>

                <?php if ($total_price > 0) : ?>
                    <div class="couponBoxOuter">
                        <h5>Got a coupon? Redeem it here.</h5>
                        <div class="couponInputBox">
                            <input type="text" id="skd_coupon_code" class="couponCode"
                                placeholder="Enter coupon code">
                            <button class="couponCodeBtn" type="button" id="skd_apply_coupon">Apply Coupon</button>
                            <p id="skd_coupon_message"></p>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="orderSummeryBoxOuter">
                    <div class="orderSummeryHeader">
                        <h3 class="orderSummeryHeader__title">Order Summary</h3>
                    </div>
                    <div class="orderSummeryBody">
                        <div class="orderSummery-table-responsive">
                            <table id="orderSummery-checkout-table" class="orderSummery-table">
                                <tbody>
                                    <tr>
                                        <td colspan="2" class="">

                                            <label for="657_1"></label> <span class="orderSummery-summery-label">
                                                <?php echo esc_html($plan->plan_name); ?> </span>
                                        </td>
                                        <td class="orderSummery-text-right">
                                            <span class="orderSummery-summery-amount">
                                                $<?php echo number_format($total_price, 2); ?> </span>
                                        </td>
                                    </tr>
                                    <?php if ($total_price > 0) : ?>
                                        <tr>
                                            <td colspan="2" class="">
                                                <label for="_tax"></label> <span class="orderSummery-summery-label"> GST
                                                </span>
                                            </td>
                                            <td class="orderSummery-text-right">
                                                <span class="orderSummery-summery-amount">
                                                    $<?php echo number_format($gst_amount, 2); ?> </span>
                                            </td>
                                        </tr>
                                        <tr class="atbdp_ch_subtotal">
                                            <td colspan="2" class="">
                                                <span class="orderSummery-summery-label">Subtotal</span>
                                            </td>
                                            <td class="orderSummery-text-right">
                                                <div id="atbdp_checkout_subtotal_amount"
                                                    class="orderSummery-summery-amount">
                                                    $<?php echo number_format($total_price, 2); ?> </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr class="orderSummery-summery-total">
                                        <td colspan="2" class="">
                                            <span class="orderSummery-summery-label">Total amount [AUD]
                                            </span>
                                        </td>
                                        <td class="orderSummery-text-right">
                                            <div id="atbdp_checkout_total_amount"
                                                class="orderSummery-summery-amount">
                                                $<span id="skd_total_price"><?php echo number_format($total_price, 2); ?></span></div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table> <!--ends table-->
                        </div>
                    </div>

                </div>
                <!-- <div class="orderSummeryBoxOuter">
                    <div class="orderSummeryHeader">
                        <h3 class="orderSummeryHeader__title">Choose a payment method</h3>
                    </div>
                    <div class="orderSummeryBody">
                        <ul>
                            <li class="list-group-item">
                                <div class="gateway_list orderSummery-radio orderSummery-radio-circle">
                                    <input type="radio" id="stripe_gateway" name="payment_gateway"
                                        value="stripe_gateway" checked="">
                                    <label for="stripe_gateway" class="orderSummery-radio__label">
                                        Stripe
                                    </label>
                                </div>
                                <p class="orderSummery-payment-text">You can make payment using your credit card
                                    using stripe if you choose this
                                    payment gateway.</p>
                            </li>
                        </ul>
                    </div>

                </div> -->
                <div class="buttonsGroup">
                    <a href="/pricing" class="backBtn">Not Now</a>
                    <button type="button" id="skd_place_order" class="skd-btn proceedBtn" data-plan-id="<?php echo esc_attr($plan->id); ?>">
                        Place Order
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://js.stripe.com/v3/"></script>

<script>
    jQuery(document).ready(function($) {
        var skd_ajax = {
            ajax_url: '<?php echo admin_url('admin-ajax.php'); ?>'
        };

        let finalTotalPrice = <?php echo esc_js($total_price); ?>;
        let finalDiscountPrice = 0;
        let appliedCouponId = null;

        // Apply Coupon
        $('#skd_apply_coupon').on('click', function() {
            let couponCode = $('#skd_coupon_code').val();
            let planId = '<?php echo intval($plan->id); ?>';

            if (couponCode === '') {
                $('#skd_coupon_message').text('Please enter a coupon code.').css('color', 'red');
                return;
            }

            $.ajax({
                type: 'POST',
                url: skd_ajax.ajax_url,
                data: {
                    action: 'skd_apply_coupon',
                    coupon_code: couponCode,
                    plan_id: planId,
                },
                beforeSend: function() {
                    $('#skd_apply_coupon').prop('disabled', true).text('Applying...');
                },
                success: function(response) {
                    console.log(response);
                    if (response.success) {
                        $('#skd_coupon_message').text(response.data.message).css('color', 'green');
                        $('#skd_total_price').text(response.data.total_price);
                        finalTotalPrice = parseFloat(response.data.total_price);
                        finalDiscountPrice = parseFloat(response.data.total_discount);
                        appliedCouponId = response.data.coupon_id;
                    } else {
                        $('#skd_coupon_message').text(response.data.message).css('color', 'red');
                    }
                },
                complete: function() {
                    $('#skd_apply_coupon').prop('disabled', false).text('Apply Coupon');
                }
            });
        });

        // Place Order Button Click
        $('#skd_place_order').on('click', function() {
            let planId = '<?php echo intval($plan->id); ?>';

            if (finalTotalPrice <= 0) {
                // Free Order - Process Order Directly
                $.ajax({
                    type: 'POST',
                    url: skd_ajax.ajax_url,
                    data: {
                        action: 'skd_process_free_plan',
                        plan_id: planId,
                        coupon_id: appliedCouponId,
                        final_total_price: 0,
                        final_discount: finalDiscountPrice,
                    },
                    beforeSend: function() {
                        $('#skd_place_order').prop('disabled', true).text('Processing...');
                    },
                    success: function(response) {
                        if (response.success) {
                            window.location.href = response.data.redirect;
                        } else {
                            alert(response.data.message);
                        }
                    },
                    complete: function() {
                        $('#skd_place_order').prop('disabled', false).text('Place Order');
                    }
                });
            } else {
                // Paid Order - Redirect to Stripe Payment
                $.ajax({
                    type: 'POST',
                    url: skd_ajax.ajax_url,
                    data: {
                        action: 'skd_process_paid_order',
                        plan_id: planId,
                        coupon_id: appliedCouponId,
                        final_total_price: finalTotalPrice,
                        final_discount: finalDiscountPrice,
                    },
                    beforeSend: function() {
                        $('#skd_place_order').prop('disabled', true).text('Redirecting...');
                    },
                    success: function(response) {
                        if (response.success) {
                            window.location.href = response.data.payment_url; // Redirect to Stripe Checkout
                        } else {
                            alert(response.data.message);
                        }
                    },
                    complete: function() {
                        $('#skd_place_order').prop('disabled', false).text('Place Order');
                    }
                });
            }
        });
    });
</script>