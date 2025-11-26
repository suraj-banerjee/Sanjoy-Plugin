<!-- 
Template: VDA Public Profile Page
Shortcode: [vda_profile]
-->
<?php
// Get VDA user ID from URL parameter
$vda_id = isset($_GET['vda_id']) ? intval($_GET['vda_id']) : 0;

if (!$vda_id) {
    echo '<div class="vda-error">VDA not found.</div>';
    return;
}

global $wpdb;

// Fetch VDA profile data
$profile = $wpdb->get_row($wpdb->prepare(
    "SELECT * FROM {$wpdb->prefix}skd_pl_user_profiles WHERE user_id = %d",
    $vda_id
));

if (!$profile) {
    echo '<div class="vda-error">VDA profile not found.</div>';
    return;
}

$user = get_userdata($vda_id);
if (!$user) {
    echo '<div class="vda-error">User not found.</div>';
    return;
}

// Parse JSON fields
$skills = !empty($profile->skills) ? json_decode($profile->skills, true) : [];
$software_used = !empty($profile->software_used) ? json_decode($profile->software_used, true) : [];
$certifications = !empty($profile->certifications) ? json_decode($profile->certifications, true) : [];
$portfolio_items = !empty($profile->portfolio_items) ? json_decode($profile->portfolio_items, true) : [];
$specializations = !empty($profile->specializations) ? json_decode($profile->specializations, true) : [];

// Calculate stats
$total_jobs = $wpdb->get_var($wpdb->prepare(
    "SELECT COUNT(*) FROM {$wpdb->prefix}skd_pl_job_applications WHERE applicant_id = %d",
    $vda_id
));

$avg_rating = 4.8; // TODO: Calculate from reviews table
$total_reviews = 127; // TODO: Get from reviews table
$completion_rate = 98; // TODO: Calculate from orders

// Profile completion
$profile_completion = 0;
if ($profile->headline) $profile_completion += 10;
if ($profile->bio) $profile_completion += 15;
if (!empty($skills)) $profile_completion += 15;
if (!empty($software_used)) $profile_completion += 10;
if (!empty($certifications)) $profile_completion += 10;
if (!empty($portfolio_items)) $profile_completion += 20;
if ($profile->hourly_rate) $profile_completion += 10;
if ($profile->experience_level) $profile_completion += 10;

$is_verified = $profile_completion >= 80;
$is_top_rated = $avg_rating >= 4.5 && $total_reviews >= 50;
?>

<div class="vda-profile-page">
    <div class="vda-profile-container">

        <!-- Sidebar -->
        <aside class="vda-profile-sidebar">

            <!-- Main Profile Card -->
            <div class="vda-profile-card-widget vda-profile-main-card">
                <div class="vda-profile-avatar-large">
                    <?php if ($profile->profile_image): ?>
                        <img src="<?php echo esc_url($profile->profile_image); ?>"
                            alt="<?php echo esc_attr($user->display_name); ?>">
                    <?php else: ?>
                        <div class="vda-avatar-placeholder">
                            <?php echo esc_html(strtoupper(substr($user->display_name, 0, 1))); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($is_verified): ?>
                        <span class="vda-verified-badge" title="Verified Professional">
                            <iconify-icon icon="mdi:check-decagram"></iconify-icon>
                        </span>
                    <?php endif; ?>
                </div>

                <h2 class="vda-profile-name"><?php echo esc_html($user->display_name); ?></h2>

                <?php if ($profile->headline): ?>
                    <p class="vda-profile-title"><?php echo esc_html($profile->headline); ?></p>
                <?php endif; ?>

                <div class="vda-profile-rating">
                    <span class="vda-stars">
                        <?php for ($i = 0; $i < 5; $i++): ?>
                            <iconify-icon icon="<?php echo $i < floor($avg_rating) ? 'mdi:star' : 'mdi:star-outline'; ?>"></iconify-icon>
                        <?php endfor; ?>
                    </span>
                    <strong><?php echo number_format($avg_rating, 1); ?></strong>
                    <span class="vda-rating-count">(<?php echo esc_html($total_reviews); ?> reviews)</span>
                </div>

                <div class="vda-profile-badges">
                    <?php if ($is_top_rated): ?>
                        <span class="vda-badge top-rated">
                            <iconify-icon icon="mdi:star"></iconify-icon> Top Rated
                        </span>
                    <?php endif; ?>

                    <?php if ($is_verified): ?>
                        <span class="vda-badge verified">
                            <iconify-icon icon="mdi:check-circle"></iconify-icon> Verified
                        </span>
                    <?php endif; ?>

                    <?php if ($profile->is_featured): ?>
                        <span class="vda-badge featured">
                            <iconify-icon icon="mdi:lightning-bolt"></iconify-icon> Featured
                        </span>
                    <?php endif; ?>
                </div>

                <div class="vda-contact-actions">
                    <a href="<?php echo esc_url(home_url('/hire-vda/?vda_id=' . $vda_id)); ?>"
                        class="vda-btn-contact primary">
                        <iconify-icon icon="mdi:briefcase-check"></iconify-icon>
                        Hire Now
                    </a>
                    <a href="<?php echo esc_url(home_url('/contact-vda/?vda_id=' . $vda_id)); ?>"
                        class="vda-btn-contact secondary">
                        <iconify-icon icon="mdi:email"></iconify-icon>
                        Contact Me
                    </a>
                </div>
            </div>

            <!-- Pricing Widget -->
            <?php if ($profile->hourly_rate || $profile->project_rate): ?>
                <div class="vda-profile-card-widget vda-pricing-info">
                    <h3>
                        <iconify-icon icon="mdi:cash"></iconify-icon> Pricing
                    </h3>

                    <?php if ($profile->hourly_rate): ?>
                        <div class="vda-price-item">
                            <span class="vda-price-label">Hourly Rate</span>
                            <span class="vda-price-value">$<?php echo esc_html($profile->hourly_rate); ?>/hr</span>
                        </div>
                    <?php endif; ?>

                    <?php if ($profile->project_rate): ?>
                        <div class="vda-price-item">
                            <span class="vda-price-label">Project Rate</span>
                            <span class="vda-price-value">From $<?php echo esc_html($profile->project_rate); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Skills Widget -->
            <?php if (!empty($skills)): ?>
                <div class="vda-profile-card-widget">
                    <h3>
                        <iconify-icon icon="mdi:lightbulb"></iconify-icon> Skills
                    </h3>
                    <div class="vda-skills-list">
                        <?php foreach ($skills as $skill): ?>
                            <span class="vda-skill-tag"><?php echo esc_html($skill); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Software Widget -->
            <?php if (!empty($software_used)): ?>
                <div class="vda-profile-card-widget">
                    <h3>
                        <iconify-icon icon="mdi:monitor"></iconify-icon> Software
                    </h3>
                    <div class="vda-skills-list">
                        <?php foreach ($software_used as $software): ?>
                            <span class="vda-skill-tag"><?php echo esc_html($software); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Certifications Widget -->
            <?php if (!empty($certifications)): ?>
                <div class="vda-profile-card-widget">
                    <h3>
                        <iconify-icon icon="mdi:certificate"></iconify-icon> Certifications
                    </h3>
                    <div class="vda-info-list">
                        <?php foreach ($certifications as $cert): ?>
                            <div class="vda-info-item">
                                <iconify-icon icon="mdi:check-circle" class="vda-info-icon"></iconify-icon>
                                <div class="vda-info-content">
                                    <p><?php echo esc_html($cert); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Experience & Info Widget -->
            <div class="vda-profile-card-widget">
                <h3>
                    <iconify-icon icon="mdi:information"></iconify-icon> Details
                </h3>
                <div class="vda-info-list">
                    <?php if ($profile->experience_level): ?>
                        <div class="vda-info-item">
                            <iconify-icon icon="mdi:chart-line" class="vda-info-icon"></iconify-icon>
                            <div class="vda-info-content">
                                <h4>Experience Level</h4>
                                <p><?php echo esc_html(ucfirst($profile->experience_level)); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($profile->location): ?>
                        <div class="vda-info-item">
                            <iconify-icon icon="mdi:map-marker" class="vda-info-icon"></iconify-icon>
                            <div class="vda-info-content">
                                <h4>Location</h4>
                                <p><?php echo esc_html($profile->location); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($profile->timezone): ?>
                        <div class="vda-info-item">
                            <iconify-icon icon="mdi:clock" class="vda-info-icon"></iconify-icon>
                            <div class="vda-info-content">
                                <h4>Timezone</h4>
                                <p><?php echo esc_html($profile->timezone); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($profile->language_proficiency): ?>
                        <div class="vda-info-item">
                            <iconify-icon icon="mdi:translate" class="vda-info-icon"></iconify-icon>
                            <div class="vda-info-content">
                                <h4>English Level</h4>
                                <p><?php echo esc_html($profile->language_proficiency); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="vda-info-item">
                        <iconify-icon icon="mdi:briefcase" class="vda-info-icon"></iconify-icon>
                        <div class="vda-info-content">
                            <h4>Jobs Completed</h4>
                            <p><?php echo esc_html($total_jobs); ?></p>
                        </div>
                    </div>

                    <div class="vda-info-item">
                        <iconify-icon icon="mdi:check-all" class="vda-info-icon"></iconify-icon>
                        <div class="vda-info-content">
                            <h4>Completion Rate</h4>
                            <p><?php echo esc_html($completion_rate); ?>%</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Specializations Widget -->
            <?php if (!empty($specializations)): ?>
                <div class="vda-profile-card-widget">
                    <h3>
                        <iconify-icon icon="mdi:star-box"></iconify-icon> Specializations
                    </h3>
                    <div class="vda-skills-list">
                        <?php foreach ($specializations as $spec): ?>
                            <span class="vda-skill-tag"><?php echo esc_html($spec); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

        </aside>

        <!-- Main Content -->
        <main class="vda-profile-main">

            <!-- About Section -->
            <?php if ($profile->bio): ?>
                <section class="vda-content-section">
                    <div class="vda-section-header">
                        <h2 class="vda-section-title">About Me</h2>
                    </div>
                    <div class="vda-about-text">
                        <?php echo wp_kses_post(nl2br($profile->bio)); ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Portfolio Section -->
            <?php if (!empty($portfolio_items)): ?>
                <section class="vda-content-section">
                    <div class="vda-section-header">
                        <h2 class="vda-section-title">Portfolio</h2>
                        <span><?php echo count($portfolio_items); ?> Projects</span>
                    </div>

                    <div class="vda-portfolio-grid">
                        <?php foreach ($portfolio_items as $item): ?>
                            <div class="vda-portfolio-item"
                                onclick="openPortfolioModal(<?php echo htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8'); ?>)">
                                <?php if (!empty($item['image_url'])): ?>
                                    <img src="<?php echo esc_url($item['image_url']); ?>"
                                        alt="<?php echo esc_attr($item['title'] ?? 'Portfolio Item'); ?>"
                                        class="vda-portfolio-image">
                                <?php else: ?>
                                    <div class="vda-portfolio-image" style="background: linear-gradient(135deg, #667eea, #764ba2);"></div>
                                <?php endif; ?>

                                <div class="vda-portfolio-info">
                                    <h4><?php echo esc_html($item['title'] ?? 'Untitled Project'); ?></h4>
                                    <div class="vda-portfolio-meta">
                                        <?php if (!empty($item['category'])): ?>
                                            <span>
                                                <iconify-icon icon="mdi:folder"></iconify-icon>
                                                <?php echo esc_html($item['category']); ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if (!empty($item['software_used'])): ?>
                                            <span>
                                                <iconify-icon icon="mdi:tools"></iconify-icon>
                                                <?php echo esc_html($item['software_used']); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Reviews Section -->
            <section class="vda-content-section">
                <div class="vda-section-header">
                    <h2 class="vda-section-title">Reviews & Ratings</h2>
                </div>

                <!-- Reviews Summary -->
                <div class="vda-reviews-summary">
                    <div class="vda-avg-rating">
                        <div class="vda-avg-rating-score"><?php echo number_format($avg_rating, 1); ?></div>
                        <div class="vda-avg-rating-stars">
                            <?php for ($i = 0; $i < 5; $i++): ?>
                                <iconify-icon icon="<?php echo $i < floor($avg_rating) ? 'mdi:star' : 'mdi:star-outline'; ?>"></iconify-icon>
                            <?php endfor; ?>
                        </div>
                        <div class="vda-avg-rating-count"><?php echo esc_html($total_reviews); ?> reviews</div>
                    </div>

                    <div class="vda-rating-breakdown">
                        <?php
                        $rating_distribution = [5 => 85, 4 => 25, 3 => 10, 2 => 5, 1 => 2]; // TODO: Get from database
                        foreach ($rating_distribution as $stars => $count):
                            $percentage = ($count / $total_reviews) * 100;
                        ?>
                            <div class="vda-rating-bar">
                                <span class="vda-rating-bar-label"><?php echo $stars; ?> stars</span>
                                <div class="vda-rating-bar-fill">
                                    <div class="vda-rating-bar-inner" style="width: <?php echo $percentage; ?>%"></div>
                                </div>
                                <span class="vda-rating-bar-count"><?php echo $count; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Reviews List -->
                <div class="vda-reviews-list">
                    <?php
                    // TODO: Fetch real reviews from database
                    $sample_reviews = [
                        ['name' => 'Sarah Johnson', 'rating' => 5, 'date' => '2 weeks ago', 'text' => 'Absolutely fantastic work! The VDA understood my vision perfectly and delivered beyond expectations. Highly professional and responsive throughout the project.'],
                        ['name' => 'Michael Chen', 'rating' => 5, 'date' => '1 month ago', 'text' => 'Great communication and exceptional design skills. Completed the project on time and made all requested revisions quickly. Will definitely work together again!'],
                        ['name' => 'Emily Rodriguez', 'rating' => 4, 'date' => '2 months ago', 'text' => 'Very talented designer with a good eye for detail. The final result was professional and exactly what I needed for my client presentation.'],
                    ];

                    foreach ($sample_reviews as $review): ?>
                        <div class="vda-review-item">
                            <div class="vda-review-header">
                                <div class="vda-reviewer-avatar" style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700;">
                                    <?php echo esc_html(strtoupper(substr($review['name'], 0, 1))); ?>
                                </div>
                                <div class="vda-reviewer-info">
                                    <div class="vda-reviewer-name"><?php echo esc_html($review['name']); ?></div>
                                    <div class="vda-review-meta">
                                        <span class="vda-review-stars">
                                            <?php for ($i = 0; $i < 5; $i++): ?>
                                                <iconify-icon icon="<?php echo $i < $review['rating'] ? 'mdi:star' : 'mdi:star-outline'; ?>"></iconify-icon>
                                            <?php endfor; ?>
                                        </span>
                                        <span><?php echo esc_html($review['date']); ?></span>
                                    </div>
                                </div>
                            </div>
                            <p class="vda-review-text"><?php echo esc_html($review['text']); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

        </main>

    </div>
</div>

<script>
    function openPortfolioModal(itemData) {
        Swal.fire({
            title: itemData.title || 'Portfolio Item',
            html: `
            ${itemData.image_url ? `<img src="${itemData.image_url}" style="width: 100%; max-height: 500px; object-fit: contain; margin-bottom: 15px;">` : ''}
            <p style="text-align: left; color: #666; line-height: 1.6;">${itemData.description || ''}</p>
            ${itemData.category ? `<p style="text-align: left; margin-top: 10px;"><strong>Category:</strong> ${itemData.category}</p>` : ''}
            ${itemData.software_used ? `<p style="text-align: left;"><strong>Software:</strong> ${itemData.software_used}</p>` : ''}
            ${itemData.completion_date ? `<p style="text-align: left;"><strong>Completed:</strong> ${itemData.completion_date}</p>` : ''}
        `,
            width: '800px',
            confirmButtonColor: '#667eea',
            confirmButtonText: 'Close'
        });
    }
</script>