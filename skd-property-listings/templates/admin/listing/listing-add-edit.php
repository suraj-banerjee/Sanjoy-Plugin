<div class="wrap" id="skd-pl-listing-add-edit">
    <?php if ($action === 'edit') { ?>
        <h1 class="wp-heading-inline"><?php _e('Edit Listing', 'skd-property-listings'); ?></h1>
    <?php } else { ?>
        <h1 class="wp-heading-inline"><?php _e('Add Listing', 'skd-property-listings'); ?></h1>
    <?php } ?>
    <a href="<?php echo admin_url('admin.php?page=skd-pl-all-listings'); ?>" class="page-title-action">
        <?php _e('Back to List', 'skd-property-listings'); ?>
    </a>

    <form method="POST">
        <?php if (!empty($_GET['message']) && $_GET['message'] === 'success') : ?>
            <div class="updated notice is-dismissible">
                <p><?php _e('Listing updated successfully!', 'skd-property-listings'); ?></p>
            </div>
        <?php endif; ?>
        <?php wp_nonce_field('save_listing', 'skd_listing_nonce'); ?>
        <input type="hidden" name="listing_id" value="<?php echo $listing ? $listing->id : ''; ?>">

        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">

                <!-- Left Side: Listing Details -->
                <div id="post-body-content">

                    <!-- Section 1: Listing Details -->
                    <div class="postbox">
                        <h2 class="hndle"><span><?php _e('Listing Details', 'skd-property-listings'); ?></span></h2>
                        <div class="inside">
                            <table class="form-table">
                                <tr>
                                    <th><label for="listing_title"><?php _e('Title', 'skd-property-listings'); ?></label></th>
                                    <td><input type="text" name="listing_title" id="listing_title" class="regular-text" required style="width: 100%;" value="<?php echo $listing ? esc_attr($listing->listing_title) : ''; ?>"></td>
                                </tr>
                                <tr>
                                    <th><label for="listing_description"><?php _e('Description', 'skd-property-listings'); ?></label></th>
                                    <td><textarea name="listing_description" id="listing_description" rows="3" style="width: 100%;"><?php echo $listing ? esc_textarea($listing->listing_description) : ''; ?></textarea></td>
                                </tr>
                                <tr>
                                    <th><label for="tagline"><?php _e('Tagline', 'skd-property-listings'); ?></label></th>
                                    <td><input type="text" name="tagline" id="tagline" class="regular-text" value="<?php echo $listing ? esc_attr($listing->tagline) : ''; ?>"></td>
                                </tr>
                                <tr style="display: none;">
                                    <th><label for="price"><?php _e('Pricing', 'skd-property-listings'); ?></label></th>
                                    <td><input type="number" name="price" id="price" class="regular-text" value="<?php echo $listing ? esc_attr($listing->price) : ''; ?>"></td>
                                </tr>
                                <tr>
                                    <th><label for="view_count"><?php _e('View Count', 'skd-property-listings'); ?></label></th>
                                    <td><input type="number" name="view_count" id="view_count" class="regular-text" value="<?php echo $listing ? esc_attr($listing->view_count) : ''; ?>"></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Section 2: Contact Information -->
                    <div class="postbox">
                        <h2 class="hndle"><span><?php _e('Contact Information', 'skd-property-listings'); ?></span></h2>
                        <div class="inside">
                            <table class="form-table">
                                <tr>
                                    <th><label for="contact_details"><?php _e('Contact Details', 'skd-property-listings'); ?></label></th>
                                    <td><input type="text" name="contact_details" id="contact_details" class="regular-text" value="<?php echo $listing ? esc_attr($listing->contact_details) : ''; ?>"></td>
                                </tr>
                                <tr>
                                    <th><label for="contact_phone"><?php _e('Phone', 'skd-property-listings'); ?></label></th>
                                    <td><input type="text" name="contact_phone" id="contact_phone" class="regular-text" value="<?php echo $listing ? esc_attr($listing->contact_phone) : ''; ?>" required></td>
                                </tr>
                                <tr>
                                    <th><label for="contact_phone2"><?php _e('Phone 2', 'skd-property-listings'); ?></label></th>
                                    <td><input type="text" name="contact_phone2" id="contact_phone2" class="regular-text" value="<?php echo $listing ? esc_attr($listing->contact_phone2) : ''; ?>"></td>
                                </tr>
                                <tr>
                                    <th><label for="contact_email"><?php _e('Email', 'skd-property-listings'); ?></label></th>
                                    <td><input type="email" name="contact_email" id="contact_email" class="regular-text" value="<?php echo $listing ? esc_attr($listing->contact_email) : ''; ?>" required></td>
                                </tr>
                                <tr>
                                    <th><label for="contact_zip"><?php _e('Zip/Post Code', 'skd-property-listings'); ?></label></th>
                                    <td><input type="text" name="contact_zip" id="contact_zip" class="regular-text" value="<?php echo $listing ? esc_attr($listing->contact_zip) : ''; ?>" required></td>
                                </tr>
                                <tr>
                                    <th><label for="contact_website"><?php _e('Website', 'skd-property-listings'); ?></label></th>
                                    <td><input type="text" name="contact_website" id="contact_website" class="regular-text" value="<?php echo $listing ? esc_attr($listing->contact_website) : ''; ?>"></td>
                                </tr>
                                <tr>
                                    <th><label for="hide_owner_form"><?php _e('Hide contact owner form for single listing page', 'skd-property-listings'); ?></label></th>
                                    <td><input type="checkbox" name="hide_owner_form" id="hide_owner_form" value="1" <?php checked($listing && $listing->hide_owner_form, 1); ?>></td>
                                </tr>
                            </table>

                            <!-- Social Info -->
                            <h3><?php _e('Social Info', 'skd-property-listings'); ?></h3>
                            <div id="skd-social-container">
                                <!-- Existing social info rows will be added here -->
                                <?php if (!empty($listing) && !empty($listing->social_info)) {
                                    $socialInfoArr = json_decode($listing->social_info, true);

                                    if (is_array($socialInfoArr)) {
                                        foreach ($socialInfoArr as $social) { ?>
                                            <div class="skd-social-row">
                                                <select name="social_networks[]" class="skd-social-select">
                                                    <option value="behance" <?php selected($social['network'], 'behance'); ?>>Behance</option>
                                                    <option value="dribbble" <?php selected($social['network'], 'dribbble'); ?>>Dribbble</option>
                                                    <option value="facebook" <?php selected($social['network'], 'facebook'); ?>>Facebook</option>
                                                    <option value="flickr" <?php selected($social['network'], 'flickr'); ?>>Flickr</option>
                                                    <option value="github" <?php selected($social['network'], 'github'); ?>>Github</option>
                                                    <option value="instagram" <?php selected($social['network'], 'instagram'); ?>>Instagram</option>
                                                    <option value="linkedin" <?php selected($social['network'], 'linkedin'); ?>>LinkedIn</option>
                                                    <option value="pinterest" <?php selected($social['network'], 'pinterest'); ?>>Pinterest</option>
                                                    <option value="reddit" <?php selected($social['network'], 'reddit'); ?>>Reddit</option>
                                                    <option value="snapchat" <?php selected($social['network'], 'snapchat'); ?>>Snapchat</option>
                                                    <option value="soundcloud" <?php selected($social['network'], 'soundcloud'); ?>>SoundCloud</option>
                                                    <option value="stack-overflow" <?php selected($social['network'], 'stack-overflow'); ?>>StackOverFLow</option>
                                                    <option value="tumblr" <?php selected($social['network'], 'tumblr'); ?>>Tumblr</option>
                                                    <option value="twitter" <?php selected($social['network'], 'twitter'); ?>>Twitter</option>
                                                    <option value="vimeo" <?php selected($social['network'], 'vimeo'); ?>>Vimeo</option>
                                                    <option value="vine" <?php selected($social['network'], 'vine'); ?>>Vine</option>
                                                    <option value="youtube" <?php selected($social['network'], 'youtube'); ?>>Youtube</option>
                                                </select>
                                                <input type="url" name="social_urls[]" class="skd-social-url" placeholder="Enter URL" value="<?php echo esc_url($social['url']); ?>">
                                                <button type="button" class="button skd-remove-social">X</button>
                                            </div>
                                <?php }
                                    }
                                } ?>
                            </div>
                            <button type="button" id="add_social_info" class="button"><?php _e('Add Social', 'skd-property-listings'); ?></button>
                        </div>
                    </div>

                    <!-- Section 3: Address & Map -->
                    <div class="postbox">
                        <h2 class="hndle"><span><?php _e('Address & Map', 'skd-property-listings'); ?></span></h2>
                        <div class="inside">
                            <table class="form-table">
                                <tr>
                                    <th><label for="list_address"><?php _e('Address', 'skd-property-listings'); ?></label></th>
                                    <td><input type="text" name="list_address" id="list_address" class="regular-text" autocomplete="off" value="<?php echo $listing ? esc_attr($listing->list_address) : ''; ?>"></td>
                                </tr>
                                <tr>
                                    <th><?php _e('Online-only business?', 'skd-property-listings'); ?></th>
                                    <td><input type="checkbox" name="is_online_only" id="is_online_only" <?php checked($listing && $listing->is_online_only, 1); ?>></td>
                                </tr>
                                <tr>
                                    <th><?php _e('Show Map', 'skd-property-listings'); ?></th>
                                    <td>
                                        <div id="map_canvas" style="height: 300px;"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <th><label for="manual_coordinates"><?php _e('Enter Coordinates Manually', 'skd-property-listings'); ?></label></th>
                                    <td><input type="checkbox" id="manual_coordinates" name="manual_coordinates" <?php checked($listing && $listing->manual_coordinates, 1); ?>></td>
                                </tr>
                                <tr class="manual-coordinates-fields" style="display: <?php echo $listing && $listing->manual_coordinates == 1 ? '' : 'none' ?>;">
                                    <th><label for="latitude"><?php _e('Latitude', 'skd-property-listings'); ?></label></th>
                                    <td><input type="text" name="latitude" id="latitude" value="<?php echo $listing ? esc_attr($listing->latitude) : ''; ?>"></td>
                                </tr>
                                <tr class="manual-coordinates-fields" style="display: <?php echo $listing && $listing->manual_coordinates == 1 ? '' : 'none' ?>;">
                                    <th><label for="longitude"><?php _e('Longitude', 'skd-property-listings'); ?></label></th>
                                    <td><input type="text" name="longitude" id="longitude" value="<?php echo $listing ? esc_attr($listing->longitude) : ''; ?>"></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Section 4: Images & Video -->
                    <div class="postbox">
                        <h2 class="hndle"><span><?php _e('Images & Video', 'skd-property-listings'); ?></span></h2>
                        <div class="inside">
                            <table class="form-table">
                                <tr>
                                    <th><label for="skd_logo"><?php _e('Upload Logo/Preview Image', 'skd-property-listings'); ?></label></th>
                                    <td>
                                        <input type="hidden" name="skd_logo" id="skd_logo" value="<?php echo $listing ? esc_url($listing->skd_logo) : ''; ?>">
                                        <button type="button" class="button skd-upload-logo" data-target="#skd_logo">Select Logo</button>
                                        <div id="skd_logo_preview" style="margin-top: 10px;">
                                            <?php if ($listing && $listing->skd_logo) : ?>
                                                <img src="<?php echo esc_url($listing->skd_logo); ?>" style="max-width: 150px; height: auto;">
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <th><label for="skd_gallery"><?php _e('Gallery Images', 'skd-property-listings'); ?></label></th>
                                    <td>
                                        <input type="hidden" name="skd_gallery" id="skd_gallery" value="<?php echo isset($listing->skd_gallery) ? esc_attr(implode(',', json_decode($listing->skd_gallery, true))) : ''; ?>">
                                        <button type="button" class="button skd-upload-gallery">Select Gallery Images</button>
                                        <div id="skd_gallery_preview" style="margin-top: 10px; display: flex; flex-wrap: wrap; gap: 10px;">
                                            <?php
                                            if (!empty($listing->skd_gallery)) {
                                                $gallery_images = json_decode($listing->skd_gallery, true); // Decode JSON to array
                                                if (!empty($gallery_images) && is_array($gallery_images)) {
                                                    foreach ($gallery_images as $image_url) { ?>
                                                        <div class="skd-gallery-item" style="position: relative; display: inline-block; margin-right: 10px;">
                                                            <img src="<?php echo esc_url($image_url); ?>" style="max-width: 100px; height: auto; display: block;">
                                                            <button class="remove-gallery-image" data-url="<?php echo esc_url($image_url); ?>" style="position: absolute; top: 0; right: 0; background: red; color: white; border: none; cursor: pointer;">X</button>
                                                        </div>
                                            <?php }
                                                }
                                            }
                                            ?>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <th><label for="video"><?php _e('Video URL', 'skd-property-listings'); ?></label></th>
                                    <td><input type="url" name="video" id="video" value="<?php echo $listing ? esc_url($listing->video) : ''; ?>"></td>
                                </tr>
                                <tr>
                                    <th><label for="privacy_policy"><?php _e('I agree to the Privacy Policy and Terms of Service', 'skd-property-listings'); ?></label></th>
                                    <td><input type="checkbox" name="privacy_policy" id="privacy_policy" <?php checked($listing && $listing->privacy_policy, 1); ?> required></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                </div>

                <!-- Right Side: Publish Box & Meta Fields -->
                <div id="postbox-container-1" class="postbox-container">

                    <!-- Section 1: Publish Box -->
                    <div class="postbox">
                        <h2 class="hndle"><span><?php _e('Publish', 'skd-property-listings'); ?></span></h2>
                        <div class="inside">
                            <p>
                                <strong><?php _e('Status:', 'skd-property-listings'); ?></strong>
                                <select name="listing_status">
                                    <option value="publish" <?php selected($listing && $listing->listing_status === 'publish'); ?>><?php _e('Published', 'skd-property-listings'); ?></option>
                                    <option value="pending" <?php selected($listing && $listing->listing_status === 'pending'); ?>><?php _e('Pending', 'skd-property-listings'); ?></option>
                                    <option value="draft" <?php selected($listing && $listing->listing_status === 'draft'); ?>><?php _e('Draft', 'skd-property-listings'); ?></option>
                                </select>
                            </p>

                            <p>
                                <strong><?php _e('Author:', 'skd-property-listings'); ?></strong>
                                <select name="user_id" id="" required style="width: 100%;">
                                    <?php foreach ($user_list as $uKey => $userName) { ?>
                                        <option value="<?php echo $uKey; ?>" <?php selected($listing && $listing->user_id == $uKey || (!$listing && $uKey === $current_user_id)); ?>>
                                            <?php echo esc_html($userName); ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </p>

                            <input type="submit" name="save_listing" class="button-primary" value="<?php _e('Save', 'skd-property-listings'); ?>">
                        </div>
                    </div>

                    <!-- Section 2: Belong to Plan -->
                    <?php
                    $selected_plan_id = $listing ? esc_attr($listing->plan_id) : '';
                    $selected_listing_type = $listing ? esc_attr($listing->listing_type) : '';
                    $expiration_date = $listing ? esc_attr($listing->expiration_date) : '';
                    ?>
                    <input type="hidden" name="order_id" value="<?php echo $listing ? esc_attr($listing->order_id) : 0; ?>">
                    <div class="postbox">
                        <h2 class="hndle"><span><?php _e('Belong to Plan', 'skd-property-listings'); ?></span></h2>
                        <div class="inside">
                            <label for="plan_id"><?php _e('Select Plan', 'skd-property-listings'); ?></label>
                            <select name="plan_id" id="plan_id" required>
                                <option value=""><?php _e('Select a Plan', 'skd-property-listings'); ?></option>
                                <?php foreach ($pricePlanList as $plan) { ?>
                                    <option value="<?php echo esc_attr($plan['id']); ?>" <?php selected($selected_plan_id, $plan['id']); ?>>
                                        <?php echo esc_html($plan['plan_name']); ?>
                                    </option>
                                <?php } ?>
                            </select>

                            <div id="listing_options" style="margin-top: 10px; display: none;">
                                <label><strong><?php _e('Select Listing Type:', 'skd-property-listings'); ?></strong></label>
                                <div>
                                    <input type="radio" name="listing_type" id="regular_listing" value="regular" <?php checked($selected_listing_type, 'regular'); ?> required>
                                    <label for="regular_listing"></label>
                                </div>
                                <div>
                                    <input type="radio" name="listing_type" id="featured_listing" value="featured" <?php checked($selected_listing_type, 'featured'); ?> required>
                                    <label for="featured_listing"></label>
                                </div>
                            </div>

                            <p><label><?php _e('Expiration Date & Time:', 'skd-property-listings'); ?></label></p>
                            <input type="datetime-local" name="expiration_date" id="expiration_date" value="<?php echo $expiration_date ? date('Y-m-d\TH:i', strtotime($expiration_date)) : ''; ?>">
                        </div>
                    </div>

                    <!-- header image -->
                    <div class="postbox">
                        <h2 class="hndle"><span><?php _e('Header Image', 'skd-property-listings'); ?></span></h2>
                        <div class="inside">
                            <button type="button" id="skd-upload-header-image" class="button"><?php _e('Upload Image', 'skd-property-listings'); ?></button>
                            <div id="skd-header-image-preview" style="margin-top: 10px;">
                                <?php if (!empty($listing->skd_header_image)) : ?>
                                    <img src="<?php echo esc_url($listing->skd_header_image); ?>" alt="Header Image" style="max-width: 100%; height: auto; display: block;">
                                    <button type="button" id="skd-remove-header-image" class="button button-link"><?php _e('Remove', 'skd-property-listings'); ?></button>
                                <?php endif; ?>
                            </div>
                            <input type="hidden" id="skd-header-image" name="skd_header_image" value="<?php echo $listing ? esc_url($listing->skd_header_image) : ''; ?>">
                        </div>
                    </div>

                    <!-- Section 3: Categories -->
                    <?php
                    $selected_categories = [];
                    if (!empty($listing->category_ids)) {
                        $selected_categories = json_decode($listing->category_ids, true); // Convert JSON to an array
                    }
                    ?>
                    <div class="postbox">
                        <h2 class="hndle"><span><?php _e('Category Selection', 'skd-property-listings'); ?></span></h2>
                        <div class="inside">
                            <div id="skd-category-list">
                                <?php echo $this->render_category_hierarchy(0, 0, $selected_categories); // Function to render the category tree 
                                ?>
                            </div>

                            <button type="button" id="skd-add-category-btn" class="button"><?php _e('Add New Category', 'skd-property-listings'); ?></button>

                            <div id="skd-add-category-form" style="display: none; margin-top: 10px;">
                                <input type="text" id="skd-category-name" placeholder="<?php _e('Category Name', 'skd-property-listings'); ?>" class="">

                                <select id="skd-parent-category">
                                    <option value="0"><?php _e('Parent Category (Optional)', 'skd-property-listings'); ?></option>
                                    <?php echo $this->get_category_dropdown_options(); // Function to get category dropdown options 
                                    ?>
                                </select>

                                <button type="button" id="skd-save-category" class="button button-primary"><?php _e('Save Category', 'skd-property-listings'); ?></button>
                                <button type="button" id="skd-cancel-category" class="button"><?php _e('Cancel', 'skd-property-listings'); ?></button>
                            </div>
                        </div>
                    </div>

                    <!-- Section 4: Location -->
                    <?php
                    $selected_locations = [];
                    if (!empty($listing->location_ids)) {
                        $selected_locations = json_decode($listing->location_ids, true); // Convert JSON to an array
                    }
                    ?>
                    <div class="postbox">
                        <h2 class="hndle"><span><?php _e('Location Selection', 'skd-property-listings'); ?></span></h2>
                        <div class="inside">
                            <div id="skd-location-list">
                                <?php echo $this->render_location_hierarchy(0, 0, $selected_locations); ?>
                            </div>

                            <button type="button" id="skd-add-location-btn" class="button">
                                <?php _e('Add New Location', 'skd-property-listings'); ?>
                            </button>

                            <div id="skd-add-location-form" style="display: none; margin-top: 10px;">
                                <input type="text" id="skd-location-name" placeholder="<?php _e('Location Name', 'skd-property-listings'); ?>" class="">

                                <select id="skd-parent-location">
                                    <option value="0"><?php _e('Parent Location (Optional)', 'skd-property-listings'); ?></option>
                                    <?php echo $this->get_location_dropdown_options(); ?>
                                </select>

                                <button type="button" id="skd-save-location" class="button button-primary">
                                    <?php _e('Save Location', 'skd-property-listings'); ?>
                                </button>
                                <button type="button" id="skd-cancel-location" class="button">
                                    <?php _e('Cancel', 'skd-property-listings'); ?>
                                </button>
                            </div>
                        </div>
                    </div>

                    <?php
                    $selected_features = [];
                    if (!empty($listing->features)) {
                        $selected_features = json_decode($listing->features, true); // Convert JSON to an array
                    }
                    ?>
                    <div class="postbox">
                        <h2 class="hndle"><span><?php _e('Features', 'skd-property-listings'); ?></span></h2>
                        <div class="inside">
                            <input type="text" id="skd-feature-input" placeholder="<?php _e('Enter features...', 'skd-property-listings'); ?>">
                            <button type="button" id="skd-add-feature" class="button"><?php _e('Add', 'skd-property-listings'); ?></button>
                            <p>Separate features with commas</p>
                            <div id="skd-feature-list">
                                <?php
                                if (!empty($selected_features)) {
                                    foreach ($selected_features as $feature) {
                                        echo '<span class="skd-feature-item">' . esc_html($feature) .
                                            ' <button type="button" class="remove-feature" data-feature="' . esc_attr($feature) . '">&times;</button></span>';
                                    }
                                }
                                ?>
                            </div>
                            <input type="hidden" id="skd-hidden-features" name="skd_features" value="<?php echo esc_attr(implode(',', $selected_features)); ?>">
                        </div>
                    </div>

                    <?php
                    $selected_tags = [];
                    if (!empty($listing->tags)) {
                        $selected_tags = json_decode($listing->tags, true); // Convert JSON to an array
                    }
                    ?>
                    <div class="postbox">
                        <h2 class="hndle"><span><?php _e('Tags', 'skd-property-listings'); ?></span></h2>
                        <div class="inside">
                            <input type="text" id="skd-tag-input" placeholder="<?php _e('Enter tags...', 'skd-property-listings'); ?>">
                            <button type="button" id="skd-add-tag" class="button"><?php _e('Add', 'skd-property-listings'); ?></button>
                            <p>Separate tags with commas</p>
                            <div id="skd-tag-list">
                                <?php
                                if (!empty($selected_tags)) {
                                    foreach ($selected_tags as $tag) {
                                        echo '<span class="skd-tag-item">' . esc_html($tag) .
                                            ' <button type="button" class="remove-tag" data-tag="' . esc_attr($tag) . '">&times;</button></span>';
                                    }
                                }
                                ?>
                            </div>
                            <input type="hidden" id="skd-hidden-tags" name="skd_tags" value="<?php echo esc_attr(implode(',', $selected_tags)); ?>">
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </form>
</div>

<script>
    jQuery(document).ready(function($) {
        //======= social info functionality===============
        $('#add_social_info').on('click', function(e) {
            e.preventDefault();

            let socialRow = `
            <div class="skd-social-row">
                <select name="social_networks[]" class="skd-social-select">
                    <option value="behance">Behance</option>
                    <option value="dribbble">Dribbble</option>
                    <option value="facebook">Facebook</option>
                    <option value="flickr">Flickr</option>
                    <option value="github">Github</option>
                    <option value="instagram">Instagram</option>
                    <option value="linkedin">LinkedIn</option>
                    <option value="pinterest">Pinterest</option>
                    <option value="reddit">Reddit</option>
                    <option value="snapchat">Snapchat</option>
                    <option value="soundcloud">SoundCloud</option>
                    <option value="stack-overflow">StackOverFLow</option>
                    <option value="tumblr">Tumblr</option>
                    <option value="twitter">Twitter</option>
                    <option value="vimeo">Vimeo</option>
                    <option value="vine">Vine</option>
                    <option value="youtube">Youtube</option>
                </select>
                <input type="url" name="social_urls[]" class="skd-social-url" placeholder="Enter URL">
                <button type="button" class="button skd-remove-social">X</button>
            </div>`;

            $('#skd-social-container').append(socialRow);
        });

        // Function to remove a social info row
        $(document).on('click', '.skd-remove-social', function() {
            $(this).closest('.skd-social-row').remove();
        });
        //======= social info functionality===============

        //======= map functionality===============
        let map;
        let marker;

        //when edit listing generate map
        let lat = parseFloat($('#latitude').val());
        let lng = parseFloat($('#longitude').val());
        if ($('#list_address').val()) {
            let geocoder = new google.maps.Geocoder();
            geocoder.geocode({
                'address': $('#list_address').val()
            }, function(results, status) {
                if (status === 'OK') {
                    let location = results[0].geometry.location;
                    initMap(location.lat(), location.lng());
                }
            });
        } else if (!isNaN(lat) && !isNaN(lng)) {
            initMap(lat, lng);
        } else {
            initMap();
        }

        // Initialize Google Maps Autocomplete
        function initAutocomplete() {
            let listAddress = document.getElementById("list_address");
            let autocomplete = new google.maps.places.Autocomplete(listAddress);

            autocomplete.addListener("place_changed", function() {
                let place = autocomplete.getPlace();
                if (!place.geometry) {
                    return;
                }

                let lat = place.geometry.location.lat();
                let lng = place.geometry.location.lng();

                $("#latitude").val(lat);
                $("#longitude").val(lng);

                updateMap(lat, lng);
            });
        }

        // Initialize Google Map
        function initMap(lat = 37.7749, lng = -122.4194) {
            map = new google.maps.Map(document.getElementById("map_canvas"), {
                center: {
                    lat: lat,
                    lng: lng
                },
                zoom: 16,
            });

            marker = new google.maps.Marker({
                position: {
                    lat: lat,
                    lng: lng
                },
                map: map,
                draggable: true,
            });

            // Update latitude and longitude on marker drag
            google.maps.event.addListener(marker, "dragend", function(event) {
                $("#latitude").val(event.latLng.lat());
                $("#longitude").val(event.latLng.lng());
            });
        }

        // Function to update map with new coordinates
        function updateMap(lat, lng) {
            let latLng = new google.maps.LatLng(lat, lng);
            marker.setPosition(latLng);
            map.setCenter(latLng);
        }

        // Toggle manual coordinates input fields
        $("#manual_coordinates").on("change", function() {
            if ($(this).is(":checked")) {
                $(".manual-coordinates-fields").show();
            } else {
                $(".manual-coordinates-fields").hide();
            }
        });

        // Generate map based on entered latitude and longitude
        $("<button/>", {
            text: "Generate on Map",
            id: "skd-generate-map",
            class: "button",
        }).insertAfter("#longitude");

        $("#skd-generate-map").on("click", function(e) {
            e.preventDefault();
            let lat = parseFloat($("#latitude").val());
            let lng = parseFloat($("#longitude").val());

            if (!isNaN(lat) && !isNaN(lng)) {
                updateMap(lat, lng);
                getAddressFromLatLng(lat, lng);
            } else {
                alert("Please enter valid latitude and longitude.");
            }
        });

        // Function to Convert Lat/Lng to Address (Without Plus Codes)
        function getAddressFromLatLng(lat, lng) {
            let geocoder = new google.maps.Geocoder();
            let latlng = {
                lat: lat,
                lng: lng
            };

            geocoder.geocode({
                location: latlng
            }, function(results, status) {
                if (status === "OK") {
                    if (results.length > 0) {
                        let formattedAddress = getFormattedAddress(results);
                        $("#list_address").val(formattedAddress); // Set the cleaned address
                    } else {
                        alert("No address found for these coordinates.");
                    }
                } else {
                    alert("Geocoder failed due to: " + status);
                }
            });
        }

        // Function to Extract a Clean Address (Removes Plus Codes but Keeps a Fallback)
        function getFormattedAddress(results) {
            for (let i = 0; i < results.length; i++) {
                let address = results[i].formatted_address;
                if (!address.match(/^\w+\+\w+/)) { // Exclude addresses starting with a Plus Code (e.g., "M9VH+449")
                    return address; // Return the first clean address found
                }
            }
            return results[0].formatted_address; // Fallback: return first result if all have Plus Codes
        }

        // Initialize Google Maps and Autocomplete
        initMap();
        initAutocomplete();
        //======= map functionality===============

        //======= logo or images functionality===============
        // Open WP Media Uploader for Single Logo Upload
        $(".skd-upload-logo").on("click", function(e) {
            e.preventDefault();

            let targetField = $(this).data("target");
            let previewContainer = $("#skd_logo_preview");

            const mediaUploader = wp.media({
                title: "Select or Upload Logo",
                button: {
                    text: "Use This Logo",
                },
                multiple: false, // Single image only
            });

            mediaUploader.on("select", function() {
                const attachment = mediaUploader.state().get("selection").first().toJSON();
                $(targetField).val(attachment.url); // Store URL in hidden input

                // Display Preview
                previewContainer.html(`<img src="${attachment.url}" style="max-width: 150px; height: auto;">`);
            });

            mediaUploader.open();
        });

        // Open WP Media Uploader for Multiple Gallery Images
        $(".skd-upload-gallery").on("click", function(e) {
            e.preventDefault();

            let galleryField = $("#skd_gallery");
            let galleryPreviewContainer = $("#skd_gallery_preview");

            const mediaUploader = wp.media({
                title: "Select or Upload Gallery Images",
                button: {
                    text: "Use These Images",
                },
                multiple: true, // Allow multiple selections
            });

            mediaUploader.on("select", function() {
                const selection = mediaUploader.state().get("selection");
                let existingImages = galleryField.val() ? galleryField.val().split(",") : []; // Get existing images
                let newImages = [];

                selection.each(function(attachment) {
                    let imageUrl = attachment.toJSON().url;

                    // Add new image only if it's not already present
                    if (!existingImages.includes(imageUrl)) {
                        existingImages.push(imageUrl);
                        newImages.push(imageUrl);

                        // Append new image preview
                        galleryPreviewContainer.append(`
                        <div class="skd-gallery-item" style="position: relative; display: inline-block; margin-right: 10px;">
                            <img src="${imageUrl}" style="max-width: 100px; height: auto; display: block;">
                            <button class="remove-gallery-image" data-url="${imageUrl}" style="position: absolute; top: 0; right: 0; background: red; color: white; border: none; cursor: pointer;">X</button>
                        </div>
                    `);
                    }
                });

                // Update the hidden input field with all images
                galleryField.val(existingImages.join(","));
            });

            mediaUploader.open();
        });

        // Remove Gallery Image Preview
        $("#skd_gallery_preview").on("click", ".remove-gallery-image", function() {
            let imageUrl = $(this).data("url");
            $(this).parent().remove(); // Remove the image preview

            let galleryField = $("#skd_gallery");
            let imagesArray = galleryField.val().split(",");

            // Remove the selected image from the array
            let updatedGallery = imagesArray.filter(img => img !== imageUrl);
            galleryField.val(updatedGallery.join(",")); // Update hidden input
        });
        //======= logo or images functionality===============

        //======= belongs to plan functionality===============
        var skdPlanDetails = <?php echo json_encode($pricePlanList); ?>;
        var skdPlanUsage = <?php echo json_encode($plan_usage); ?>;

        let selectedPlanId = $("#plan_id").val();
        let selectedListingType = $("input[name='listing_type']:checked").val();
        let savedExpirationDate = "<?php echo isset($listing) ? esc_js($listing->expiration_date) : ''; ?>";
        let listingCreatedDate = "<?php echo isset($listing) ? esc_js($listing->created_at) : ''; ?>";
        let userChangedDate = false;

        function calculateExpirationDate(baseDate, plan, isEditMode) {
            if (plan.never_expire == 1) {
                return "9999-12-31T23:59";
            }

            let newExpiration = new Date(baseDate);
            let durationMultiplier = {
                "days": 1,
                "weeks": 7,
                "months": 30,
                "years": 365
            };

            if (plan.listing_duration == 0) {
                newExpiration.setFullYear(newExpiration.getFullYear() + 1);
            } else if (durationMultiplier[plan.duration_unit]) {
                newExpiration.setDate(newExpiration.getDate() + (plan.listing_duration * durationMultiplier[plan.duration_unit]));
            }

            return newExpiration.toISOString().slice(0, 16);
        }

        function setExpirationDate(forceUpdate = false) {
            let baseDate = listingCreatedDate ? new Date(listingCreatedDate) : new Date();
            let selectedPlan = skdPlanDetails.find(p => p.id == $("#plan_id").val());

            if (!selectedPlan) return;

            let newExpiration;

            if (savedExpirationDate) {
                // Editing existing listing
                newExpiration = selectedPlan.never_expire == 1 ? savedExpirationDate : calculateExpirationDate(baseDate, selectedPlan, true);
            } else {
                // Adding new listing
                newExpiration = calculateExpirationDate(baseDate, selectedPlan, false);
            }

            if (!userChangedDate || forceUpdate) {
                $("#expiration_date").val(newExpiration);
            }

            // Set max date based on condition
            $("#expiration_date").attr("max", selectedPlan.never_expire == 1 ? "9999-12-31T23:59" : newExpiration);
        }

        $("#expiration_date").on("input", function() {
            userChangedDate = true;
        });

        $("#plan_id").on("change", function() {
            userChangedDate = false;
            $("#regular_listing, #featured_listing").prop("checked", false);
            let selectedPlanId = $(this).val();

            if (!selectedPlanId) {
                $("#listing_options").hide();
                $("#expiration_date").val("");
                setExpirationDate(true);
                return;
            }

            let plan = skdPlanDetails.find(p => p.id == selectedPlanId);
            let usage = skdPlanUsage[selectedPlanId] || {
                used_regular: 0,
                used_featured: 0
            };
            if (!plan) return;

            setExpirationDate();

            if (plan.plan_type === "pay_per_listing") {
                $("#listing_options").hide();
                $("input[name='listing_type']").prop("required", false);
            } else {
                $("#listing_options").show();
                $("input[name='listing_type']").prop("required", true);
            }

            let remainingRegular = plan.mark_as_unlimited == 1 ? "Unlimited" : ((plan.no_of_listing - usage.used_regular) > 0 ? (plan.no_of_listing - usage.used_regular) + ' Remaining' : '0 Remaining');
            let remainingFeatured = plan.mark_feature_unlimited == 1 ? "Unlimited" : ((plan.no_of_feature_listing - usage.used_featured) > 0 ? (plan.no_of_feature_listing - usage.used_featured) + ' Remaining' : '0 Remaining');

            $("#regular_listing").next("label").text(`Regular Listing (${remainingRegular})`);
            $("#featured_listing").next("label").text(`Featured Listing (${remainingFeatured})`);

            $("#regular_listing").prop("disabled", !(plan.mark_as_unlimited == 1 || plan.no_of_listing > 0));
            $("#featured_listing").prop("disabled", !(plan.mark_feature_unlimited == 1 || plan.no_of_feature_listing > 0));

            // $("form").off("submit").on("submit", function(e) {
            //     if ($("#listing_options").is(":visible") && !$("input[name='listing_type']:checked").length) {
            //         alert("Please select a listing type before submitting.");
            //         e.preventDefault();
            //         return;
            //     }

            //     if (remainingRegular === '0 Remaining' && remainingFeatured === '0 Remaining') {
            //         alert("You have reached the maximum number of listings for this plan.");
            //         e.preventDefault();
            //     }
            // });
        });

        if (selectedPlanId) {
            $("#plan_id").trigger("change");
            setTimeout(function() {
                if (selectedListingType) {
                    $("input[name='listing_type'][value='" + selectedListingType + "']").prop("checked", true);
                }
            }, 500);
        }
        //======= belongs to plan functionality===============

        //======= Category functionality===============
        var skdCategoryData = {
            ajaxurl: '<?php echo admin_url('admin-ajax.php'); ?>',
            nonce: '<?php echo wp_create_nonce('skd_category_nonce'); ?>',
            parentText: '<?php _e("Parent Category (Optional)", "skd-property-listings"); ?>'
        };
        // Show the category form when button is clicked
        $("#skd-add-category-btn").on("click", function() {
            $("#skd-add-category-form").slideDown();
        });

        // Hide the category form when cancel is clicked
        $("#skd-cancel-category").on("click", function() {
            $("#skd-add-category-form").slideUp();
        });

        // Handle category save
        $("#skd-save-category").on("click", function() {
            let categoryName = $("#skd-category-name").val().trim();
            let parentId = $("#skd-parent-category").val();

            if (categoryName === "") {
                alert("Please enter a category name.");
                return;
            }

            // Collect all currently checked category IDs before AJAX request
            let checkedCategories = [];
            $('input[name="skd_category[]"]:checked').each(function() {
                checkedCategories.push($(this).val());
            });

            $.ajax({
                url: skdCategoryData.ajaxurl,
                type: "POST",
                data: {
                    action: "skd_add_new_category",
                    category_name: categoryName,
                    parent_id: parentId,
                    security: skdCategoryData.nonce
                },
                beforeSend: function() {
                    $("#skd-save-category").prop("disabled", true).text("Saving...");
                },
                success: function(response) {
                    if (response.success) {
                        let newCategoryId = response.data.new_category_id; // Newly added category ID

                        // Update the category list
                        $("#skd-category-list").html(response.data.updated_list);

                        // Add new category to checked list
                        checkedCategories.push(newCategoryId);

                        // Re-check all previously selected categories
                        checkedCategories.forEach(function(categoryId) {
                            $('input[name="skd_category[]"][value="' + categoryId + '"]').prop("checked", true);
                        });

                        // Update dropdown with default option
                        $("#skd-parent-category").html('<option value="0">' + skdCategoryData.parentText + '</option>' + response.data.updated_dropdown);

                        $("#skd-category-name").val(""); // Clear input
                        $("#skd-add-category-form").slideUp(); // Hide form
                    } else {
                        alert(response.data.message);
                    }
                },
                complete: function() {
                    $("#skd-save-category").prop("disabled", false).text("Save Category");
                }
            });
        });
        //======= Category functionality===============

        //======= Location functionality===============
        var skdLocationData = {
            ajaxurl: '<?php echo admin_url('admin-ajax.php'); ?>',
            nonce: '<?php echo wp_create_nonce('skd_location_nonce'); ?>',
            parentText: '<?php _e("Parent Location (Optional)", "skd-property-listings"); ?>'
        };

        // Show the location form when button is clicked
        $("#skd-add-location-btn").on("click", function() {
            $("#skd-add-location-form").slideDown();
        });

        // Hide the location form when cancel is clicked
        $("#skd-cancel-location").on("click", function() {
            $("#skd-add-location-form").slideUp();
        });

        // Handle location save
        $("#skd-save-location").on("click", function() {
            let locationName = $("#skd-location-name").val().trim();
            let parentId = $("#skd-parent-location").val();

            if (locationName === "") {
                alert("Please enter a location name.");
                return;
            }

            // Collect all currently checked location IDs before AJAX request
            let checkedLocations = [];
            $('input[name="skd_location[]"]:checked').each(function() {
                checkedLocations.push($(this).val());
            });

            $.ajax({
                url: skdLocationData.ajaxurl,
                type: "POST",
                data: {
                    action: "skd_add_new_location",
                    location_name: locationName,
                    parent_id: parentId,
                    security: skdLocationData.nonce
                },
                beforeSend: function() {
                    $("#skd-save-location").prop("disabled", true).text("Saving...");
                },
                success: function(response) {
                    if (response.success) {
                        let newLocationId = response.data.new_location_id;

                        // Update the location list
                        $("#skd-location-list").html(response.data.updated_list);

                        // Add new location to checked list
                        checkedLocations.push(newLocationId);

                        // Re-check all previously selected locations
                        checkedLocations.forEach(function(locationId) {
                            $('input[name="skd_location[]"][value="' + locationId + '"]').prop("checked", true);
                        });

                        // Update dropdown with default option
                        $("#skd-parent-location").html('<option value="0">' + skdLocationData.parentText + '</option>' + response.data.updated_dropdown);

                        $("#skd-location-name").val(""); // Clear input
                        $("#skd-add-location-form").slideUp(); // Hide form
                    } else {
                        alert(response.data.message);
                    }
                },
                complete: function() {
                    $("#skd-save-location").prop("disabled", false).text("Save Location");
                }
            });
        });
        //======= Location functionality===============

        //======= Tags functionality===============
        var skdTagData = {
            ajaxurl: '<?php echo admin_url('admin-ajax.php'); ?>',
            nonce: '<?php echo wp_create_nonce('skd_tag_nonce'); ?>'
        };
        var selectedTags = []; // Store assigned tags
        let existingTags = $("#skd-hidden-tags").val();
        if (existingTags) {
            selectedTags = existingTags.split(",");
        }

        // Autocomplete for existing tags
        $("#skd-tag-input").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: skdTagData.ajaxurl,
                    type: "POST",
                    dataType: "json",
                    data: {
                        action: "skd_search_tags",
                        term: request.term,
                        security: skdTagData.nonce
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            select: function(event, ui) {
                addTags(ui.item.value); // Add selected tag
                $("#skd-tag-input").val('');
                return false;
            }
        });

        // Handle adding tags on button click
        $("#skd-add-tag").on("click", function() {
            let tagInput = $("#skd-tag-input").val().trim();
            if (tagInput !== "") {
                let tagsArray = tagInput.split(',').map(tag => tag.trim()); // Split by comma
                addTags(tagsArray);
                $("#skd-tag-input").val('');
            }
        });

        // Handle adding tags on Enter key press
        $("#skd-tag-input").on("keypress", function(e) {
            if (e.which == 13) { // Enter key
                let tagInput = $("#skd-tag-input").val().trim();
                if (tagInput !== "") {
                    let tagsArray = tagInput.split(',').map(tag => tag.trim());
                    addTags(tagsArray);
                    $("#skd-tag-input").val('');
                }
                return false;
            }
        });

        function addTags(tags) {
            if (!Array.isArray(tags)) {
                tags = [tags]; // Convert single tag to array
            }

            tags.forEach(tagName => {
                if (tagName !== "" && !selectedTags.includes(tagName)) {
                    selectedTags.push(tagName);

                    $("#skd-tag-list").append(
                        '<span class="skd-tag-item">' + tagName +
                        ' <button type="button" class="remove-tag" data-tag="' + tagName + '">&times;</button></span>'
                    );
                }
            });

            updateHiddenInput(); // Update hidden field with selected tags
        }

        // Remove tag from list
        $(document).on("click", ".remove-tag", function() {
            let tagName = $(this).data("tag");
            selectedTags = selectedTags.filter(tag => tag !== tagName);
            $(this).parent().remove();
            updateHiddenInput(); // Update hidden field after removal
        });

        // Update hidden input field
        function updateHiddenInput() {
            $("#skd-hidden-tags").val(selectedTags.join(","));
        }
        //======= Tags functionality===============

        //======= feature functionality===============
        var skdFeatureData = {
            ajaxurl: '<?php echo admin_url('admin-ajax.php'); ?>',
            nonce: '<?php echo wp_create_nonce('skd_feature_nonce'); ?>'
        };
        var selectedFeatures = []; // Store assigned features
        let existingFeatures = $("#skd-hidden-features").val();
        if (existingFeatures) {
            selectedFeatures = existingFeatures.split(",");
        }

        // Autocomplete for existing features
        $("#skd-feature-input").autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: skdFeatureData.ajaxurl,
                    type: "POST",
                    dataType: "json",
                    data: {
                        action: "skd_search_features",
                        term: request.term,
                        security: skdFeatureData.nonce
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            select: function(event, ui) {
                addFeatures(ui.item.value); // Add selected feature
                $("#skd-feature-input").val('');
                return false;
            }
        });

        // Handle adding features on button click
        $("#skd-add-feature").on("click", function() {
            let featureInput = $("#skd-feature-input").val().trim();
            if (featureInput !== "") {
                let featuresArray = featureInput.split(',').map(feature => feature.trim()); // Split by comma
                addFeatures(featuresArray);
                $("#skd-feature-input").val('');
            }
        });

        // Handle adding features on Enter key press
        $("#skd-feature-input").on("keypress", function(e) {
            if (e.which == 13) { // Enter key
                let featureInput = $("#skd-feature-input").val().trim();
                if (featureInput !== "") {
                    let featuresArray = featureInput.split(',').map(feature => feature.trim());
                    addFeatures(featuresArray);
                    $("#skd-feature-input").val('');
                }
                return false;
            }
        });

        function addFeatures(features) {
            if (!Array.isArray(features)) {
                features = [features]; // Convert single feature to array
            }

            features.forEach(featureName => {
                if (featureName !== "" && !selectedFeatures.includes(featureName)) {
                    selectedFeatures.push(featureName);

                    $("#skd-feature-list").append(
                        '<span class="skd-feature-item">' + featureName +
                        ' <button type="button" class="remove-feature" data-feature="' + featureName + '">&times;</button></span>'
                    );
                }
            });

            updateFeatureHiddenInput(); // Update hidden field with selected features
        }

        // Remove feature from list
        $(document).on("click", ".remove-feature", function() {
            let featureName = $(this).data("feature");
            selectedFeatures = selectedFeatures.filter(feature => feature !== featureName);
            $(this).parent().remove();
            updateFeatureHiddenInput(); // Update hidden field after removal
        });

        // Update hidden input field
        function updateFeatureHiddenInput() {
            $("#skd-hidden-features").val(selectedFeatures.join(","));
        }
        //======= feature functionality===============

        //======= header image functionality===============
        $('#skd-upload-header-image').on('click', function(e) {
            e.preventDefault();

            // Open a new media uploader instance
            const mediaUploader = wp.media({
                title: 'Select Header Image',
                button: {
                    text: 'Use this image'
                },
                multiple: false
            });

            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#skd-header-image').val(attachment.url); // Store in hidden input
                $('#skd-header-image-preview').html(
                    '<img src="' + attachment.url + '" style="max-width: 100%; height: auto; display: block;">' +
                    '<button type="button" id="skd-remove-header-image" class="button button-link">Remove</button>'
                );
            });

            mediaUploader.open();
        });
        // Remove Image
        $(document).on('click', '#skd-remove-header-image', function() {
            $('#skd-header-image').val('');
            $('#skd-header-image-preview').html('');
        });
        //======= header image functionality===============

    });
</script>