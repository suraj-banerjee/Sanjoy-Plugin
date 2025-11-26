<section class="inner-banner-section" style="background-image: url(<?php echo $listing->skd_header_image ? esc_url($listing->skd_header_image) : esc_url($listing->skd_logo); ?>);">
</section>
<!-- inner banner section -->
<section class="inner_Banner_Btm_Content_Sec">
    <div class="containerSm">
        <div class="inner_Banner_Btm_ContentBox">
            <h2 class="inner_Banner_Btm_Title"><?php echo esc_html($listing->listing_title); ?></h2>

            <?php if ($listing->is_feature) { ?>
                <span class="catagoryText bg_warning">Featured</span>
            <?php } ?>
            <!-- <span class="catagoryText bg_danger">Popular</span> -->
        </div>
        <div class="btmtagline">
            <?php if ($listing->tagline) { ?>
                <p class="inner_Banner_Btm_Tagline"><?php echo esc_html($listing->tagline); ?></p>
            <?php } ?>
        </div>
        <?php
        $category_ids = json_decode($listing->category_ids);
        $categories = [];

        if (!empty($category_ids)) {
            foreach ($category_ids as $category_id) {
                $category = $wpdb->get_row(
                    $wpdb->prepare("SELECT name, slug FROM {$wpdb->prefix}skd_pl_categories WHERE id = %d", $category_id)
                );

                if ($category) {
                    $categories[] = [
                        'name' => $category->name,
                        'slug' => $category->slug
                    ];
                }
            }
        }
        ?>
        <div class="btmBtnBox">
            <?php foreach ($categories as $extra_category) { ?>
                <li>
                    <a href="<?php echo esc_url(site_url('/single-category/' . $extra_category['slug'])); ?>" class="btn">
                        <?php echo esc_html($extra_category['name']); ?>
                    </a>
                </li>
            <?php } ?>
        </div>
    </div>
</section>

<!-- details Sec  -->

<section class="details-page">
    <div class="containerSm">
        <div class="DetailsContOuterBoxGrid">
            <div class="DetailsContOuterBox Details_Card">
                <div class="DetailsContOuterHeader">
                    <span class="DetailsContOuterHeadericon">
                        <iconify-icon icon="material-symbols-light:description-outline-rounded"></iconify-icon>
                    </span>
                    <h4>Description</h4>
                </div>
                <div class="DetailsContOuterBody">
                    <div class="DetailsCont_single_info DetailsCont_listing_details__text">
                        <p>
                            <?php echo esc_html($listing->listing_description); ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="DetailsContOuterBox Details_Card">
                <div class="DetailsContOuterHeader">
                    <span class="DetailsContOuterHeadericon">
                        <iconify-icon icon="solar:gallery-outline"></iconify-icon>
                    </span>
                    <h4>Gallery</h4>
                </div>
                <div class="DetailsContOuterBody">
                    <!-- Gallery Slider -->
                    <!-- Container for the image gallery -->
                    <div class="skd-gallery-container">

                        <!-- Full-width images with number text -->
                        <div class="mySkdSlides">
                            <img src="<?php echo esc_url($listing->skd_logo); ?>" alt="Logo/preview Image" style="width:100%">
                        </div>
                        <?php
                        $gallery_images = json_decode($listing->skd_gallery);
                        if (!empty($gallery_images)) {
                            foreach ($gallery_images as $key => $image) { ?>
                                <div class="mySkdSlides">
                                    <img src="<?php echo esc_url($image); ?>" alt="Gallery Image <?php echo $key + 1; ?>" style="width:100%">
                                </div>
                        <?php }
                        }
                        ?>

                        <!-- Next and previous buttons -->
                        <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
                        <a class="next" onclick="plusSlides(1)">&#10095;</a>

                        <!-- Thumbnail images -->
                        <?php $imgCnt = 1; ?>
                        <div class="ThumbnailOuter">
                            <div class="row">
                                <div class="column">
                                    <img class="demo cursor" src="<?php echo esc_url($listing->skd_logo); ?>" style="width:100%" onclick="skdCurrentSlide(<?php echo $imgCnt; ?>)" alt="Logo/preview Image">
                                </div>

                                <?php
                                if (!empty($gallery_images)) {
                                    foreach ($gallery_images as $key => $image) {
                                        $imgCnt++; ?>
                                        <div class="column">
                                            <img class="demo cursor" src="<?php echo esc_url($image); ?>" style="width:100%" onclick="skdCurrentSlide(<?php echo $imgCnt; ?>)" alt="Gallery Image <?php echo $key + 1; ?>">
                                        </div>
                                <?php }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="DetailsContOuterBox Details_Card">
                <div class="DetailsContOuterHeader">
                    <span class="DetailsContOuterHeadericon">
                        <iconify-icon icon="carbon:location"></iconify-icon>
                    </span>
                    <h4>Location</h4>
                </div>
                <div class="DetailsContOuterBody">
                    <div class="locationBox" id="map_canvas" style="height: 300px;">

                    </div>
                    <div class="loationFlexBox">
                        <div class="locationFlexBoxItem">
                            <span class="locIcon">
                                <iconify-icon icon="carbon:location"></iconify-icon>
                            </span>
                            <p>
                                <?php echo esc_html($listing->list_address); ?>
                            </p>
                        </div>
                        <a href="javascript:void(0);" class="locBtn" target="_blank" onclick="window.open('https://www.google.com/maps?daddr=<?php echo esc_attr($listing->latitude); ?>,<?php echo esc_attr($listing->longitude); ?>', '_blank'); return false;">
                            <iconify-icon icon="fa6-solid:paper-plane"></iconify-icon>
                            Get Directions
                        </a>
                    </div>
                </div>
            </div>
            <div class="DetailsContOuterBox Details_Card">
                <div class="DetailsContOuterHeader">
                    <span class="DetailsContOuterHeadericon">
                        <iconify-icon icon="proicons:mail"></iconify-icon>
                    </span>
                    <h4>Contact Information</h4>
                </div>
                <div class="DetailsContOuterBody">
                    <ul class="contactDetailsUl">
                        <?php if (!empty($listing->contact_phone)) : ?>
                            <li class="contactDetailsUl_single_info">
                                <div class="contactDetailsUl_single_info__label">
                                    <span class="contactDetailsUl_single_info__label-icon">
                                        <iconify-icon icon="proicons:call"></iconify-icon>
                                    </span>
                                    <span class="contactDetailsUl_single_info__label__text">Phone</span>
                                </div>
                                <div class="contactDetailsUl_single_info__value">
                                    <a href="tel:<?php echo esc_html($listing->contact_phone); ?>"><?php echo esc_html($listing->contact_phone); ?></a>
                                </div>
                            </li>
                        <?php endif; ?>
                        <?php if (!empty($listing->contact_email)) : ?>
                            <li class="contactDetailsUl_single_info">
                                <div class="contactDetailsUl_single_info__label">
                                    <span class="contactDetailsUl_single_info__label-icon">
                                        <iconify-icon icon="proicons:mail"></iconify-icon>
                                    </span>
                                    <span class="contactDetailsUl_single_info__label__text">Email</span>
                                </div>
                                <div class="contactDetailsUl_single_info__value">
                                    <a target="_top"
                                        href="mailto:<?php echo esc_html($listing->contact_email); ?>"><?php echo esc_html($listing->contact_email); ?></a>
                                </div>
                            </li>
                        <?php endif; ?>
                        <?php if (!empty($listing->list_address)) : ?>
                            <li class="contactDetailsUl_single_info">
                                <div class="contactDetailsUl_single_info__label">
                                    <span class="contactDetailsUl_single_info__label-icon">
                                        <iconify-icon icon="uiw:map"></iconify-icon>
                                    </span>
                                    <span class="contactDetailsUl_single_info__label__text">Address</span>
                                </div>
                                <div class="contactDetailsUl_single_info__value">
                                    <p><?php echo esc_html($listing->list_address); ?></p>
                                </div>
                            </li>
                        <?php endif; ?>
                        <?php if (!empty($listing->contact_website)) : ?>
                            <li class="contactDetailsUl_single_info">
                                <div class="contactDetailsUl_single_info__label">
                                    <span class="contactDetailsUl_single_info__label-icon">
                                        <iconify-icon icon="vaadin:globe"></iconify-icon>
                                    </span>
                                    <span class="contactDetailsUl_single_info__label__text">Website</span>
                                </div>
                                <div class="contactDetailsUl_single_info__value">
                                    <a target="_blank" href="<?php echo esc_html($listing->contact_website); ?>">
                                        <?php echo esc_html($listing->contact_website); ?>
                                    </a>
                                </div>
                            </li>
                        <?php endif; ?>

                        <?php
                        $social_links = json_decode($listing->social_info, true);
                        $social_links = array_filter($social_links); // Remove empty values
                        ?>
                        <li class="contactDetailsUl_single_info">
                            <div class="contactDetailsUl_single_info__label">
                                <span class="contactDetailsUl_single_info__label-icon">
                                    <iconify-icon icon="hugeicons:share-02"></iconify-icon>
                                </span>
                                <span class="contactDetailsUl_single_info__label__text">Social Info</span>
                            </div>
                            <div class="contactDetailsUl-social-links">
                                <!-- <iconify-icon icon="mage:facebook"></iconify-icon> -->
                                <?php foreach ($social_links as $linkKey => $socialLink) { ?>
                                    <a href="<?php echo esc_url($socialLink['url']); ?>" target="_blank" class="socialSvgIcon">
                                        <?php if ($socialLink['network'] == 'behance') { ?>
                                            <img src="<?php echo esc_url(SKD_PL_PLUGIN_URL . 'assets/images/behance.svg'); ?>" alt="">
                                        <?php } elseif ($socialLink['network'] == 'dribbble') { ?>
                                            <img src="<?php echo esc_url(SKD_PL_PLUGIN_URL . 'assets/images/dribbble.svg'); ?>" alt="">
                                        <?php } elseif ($socialLink['network'] == 'facebook') { ?>
                                            <img src="<?php echo esc_url(SKD_PL_PLUGIN_URL . 'assets/images/facebook.svg'); ?>" alt="">
                                        <?php } elseif ($socialLink['network'] == 'flickr') { ?>
                                            <img src="<?php echo esc_url(SKD_PL_PLUGIN_URL . 'assets/images/flickr.svg'); ?>" alt="">
                                        <?php } elseif ($socialLink['network'] == 'github') { ?>
                                            <img src="<?php echo esc_url(SKD_PL_PLUGIN_URL . 'assets/images/github.svg'); ?>" alt="">
                                        <?php } elseif ($socialLink['network'] == 'instagram') { ?>
                                            <img src="<?php echo esc_url(SKD_PL_PLUGIN_URL . 'assets/images/instagram.svg'); ?>" alt="">
                                        <?php } elseif ($socialLink['network'] == 'linkedin') { ?>
                                            <img src="<?php echo esc_url(SKD_PL_PLUGIN_URL . 'assets/images/linkedin.svg'); ?>" alt="">
                                        <?php } elseif ($socialLink['network'] == 'pinterest') { ?>
                                            <img src="<?php echo esc_url(SKD_PL_PLUGIN_URL . 'assets/images/pinterest.svg'); ?>" alt="">
                                        <?php } elseif ($socialLink['network'] == 'reddit') { ?>
                                            <img src="<?php echo esc_url(SKD_PL_PLUGIN_URL . 'assets/images/reddit.svg'); ?>" alt="">
                                        <?php } elseif ($socialLink['network'] == 'snapchat') { ?>
                                            <img src="<?php echo esc_url(SKD_PL_PLUGIN_URL . 'assets/images/snapchat.svg'); ?>" alt="">
                                        <?php } elseif ($socialLink['network'] == 'soundcloud') { ?>
                                            <img src="<?php echo esc_url(SKD_PL_PLUGIN_URL . 'assets/images/soundcloud.svg'); ?>" alt="">
                                        <?php } elseif ($socialLink['network'] == 'stack-overflow') { ?>
                                            <img src="<?php echo esc_url(SKD_PL_PLUGIN_URL . 'assets/images/stack-overflow.svg'); ?>" alt="">
                                        <?php } elseif ($socialLink['network'] == 'tumblr') { ?>
                                            <img src="<?php echo esc_url(SKD_PL_PLUGIN_URL . 'assets/images/tumblr.svg'); ?>" alt="">
                                        <?php } elseif ($socialLink['network'] == 'twitter') { ?>
                                            <img src="<?php echo esc_url(SKD_PL_PLUGIN_URL . 'assets/images/twitter.svg'); ?>" alt="">
                                        <?php } elseif ($socialLink['network'] == 'vimeo') { ?>
                                            <img src="<?php echo esc_url(SKD_PL_PLUGIN_URL . 'assets/images/vimeo.svg'); ?>" alt="">
                                        <?php } elseif ($socialLink['network'] == 'vine') { ?>
                                            <img src="<?php echo esc_url(SKD_PL_PLUGIN_URL . 'assets/images/vine.svg'); ?>" alt="">
                                        <?php } elseif ($socialLink['network'] == 'youtube') { ?>
                                            <img src="<?php echo esc_url(SKD_PL_PLUGIN_URL . 'assets/images/youtube.svg'); ?>" alt="">
                                        <?php } else { ?>
                                            <img src="<?php echo esc_url(SKD_PL_PLUGIN_URL . 'assets/images/question-circle.png'); ?>" alt="">
                                        <?php } ?>
                                    </a>
                                <?php } ?>
                            </div>
                        </li>

                    </ul>
                </div>
            </div>
            <div class="DetailsContOuterBox Details_Card">
                <div class="DetailsContOuterHeader">
                    <span class="DetailsContOuterHeadericon">
                        <iconify-icon icon="proicons:call"></iconify-icon>
                    </span>
                    <h4>Contact This Business</h4>
                </div>
                <div class="DetailsContOuterBody">
                    <form action="" id="listDetContactForm">
                        <div class="formGrid">
                            <div class="detailsInputBox">
                                <input type="text" name="cName" id="" placeholder="Name">
                            </div>
                            <div class="detailsInputBox">
                                <input type="email" name="cEmail" id="" placeholder="Email">
                            </div>
                            <div class="detailsInputBox">
                                <textarea name="cDesc" id="" cols="30" placeholder="Message..."></textarea>
                            </div>
                            <button type="button" class="details_btn details_btn_submit">Submit
                                now</button>
                        </div>
                    </form>
                </div>
            </div>
            <?php if (!empty($listing->video)) {
                $embed_url = $this->skd_get_embeddable_video_url($listing->video);
            ?>
                <div class="DetailsContOuterBox Details_Card">
                    <div class="DetailsContOuterHeader">
                        <span class="DetailsContOuterHeadericon">
                            <iconify-icon icon="formkit:file"></iconify-icon>
                        </span>
                        <h4>Video</h4>
                    </div>
                    <div class="DetailsContOuterBody">
                        <div class="detailsVideo">
                            <iframe class="directorist-embaded-video embed-responsive-item"
                                src="<?php echo $embed_url;
                                        ?>" allowfullscreen
                                title="Single Listing Video"></iframe>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>

        <?php if (!empty($related_listings)) : ?>
            <!-- <div class="DetailsContOuterBoxGrid skd-related-listings">
                <h5>Related Listings</h5>
            </div> -->
        <?php endif; ?>
    </div>
</section>

<script>
    jQuery(document).ready(function($) {
        //============ for map =============================
        let map;
        let marker;

        //when edit listing generate map
        let lat = '<?php echo $listing->latitude; ?>';
        let lng = '<?php echo $listing->longitude; ?>';
        let address = '<?php echo $listing->list_address; ?>';

        // generate map on page load if address is available or lat and lng is available

        if (lat && lng) {
            initMap(parseFloat(lat), parseFloat(lng));
        } else if (address) {
            let geocoder = new google.maps.Geocoder();
            geocoder.geocode({
                address: address
            }, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    let lat = results[0].geometry.location.lat();
                    let lng = results[0].geometry.location.lng();

                    initMap(lat, lng);
                }
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
        //============ for map =============================

        //============ image gallery =============================
        let slideIndex = 1;
        showSlides(slideIndex);

        // Next/previous controls
        window.plusSlides = function(n) {
            showSlides(slideIndex += n);
        };

        // Thumbnail image controls
        window.skdCurrentSlide = function(n) {
            showSlides(slideIndex = n);
        };

        function showSlides(n) {
            let i;
            let slides = document.getElementsByClassName("mySkdSlides");
            let dots = document.getElementsByClassName("demo");
            if (n > slides.length) {
                slideIndex = 1
            }
            if (n < 1) {
                slideIndex = slides.length
            }
            for (i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            for (i = 0; i < dots.length; i++) {
                dots[i].className = dots[i].className.replace(" active", "");
            }
            slides[slideIndex - 1].style.display = "block";
            dots[slideIndex - 1].className += " active";
        }
        //============ image gallery =============================

        //============ contact form submit =============================
        $("#listDetContactForm .details_btn_submit").click(function() {
            let form = $("#listDetContactForm");
            let name = form.find("input[name='cName']").val().trim();
            let email = form.find("input[name='cEmail']").val().trim();
            let message = form.find("textarea[name='cDesc']").val().trim();
            let listingId = <?php echo json_encode($listing->id); ?>;
            let listingUrl = window.location.href;

            // Clear previous messages
            $(".formMessage").remove();

            // Validate form
            if (name === "" || email === "" || message === "") {
                form.prepend('<p class="formMessage error">All fields are required.</p>');
                return;
            }

            // Send AJAX request
            $.ajax({
                url: "<?php echo admin_url('admin-ajax.php'); ?>",
                type: "POST",
                data: {
                    action: "skd_submit_list_contact_form",
                    name: name,
                    email: email,
                    message: message,
                    listing_id: listingId,
                    listing_url: listingUrl,
                },
                beforeSend: function() {
                    form.find(".details_btn_submit").text("Submitting...").prop("disabled", true);
                },
                success: function(response) {
                    if (response.success) {
                        form.prepend('<p class="formMessage success">Your message has been sent successfully.</p>');
                        form[0].reset(); // Clear form
                    } else {
                        form.prepend('<p class="formMessage error">' + response.data + '</p>');
                    }
                },
                complete: function() {
                    form.find(".details_btn_submit").text("Submit now").prop("disabled", false);
                }
            });
        });
        //============ contact form submit =============================

        //============ related listing carousal =============================
        $('.skd-listing-carousel').slick({
            slidesToShow: 2, // Always show 3 slides
            // slidesToScroll: 1,
            // autoplay: 'true',
            // autoplaySpeed: 30000000000000000,
            dots: false,
            arrows: true,
            // centerMode: false, // Enables center focus effect
            // centerPadding: '0px', // Ensures proper alignment
            // responsive: [{
            //         breakpoint: 1024,
            //         settings: {
            //             slidesToShow: 2,
            //         }
            //     },
            //     {
            //         breakpoint: 768,
            //         settings: {
            //             slidesToShow: 1,
            //         }
            //     }
            // ]
        });
        //============ related listing carousal =============================
    });
</script>