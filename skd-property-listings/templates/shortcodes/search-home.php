<section class="sec7">
    <div class="container">
        <div class="gridBoxCenterDemo">
            <div class="directoristSearchBox">
                <div class="directoristSearchBoxContent">
                    <div class="directoristSearchBoxForm">
                        <form id="skd-search-form">
                            <div class="directoristSearchModalBox ">
                                <div class="directoristSearchModalBox1">
                                    <div class="search_form__box">
                                        <input type="text" id="search" name="search" placeholder="What are you looking for?">
                                    </div>
                                </div>
                                <div class="directoristSearchModalBox1">
                                    <div class="search_form__box">
                                        <select id="skd-category" name="category" class="skd-select2">
                                            <option value="">Select a Category</option>
                                            <?php echo $this->skd_build_category_dropdown($categories); ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="directoristSearchModalBox">
                                <div class="directoristSearchModalBox1">
                                    <div class="search_form__box">
                                        <input type="text" placeholder="Post Code" name="postcode">
                                    </div>
                                </div>
                                <div class="directoristSearchModalBox1 SearchModalGrid">
                                    <div class="wrapper_slider">
                                        <div class="valueOuter">
                                            <h5>Radius Search</h5>
                                            <div class="values">
                                                <span id="range1">
                                                    0
                                                </span>
                                                <span> &dash; </span>
                                                <span id="range2">
                                                    100
                                                </span>
                                                <p>Kilometers</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="Range_container">
                                        <div class="slider-track"></div>
                                        <input type="hidden" id="currentLat" name="lat">
                                        <input type="hidden" id="currentLong" name="long">
                                        <input type="range" min="0" max="1000" name="range" value="100" id="slider-2" class="slider2" oninput="slideTwo()">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="directoristSearchBoxButton">
                        <iconify-icon icon="material-symbols:search"></iconify-icon>
                        <a href="javascript:void(0)">
                            Search Listing
                        </a>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    jQuery(document).ready(function($) {
        var skd_ajax = {
            ajax_url: "<?php echo admin_url('admin-ajax.php'); ?>"
        };

        $("#skd-category").select2({
            placeholder: "Select a Category",
            allowClear: true
        });

        $(".directoristSearchBoxButton a").on("click", function(e) {
            e.preventDefault(); // Prevent default link behavior

            let formData = $("#skd-search-form").serializeArray();
            let queryParams = new URLSearchParams();

            // Append all form fields to the query parameters
            formData.forEach(item => {
                queryParams.append(item.name, item.value);
            });

            // Add default values
            queryParams.set("list_type", "general");
            queryParams.set("sort_by", "created_at");
            queryParams.set("page", "1");

            // Redirect to search results page with query parameters
            window.location.href = "<?php echo site_url('/search-result/'); ?>" + "?" + queryParams.toString();
        });

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                let latitude = position.coords.latitude;
                let longitude = position.coords.longitude;

                // Set the values in hidden inputs
                $('#currentLat').val(latitude);
                $('#currentLong').val(longitude);

                // console.log("Latitude: " + latitude + ", Longitude: " + longitude);
            }, function(error) {
                console.log("Geolocation error: " + error.message);
            });
        } else {
            console.log("Geolocation is not supported by this browser.");
        }

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