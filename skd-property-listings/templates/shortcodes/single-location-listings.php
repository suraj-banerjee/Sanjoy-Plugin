<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<section class="sec1">
    <div class="container">
        <div class="myRowBox">
            <?php if (!empty($listings)) : ?>
                <?php foreach ($listings as $listing) : ?>
                    <div class="directoristItemOuter_Col_4">
                        <div class="directoristItemInner">
                            <!--  -->
                            <div class="directoristItemHeader">
                                <div class="directoristItemHeaderLeft">
                                    <img class="AvtarSb" src="<?php echo esc_url(SKD_PL_PLUGIN_URL . 'assets/images/defalt.jpg'); ?>" alt="">
                                </div>
                                <div class="directoristItemHeaderInfo">
                                    <a href="<?php echo esc_url(site_url('/single-detail/' . $listing->slug)); ?>"><?php echo esc_html($listing->listing_title); ?></a>
                                    <?php if (!empty($listing->tagline)) : ?>
                                        <p><?php echo esc_html($listing->tagline); ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="directoristItemHeaderRight">
                                    <!-- <button class="WishistBtn"><iconify-icon icon="mynaui:heart"></iconify-icon></button> -->
                                </div>
                            </div>
                            <!--  -->
                            <div class="directoristInfo">
                                <div class="directoristIcon">
                                    <?php if ($listing->is_feature) : ?>
                                        <iconify-icon icon="material-symbols:star-rounded"></iconify-icon>
                                        <span class="directoristTooltip">Featured</span>
                                    <?php endif; ?>
                                </div>
                                <!-- <div class="directoristIcon">
                                    <iconify-icon icon="fluent:fire-24-filled"></iconify-icon>
                                    <span class="directoristTooltip">Popular</span>
                                </div> -->
                            </div>
                            <!--  -->
                            <?php
                            $location_ids = json_decode($listing->location_ids); // Assuming it's stored as JSON
                            $locations = [];

                            if (!empty($location_ids)) {
                                foreach ($location_ids as $location_id) {
                                    $location = $wpdb->get_row(
                                        $wpdb->prepare("SELECT name, slug FROM {$wpdb->prefix}skd_pl_locations WHERE id = %d", $location_id)
                                    );

                                    if ($location) {
                                        $locations[] = [
                                            'name' => $location->name,
                                            'slug' => $location->slug
                                        ];
                                    }
                                }
                            }
                            ?>
                            <div class="directoristBody">
                                <ul class="directorist-listing-single__info__list">
                                    <?php if (!empty($locations)) : ?>
                                        <li class="directorist-listing-card-location">
                                            <iconify-icon icon="humbleicons:location"></iconify-icon>
                                            <div class="directorist-listing-card-location-list">
                                                <?php if (!empty($locations)) { ?>
                                                    <?php foreach ($locations as $index => $location) { ?>
                                                        <a href="<?php echo esc_url(site_url('/single-location/' . $location['slug'])); ?>" rel="tag">
                                                            <?php echo esc_html($location['name']); ?>
                                                        </a>
                                                        <?php if ($index < count($locations) - 1) echo ', '; ?>
                                                    <?php } ?>
                                                <?php } ?>
                                            </div>
                                        </li>
                                    <?php endif; ?>
                                    <?php if (!empty($listing->contact_phone)) : ?>
                                        <li class="directorist-listing-card-phone">
                                            <iconify-icon icon="heroicons:phone"></iconify-icon>
                                            <a href="tel:<?php echo esc_html($listing->contact_phone); ?>"><?php echo esc_html($listing->contact_phone); ?></a>
                                        </li>
                                    <?php endif; ?>
                                    <?php if (!empty($listing->contact_email)) : ?>
                                        <li class="directorist-listing-card-email">
                                            <iconify-icon icon="octicon:mail-24"></iconify-icon>
                                            <a target="_top" href="mailto:<?php echo esc_html($listing->contact_email); ?>"><?php echo esc_html($listing->contact_email); ?></a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                            <!--  -->
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
                            <div class="directoristFooter">
                                <div class="directoristFooterLeft">
                                    <?php if (!empty($categories)) { ?>
                                        <a href="<?php echo esc_url(site_url('/single-category/' . $categories[0]['slug'])); ?>">
                                            <span class="folderIcon">
                                                <iconify-icon icon="mynaui:folder"></iconify-icon>
                                            </span>
                                            <h5><?php echo esc_html($categories[0]['name']); ?></h5>
                                        </a>

                                        <?php if (count($categories) > 1) { ?>
                                            <div class="listing_category__popup">
                                                <div class="listing_category__popup__content__title">
                                                    +<?php echo (count($categories) - 1); ?>
                                                </div>
                                                <ul class="listing_category__popup__content__list">
                                                    <?php foreach (array_slice($categories, 1) as $extra_category) { ?>
                                                        <li>
                                                            <a href="<?php echo esc_url(site_url('/single-category/' . $extra_category['slug'])); ?>">
                                                                <span class="listingPopupIcon">
                                                                    <iconify-icon icon="mynaui:folder"></iconify-icon>
                                                                </span>
                                                                <?php echo esc_html($extra_category['name']); ?>
                                                            </a>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                                <div class="directoristFooterRight">
                                    <!-- <span class="wishlistBoxIcon">
                                        <iconify-icon icon="mynaui:heart"></iconify-icon>
                                    </span>
                                    9 -->
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <p>No listings found.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php if ($total_pages > 1) : ?>
    <div class="skd-pagination">
        <?php if ($current_page > 1) : ?>
            <a href="#" class="skd-pagination-prev" data-page="<?php echo $current_page - 1; ?>">« Prev</a>
        <?php endif; ?>

        <?php
        $pagination_range = 3; // Number of pages to show before and after the current page
        $start_page = max(1, $current_page - $pagination_range);
        $end_page = min($total_pages, $current_page + $pagination_range);

        for ($i = $start_page; $i <= $end_page; $i++) :
        ?>
            <a href="#" class="skd-pagination-link <?php echo ($i == $current_page) ? 'active' : ''; ?>" data-page="<?php echo $i; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>

        <?php if ($current_page < $total_pages) : ?>
            <a href="#" class="skd-pagination-next" data-page="<?php echo $current_page + 1; ?>">Next »</a>
        <?php endif; ?>
    </div>
<?php endif; ?>