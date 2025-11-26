<?php if (!defined('ABSPATH')) exit;

if (!empty($locations)) : ?>
    <div class="skd-location-container skd-columns-<?php echo esc_attr($atts['columns']); ?> skd-layout-<?php echo esc_attr($atts['layout']); ?>">
        <section class="sec6">
            <div class="container">
                <div class="myRowBox myRowBoxCenter">
                    <?php foreach ($locations as $location) : ?>
                        <div class="directoristItemOuter_Col_4">
                            <div class="directoristItemLocationBox">
                                <a href="<?php echo esc_url(get_site_url() . '/single-location/' . $location->slug); ?>">
                                    <div class="directoristItemLocationBoxContent">
                                        <div class="directoristItemLocationBoxImage">
                                            <?php if (!empty($location->image_url)) : ?>
                                                <img src="<?php echo esc_url($location->image_url); ?>" alt="<?php echo esc_attr($location->name); ?>">
                                            <?php else : ?>
                                                <img src="<?php echo esc_url(SKD_PL_PLUGIN_URL . 'assets/images/default-location.jpg'); ?>" alt="Default Image">
                                            <?php endif; ?>
                                        </div>
                                        <div class="directorItemLocationContent">
                                            <h2> <?php echo esc_html($location->name); ?> </h2>
                                            <?php if ($atts['show_count']) : ?>
                                                <div class="directoristItemLocationListing">
                                                    <h5><?php echo esc_html($location->listing_count); ?> Listings</h5>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </div>
<?php else : ?>
    <p class="skd-no-locations">No locations found.</p>
<?php endif; ?>