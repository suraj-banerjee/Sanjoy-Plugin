<div class="wrap" id="skd-pl-price-plan-add-edit">
    <?php if ($action === 'edit') { ?>
        <h1 class="wp-heading-inline"><?php _e('Edit Price Plan', 'skd-property-listings'); ?></h1>
    <?php } else { ?>
        <h1 class="wp-heading-inline"><?php _e('Add Price Plan', 'skd-property-listings'); ?></h1>
    <?php } ?>
    <a href="<?php echo admin_url('admin.php?page=skd-pl-price-plans'); ?>" class="page-title-action">
        <?php _e('Back to List', 'skd-property-listings'); ?>
    </a>

    <form method="POST">
        <?php wp_nonce_field('save_price_plan', 'price_plan_nonce'); ?>
        <input type="hidden" name="plan_id" value="<?php echo $planDetails ? $planDetails->id : ''; ?>">

        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <!-- Left Side: Main Form -->
                <div id="post-body-content">
                    <div class="postbox">
                        <h2 class="hndle"><span><?php _e('Plan Info', 'skd-property-listings'); ?></span></h2>
                        <div class="inside">
                            <table class="form-table">
                                <tr>
                                    <th><label for="plan_name"><?php _e('Plan Name', 'skd-property-listings'); ?></label></th>
                                    <td><input type="text" name="plan_name" id="plan_name" class="regular-text" required style="width: 100%;" value="<?php echo $planDetails ? $planDetails->plan_name : ''; ?>"></td>
                                </tr>
                                <tr>
                                    <th><label for="plan_description"><?php _e('Plan Description', 'skd-property-listings'); ?></label></th>
                                    <td><textarea name="plan_description" id="plan_description" rows="5" style="width: 100%;"><?php echo $planDetails ? $planDetails->plan_description : ''; ?></textarea></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Basic Configuration -->
                    <div class="postbox">
                        <h2 class="hndle"><span><?php _e('Basic Configurations', 'skd-property-listings'); ?></span></h2>
                        <div class="inside">
                            <table class="form-table">
                                <tr>
                                    <th><label for="plan_type"><?php _e('Plan Type', 'skd-property-listings'); ?></label></th>
                                    <td>
                                        <label>
                                            <input type="radio" name="plan_type" value="pay_per_listing" <?php echo $planDetails && $planDetails->plan_type == 'pay_per_listing' ? 'checked' : ''; ?>>
                                            <?php _e('Pay Per Listing', 'skd-property-listings'); ?>
                                        </label>
                                        <label>
                                            <input type="radio" name="plan_type" value="package" <?php echo !$planDetails || $planDetails->plan_type == 'package' ? 'checked' : ''; ?>>
                                            <?php _e('Package', 'skd-property-listings'); ?>
                                        </label>
                                    </td>
                                </tr>

                                <tr>
                                    <th><label for="price"><?php _e('Price', 'skd-property-listings'); ?></label></th>
                                    <td>
                                        <input type="number" name="price" id="price" step="0.01" value="<?php echo $planDetails &&  $planDetails->is_free != 1 ? $planDetails->price : ''; ?>" <?php echo $planDetails && $planDetails->is_free == 1 ? 'disabled' : ''; ?>>
                                        <br>
                                        <input type="checkbox" id="add_gst_rate" name="add_gst_rate" <?php echo $planDetails && $planDetails->add_gst_rate == 1 ? 'checked' : ''; ?> <?php echo $planDetails && $planDetails->is_free == 1 ? 'disabled' : ''; ?>>
                                        <label for="add_gst_rate"><?php _e('Add GST Rate', 'skd-property-listings'); ?></label>

                                        <input type="checkbox" id="is_free" name="is_free" <?php echo $planDetails && $planDetails->is_free == 1 ? 'checked' : ''; ?>>
                                        <label for="is_free"><?php _e('Free', 'skd-property-listings'); ?></label>
                                    </td>
                                </tr>

                                <tr id="gst_fields" style="display: <?php echo $planDetails && $planDetails->add_gst_rate == 1 ? '' : 'none'; ?>;">
                                    <th></th>
                                    <td>
                                        <select name="gst_type" id="gst_type">
                                            <option value="flat" <?php echo $planDetails && $planDetails->gst_type == "flat" ? 'selected' : ''; ?>>Flat Rate</option>
                                            <option value="percentage" <?php echo $planDetails && $planDetails->gst_type == "percentage" ? 'selected' : ''; ?>>Percentage</option>
                                        </select>
                                        <input type="number" name="gst_rate" id="gst_rate" step="0.01" value="<?php echo $planDetails ? $planDetails->gst_rate : ''; ?>">
                                    </td>
                                </tr>

                                <tr>
                                    <th><label for="listing_duration"><?php _e('Listing Duration', 'skd-property-listings'); ?></label></th>
                                    <td>
                                        <span>
                                            <input type="number" name="listing_duration" id="listing_duration" step="1" value="<?php echo $planDetails && $planDetails->never_expire == 1 ? '' : ($planDetails ? $planDetails->listing_duration : '30'); ?>" <?php echo $planDetails && $planDetails->never_expire == 1 ? 'disabled' : ''; ?>>
                                            <select name="duration_unit" id="duration_unit" <?php echo $planDetails && $planDetails->never_expire == 1 ? 'disabled' : ''; ?>>
                                                <option value="days" <?php echo $planDetails && $planDetails->duration_unit == "days" ? 'selected' : ''; ?>>Day(s)</option>
                                                <option value="weeks" <?php echo $planDetails && $planDetails->duration_unit == "weeks" ? 'selected' : ''; ?>>Week(s)</option>
                                                <option value="months" <?php echo $planDetails && $planDetails->duration_unit == "months" ? 'selected' : ''; ?>>Month(s)</option>
                                                <option value="years" <?php echo $planDetails && $planDetails->duration_unit == "years" ? 'selected' : ''; ?>>Year(s)</option>
                                            </select>
                                        </span>
                                        <br>
                                        <input type="checkbox" id="never_expire" name="never_expire" <?php echo $planDetails && $planDetails->never_expire == 1 ? 'checked' : ''; ?>>
                                        <label for="never_expire"><?php _e('Never expire', 'skd-property-listings'); ?></label>
                                    </td>
                                </tr>

                                <tr class="pay_per_listing_div" style="display: <?php echo $planDetails && $planDetails->plan_type == 'pay_per_listing' ? '' : 'none'; ?>">
                                    <th>
                                        <label for="featured_the_list"><?php _e('Featured the Listing', 'skd-property-listings'); ?></label>
                                        <div class="skd055textlabel">
                                            <input type="checkbox" id="featured_the_list_hide" name="featured_the_list_hide" <?php echo $planDetails && $planDetails->plan_type == 'pay_per_listing' && $planDetails->featured_the_list_hide == 1 ? 'checked' : ''; ?>>
                                            <label for="featured_the_list_hide"><?php _e('Hide this from pricing plan page', 'skd-property-listings'); ?></label>
                                        </div>
                                    </th>
                                    <td>
                                        <div class="skdSwitch">
                                            <label class="skdSwitchInner">
                                                <input type="checkbox" name="featured_the_list" id="featured_the_list" value="yes" <?php echo $planDetails && $planDetails->plan_type == 'pay_per_listing' && $planDetails->featured_the_list == 'yes' ? 'checked' : ''; ?>>
                                                <span class="skdSwitchInner-switch-yes">Yes</span>
                                                <span class="skdSwitchInner-switch-no">No</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>

                                <tr class="package_listing_div" style="display: <?php echo $planDetails && $planDetails->plan_type == 'package' ? '' : 'none'; ?>">
                                    <th>
                                        <label for="enable_subscription"><?php _e('Enable Subscription', 'skd-property-listings'); ?></label>
                                        <div class="skd055textlabel">
                                            <input type="checkbox" id="enable_subscription_hide" name="enable_subscription_hide" <?php echo $planDetails && $planDetails->plan_type == 'package' && $planDetails->enable_subscription_hide == 1 ? 'checked' : ''; ?>>
                                            <label for="enable_subscription_hide"><?php _e('Hide this from pricing plan page', 'skd-property-listings'); ?></label>
                                        </div>
                                    </th>
                                    <td>
                                        <div class="skdSwitch">
                                            <label class="skdSwitchInner">
                                                <input type="checkbox" name="enable_subscription" id="enable_subscription" value="yes" <?php echo $planDetails && $planDetails->plan_type == 'package' && $planDetails->enable_subscription == 'yes' ? 'checked' : ''; ?>>
                                                <span class="skdSwitchInner-switch-yes">Yes</span>
                                                <span class="skdSwitchInner-switch-no">No</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>

                                <tr class="package_listing_div" style="display: <?php echo $planDetails && $planDetails->plan_type == 'package' ? '' : 'none'; ?>">
                                    <th><label for="no_of_listing"><?php _e('Number of Listings', 'skd-property-listings'); ?></label></th>
                                    <td>
                                        <input type="number" name="no_of_listing" id="no_of_listing" step="1" value="<?php echo $planDetails && $planDetails->plan_type == 'package' && $planDetails->plan_type == 'package' && $planDetails->mark_as_unlimited != 1 ? $planDetails->no_of_listing : ''; ?>" <?php echo $planDetails && $planDetails->plan_type == 'package' && $planDetails->mark_as_unlimited == 1 ? 'disabled' : ''; ?>>
                                        <br>
                                        <input type="checkbox" id="mark_as_unlimited" name="mark_as_unlimited" <?php echo $planDetails && $planDetails->plan_type == 'package' && $planDetails->mark_as_unlimited == 1 ? 'checked' : ''; ?>>
                                        <label for="mark_as_unlimited"><?php _e('Or mark as unlimited', 'skd-property-listings'); ?></label>
                                    </td>
                                </tr>

                                <tr class="package_listing_div" style="display: <?php echo $planDetails && $planDetails->plan_type == 'package' ? '' : 'none'; ?>">
                                    <th><label for="no_of_feature_listing"><?php _e('Number of Featured Listings', 'skd-property-listings'); ?></label></th>
                                    <td>
                                        <input type="number" name="no_of_feature_listing" id="no_of_feature_listing" step="1" value="<?php echo $planDetails && $planDetails->plan_type == 'package' && $planDetails->mark_feature_unlimited != 1 ? $planDetails->no_of_feature_listing : ''; ?>" <?php echo $planDetails && $planDetails->plan_type == 'package' && $planDetails->mark_feature_unlimited == 1 ? 'disabled' : ''; ?>>
                                        <br>
                                        <input type="checkbox" id="mark_feature_unlimited" name="mark_feature_unlimited" <?php echo $planDetails && $planDetails->plan_type == 'package' && $planDetails->mark_feature_unlimited == 1 ? 'checked' : ''; ?>>
                                        <label for="mark_feature_unlimited"><?php _e('Or mark as unlimited', 'skd-property-listings'); ?></label>
                                    </td>
                                </tr>

                                <tr>
                                    <th><label for="contact_owner"><?php _e('Contact Listing Owner', 'skd-property-listings'); ?></label>
                                        <div class="skd055textlabel">
                                            <input type="checkbox" id="contact_owner_hide" name="contact_owner_hide" <?php echo $planDetails && $planDetails->contact_owner_hide == 1 ? 'checked' : ''; ?>>
                                            <label for="contact_owner_hide"><?php _e('Hide this from pricing plan page', 'skd-property-listings'); ?></label>
                                        </div>
                                    </th>
                                    <td>
                                        <div class="skdSwitch">
                                            <label class="skdSwitchInner">
                                                <input type="checkbox" name="contact_owner" id="contact_owner" value="yes" <?php echo $planDetails && $planDetails->contact_owner == 'yes' ? 'checked' : ''; ?>>
                                                <span class="skdSwitchInner-switch-yes">Yes</span>
                                                <span class="skdSwitchInner-switch-no">No</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <th>
                                        <label for="customer_review"><?php _e('Customer Reviews', 'skd-property-listings'); ?></label>
                                        <div class="skd055textlabel">
                                            <input type="checkbox" id="customer_review_hide" name="customer_review_hide" <?php echo $planDetails && $planDetails->customer_review_hide == 1 ? 'checked' : ''; ?>>
                                            <label for="customer_review_hide"><?php _e('Hide this from pricing plan page', 'skd-property-listings'); ?></label>
                                        </div>
                                    </th>
                                    <td>
                                        <div class="skdSwitch">
                                            <label class="skdSwitchInner">
                                                <input type="checkbox" name="customer_review" id="customer_review" value="yes" <?php echo $planDetails && $planDetails->customer_review == 'yes' ? 'checked' : ''; ?>>
                                                <span class="skdSwitchInner-switch-yes">Yes</span>
                                                <span class="skdSwitchInner-switch-no">No</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <th>
                                        <label for="mark_as_sold"><?php _e('Mark as Sold', 'skd-property-listings'); ?></label>
                                        <div class="skd055textlabel">
                                            <input type="checkbox" id="mark_as_sold_hide" name="mark_as_sold_hide" <?php echo $planDetails && $planDetails->mark_as_sold_hide == 1 ? 'checked' : ''; ?>>
                                            <label for="mark_as_sold_hide"><?php _e('Hide this from pricing plan page', 'skd-property-listings'); ?></label>
                                        </div>
                                    </th>
                                    <td>
                                        <div class="skdSwitch">
                                            <label class="skdSwitchInner">
                                                <input type="checkbox" name="mark_as_sold" id="mark_as_sold" value="yes" <?php echo $planDetails && $planDetails->mark_as_sold == 'yes' ? 'checked' : ''; ?>>
                                                <span class="skdSwitchInner-switch-yes">Yes</span>
                                                <span class="skdSwitchInner-switch-no">No</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <th><label for="recomend_plan"><?php _e('Recommend this Plan', 'skd-property-listings'); ?></label></th>
                                    <td>
                                        <div class="skdSwitch">
                                            <label class="skdSwitchInner">
                                                <input type="checkbox" name="recomend_plan" id="recomend_plan" value="yes" <?php echo $planDetails && $planDetails->recomend_plan == 'yes' ? 'checked' : ''; ?>>
                                                <span class="skdSwitchInner-switch-yes">Yes</span>
                                                <span class="skdSwitchInner-switch-no">No</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <th><label for="hide_from_plan"><?php _e('Hide form All Plans', 'skd-property-listings'); ?></label></th>
                                    <td>
                                        <div class="skdSwitch">
                                            <label class="skdSwitchInner">
                                                <input type="checkbox" name="hide_from_plan" id="hide_from_plan" value="yes" <?php echo $planDetails && $planDetails->hide_from_plan == 'yes' ? 'checked' : ''; ?>>
                                                <span class="skdSwitchInner-switch-yes">Yes</span>
                                                <span class="skdSwitchInner-switch-no">No</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Sorting Configuration -->
                    <div class="postbox">
                        <h2 class="hndle"><span><?php _e('Sorting Configurations', 'skd-property-listings'); ?></span></h2>
                        <div class="inside">
                            <table class="form-table">
                                <tr>
                                    <th>
                                        <label for="listing_sorting_order"><?php _e('Listing Sorting Order', 'skd-property-listings'); ?></label>
                                    </th>
                                    <td>
                                        <input type="number" name="listing_sorting_order" id="listing_sorting_order" step="1" value="<?php echo $planDetails ? $planDetails->listing_sorting_order : '0'; ?>">
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Field Configuration -->
                    <div class="postbox">
                        <h2 class="hndle"><span><?php _e('Field Configurations', 'skd-property-listings'); ?></span></h2>
                        <div class="inside">
                            <table class="form-table">
                                <tr>
                                    <th>
                                        <label for="business_name"><?php _e('Business Name', 'skd-property-listings'); ?></label>
                                        <div class="skd055textlabel">
                                            <input type="checkbox" id="business_name_hide" name="business_name_hide" <?php echo $planDetails && $planDetails->business_name_hide == 1 ? 'checked' : ''; ?>>
                                            <label for="business_name_hide"><?php _e('Hide this from pricing plan page', 'skd-property-listings'); ?></label>
                                        </div>
                                    </th>
                                    <td>
                                        <div class="skdSwitch">
                                            <label class="skdSwitchInner">
                                                <input type="checkbox" name="business_name" id="business_name" value="yes" <?php echo $planDetails && $planDetails->business_name == 'yes' ? 'checked' : ''; ?>>
                                                <span class="skdSwitchInner-switch-yes">Yes</span>
                                                <span class="skdSwitchInner-switch-no">No</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <th>
                                        <label for="pricing_fld"><?php _e('Pricing', 'skd-property-listings'); ?></label>
                                        <div class="skd055textlabel">
                                            <input type="checkbox" id="pricing_fld_hide" name="pricing_fld_hide" <?php echo $planDetails && $planDetails->pricing_fld_hide == 1 ? 'checked' : ''; ?>>
                                            <label for="pricing_fld_hide"><?php _e('Hide this from pricing plan page', 'skd-property-listings'); ?></label>
                                        </div>
                                    </th>
                                    <td>
                                        <div class="skdSwitch">
                                            <label class="skdSwitchInner">
                                                <input type="checkbox" name="pricing_fld" id="pricing_fld" value="yes" <?php echo $planDetails && $planDetails->pricing_fld == 'yes' ? 'checked' : ''; ?>>
                                                <span class="skdSwitchInner-switch-yes">Yes</span>
                                                <span class="skdSwitchInner-switch-no">No</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <th>
                                        <label for="location_fld"><?php _e('Location', 'skd-property-listings'); ?></label>
                                        <div class="skd055textlabel">
                                            <input type="checkbox" id="location_fld_hide" name="location_fld_hide" <?php echo $planDetails && $planDetails->location_fld_hide == 1 ? 'checked' : ''; ?>>
                                            <label for="location_fld_hide"><?php _e('Hide this from pricing plan page', 'skd-property-listings'); ?></label>
                                        </div>
                                    </th>
                                    <td>
                                        <div class="skdSwitch">
                                            <label class="skdSwitchInner">
                                                <input type="checkbox" name="location_fld" id="location_fld" value="yes" <?php echo $planDetails && $planDetails->location_fld == 'yes' ? 'checked' : ''; ?>>
                                                <span class="skdSwitchInner-switch-yes">Yes</span>
                                                <span class="skdSwitchInner-switch-no">No</span>
                                            </label>
                                        </div>
                                        <div id="location_fld_div" style="display: <?php echo $planDetails && $planDetails->location_fld == 'yes' ? '' : 'none'; ?>;">
                                            <input type="number" name="location_fld_limit" id="location_fld_limit" step="1" placeholder="Set a limit" value="<?php echo $planDetails && $planDetails->location_fld_limit_unlimited != 1 ? $planDetails->location_fld_limit : ''; ?>" <?php echo $planDetails && $planDetails->location_fld_limit_unlimited == 1 ? 'disabled' : ''; ?>>
                                            <br>
                                            <input type="checkbox" id="location_fld_limit_unlimited" name="location_fld_limit_unlimited" <?php echo $planDetails && $planDetails->location_fld_limit_unlimited == 1 ? 'checked' : ''; ?>>
                                            <label for="location_fld_limit_unlimited"><?php _e('Or mark as unlimited', 'skd-property-listings'); ?></label>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <th>
                                        <label for="tag_fld"><?php _e('Tag', 'skd-property-listings'); ?></label>
                                        <div class="skd055textlabel">
                                            <input type="checkbox" id="tag_fld_hide" name="tag_fld_hide" <?php echo $planDetails && $planDetails->tag_fld_hide == 1 ? 'checked' : ''; ?>>
                                            <label for="tag_fld_hide"><?php _e('Hide this from pricing plan page', 'skd-property-listings'); ?></label>
                                        </div>
                                    </th>
                                    <td>
                                        <div class="skdSwitch">
                                            <label class="skdSwitchInner">
                                                <input type="checkbox" name="tag_fld" id="tag_fld" value="yes" <?php echo $planDetails && $planDetails->tag_fld == 'yes' ? 'checked' : ''; ?>>
                                                <span class="skdSwitchInner-switch-yes">Yes</span>
                                                <span class="skdSwitchInner-switch-no">No</span>
                                            </label>
                                        </div>
                                        <div id="tag_fld_div" style="display: <?php echo $planDetails && $planDetails->tag_fld == 'yes' ? '' : 'none'; ?>;">
                                            <input type="number" name="tag_fld_limit" id="tag_fld_limit" step="1" placeholder="Set a limit" value="<?php echo $planDetails && $planDetails->tag_fld_limit_unlimited != 1 ? $planDetails->tag_fld_limit : ''; ?>" <?php echo $planDetails && $planDetails->tag_fld_limit_unlimited == 1 ? 'disabled' : ''; ?>>
                                            <br>
                                            <input type="checkbox" id="tag_fld_limit_unlimited" name="tag_fld_limit_unlimited" <?php echo $planDetails && $planDetails->tag_fld_limit_unlimited == 1 ? 'checked' : ''; ?>>
                                            <label for="tag_fld_limit_unlimited"><?php _e('Or mark as unlimited', 'skd-property-listings'); ?></label>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <th>
                                        <label for="category_fld"><?php _e('Category', 'skd-property-listings'); ?></label>
                                        <div class="skd055textlabel">
                                            <input type="checkbox" id="category_fld_hide" name="category_fld_hide" <?php echo $planDetails && $planDetails->category_fld_hide == 1 ? 'checked' : ''; ?>>
                                            <label for="category_fld_hide"><?php _e('Hide this from pricing plan page', 'skd-property-listings'); ?></label>
                                        </div>
                                    </th>
                                    <td>
                                        <div class="skdSwitch">
                                            <label class="skdSwitchInner">
                                                <input type="checkbox" name="category_fld" id="category_fld" value="yes" <?php echo $planDetails && $planDetails->category_fld == 'yes' ? 'checked' : ''; ?>>
                                                <span class="skdSwitchInner-switch-yes">Yes</span>
                                                <span class="skdSwitchInner-switch-no">No</span>
                                            </label>
                                        </div>
                                        <div id="category_fld_div" style="display: <?php echo $planDetails && $planDetails->category_fld == 'yes' ? '' : 'none'; ?>;">
                                            <input type="number" name="category_fld_limit" id="category_fld_limit" step="1" placeholder="Set a limit" value="<?php echo $planDetails && $planDetails->category_fld_limit_unlimited != 1 ? $planDetails->category_fld_limit : ''; ?>" <?php echo $planDetails && $planDetails->category_fld_limit_unlimited == 1 ? 'disabled' : ''; ?>>
                                            <br>
                                            <input type="checkbox" id="category_fld_limit_unlimited" name="category_fld_limit_unlimited" <?php echo $planDetails && $planDetails->category_fld_limit_unlimited == 1 ? 'checked' : ''; ?>>
                                            <label for="category_fld_limit_unlimited"><?php _e('Or mark as unlimited', 'skd-property-listings'); ?></label>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <th>
                                        <label for="phone_fld"><?php _e('Phone', 'skd-property-listings'); ?></label>
                                        <div class="skd055textlabel">
                                            <input type="checkbox" id="phone_fld_hide" name="phone_fld_hide" <?php echo $planDetails && $planDetails->phone_fld_hide == 1 ? 'checked' : ''; ?>>
                                            <label for="phone_fld_hide"><?php _e('Hide this from pricing plan page', 'skd-property-listings'); ?></label>
                                        </div>
                                    </th>
                                    <td>
                                        <div class="skdSwitch">
                                            <label class="skdSwitchInner">
                                                <input type="checkbox" name="phone_fld" id="phone_fld" value="yes" <?php echo $planDetails && $planDetails->phone_fld == 'yes' ? 'checked' : ''; ?>>
                                                <span class="skdSwitchInner-switch-yes">Yes</span>
                                                <span class="skdSwitchInner-switch-no">No</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <th>
                                        <label for="phone_2_fld"><?php _e('Phone 2', 'skd-property-listings'); ?></label>
                                        <div class="skd055textlabel">
                                            <input type="checkbox" id="phone_2_fld_hide" name="phone_2_fld_hide" <?php echo $planDetails && $planDetails->phone_2_fld_hide == 1 ? 'checked' : ''; ?>>
                                            <label for="phone_2_fld_hide"><?php _e('Hide this from pricing plan page', 'skd-property-listings'); ?></label>
                                        </div>
                                    </th>
                                    <td>
                                        <div class="skdSwitch">
                                            <label class="skdSwitchInner">
                                                <input type="checkbox" name="phone_2_fld" id="phone_2_fld" value="yes" <?php echo $planDetails && $planDetails->phone_2_fld == 'yes' ? 'checked' : ''; ?>>
                                                <span class="skdSwitchInner-switch-yes">Yes</span>
                                                <span class="skdSwitchInner-switch-no">No</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <th>
                                        <label for="email_fld"><?php _e('Email', 'skd-property-listings'); ?></label>
                                        <div class="skd055textlabel">
                                            <input type="checkbox" id="email_fld_hide" name="email_fld_hide" <?php echo $planDetails && $planDetails->email_fld_hide == 1 ? 'checked' : ''; ?>>
                                            <label for="email_fld_hide"><?php _e('Hide this from pricing plan page', 'skd-property-listings'); ?></label>
                                        </div>
                                    </th>
                                    <td>
                                        <div class="skdSwitch">
                                            <label class="skdSwitchInner">
                                                <input type="checkbox" name="email_fld" id="email_fld" value="yes" <?php echo $planDetails && $planDetails->email_fld == 'yes' ? 'checked' : ''; ?>>
                                                <span class="skdSwitchInner-switch-yes">Yes</span>
                                                <span class="skdSwitchInner-switch-no">No</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <th>
                                        <label for="website_fld"><?php _e('Website', 'skd-property-listings'); ?></label>
                                        <div class="skd055textlabel">
                                            <input type="checkbox" id="website_fld_hide" name="website_fld_hide" <?php echo $planDetails && $planDetails->website_fld_hide == 1 ? 'checked' : ''; ?>>
                                            <label for="website_fld_hide"><?php _e('Hide this from pricing plan page', 'skd-property-listings'); ?></label>
                                        </div>
                                    </th>
                                    <td>
                                        <div class="skdSwitch">
                                            <label class="skdSwitchInner">
                                                <input type="checkbox" name="website_fld" id="website_fld" value="yes" <?php echo $planDetails && $planDetails->website_fld == 'yes' ? 'checked' : ''; ?>>
                                                <span class="skdSwitchInner-switch-yes">Yes</span>
                                                <span class="skdSwitchInner-switch-no">No</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <th>
                                        <label for="map_fld"><?php _e('Map', 'skd-property-listings'); ?></label>
                                        <div class="skd055textlabel">
                                            <input type="checkbox" id="map_fld_hide" name="map_fld_hide" <?php echo $planDetails && $planDetails->map_fld_hide == 1 ? 'checked' : ''; ?>>
                                            <label for="map_fld_hide"><?php _e('Hide this from pricing plan page', 'skd-property-listings'); ?></label>
                                        </div>
                                    </th>
                                    <td>
                                        <div class="skdSwitch">
                                            <label class="skdSwitchInner">
                                                <input type="checkbox" name="map_fld" id="map_fld" value="yes" <?php echo $planDetails && $planDetails->map_fld == 'yes' ? 'checked' : ''; ?>>
                                                <span class="skdSwitchInner-switch-yes">Yes</span>
                                                <span class="skdSwitchInner-switch-no">No</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <th>
                                        <label for="hide_owner_form_listing"><?php _e('Hide contact owner form for single listing page', 'skd-property-listings'); ?></label>
                                        <div class="skd055textlabel">
                                            <input type="checkbox" id="cntct_owner_price_pln_hide" name="cntct_owner_price_pln_hide" <?php echo $planDetails && $planDetails->cntct_owner_price_pln_hide == 1 ? 'checked' : ''; ?>>
                                            <label for="cntct_owner_price_pln_hide"><?php _e('Hide this from pricing plan page', 'skd-property-listings'); ?></label>
                                        </div>
                                    </th>
                                    <td>
                                        <div class="skdSwitch">
                                            <label class="skdSwitchInner">
                                                <input type="checkbox" name="hide_owner_form_listing" id="hide_owner_form_listing" value="yes" <?php echo $planDetails && $planDetails->hide_owner_form_listing == 'yes' ? 'checked' : ''; ?>>
                                                <span class="skdSwitchInner-switch-yes">Yes</span>
                                                <span class="skdSwitchInner-switch-no">No</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <th>
                                        <label for="tagline_fld"><?php _e('Tagline', 'skd-property-listings'); ?></label>
                                        <div class="skd055textlabel">
                                            <input type="checkbox" id="tagline_fld_hide" name="tagline_fld_hide" <?php echo $planDetails && $planDetails->tagline_fld_hide == 1 ? 'checked' : ''; ?>>
                                            <label for="tagline_fld_hide"><?php _e('Hide this from pricing plan page', 'skd-property-listings'); ?></label>
                                        </div>
                                    </th>
                                    <td>
                                        <div class="skdSwitch">
                                            <label class="skdSwitchInner">
                                                <input type="checkbox" name="tagline_fld" id="tagline_fld" value="yes" <?php echo $planDetails && $planDetails->tagline_fld == 'yes' ? 'checked' : ''; ?>>
                                                <span class="skdSwitchInner-switch-yes">Yes</span>
                                                <span class="skdSwitchInner-switch-no">No</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <th>
                                        <label for="address_fld"><?php _e('Address', 'skd-property-listings'); ?></label>
                                        <div class="skd055textlabel">
                                            <input type="checkbox" id="address_fld_hide" name="address_fld_hide" <?php echo $planDetails && $planDetails->address_fld_hide == 1 ? 'checked' : ''; ?>>
                                            <label for="address_fld_hide"><?php _e('Hide this from pricing plan page', 'skd-property-listings'); ?></label>
                                        </div>
                                    </th>
                                    <td>
                                        <div class="skdSwitch">
                                            <label class="skdSwitchInner">
                                                <input type="checkbox" name="address_fld" id="address_fld" value="yes" <?php echo $planDetails && $planDetails->address_fld == 'yes' ? 'checked' : ''; ?>>
                                                <span class="skdSwitchInner-switch-yes">Yes</span>
                                                <span class="skdSwitchInner-switch-no">No</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <th>
                                        <label for="social_info"><?php _e('Social Info', 'skd-property-listings'); ?></label>
                                        <div class="skd055textlabel">
                                            <input type="checkbox" id="social_info_hide" name="social_info_hide" <?php echo $planDetails && $planDetails->social_info_hide == 1 ? 'checked' : ''; ?>>
                                            <label for="social_info_hide"><?php _e('Hide this from pricing plan page', 'skd-property-listings'); ?></label>
                                        </div>
                                    </th>
                                    <td>
                                        <div class="skdSwitch">
                                            <label class="skdSwitchInner">
                                                <input type="checkbox" name="social_info" id="social_info" value="yes" <?php echo $planDetails && $planDetails->social_info == 'yes' ? 'checked' : ''; ?>>
                                                <span class="skdSwitchInner-switch-yes">Yes</span>
                                                <span class="skdSwitchInner-switch-no">No</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <th>
                                        <label for="zip_post_code"><?php _e('Zip/Post Code', 'skd-property-listings'); ?></label>
                                        <div class="skd055textlabel">
                                            <input type="checkbox" id="zip_post_code_hide" name="zip_post_code_hide" <?php echo $planDetails && $planDetails->zip_post_code_hide == 1 ? 'checked' : ''; ?>>
                                            <label for="zip_post_code_hide"><?php _e('Hide this from pricing plan page', 'skd-property-listings'); ?></label>
                                        </div>
                                    </th>
                                    <td>
                                        <div class="skdSwitch">
                                            <label class="skdSwitchInner">
                                                <input type="checkbox" name="zip_post_code" id="zip_post_code" value="yes" <?php echo $planDetails && $planDetails->zip_post_code == 'yes' ? 'checked' : ''; ?>>
                                                <span class="skdSwitchInner-switch-yes">Yes</span>
                                                <span class="skdSwitchInner-switch-no">No</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <th>
                                        <label for="description_fld"><?php _e('Description', 'skd-property-listings'); ?></label>
                                        <div class="skd055textlabel">
                                            <input type="checkbox" id="description_fld_hide" name="description_fld_hide" <?php echo $planDetails && $planDetails->description_fld_hide == 1 ? 'checked' : ''; ?>>
                                            <label for="description_fld_hide"><?php _e('Hide this from pricing plan page', 'skd-property-listings'); ?></label>
                                        </div>
                                    </th>
                                    <td>
                                        <div class="skdSwitch">
                                            <label class="skdSwitchInner">
                                                <input type="checkbox" name="description_fld" id="description_fld" value="yes" <?php echo $planDetails && $planDetails->description_fld == 'yes' ? 'checked' : ''; ?>>
                                                <span class="skdSwitchInner-switch-yes">Yes</span>
                                                <span class="skdSwitchInner-switch-no">No</span>
                                            </label>
                                        </div>
                                        <div id="description_fld_div" style="display: <?php echo $planDetails && $planDetails->description_fld == 'yes' ? '' : 'none'; ?>;">
                                            <input type="number" name="description_fld_limit" id="description_fld_limit" step="1" placeholder="Set a limit" value="<?php echo $planDetails && $planDetails->description_fld_limit_unlimited != 1 ? $planDetails->description_fld_limit : ''; ?>" <?php echo $planDetails && $planDetails->description_fld_limit_unlimited == 1 ? 'disabled' : ''; ?>>
                                            <br>
                                            <input type="checkbox" id="description_fld_limit_unlimited" name="description_fld_limit_unlimited" <?php echo $planDetails && $planDetails->description_fld_limit_unlimited == 1 ? 'checked' : ''; ?>>
                                            <label for="description_fld_limit_unlimited"><?php _e('Or mark as unlimited', 'skd-property-listings'); ?></label>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <th>
                                        <label for="video_fld"><?php _e('Video', 'skd-property-listings'); ?></label>
                                        <div class="skd055textlabel">
                                            <input type="checkbox" id="video_fld_hide" name="video_fld_hide" <?php echo $planDetails && $planDetails->video_fld_hide == 1 ? 'checked' : ''; ?>>
                                            <label for="video_fld_hide"><?php _e('Hide this from pricing plan page', 'skd-property-listings'); ?></label>
                                        </div>
                                    </th>
                                    <td>
                                        <div class="skdSwitch">
                                            <label class="skdSwitchInner">
                                                <input type="checkbox" name="video_fld" id="video_fld" value="yes" <?php echo $planDetails && $planDetails->video_fld == 'yes' ? 'checked' : ''; ?>>
                                                <span class="skdSwitchInner-switch-yes">Yes</span>
                                                <span class="skdSwitchInner-switch-no">No</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <th>
                                        <label for="images_fld"><?php _e('Images', 'skd-property-listings'); ?></label>
                                        <div class="skd055textlabel">
                                            <input type="checkbox" id="images_fld_hide" name="images_fld_hide" <?php echo $planDetails && $planDetails->images_fld_hide == 1 ? 'checked' : ''; ?>>
                                            <label for="images_fld_hide"><?php _e('Hide this from pricing plan page', 'skd-property-listings'); ?></label>
                                        </div>
                                    </th>
                                    <td>
                                        <div class="skdSwitch">
                                            <label class="skdSwitchInner">
                                                <input type="checkbox" name="images_fld" id="images_fld" value="yes" <?php echo $planDetails && $planDetails->images_fld == 'yes' ? 'checked' : ''; ?>>
                                                <span class="skdSwitchInner-switch-yes">Yes</span>
                                                <span class="skdSwitchInner-switch-no">No</span>
                                            </label>
                                        </div>
                                        <div id="images_fld_div" style="display: <?php echo $planDetails && $planDetails->images_fld == 'yes' ? '' : 'none'; ?>;">
                                            <input type="number" name="images_fld_limit" id="images_fld_limit" step="1" placeholder="Set a limit" value="<?php echo $planDetails && $planDetails->images_fld_limit_unlimited != 1 ? $planDetails->images_fld_limit : ''; ?>" <?php echo $planDetails && $planDetails->images_fld_limit_unlimited == 1 ? 'disabled' : ''; ?>>
                                            <br>
                                            <input type="checkbox" id="images_fld_limit_unlimited" name="images_fld_limit_unlimited" <?php echo $planDetails && $planDetails->images_fld_limit_unlimited == 1 ? 'checked' : ''; ?>>
                                            <label for="images_fld_limit_unlimited"><?php _e('Or mark as unlimited', 'skd-property-listings'); ?></label>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <th>
                                        <label for="online_business"><?php _e('Online-only business?', 'skd-property-listings'); ?></label>
                                        <div class="skd055textlabel">
                                            <input type="checkbox" id="online_business_hide" name="online_business_hide" <?php echo $planDetails && $planDetails->online_business_hide == 1 ? 'checked' : ''; ?>>
                                            <label for="online_business_hide"><?php _e('Hide this from pricing plan page', 'skd-property-listings'); ?></label>
                                        </div>
                                    </th>
                                    <td>
                                        <div class="skdSwitch">
                                            <label class="skdSwitchInner">
                                                <input type="checkbox" name="online_business" id="online_business" value="yes" <?php echo $planDetails && $planDetails->online_business == 'yes' ? 'checked' : ''; ?>>
                                                <span class="skdSwitchInner-switch-yes">Yes</span>
                                                <span class="skdSwitchInner-switch-no">No</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <th>
                                        <label for="upload_logo_images"><?php _e('Upload Logo/Preview Image First, and then upload your sample images to showcase you work!', 'skd-property-listings'); ?></label>
                                        <div class="skd055textlabel">
                                            <input type="checkbox" id="upload_logo_images_hide" name="upload_logo_images_hide" <?php echo $planDetails && $planDetails->upload_logo_images_hide == 1 ? 'checked' : ''; ?>>
                                            <label for="upload_logo_images_hide"><?php _e('Hide this from pricing plan page', 'skd-property-listings'); ?></label>
                                        </div>
                                    </th>
                                    <td>
                                        <div class="skdSwitch">
                                            <label class="skdSwitchInner">
                                                <input type="checkbox" name="upload_logo_images" id="upload_logo_images" value="yes" <?php echo $planDetails && $planDetails->upload_logo_images == 'yes' ? 'checked' : ''; ?>>
                                                <span class="skdSwitchInner-switch-yes">Yes</span>
                                                <span class="skdSwitchInner-switch-no">No</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <th>
                                        <label for="view_count"><?php _e('View Count', 'skd-property-listings'); ?></label>
                                        <div class="skd055textlabel">
                                            <input type="checkbox" id="view_count_hide" name="view_count_hide" <?php echo $planDetails && $planDetails->view_count_hide == 1 ? 'checked' : ''; ?>>
                                            <label for="view_count_hide"><?php _e('Hide this from pricing plan page', 'skd-property-listings'); ?></label>
                                        </div>
                                    </th>
                                    <td>
                                        <div class="skdSwitch">
                                            <label class="skdSwitchInner">
                                                <input type="checkbox" name="view_count" id="view_count" value="yes" <?php echo $planDetails && $planDetails->view_count == 'yes' ? 'checked' : ''; ?>>
                                                <span class="skdSwitchInner-switch-yes">Yes</span>
                                                <span class="skdSwitchInner-switch-no">No</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <th>
                                        <label for="contact_details"><?php _e('Contact Details', 'skd-property-listings'); ?></label>
                                        <div class="skd055textlabel">
                                            <input type="checkbox" id="contact_details_hide" name="contact_details_hide" <?php echo $planDetails && $planDetails->contact_details_hide == 1 ? 'checked' : ''; ?>>
                                            <label for="contact_details_hide"><?php _e('Hide this from pricing plan page', 'skd-property-listings'); ?></label>
                                        </div>
                                    </th>
                                    <td>
                                        <div class="skdSwitch">
                                            <label class="skdSwitchInner">
                                                <input type="checkbox" name="contact_details" id="contact_details" value="yes" <?php echo $planDetails && $planDetails->contact_details == 'yes' ? 'checked' : ''; ?>>
                                                <span class="skdSwitchInner-switch-yes">Yes</span>
                                                <span class="skdSwitchInner-switch-no">No</span>
                                            </label>
                                        </div>
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
                                <strong><?php _e('Status:', 'skd-property-listings'); ?></strong>
                                <select name="plan_status">
                                    <option value="published" <?php echo $planDetails && $planDetails->plan_status == 'published' ? 'selected' : ''; ?>>Published</option>
                                    <option value="pending" <?php echo $planDetails && $planDetails->plan_status == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="draft" <?php echo $planDetails && $planDetails->plan_status == 'draft' ? 'selected' : ''; ?>>Draft</option>
                                </select>
                            </p>
                            <p>
                                <strong><?php _e('Created On:', 'skd-property-listings'); ?></strong>
                                <input type="text" name="created_date" value="<?php echo $planDetails && $planDetails->created_date ? $planDetails->created_date : current_time('Y-m-d H:i:s'); ?>" readonly>
                            </p>
                            <p>
                                <?php if ($action === 'edit') { ?>
                                    <input type="submit" name="save_price_plan" class="button button-primary" value="<?php _e('Update Plan', 'skd-property-listings'); ?>">
                                <?php } else { ?>
                                    <input type="submit" name="save_price_plan" class="button button-primary" value="<?php _e('Save Plan', 'skd-property-listings'); ?>">
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
        $('input[name="plan_type"]').change(function() {
            if ($(this).val() == 'pay_per_listing') {
                $('.pay_per_listing_div').show();
                $('.package_listing_div').hide();
            } else {
                $('.pay_per_listing_div').hide();
                $('.package_listing_div').show();
            }
        }).each(function() {
            if ($(this).is(':checked')) {
                if ($(this).val() == 'pay_per_listing') {
                    $('.pay_per_listing_div').show();
                    $('.package_listing_div').hide();
                } else {
                    $('.pay_per_listing_div').hide();
                    $('.package_listing_div').show();
                }
            }
        });
    });
</script>