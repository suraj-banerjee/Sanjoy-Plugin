<div class="wrap" id="skd-pl-coupon-add-edit">
    <?php if ($action === 'edit') { ?>
        <h1 class="wp-heading-inline"><?php _e('Edit Coupon', 'skd-property-listings'); ?></h1>
    <?php } else { ?>
        <h1 class="wp-heading-inline"><?php _e('Add Coupon', 'skd-property-listings'); ?></h1>
    <?php } ?>
    <a href="<?php echo admin_url('admin.php?page=skd-pl-coupons'); ?>" class="page-title-action">
        <?php _e('Back to List', 'skd-property-listings'); ?>
    </a>

    <form method="POST">
        <?php if (!empty($_GET['message']) && $_GET['message'] === 'success') : ?>
            <div class="updated notice is-dismissible">
                <p><?php _e('Coupon updated successfully!', 'skd-property-listings'); ?></p>
            </div>
        <?php endif; ?>
        <?php wp_nonce_field('save_coupon', 'coupon_nonce'); ?>
        <input type="hidden" name="coupon_id" value="<?php echo $coupon ? $coupon->id : ''; ?>">

        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <!-- Left Side: Coupon Details -->
                <div id="post-body-content">
                    <div class="postbox">
                        <h2 class="hndle"><span><?php _e('Coupon Details', 'skd-property-listings'); ?></span></h2>
                        <div class="inside">
                            <table class="form-table">
                                <tr>
                                    <th><label for="coupon_title"><?php _e('Coupon Title', 'skd-property-listings'); ?></label></th>
                                    <td><input type="text" name="coupon_title" id="coupon_title" class="regular-text" required style="width: 100%;" value="<?php echo $coupon ? esc_attr($coupon->title) : ''; ?>"></td>
                                </tr>
                                <tr>
                                    <th><label for="coupon_description"><?php _e('Coupon Description', 'skd-property-listings'); ?></label></th>
                                    <td><textarea name="coupon_description" id="coupon_description" rows="3" style="width: 100%;"><?php echo $coupon ? esc_textarea($coupon->description) : ''; ?></textarea></td>
                                </tr>
                                <tr>
                                    <th><label for="coupon_code"><?php _e('Coupon Code', 'skd-property-listings'); ?></label></th>
                                    <td>
                                        <input type="text" name="coupon_code" id="coupon_code" class="regular-text" required value="<?php echo $coupon ? esc_attr($coupon->coupon_code) : ''; ?>">
                                        <button type="button" id="generate_coupon_code" class="button"><?php _e('Generate Code', 'skd-property-listings'); ?></button>
                                    </td>
                                </tr>
                                <tr>
                                    <th><label for="coupon_type"><?php _e('Coupon Type', 'skd-property-listings'); ?></label></th>
                                    <td>
                                        <select name="coupon_type" id="coupon_type">
                                            <option value="percentage" <?php selected($coupon && $coupon->discount_type === 'percentage'); ?>><?php _e('Percentage Discount', 'skd-property-listings'); ?></option>
                                            <option value="fixed_cart" <?php selected($coupon && $coupon->discount_type === 'fixed_cart'); ?>><?php _e('Fixed Cart Discount', 'skd-property-listings'); ?></option>
                                            <?php if (count($price_plans) > 0) { ?>
                                                <option value="fixed_product" <?php selected($coupon && $coupon->discount_type === 'fixed_product'); ?>><?php _e('Fixed Product Discount', 'skd-property-listings'); ?></option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr id="select_products_row" style="display: none;">
                                    <th><?php _e('Select Products (Price Plans)', 'skd-property-listings'); ?></th>
                                    <td>
                                        <?php
                                        if (!empty($price_plans)) {
                                            foreach ($price_plans as $plan) {
                                                $checked = (!empty($coupon->product_ids) && in_array($plan['id'], explode(',', $coupon->product_ids))) ? 'checked' : '';
                                                echo '<label><input type="checkbox" name="product_ids[]" value="' . esc_attr($plan['id']) . '" ' . $checked . '> ' . esc_html($plan['plan_name']) . '</label><br>';
                                            }
                                        } else {
                                            echo '<p>' . __('No price plans found.', 'skd-property-listings') . '</p>';
                                        }
                                        ?>
                                    </td>
                                </tr>

                                <tr>
                                    <th><label for="coupon_amount"><?php _e('Coupon Amount', 'skd-property-listings'); ?></label></th>
                                    <td><input type="number" name="coupon_amount" id="coupon_amount" class="regular-text" required value="<?php echo $coupon ? esc_attr($coupon->discount_amount) : ''; ?>"></td>
                                </tr>
                                <tr>
                                    <th><label for="expiry_date"><?php _e('Expiry Date', 'skd-property-listings'); ?></label></th>
                                    <td><input type="date" name="expiry_date" id="expiry_date" value="<?php echo $coupon ? esc_attr($coupon->expiry_date) : ''; ?>"></td>
                                </tr>
                                <tr>
                                    <th><label for="usage_limit"><?php _e('Usage Limit Per User', 'skd-property-listings'); ?></label></th>
                                    <td><input type="number" name="usage_limit" id="usage_limit" class="regular-text" value="<?php echo $coupon ? esc_attr($coupon->usage_limit) : ''; ?>"></td>
                                </tr>
                                <tr>
                                    <th><label for="gst_exemption"><?php _e('GST Exemption', 'skd-property-listings'); ?></label></th>
                                    <td>
                                        <select name="gst_exemption" id="gst_exemption">
                                            <option value="apply_gst" <?php selected($coupon && $coupon->gst_exemption === 'apply_gst'); ?>><?php _e('Apply GST after discount', 'skd-property-listings'); ?></option>
                                            <option value="exempt_gst" <?php selected($coupon && $coupon->gst_exemption === 'exempt_gst'); ?>><?php _e('Exempt GST (no GST charged)', 'skd-property-listings'); ?></option>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Right Side: Publish Box -->
                <div id="postbox-container-1" class="postbox-container">
                    <div class="postbox">
                        <h2 class="hndle"><span><?php _e('Publish', 'skd-property-listings'); ?></span></h2>
                        <div class="inside">
                            <p>
                                <label for="coupon_status"><?php _e('Status:', 'skd-property-listings'); ?></label>
                                <select name="coupon_status" id="coupon_status">
                                    <option value="publish" <?php selected($coupon && $coupon->coupon_status, 'publish'); ?>><?php _e('Publish', 'skd-property-listings'); ?></option>
                                    <option value="pending" <?php selected($coupon && $coupon->coupon_status, 'pending'); ?>><?php _e('Pending', 'skd-property-listings'); ?></option>
                                    <option value="draft" <?php selected($coupon && $coupon->coupon_status, 'draft'); ?>><?php _e('Draft', 'skd-property-listings'); ?></option>
                                </select>
                            </p>
                            <p>
                                <?php if ($action === 'edit') { ?>
                                    <input type="submit" name="save_coupon" class="button button-primary" value="<?php _e('Update Coupon', 'skd-property-listings'); ?>">
                                <?php } else { ?>
                                    <input type="submit" name="save_coupon" class="button button-primary" value="<?php _e('Save Coupon', 'skd-property-listings'); ?>">
                                <?php } ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    jQuery(document).ready(function($) {
        // Toggle product selection row based on coupon type
        function toggleProductSelection() {
            if ($('#coupon_type').val() === 'fixed_product') {
                $('#select_products_row').show();
            } else {
                $('#select_products_row').hide();
            }
        }

        $('#coupon_type').change(toggleProductSelection);
        toggleProductSelection(); // Run on page load

        // Generate unique coupon code via AJAX
        $('#generate_coupon_code').click(function() {
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'generateCouponCode',
                    security: $('input[name="coupon_nonce"]').val() // Use the existing nonce field
                },
                success: function(response) {
                    if (response.success) {
                        $('#coupon_code').val(response.data.coupon_code);
                    } else {
                        // alert('Error: ' + response.data.error);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('AJAX Error:', xhr.responseText);
                }
            });
        });

    });
</script>