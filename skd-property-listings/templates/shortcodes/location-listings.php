<div id="skd-location-listings-container">
    <?php
    if (get_query_var('skd_location_slug')) {
        $locationSlug = get_query_var('skd_location_slug');
        $this->skd_load_location_listings(1, $locationSlug);
    } else {
        echo '<p class="skd-no-locations">No locations found.</p>';
    }
    ?>
</div>

<script>
    jQuery(document).ready(function($) {
        function loadListings(page) {
            $.ajax({
                type: "POST",
                url: "<?php echo admin_url('admin-ajax.php'); ?>",
                data: {
                    action: "skd_fetch_location_listings",
                    page: page,
                    location_slug: "<?php echo get_query_var('skd_location_slug'); ?>"
                },
                beforeSend: function() {
                    $("#skd-location-listings-container").css("opacity", "0.5");
                },
                success: function(response) {
                    $("#skd-location-listings-container").html(response.listings);
                    $("#skd-location-listings-container").css("opacity", "1");
                }
            });
        }

        $(document).on("click", ".skd-pagination a", function(e) {
            e.preventDefault();
            var page = $(this).data("page");
            loadListings(page);
        });
    });
</script>