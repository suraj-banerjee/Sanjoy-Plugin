<?php if (!defined('ABSPATH')) exit; ?>

<?php if (!empty($categories)) : ?>
    <div class="skd-category-container skd-columns-<?php echo esc_attr($atts['columns']); ?>">
        <section class="catagorySec">
            <div class="container">
                <div class="myRowBox myRowBoxCenter">
                    <?php foreach ($categories as $category) : ?>
                        <div class="catagory_Col_3">
                            <div class="catagoryBoxOuter">
                                <div class="catagoryBoxInner">
                                    <a class="catagoryImgBox" href="<?php echo esc_url(get_site_url() . '/single-category/' . $category->slug); ?>">
                                        <span class="imgIcon">
                                            <iconify-icon icon="pepicons-pop:arrow-right"></iconify-icon>
                                        </span>
                                        <div class="catagoryImgBoxBg">
                                            <?php if (!empty($category->image_url)) : ?>
                                                <img src="<?php echo esc_url($category->image_url); ?>" alt="<?php echo esc_attr($category->name); ?>">
                                            <?php else : ?>
                                                <img src="<?php echo esc_url(SKD_PL_PLUGIN_URL . 'assets/images/default-category.jpg'); ?>" alt="Default Image">
                                            <?php endif; ?>
                                        </div>
                                    </a>
                                </div>

                                <div class="catagoryContent">
                                    <span class="catagoryIcon">
                                        <iconify-icon icon="famicons:home"></iconify-icon>
                                    </span>
                                    <a href="<?php echo esc_url(get_site_url() . '/single-category/' . $category->slug); ?>">
                                        <h2><?php echo esc_html($category->name); ?></h2>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </div>
<?php else : ?>
    <p class="skd-no-categories">No categories found.</p>
<?php endif; ?>