<div id="skd-category-listings-container">
    <?php
    if (get_query_var('skd_category_slug')) {
        $categorySlug = get_query_var('skd_category_slug');
        $this->skd_load_category_listings(1, $categorySlug);
    } else {
        echo '<p class="skd-no-listings">No listings found.</p>';
    }
    ?>
</div>

<script>
    jQuery(document).ready(function($) {
        function loadCategoryListings(page) {
            $.ajax({
                type: "POST",
                url: "<?php echo admin_url('admin-ajax.php'); ?>",
                data: {
                    action: "skd_fetch_category_listings",
                    page: page,
                    category_slug: "<?php echo get_query_var('skd_category_slug'); ?>"
                },
                beforeSend: function() {
                    $("#skd-category-listings-container").css("opacity", "0.5");
                },
                success: function(response) {
                    $("#skd-category-listings-container").html(response.listings);
                    $("#skd-category-listings-container").css("opacity", "1");
                }
            });
        }

        $(document).on("click", ".skd-category-pagination a", function(e) {
            e.preventDefault();
            var page = $(this).data("page");
            loadCategoryListings(page);
        });
    });
</script>