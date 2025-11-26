<?php if (!defined('ABSPATH')) exit; ?>

<?php if (!empty($listings)) : ?>
    <div class="skd-carousel-container">
        <div class="skd-listing-carousel">
            <?php foreach ($listings as $listing) :
                $logo = !empty($listing->skd_logo) ? $listing->skd_logo : SKD_PL_PLUGIN_URL . 'assets/img/default-logo.png';
                $created_at = strtotime($listing->created_at);
                $time_diff = human_time_diff($created_at, current_time('timestamp'));
            ?>
                <a href="<?php echo esc_url(site_url('/single-detail/' . $listing->slug)); ?>">
                    <div class="skd-carousel-item">
                        <div class="skd-carousel-content">
                            <img src="<?php echo esc_url($logo); ?>" alt="<?php echo esc_attr($listing->listing_title); ?>" class="skd-carousel-logo">
                            <h3 class="skd-carousel-title"><?php echo esc_html($listing->listing_title); ?></h3>
                            <p class="skd-carousel-time"><?php echo sprintf(__('Posted %s ago', 'skd-property-listings'), $time_diff); ?></p>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        jQuery(document).ready(function($) {
            $('.skd-listing-carousel').each(function() {
                let $carousel = $(this);
                let slideCount = $carousel.find('.slick-slide').length || $carousel.children().length;

                let centerMode = true;
                if (slideCount < 3) {
                    centerMode = false;
                }

                $carousel.slick({
                    slidesToShow: 3,
                    slidesToScroll: 1,
                    autoplay: <?php echo $atts['autoplay'] == 'yes' ? 'true' : 'false'; ?>,
                    autoplaySpeed: 3000,
                    // dots: true,
                    arrows: true,
                    centerMode: centerMode,
                    centerPadding: '0px',
                    responsive: [{
                            breakpoint: 1024,
                            settings: {
                                slidesToShow: Math.min(slideCount, 2),
                                centerMode: slideCount < 2,
                                centerPadding: '30px'
                            }
                        },
                        {
                            breakpoint: 768,
                            settings: {
                                slidesToShow: 1,
                                centerMode: true,
                                centerPadding: '20px'
                            }
                        }
                    ]
                });
            });
        });
    </script>
<?php else : ?>
    <p><?php _e('No listings found.', 'skd-property-listings'); ?></p>
<?php endif; ?>