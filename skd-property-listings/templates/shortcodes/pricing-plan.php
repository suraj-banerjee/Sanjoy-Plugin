<section class="sec5">
    <div class="container">
        <div class="myRowBox myRowBoxCenter">
            <?php foreach ($plans as $plan):
                $gst_tooltip = '';
                if ($plan->is_free) {
                    $total_price = 0;
                } else if ($plan->add_gst_rate) {
                    $gst_amount = ($plan->gst_type == 'percentage') ? ($plan->price * $plan->gst_rate / 100) : $plan->gst_rate;
                    $total_price = $plan->price + $gst_amount;
                    $gst_tooltip = 'Include $' . number_format($gst_amount, 2) . ' GST';
                } else {
                    $total_price = $plan->price;
                }

                // Check if the user has purchased this plan
                $is_purchased = isset($purchased_plans[$plan->id]);
                $order_id = $is_purchased ? $purchased_plans[$plan->id] : null;

                // Button logic
                $button_url = $is_purchased
                    ? site_url('/add-new-listing/?order=' . $order_id)
                    : site_url('/checkout/?plan=' . $plan->id);

                $gst_text = $plan->plan_type == 'package' ? 'Include GST Per Package' : 'Include GST Per Listing';

                $duration_text = '';
                if ($plan->never_expire) {
                    $duration_text = '/Lifetime';
                } else if (!$plan->is_free && $plan->plan_type == 'package') {
                    if ($plan->duration_unit == 'days') {
                        $duration_text = '/' . ($plan->listing_duration > 1 ? $plan->listing_duration : '') . ' Day';
                    } else if ($plan->duration_unit == 'weeks') {
                        $duration_text = '/' . ($plan->listing_duration > 1 ? $plan->listing_duration : '') . ' Week';
                    } else if ($plan->duration_unit == 'months') {
                        $duration_text = '/' . ($plan->listing_duration > 1 ? $plan->listing_duration : '') . ' Month';
                    } else if ($plan->duration_unit == 'years') {
                        $duration_text = '/' . ($plan->listing_duration > 1 ? $plan->listing_duration : '') . ' Year';
                    }
                }
            ?>
                <div class="directoristItemOuter_Col_4">
                    <div class="directoristPriceItemInner">
                        <div class="directoristPriceItemPriceTitle">
                            <h4><?php echo esc_html($plan->plan_name); ?></h4>
                            <?php if ($is_purchased) { ?>
                                <iconify-icon icon="material-symbols:check"></iconify-icon>
                                <h5>Active</h5>
                            <?php } ?>
                        </div>
                        <div class="directoristPriceItemPricingPrice">
                            <div class="directoristPriceItemPricingValue">
                                <?php if (!$plan->is_free) { ?>
                                    <h2> $ </h2>
                                <?php } ?>
                                <div class="directoristPriceItemPricingValueInfoBox">
                                    <div class="directoristPriceItemPricingPriceInfo1">
                                        <h2>
                                            <?php
                                            if ($plan->is_free) {
                                                echo 'Free';
                                            } else {
                                                echo  number_format($total_price);
                                            }
                                            ?>
                                        </h2>
                                        <img src="<?php echo esc_url(SKD_PL_PLUGIN_URL . 'assets/images/question-circle.png'); ?>" alt="">
                                        <h4>
                                            <?php
                                            if (!$plan->is_free) {
                                                echo esc_html($duration_text);
                                            }
                                            ?>
                                        </h4>
                                        <div class="tooltip_directoristPriceItemPricingPriceInfo1">
                                            <h5><?php echo esc_html($gst_tooltip); ?></h5>
                                        </div>
                                    </div>
                                    <div class="directoristPriceItemPricingPriceInfo2">
                                        <h2><?php echo esc_html($gst_text); ?></h2>
                                    </div>
                                </div>
                            </div>
                            <p class="directoristPriceItemPlan_description"><?php echo esc_html($plan->plan_description); ?></p>
                        </div>
                        <div class="directoristPriceItemPriceFeatures">
                            <ul>
                                <?php if (!$plan->enable_subscription_hide && $plan->enable_subscription == 'yes') { ?>
                                    <li>Auto renewing</li>
                                <?php } ?>

                                <?php if ($plan->mark_as_unlimited) {
                                    echo '<li>Regular Listings</li>';
                                } else if ($plan->no_of_listing > 0) {
                                    echo '<li>' . $plan->no_of_listing . ' Regular Listings</li>';
                                }
                                ?>

                                <?php
                                $homeStateTxt = '';
                                if ($plan->id == 3) {
                                    $homeStateTxt = '(State Only)';
                                } else if ($plan->id == 4) {
                                    $homeStateTxt = '(Home & State)';
                                }
                                if ($plan->mark_feature_unlimited) {
                                    echo "<li>Featured Listings {$homeStateTxt}</li>";
                                } else if ($plan->no_of_feature_listing > 0) {
                                    echo '<li>' . $plan->no_of_feature_listing . ' Featured Listings ' . $homeStateTxt . '</li>';
                                }
                                ?>

                                <?php if (!$plan->contact_owner_hide && $plan->contact_owner == 'yes') { ?>
                                    <li>Contact Owner</li>
                                <?php } ?>

                                <?php if (!$plan->business_name_hide && $plan->business_name == 'yes') { ?>
                                    <li>Business Name</li>
                                <?php } ?>

                                <?php if (!$plan->description_fld_hide && $plan->description_fld == 'yes') { ?>
                                    <li>Description</li>
                                <?php } ?>

                                <?php if (!$plan->video_fld_hide && $plan->video_fld == 'yes') { ?>
                                    <li>Video</li>
                                <?php } ?>

                                <?php if (!$plan->location_fld_hide && $plan->location_fld == 'yes') { ?>
                                    <li>Location
                                        <span>
                                            <?php echo esc_html($plan->location_fld_limit_unlimited ? '(Unlimited)' : ($plan->location_fld_limit > 0 ? '(Maximum of ' . $plan->location_fld_limit . ')' : '')); ?>
                                        </span>
                                    </li>
                                <?php } ?>

                                <?php if (!$plan->tag_fld_hide && $plan->tag_fld == 'yes') { ?>
                                    <li>Tag
                                        <span>
                                            <?php echo esc_html($plan->tag_fld_limit_unlimited ? '(Unlimited)' : ($plan->tag_fld_limit > 0 ? '(Maximum of ' . $plan->tag_fld_limit . ')' : '')); ?>
                                        </span>
                                    </li>
                                <?php } ?>

                                <?php if (!$plan->category_fld_hide && $plan->category_fld == 'yes') { ?>
                                    <li>Category
                                        <span>
                                            <?php echo esc_html($plan->category_fld_limit_unlimited ? '(Unlimited)' : ($plan->category_fld_limit > 0 ? '(Maximum of ' . $plan->category_fld_limit . ')' : '')); ?>
                                        </span>
                                    </li>
                                <?php } ?>

                                <?php if (!$plan->images_fld_hide && $plan->images_fld == 'yes') { ?>
                                    <li>Images
                                        <span>
                                            <?php echo esc_html($plan->images_fld_limit_unlimited ? '(Unlimited)' : ($plan->images_fld_limit > 0 ? '(Maximum of ' . $plan->images_fld_limit . ')' : '')); ?>
                                        </span>
                                    </li>
                                <?php } ?>

                                <?php if (!$plan->online_business_hide && $plan->online_business == 'yes') { ?>
                                    <li>Online-only business?</li>
                                <?php } ?>

                                <?php if (!$plan->upload_logo_images_hide && $plan->upload_logo_images == 'yes') { ?>
                                    <li>Upload Logo/Preview Image First, and then upload your sample images to showcase you work!</li>
                                <?php } ?>

                                <?php if (!$plan->phone_fld_hide && $plan->phone_fld == 'yes') { ?>
                                    <li>Phone</li>
                                <?php } ?>

                                <?php if (!$plan->phone_2_fld_hide && $plan->phone_2_fld == 'yes') { ?>
                                    <li>Phone 2</li>
                                <?php } ?>

                                <?php if (!$plan->email_fld_hide && $plan->email_fld == 'yes') { ?>
                                    <li>Email</li>
                                <?php } ?>

                                <?php if (!$plan->website_fld_hide && $plan->website_fld == 'yes') { ?>
                                    <li>Website</li>
                                <?php } ?>

                                <?php if (!$plan->view_count_hide && $plan->view_count == 'yes') { ?>
                                    <li>View Count</li>
                                <?php } ?>

                                <?php if (!$plan->map_fld_hide && $plan->map_fld == 'yes') { ?>
                                    <li>Map</li>
                                <?php } ?>

                                <?php if (!$plan->address_fld_hide && $plan->address_fld == 'yes') { ?>
                                    <li>Address</li>
                                <?php } ?>

                                <?php if (!$plan->social_info_hide && $plan->social_info == 'yes') { ?>
                                    <li>Social Info</li>
                                <?php } ?>

                                <?php if (!$plan->zip_post_code_hide && $plan->zip_post_code == 'yes') { ?>
                                    <li>Zip/Post Code</li>
                                <?php } ?>

                                <?php if (!$plan->contact_details_hide && $plan->contact_details == 'yes') { ?>
                                    <li>Contact Details</li>
                                <?php } ?>
                            </ul>
                            <div class="directoristFeaturesButton">
                                <a href="<?php echo esc_url($button_url); ?>">
                                    Continue
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>