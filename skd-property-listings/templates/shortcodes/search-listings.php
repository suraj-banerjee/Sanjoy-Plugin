<section class="sec2">
    <form id="skd-search-form">
        <div class="container">
            <div class="search_form_box_Outer">
                <div class="search_form_box_Inner">
                    <?php
                    $searchText = isset($_GET['search']) ? $_GET['search'] : '';
                    ?>
                    <div class="search_form__box">
                        <input type="text" id="search" name="search" placeholder="What are you looking for?" value="<?php echo esc_attr($searchText); ?>">
                    </div>

                    <?php
                    $categoryId = isset($_GET['category']) ? $_GET['category'] : '';
                    ?>
                    <div class="search_form__box">
                        <select id="skd-category" name="category" class="skd-select2">
                            <option value="">Select a Category</option>
                            <?php echo $this->skd_build_category_dropdown($categories, 0, 0, $categoryId); ?>
                        </select>
                    </div>
                    <?php
                    $postCode = isset($_GET['postcode']) ? $_GET['postcode'] : '';
                    ?>
                    <div class="search_form__box">
                        <input type="text" placeholder="Post Code" name="postcode" value="<?php echo esc_attr($postCode); ?>">
                    </div>
                </div>
                <?php
                $slidrRange = isset($_GET['range']) ? $_GET['range'] : '100';
                ?>
                <div class="wrapper_slider">
                    <div class="wrapper_slider">
                        <div class="valueOuter">
                            <h5>Radius Search</h5>
                            <div class="values">
                                <span id="range1">
                                    0
                                </span>
                                <span> &dash; </span>
                                <span id="range2">
                                    <?php echo esc_html($slidrRange); ?>
                                </span>
                                <p>Kilometers</p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                $latText = isset($_GET['lat']) ? $_GET['lat'] : '';
                $longText = isset($_GET['long']) ? $_GET['long'] : '';
                ?>
                <div class="Range_container">
                    <div class="slider-track"></div>
                    <input type="hidden" id="currentLat" name="lat" value="<?php echo esc_attr($latText); ?>">
                    <input type="hidden" id="currentLong" name="long" value="<?php echo esc_attr($longText); ?>">
                    <input type="range" min="0" max="1000" name="range" id="slider-2" class="slider2" oninput="slideTwo()" value="<?php echo esc_attr($slidrRange); ?>">
                </div>
            </div>
            <div class="shortingTopbar">
                <div class="shortingTopbar_header__left">
                    <span class="shortingTopbar_headericon">
                        <iconify-icon icon="stash:filter-light"></iconify-icon>
                    </span>
                </div>
                <div class="shortingTopbar_header__right">
                    <select class="shortingSelectBox" id="sort_by" name="sort_by">
                        <option disabled value="">Sort By</option>
                        <option value="Oldest listings" <?php echo (isset($_GET['sort_by']) && $_GET['sort_by'] === 'Oldest listings') ? 'selected' : ''; ?>>Oldest Listings</option>
                        <option value="A to Z (title)" <?php echo (isset($_GET['sort_by']) && $_GET['sort_by'] === 'A to Z (title)') ? 'selected' : ''; ?>>A to Z (Title)</option>
                        <option value="Z to A (title)" <?php echo (isset($_GET['sort_by']) && $_GET['sort_by'] === 'Z to A (title)') ? 'selected' : ''; ?>>Z to A (Title)</option>
                        <option value="Popular listings" <?php echo (isset($_GET['sort_by']) && $_GET['sort_by'] === 'Popular listings') ? 'selected' : ''; ?>>Popular Listings</option>
                        <option value="Price (low to high)" <?php echo (isset($_GET['sort_by']) && $_GET['sort_by'] === 'Price (low to high)') ? 'selected' : ''; ?>>Price (Low to High)</option>
                        <option value="Price (high to low)" <?php echo (isset($_GET['sort_by']) && $_GET['sort_by'] === 'Price (high to low)') ? 'selected' : ''; ?>>Price (High to Low)</option>
                        <option value="Random listings" <?php echo (isset($_GET['sort_by']) && $_GET['sort_by'] === 'Random listings') ? 'selected' : ''; ?>>Random Listings</option>
                    </select>
                    <!-- <div class="shortingTopbar_header__rightBtns">
                        <button class="shortingTopbar_header__right__box__btn active">
                            <iconify-icon icon="fa-solid:grip-horizontal"></iconify-icon>
                        </button>
                        <button class="shortingTopbar_header__right__box__btn">
                            <iconify-icon icon="mynaui:list"></iconify-icon>
                        </button>
                    </div> -->
                </div>
            </div>
            <div class="customRow">
                <div class="filterslideBarOuter mobFilter">
                    <div class="filterslideBarInner">
                        <div class="filterslideBarInnerTop">
                            <h2 class="filterslideBarInnerTop__title">Filters</h2>
                        </div>
                        <div class="filterslideBarInnerAdvance">
                            <!-- <div class="filterslideBarInnerAdvanceItem">
                                <h3 class="filteritemBoxTitle">Review</h3>
                                <div class="advanceFilterBox">
                                    <div class="form-group">
                                        <input type="checkbox" id="5stars" name="rating[]" value="5">
                                        <label for="5stars">
                                            <ul class="starsRow">
                                                <i class="star starColorfull"></i>
                                                <i class="star starColorfull"></i>
                                                <i class="star starColorfull"></i>
                                                <i class="star starColorfull"></i>
                                                <i class="star starColorfull"></i>
                                            </ul>
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <input type="checkbox" id="4stars" name="rating[]" value="4">
                                        <label for="4stars">
                                            <ul class="starsRow">
                                                <i class="star starColorfull"></i>
                                                <i class="star starColorfull"></i>
                                                <i class="star starColorfull"></i>
                                                <i class="star starColorfull"></i>
                                                <i class="star starNoColor"></i>
                                            </ul>
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <input type="checkbox" id="3stars" name="rating[]" value="3">
                                        <label for="3stars">
                                            <ul class="starsRow">
                                                <i class="star starColorfull"></i>
                                                <i class="star starColorfull"></i>
                                                <i class="star starColorfull"></i>
                                                <i class="star starNoColor"></i>
                                                <i class="star starNoColor"></i>
                                            </ul>
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <input type="checkbox" id="2stars" name="rating[]" value="2">
                                        <label for="2stars">
                                            <ul class="starsRow">
                                                <i class="star starColorfull"></i>
                                                <i class="star starColorfull"></i>
                                                <i class="star starNoColor"></i>
                                                <i class="star starNoColor"></i>
                                                <i class="star starNoColor"></i>
                                            </ul>
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <input type="checkbox" id="1stars" name="rating[]" value="1">
                                        <label for="1stars">
                                            <ul class="starsRow">
                                                <i class="star starColorfull"></i>
                                                <i class="star starNoColor"></i>
                                                <i class="star starNoColor"></i>
                                                <i class="star starNoColor"></i>
                                                <i class="star starNoColor"></i>
                                            </ul>
                                        </label>
                                    </div>
                                </div>
                            </div> -->
                            <?php
                            // Get selected tags from the URL
                            $selected_tags = isset($_GET['tags']) ? (array) $_GET['tags'] : [];
                            ?>

                            <div class="filterslideBarInnerAdvanceItem">
                                <h3 class="filteritemBoxTitle">Tags</h3>
                                <div class="advanceFilterBox">
                                    <?php $count = 0; ?>
                                    <?php foreach ($tag_objects as $tag) : ?>
                                        <div class="form-group tag-item <?php echo ($count >= 4) ? 'hidden-tag' : ''; ?>">
                                            <input type="checkbox" name="tags[]" value="<?php echo esc_attr($tag->id); ?>"
                                                id="tag-<?php echo esc_attr($tag->id); ?>"
                                                <?php echo in_array($tag->id, $selected_tags) ? 'checked' : ''; ?>>
                                            <label for="tag-<?php echo esc_attr($tag->id); ?>">
                                                <?php echo esc_html($tag->name); ?>
                                            </label>
                                        </div>
                                        <?php $count++; ?>
                                    <?php endforeach; ?>
                                </div>

                                <?php if (count($tag_objects) > 4) : ?>
                                    <span class="readmoreBtn">Show More</span>
                                <?php endif; ?>
                            </div>

                        </div>
                        <!-- <button type="reset" id="skd-clear-filters">Clear Filters</button> -->
                    </div>
                </div>
                <div class="allProductDirectoristItemOuter skd-search-results">
                    <div class="myRowBox" id="skd-search-results">
                        <!-- Search results will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>

<script>
    jQuery(document).ready(function($) {
        var skd_ajax = {
            ajax_url: "<?php echo admin_url('admin-ajax.php'); ?>"
        };

        $("#skd-category").select2({
            placeholder: "Select a Category",
            allowClear: true
        }).trigger("change");

        $(".readmoreBtn").on("click", function() {
            $(".hidden-tag").slideToggle();
            let btnText = $(this).text() === "Show More" ? "Show Less" : "Show More";
            $(this).text(btnText);
        });

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                let latitude = position.coords.latitude;
                let longitude = position.coords.longitude;

                // Set the values in hidden inputs
                if (!$('#currentLat').val()) {
                    $('#currentLat').val(latitude);
                }
                if (!$('#currentLong').val()) {
                    $('#currentLong').val(longitude);
                }
                fetchListings();
                // console.log("Latitude: " + latitude + ", Longitude: " + longitude);
            }, function(error) {
                console.log("Geolocation error: " + error.message);
            });
        } else {
            console.log("Geolocation is not supported by this browser.");
        }

        function fetchListings() {
            let formData = $("#skd-search-form").serialize();

            // Show Loader
            // $("#skd-search-results").html('<div class="skd-loader">Loading...</div>');
            // Reduce opacity before loading
            $("#skd-search-results").css({
                "opacity": "0.25",
                "background-color": "#92d50852" // Light gray background
            });

            $.ajax({
                type: "POST",
                url: skd_ajax.ajax_url,
                data: {
                    action: "skd_fetch_search_listings",
                    page: $(".skd-pagination-link.active").data("page") || 1,
                    ...$("#skd-search-form").serializeArray().reduce((obj, item) => {
                        obj[item.name] = item.value;
                        return obj;
                    }, {})
                },
                success: function(response) {
                    $("#skd-search-results").html(response.listings);
                    updateURL(formData);
                },
                error: function() {
                    $("#skd-search-results").html('<div class="skd-error">Failed to load listings. Please try again.</div>');
                },
                complete: function() {
                    // Restore full opacity after loading
                    $("#skd-search-results").css({
                        "opacity": "1",
                        "background-color": "transparent"
                    });
                }
            });
        }

        function updateURL(params) {
            let newUrl = window.location.pathname + "?" + params;
            window.history.pushState(null, "", newUrl);
        }

        $("#skd-search-form input, #skd-search-form select").on("change keyup", function() {
            fetchListings();
        });

        $(document).on("click", ".skd-pagination-link", function(e) {
            e.preventDefault();
            $(".skd-pagination-link").removeClass("active");
            $(this).addClass("active");
            fetchListings();
        });

        fetchListings();
    });

    // range slider
    window.onload = function() {
        slideTwo();
    };

    let sliderTwo = document.getElementById("slider-2");
    let displayValTwo = document.getElementById("range2");
    let minGap = 1;
    let sliderTrack = document.querySelector(".slider-track");
    let sliderMaxValue = document.getElementById("slider-2").max;

    function slideTwo() {
        if (parseInt(sliderTwo.value) <= minGap) {
            sliderTwo.value = minGap;
        }
        displayValTwo.textContent = sliderTwo.value;
        fillColor();
    }

    function fillColor() {
        percent1 = 0;
        percent2 = (sliderTwo.value / sliderMaxValue) * 100;
        sliderTrack.style.background = `linear-gradient(to right, #e9ebf4 ${percent1}% , #92d508 ${percent1}% , #92d508 ${percent2}%, #e9ebf4 ${percent2}%)`;
    }
    // range slider
</script>