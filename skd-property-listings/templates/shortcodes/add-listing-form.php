<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<section class="sec3 skd-pl-add-listing-form">
    <div class="container">
        <form id="skd-pl-add-listing-form" method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="skd_pl_add_listing">
            <input type="hidden" name="skd_pl_add_listing_nonce" value="<?php echo wp_create_nonce('skd_pl_add_listing'); ?>">
            <input type="hidden" name="listing_id" value="<?php echo esc_attr($is_edit_mode ? $listing_data->id : ''); ?>">
            <input type="hidden" name="order_id" value="<?php echo esc_attr($order_id); ?>">

            <div class="listingOuterContainer">
                <div class="fixedtabOuter">
                    <div class="fixedtabInner__right">
                        <a href="#contact_Info" class="fixedtabInner__right__btn activeTab">
                            <span class="dotbox"></span>Contact Information <i class="arrowicon"></i></a>
                        </a>
                        <a href="#address" class="fixedtabInner__right__btn">
                            <span class="dotbox"></span> Address & Map <i class="arrowicon"></i></a>
                        </a>
                        <a href="#image_Video" class="fixedtabInner__right__btn">
                            <span class="dotbox"></span> Images & Video<i class="arrowicon"></i></a>
                        </a>

                        <a href="#finish" class="fixedtabInner__right__btn finishTab">
                            <span class="dotbox"></span>Finish<i class="arrowicon"></i></a>
                        </a>
                    </div>
                </div>
                <div class="sideContainerOuter">
                    <div class="sidebarInnerBox">
                        <div class="sidebarInnerBox__header">
                            <h2 class="sidebarInnerBox__header__title">Add Listing</h2>
                        </div>
                        <div class="sidebarInnerBox__Body">
                            <div class="tabBArCOntentOuter">
                                <div class="tabBArCOntentInputBox">
                                    <label style="font-weight: 600;" class="tabBArCOntent_label">
                                        <?php _e('Business Name', 'skd-property-listings'); ?>:<span class="tabBArCOntent_label_required"> *</span></label>
                                    <input class="tabBArCOntentInput" type="text" name="listing_title" id="listing_title" value="<?php echo esc_attr($is_edit_mode ? $listing_data->listing_title : ''); ?>" required>
                                </div>
                                <div class="tabBArCOntentInputBox">
                                    <label style="font-weight: 600;" class="tabBArCOntent_label" for="listing_description"><?php _e('Description', 'skd-property-listings'); ?>:</label>
                                    <textarea rows="15" cols="50" name="listing_description" id="listing_description"><?php echo esc_attr($is_edit_mode ? $listing_data->listing_description : ''); ?></textarea>
                                </div>
                                <div class="tabBArCOntentInputBox">
                                    <label style="font-weight: 600;" class="tabBArCOntent_label">
                                        <?php _e('Tagline', 'skd-property-listings'); ?>:</label>
                                    <input class="tabBArCOntentInput" type="text" name="tagline" id="tagline" value="<?php echo esc_attr($is_edit_mode ? $listing_data->tagline : ''); ?>">
                                </div>
                                <div class="tabBArCOntentInputBox">
                                    <label style="font-weight: 600;" class="tabBArCOntent_label">
                                        <?php _e('Location', 'skd-property-listings'); ?>:<span
                                            class="tabBArCOntent_label_required"> *</span></label>
                                    <select name="skd_locations[]" id="skd_locations" class="location-select2" multiple required>
                                        <?php echo $this->get_location_hierarchy_options($locations); ?>
                                    </select>
                                </div>
                                <div class="tabBArCOntentInputBox">
                                    <label style="font-weight: 600;" class="tabBArCOntent_label">
                                        <?php _e('Tags', 'skd-property-listings'); ?>:</label>
                                    <select name="skd_tags[]" id="skd_tags" class="tags-select2" multiple>
                                        <?php foreach ($tags as $tag) { ?>
                                            <option value="<?php echo $tag->name; ?>"><?php echo $tag->name; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="tabBArCOntentInputBox">
                                    <label style="font-weight: 600;" class="tabBArCOntent_label">
                                        <?php _e('Category', 'skd-property-listings'); ?></label>:<span
                                        class="tabBArCOntent_label_required"> *</span></label>
                                    <select name="skd_categories[]" id="skd_categories" class="category-select2" multiple required>
                                        <?php echo $this->get_category_hierarchy_options($categories); ?>
                                    </select>
                                </div>
                                <?php
                                if ($planDetails->plan_type != "pay_per_listing") {
                                    $remainingRegular = $planDetails->mark_as_unlimited == 1 ? "Unlimited" : max(0, $planDetails->no_of_listing - $regular_listing_count);
                                    $remainingFeatured = $planDetails->mark_feature_unlimited == 1 ? "Unlimited" : max(0, $planDetails->no_of_feature_listing - $featured_listing_count);

                                    $disableRegular = ($remainingRegular === 0) ? 'disabled' : '';
                                    $disableFeatured = ($remainingFeatured === 0) ? 'disabled' : '';

                                    // Determine pre-selected option
                                    $preSelectedRegular = '';
                                    $preSelectedFeatured = '';

                                    if ($is_edit_mode) {
                                        // In edit mode, check which type was previously selected
                                        $preSelectedRegular = ($listing_data->listing_type == "regular") ? 'checked' : '';
                                        $preSelectedFeatured = ($listing_data->listing_type == "featured") ? 'checked' : '';
                                    } else {
                                        // Default selection for add mode
                                        if ($remainingRegular !== 0 && $remainingFeatured === 0) {
                                            $preSelectedRegular = 'checked';
                                        } elseif ($remainingFeatured !== 0 && $remainingRegular === 0) {
                                            $preSelectedFeatured = 'checked';
                                        } elseif ($remainingRegular !== 0 && $remainingFeatured !== 0) {
                                            $preSelectedRegular = 'checked'; // Default to Regular if both are available
                                        }
                                    }
                                ?>
                                    <div class="tabBArCOntentInputBox">
                                        <label style="font-weight: 600;" class="tabBArCOntent_label">
                                            <?php _e('Select Listing Type:', 'skd-property-listings'); ?></label>:<span
                                            class="tabBArCOntent_label_required"> *</span></label>

                                        <div class="">
                                            <input type="radio" name="listing_type" id="regular_listing" value="regular" required <?php echo $disableRegular . ' ' . $preSelectedRegular; ?>>
                                            <label for="regular_listing">
                                                <?php _e('Regular Listing', 'skd-property-listings'); ?> (<?php echo ($remainingRegular === "Unlimited") ? "Unlimited" : $remainingRegular . ' Remaining'; ?>)
                                            </label>
                                        </div>
                                        <div class="">
                                            <input type="radio" name="listing_type" id="featured_listing" value="featured" required <?php echo $disableFeatured . ' ' . $preSelectedFeatured; ?>>
                                            <label for="featured_listing">
                                                <?php _e('Featured Listing', 'skd-property-listings'); ?> (<?php echo ($remainingFeatured === "Unlimited") ? "Unlimited" : $remainingFeatured . ' Remaining'; ?>)
                                            </label>
                                        </div>

                                    </div>
                                <?php } ?>

                            </div>
                        </div>
                    </div>
                    <div id="contact_Info" class="sidebarInnerBox">
                        <div class="sidebarInnerBox__header">
                            <h2 class="sidebarInnerBox__header__title"><?php _e('Contact Information', 'skd-property-listings'); ?></h2>
                        </div>
                        <div class="sidebarInnerBox__Body">
                            <div class="tabBArCOntentOuter">
                                <div class="tabBArCOntentInputBox">
                                    <label style="font-weight: 600;" class="tabBArCOntent_label">
                                        <?php _e('Contact Details', 'skd-property-listings'); ?>:</label>
                                    <input class="tabBArCOntentInput" type="text" name="contact_details" id="contact_details" value="<?php echo esc_attr($is_edit_mode ? $listing_data->contact_details : ''); ?>">
                                </div>
                                <div class="tabBArCOntentInputBox">
                                    <label style="font-weight: 600;" class="tabBArCOntent_label">
                                        <?php _e('Phone', 'skd-property-listings'); ?>:<span class="tabBArCOntent_label_required"> *</span></label>
                                    <input class="tabBArCOntentInput" type="tel" name="contact_phone" id="contact_phone" required value="<?php echo esc_attr($is_edit_mode ? $listing_data->contact_phone : ''); ?>">
                                </div>
                                <div class="tabBArCOntentInputBox">
                                    <label style="font-weight: 600;" class="tabBArCOntent_label">
                                        <?php _e('Phone 2', 'skd-property-listings'); ?>:</label>
                                    <input class="tabBArCOntentInput" type="tel" name="contact_phone2" id="contact_phone2" value="<?php echo esc_attr($is_edit_mode ? $listing_data->contact_phone2 : ''); ?>">
                                </div>
                                <div class="tabBArCOntentInputBox">
                                    <label style="font-weight: 600;" class="tabBArCOntent_label">
                                        <?php _e('Email', 'skd-property-listings'); ?>:<span class="tabBArCOntent_label_required"> *</span></label>
                                    <input class="tabBArCOntentInput" type="email" name="contact_email" id="contact_email" required value="<?php echo esc_attr($is_edit_mode ? $listing_data->contact_email : ''); ?>">
                                </div>
                                <div class="tabBArCOntentInputBox">
                                    <label style="font-weight: 600;" class="tabBArCOntent_label">
                                        <?php _e('Zip/Post Code', 'skd-property-listings'); ?>:<span class="tabBArCOntent_label_required"> *</span></label>
                                    <input class="tabBArCOntentInput" type="text" name="contact_zip" id="contact_zip" class="regular-text" required value="<?php echo esc_attr($is_edit_mode ? $listing_data->contact_zip : ''); ?>">
                                </div>
                                <div class="tabBArCOntentInputBox">
                                    <label style="font-weight: 600;" class="tabBArCOntent_label">
                                        <?php _e('Website', 'skd-property-listings'); ?>:</label>
                                    <input class="tabBArCOntentInput" type="text" name="contact_website" id="contact_website" class="regular-text" value="<?php echo esc_attr($is_edit_mode ? $listing_data->contact_website : ''); ?>">
                                </div>

                                <!-- Social Info -->
                                <h3><?php _e('Social Info', 'skd-property-listings'); ?></h3>
                                <div id="skd-social-container">
                                    <!-- Existing social info rows will be added here -->
                                    <?php if ($is_edit_mode && !empty($listing_data->social_info)) {
                                        $socialInfoArr = json_decode($listing_data->social_info, true);

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
                                <button type="button" id="add_social_info" class="button lat_long_Btn"><?php _e('Add Social', 'skd-property-listings'); ?></button>
                            </div>
                        </div>
                    </div>
                    <div id="address" class="sidebarInnerBox">
                        <div class="sidebarInnerBox__header">
                            <h2 class="sidebarInnerBox__header__title"><?php _e('Address & Map', 'skd-property-listings'); ?></h2>
                        </div>
                        <div class="sidebarInnerBox__Body">
                            <div class="tabBArCOntentOuter">
                                <div class="tabBArCOntentInputBox">
                                    <label style="font-weight: 600;" class="tabBArCOntent_label">
                                        <?php _e('Address', 'skd-property-listings'); ?>:</label>
                                    <input class="tabBArCOntentInput" type="text" name="list_address" id="list_address" autocomplete="off" value="<?php echo esc_attr($is_edit_mode ? $listing_data->list_address : ''); ?>" placeholder="Listing address eg. Perth, Australia">
                                </div>
                                <div class="tabBArCOntentInputBox">
                                    <label style="font-weight: 600;" class="tabBArCOntent_label">
                                        <?php _e('Online-only business?', 'skd-property-listings'); ?>:</label>
                                    <div class="">
                                        <input type="checkbox" name="is_online_only" id="is_online_only" <?php echo esc_attr($is_edit_mode && $listing_data->is_online_only == 1 ? 'checked' : ''); ?>>
                                        <label for="is_online_only">
                                            ONLINE
                                        </label>
                                    </div>
                                </div>
                                <div class="addressbox">
                                    <div id="map_canvas" style="height: 450px;width:100%"></div>
                                </div>
                                <div class="tabBArCOntentInputBox">
                                    <div class="">
                                        <input type="checkbox" id="manual_coordinates" name="manual_coordinates" <?php echo esc_attr($is_edit_mode && $listing_data->manual_coordinates == 1 ? 'checked' : ''); ?>>
                                        <label for="manual_coordinates" class="tabBArCOntent_label"><?php _e('Enter Coordinates Manually', 'skd-property-listings'); ?></label>
                                    </div>
                                    <div class="manual-coordinates-fields_outer">
                                        <div class="manual-coordinates-fields" style="display: <?php echo $is_edit_mode && $listing_data->manual_coordinates == 1 ? '' : 'none' ?>;">
                                            <label for="latitude" class="tabBArCOntent_label"><?php _e('Latitude', 'skd-property-listings'); ?></label>
                                            <input type="text" name="latitude" id="latitude" value="<?php echo esc_attr($is_edit_mode ? $listing_data->latitude : ''); ?>">
                                        </div>
                                        <div class="manual-coordinates-fields" style="display: <?php echo $is_edit_mode && $listing_data->manual_coordinates == 1 ? '' : 'none' ?>;">
                                            <label for="longitude" class="tabBArCOntent_label"><?php _e('Longitude', 'skd-property-listings'); ?></label>
                                            <input type="text" name="longitude" id="longitude" value="<?php echo esc_attr($is_edit_mode ? $listing_data->longitude : ''); ?>">
                                        </div>
                                        <div class="manual-coordinates-fields" style="display: <?php echo $is_edit_mode && $listing_data->manual_coordinates == 1 ? '' : 'none' ?>;">
                                            <button type="button" id="skd-generate-map" class="button lat_long_Btn"><?php _e('Generate on Map', 'skd-property-listings'); ?></button>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div id="image_Video" class="sidebarInnerBox">
                            <div class="sidebarInnerBox__header">
                                <h2 class="sidebarInnerBox__header__title"><?php _e('Images & Video', 'skd-property-listings'); ?></h2>
                            </div>
                            <div class="sidebarInnerBox__Body">
                                <div class="tabBArCOntentOuter">
                                    <p style="font-weight: 600;"><?php _e('Upload Logo/Preview Image', 'skd-property-listings'); ?>:</p>
                                    <input type="hidden" name="skd_logo" id="skd_logo" value="<?php echo $is_edit_mode ? esc_url($listing_data->skd_logo) : ''; ?>">
                                    <div id="skd_logo_drop_area" class="skd-drop-area fileuploadBox">
                                        <p>Drag & Drop Image Here <span class="orBox">or</span>
                                            <label for="skd_logo_file" class="skd-file-label BrowsebtnStyle">Browse</label>
                                        </p>
                                        <input type="file" id="skd_logo_file" accept="image/*" style="display: none;">
                                    </div>
                                    <div id="skd_logo_preview" class="showUplodedFiles" style="margin: 10px 0; display: flex; flex-wrap: wrap; gap: 10px;"></div>

                                    <p style="font-weight: 600;"><?php _e('Gallery Images', 'skd-property-listings'); ?>:</p>
                                    <input type="hidden" name="skd_gallery" id="skd_gallery" value="<?php echo $is_edit_mode ? esc_attr(json_encode(json_decode($listing_data->skd_gallery, true))) : '[]'; ?>">
                                    <div id="skd_gallery_drop_area" class="skd-drop-area fileuploadBox">
                                        <div>
                                            <p>Drag & Drop Images Here <span class="orBox">or</span> <label for="skd_gallery_files" class="skd-file-label BrowsebtnStyle">Browse</label></p>
                                            <input type="file" id="skd_gallery_files" accept="image/*" multiple style="display: none;">

                                            <?php
                                            if ($max_file_count) {
                                                echo '<p>' . sprintf(__('Maximum %d files are allowed.', 'skd-property-listings'), $max_file_count) . '</p>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div id="skd_gallery_preview" class="showUplodedFiles" style="margin: 10px 0; display: flex; flex-wrap: wrap; gap: 10px;">
                                        <!-- Gallery images preview -->
                                    </div>

                                    <div class="tabBArCOntentInputBox">
                                        <label class="tabBArCOntent_label" style="font-weight: 700;">
                                            <?php _e('Video URL', 'skd-property-listings'); ?>:</label>
                                        <input class="tabBArCOntentInput" type="text"
                                            placeholder="Only YouTube & Vimeo URLs." name="video" id="video" value="<?php echo esc_attr($is_edit_mode ? $listing_data->video : ''); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="finish" class="sidebarInnerBoxlast">
                            <div class="sidebarInnerBoxlast__Body">
                                <div class="sidebarInnerBox__footer">
                                    <span class="paperBoatIcon">
                                        <iconify-icon icon="fa6-solid:paper-plane"></iconify-icon>
                                    </span>
                                    <h2 class="publishTitle">You are about to publish</h2>
                                    <p>Are you sure you want to publish this listing?</p>
                                </div>
                                <div class="tabBArCOntentInputBox">
                                    <div class="">
                                        <input type="checkbox" name="privacy_policy" id="privacy_policy" required <?php echo esc_attr($is_edit_mode && $listing_data->privacy_policy == 1 ? 'checked' : ''); ?>>
                                        <label for="privacy_policy"><?php _e('I agree to the Privacy Policy and Terms of Service', 'skd-property-listings'); ?></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="sidebarInnerBox_bottom">
                            <button type="submit" class="sidebarInnerBoxSubmitBtn">Publish</button>
                        </div>
                    </div>
                </div>
        </form>
    </div>
</section>

<script>
    jQuery(document).ready(function($) {
        $(".location-select2").select2({
            placeholder: "Select location",
            allowClear: true
        });
        $(".tags-select2").select2({
            placeholder: "Select tag",
            allowClear: true
        });
        $(".category-select2").select2({
            placeholder: "Select category",
            allowClear: true
        });
        $(".location-select2").val(<?php echo json_encode($is_edit_mode ? json_decode($listing_data->location_ids) : []); ?>).trigger("change");
        $(".tags-select2").val(<?php echo json_encode($is_edit_mode ? json_decode($listing_data->tags) : []); ?>).trigger("change");
        $(".category-select2").val(<?php echo json_encode($is_edit_mode ? json_decode($listing_data->category_ids) : []); ?>).trigger("change");

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

        //======= image functionality===============
        let logoDropArea = $("#skd_logo_drop_area");
        let logoFileInput = $("#skd_logo_file");
        let logoPreviewArea = $("#skd_logo_preview");
        let logoHiddenInput = $("#skd_logo");

        let galleryDropArea = $("#skd_gallery_drop_area");
        let galleryFileInput = $("#skd_gallery_files");
        let galleryPreviewArea = $("#skd_gallery_preview");
        let galleryHiddenInput = $("#skd_gallery");

        let galleryFiles = []; // Store gallery file objects
        let galleryValue = galleryHiddenInput.val().trim();
        let existingGalleryImages = galleryValue ? JSON.parse(galleryValue) : [];
        if (!Array.isArray(existingGalleryImages)) {
            existingGalleryImages = [];
        }

        /** ✅ Load Existing Images on Edit */
        if (logoHiddenInput.val()) {
            logoPreviewArea.html('<img width=60 src="' + logoHiddenInput.val() + '" class="gallery-thumbnail">');
        }

        existingGalleryImages.forEach(function(imageUrl) {
            addGalleryPreview(imageUrl, false);
        });
        let existingImageCount = existingGalleryImages.length;

        /** ✅ Handle Drag & Drop for Logo */
        logoDropArea.on("dragover", function(e) {
            e.preventDefault();
            logoDropArea.addClass("drag-over");
        });

        logoDropArea.on("dragleave", function(e) {
            e.preventDefault();
            logoDropArea.removeClass("drag-over");
        });

        logoDropArea.on("drop", function(e) {
            e.preventDefault();
            logoDropArea.removeClass("drag-over");

            let files = e.originalEvent.dataTransfer.files;
            if (files.length > 1) {
                alert("Only one logo image is allowed.");
                return;
            }
            handleLogoUpload(files[0]);
        });

        logoFileInput.on("change", function() {
            handleLogoUpload(this.files[0]);
        });

        function handleLogoUpload(file) {
            let validTypes = ["image/jpeg", "image/png", "image/gif", "image/webp"];
            let maxSize = 2 * 1024 * 1024; // 2MB

            if (!validTypes.includes(file.type)) {
                alert("Invalid file type. Only JPG, PNG, WEBP and GIF are allowed.");
                return;
            }

            // if (file.size > maxSize) {
            //     alert("File size exceeds 2MB limit.");
            //     return;
            // }

            let reader = new FileReader();
            reader.onload = function(e) {
                logoPreviewArea.html('<img width=80 src="' + e.target.result + '" class="gallery-thumbnail">');
                logoHiddenInput.val(''); // Store filename (to be uploaded on form submit)
            };
            reader.readAsDataURL(file);
        }

        /** ✅ Handle Drag & Drop for Gallery */
        let maxFiles = <?php echo $max_file_count ?>;
        galleryDropArea.on("dragover", function(e) {
            e.preventDefault();
            galleryDropArea.addClass("drag-over");
        });

        galleryDropArea.on("dragleave", function(e) {
            e.preventDefault();
            galleryDropArea.removeClass("drag-over");
        });

        galleryDropArea.on("drop", function(e) {
            e.preventDefault();
            galleryDropArea.removeClass("drag-over");

            let files = e.originalEvent.dataTransfer.files;
            handleGalleryUpload(files);
        });

        galleryFileInput.on("change", function() {
            handleGalleryUpload(this.files);
        });

        function handleGalleryUpload(files) {
            let totalImages = existingGalleryImages.length + galleryFiles.length + files.length;

            if (maxFiles && totalImages > maxFiles) {
                alert("Maximum " + maxFiles + " files are allowed.");
                return;
            }

            $.each(files, function(index, file) {
                let validTypes = ["image/jpeg", "image/png", "image/gif", "image/webp"];
                let maxSize = 2 * 1024 * 1024; // 2MB

                if (!validTypes.includes(file.type)) {
                    alert("Invalid file type. Only JPG, PNG, WEBP and GIF are allowed.");
                    return;
                }

                // if (file.size > maxSize) {
                //     alert("File size exceeds 2MB limit.");
                //     return;
                // }

                galleryFiles.push(file);
                previewGalleryFile(file);
            });

            // galleryHiddenInput.val(galleryFiles.map(f => f.name).join(",")); // Store filenames
        }

        function previewGalleryFile(file) {
            let reader = new FileReader();
            reader.onload = function(e) {
                let img = $('<img width=60 src="' + e.target.result + '" class="gallery-thumbnail">');
                let removeBtn = $('<button class="remove-gallery-image"><iconify-icon icon="solar:trash-bin-minimalistic-bold"></iconify-icon></button>');
                let container = $('<div class="gallery-image-wrapper"></div>');

                removeBtn.on("click", function() {
                    let index = galleryFiles.indexOf(file);
                    if (index !== -1) {
                        galleryFiles.splice(index, 1);
                    }
                    container.remove();
                    // galleryHiddenInput.val(galleryFiles.map(f => f.name).join(",")); // Update filenames
                });

                container.append(img).append(removeBtn);
                galleryPreviewArea.append(container);
            };
            reader.readAsDataURL(file);
        }

        function addGalleryPreview(imageSrc, isNewUpload) {
            let container = $('<div class="gallery-image-wrapper"></div>');
            let img = $('<img width=60 src="' + imageSrc + '" class="gallery-thumbnail">');
            let removeBtn = $('<button class="remove-gallery-image">X</button>');

            removeBtn.on("click", function() {
                let index = galleryFiles.findIndex(file => file.name === imageSrc);
                if (index !== -1) {
                    galleryFiles.splice(index, 1);
                } else if (!isNewUpload) {
                    existingGalleryImages = existingGalleryImages.filter(url => url !== imageSrc);
                }
                container.remove();
                updateGalleryHiddenInput();
            });

            container.append(img).append(removeBtn);
            galleryPreviewArea.append(container);
        }

        /** Update Hidden Input */
        function updateGalleryHiddenInput() {
            // let allImages = [...existingGalleryImages, ...galleryFiles.map(f => f.name)];
            let allImages = [...existingGalleryImages];
            galleryHiddenInput.val(JSON.stringify(allImages));
        }
        //======= image functionality===============

        //======= form submission===============
        $("#skd-pl-add-listing-form").on("submit", function(e) {
            e.preventDefault();

            let ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
            let formData = new FormData(this);

            // Disable submit button and show spinner
            const $submitBtn = $(this).find("button[type='submit']");
            $submitBtn.prop("disabled", true);
            const originalBtnHtml = $submitBtn.html();
            $submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...');

            // Append Logo Image (if selected)
            let logoFile = $("#skd_logo_file")[0].files[0];
            if (logoFile) {
                formData.append("skd_logo_file", logoFile);
            } else if ($("#skd_logo").val() === "") {
                //sweet alert
                Swal.fire({
                    title: 'Error!',
                    text: "<?php _e('Please upload a logo image.', 'skd-property-listings'); ?>",
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#92d509',
                });
                $submitBtn.prop("disabled", false).html(originalBtnHtml); // Restore button
                return;
            }

            // Append gallery files to form data
            $.each(galleryFiles, function(index, file) {
                formData.append("skd_gallery_files[]", file);
            });

            $.ajax({
                url: ajaxurl,
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.success) {
                        // alert("Listing added successfully.");
                        //sweet alert
                        Swal.fire({
                            title: 'Success!',
                            text: response.data.message,
                            icon: 'success',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#92d509',
                        });
                        window.location.href = response.data.redirect_url;
                    } else {
                        //sweet alert
                        Swal.fire({
                            title: 'Error!',
                            text: response.data.message,
                            icon: 'error',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#92d509',
                        });
                        $submitBtn.prop("disabled", false).html(originalBtnHtml); // Restore button
                    }
                },
                error: function(xhr, status, error) {
                    // Handle error response
                    console.error("AJAX Error:", error);
                    //sweet alert
                    // Swal.fire({
                    //     title: 'Error!',
                    //     text: "<?php _e('An error occurred while submitting the form.', 'skd-property-listings'); ?>",
                    //     icon: 'error',
                    //     confirmButtonText: 'OK',
                    //     confirmButtonColor: '#92d509',
                    // });
                    $submitBtn.prop("disabled", false).html(originalBtnHtml); // Restore button
                }
            });
        });
        //======= form submission===============

        $(document).ready(function() {
            const tabButtons = $('.fixedtabInner__right__btn');
            const sections = $('.sidebarInnerBox');

            // Smooth scrolling
            tabButtons.on('click', function(e) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: $($.attr(this, 'href')).offset().top - 10
                }, 500);
            });

            // Add activeTab class on scroll
            $(window).on('scroll', function() {
                let current = '';
                sections.each(function() {
                    const sectionTop = $(this).offset().top;
                    if ($(window).scrollTop() >= sectionTop - 60) {
                        current = $(this).attr('id');
                    }
                });

                tabButtons.removeClass('activeTab');
                tabButtons.each(function() {
                    if ($(this).attr('href') === `#${current}`) {
                        $(this).addClass('activeTab');
                    }
                });
            });
        });
        $(document).ready(function() {
            function isMobileOrTablet() {
                return $(window).width() <= 768; // Adjust 1024 as needed for your tablet breakpoint
            }

            function setupAccordion() {
                if (isMobileOrTablet()) {
                    // Accordion functionality for mobile and tablet
                    $('.sidebarInnerBox__header').click(function() {
                        var body = $(this).next('.sidebarInnerBox__Body');
                        var allBodies = $('.sidebarInnerBox__Body');

                        if (body.is(':visible')) {
                            body.slideUp();
                        } else {
                            allBodies.slideUp();
                            body.slideDown();
                        }
                    });

                    // Initial state: Close all bodies except the first one (optional)
                    $('.sidebarInnerBox__Body').not(':first').hide();
                    //if you want to open the first one by default, uncomment the below line.
                    //$('.sidebarInnerBox__Body:first').show();

                } else {
                    // Remove click events and show all bodies on desktop.
                    $('.sidebarInnerBox__header').off('click');
                    $('.sidebarInnerBox__Body').show();
                }
            }

            // Initial setup
            setupAccordion();

            // Re-run setup on window resize
            $(window).resize(setupAccordion);
        });
    });
</script>