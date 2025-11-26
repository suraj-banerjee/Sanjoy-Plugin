<!-- 
Template: VDA Public Profile Page (New Design)
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

// Get skills data
$skills_ids = !empty($profile->skills) ? json_decode($profile->skills, true) : [];
$skills = [];
if (!empty($skills_ids) && is_array($skills_ids)) {
    foreach ($skills_ids as $skill_id) {
        $skill = $wpdb->get_row($wpdb->prepare(
            "SELECT name FROM {$wpdb->prefix}skd_pl_skills WHERE id = %d",
            $skill_id
        ));
        if ($skill) {
            $skills[] = $skill->name;
        }
    }
}

// Get services offered
$services_ids = !empty($profile->services_offered) ? json_decode($profile->services_offered, true) : [];
$services = [];
if (!empty($services_ids) && is_array($services_ids)) {
    foreach ($services_ids as $service_id) {
        $service = $wpdb->get_row($wpdb->prepare(
            "SELECT name FROM {$wpdb->prefix}skd_pl_services WHERE id = %d",
            $service_id
        ));
        if ($service) {
            $services[] = $service->name;
        }
    }
}

// Get project types
$project_types_ids = !empty($profile->project_types) ? json_decode($profile->project_types, true) : [];
$project_types = [];
if (!empty($project_types_ids) && is_array($project_types_ids)) {
    foreach ($project_types_ids as $type_id) {
        $type = $wpdb->get_row($wpdb->prepare(
            "SELECT name, icon_url FROM {$wpdb->prefix}skd_pl_project_types WHERE id = %d AND status = 'active'",
            $type_id
        ));
        if ($type) {
            $project_types[] = ['name' => $type->name, 'icon' => $type->icon_url];
        }
    }
}

// Get service types
$service_types_ids = !empty($profile->service_types) ? json_decode($profile->service_types, true) : [];
$service_types = [];
if (!empty($service_types_ids) && is_array($service_types_ids)) {
    foreach ($service_types_ids as $type_id) {
        $type = $wpdb->get_row($wpdb->prepare(
            "SELECT name FROM {$wpdb->prefix}skd_pl_service_types WHERE id = %d",
            $type_id
        ));
        if ($type) {
            $service_types[] = $type->name;
        }
    }
}

// Get certifications
$certifications = $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM {$wpdb->prefix}skd_pl_user_certifications WHERE user_id = %d AND status = 'approved' ORDER BY issue_date DESC",
    $vda_id
));

// Get specializations
$specializations_ids = !empty($profile->specializations) ? json_decode($profile->specializations, true) : [];
$specializations = [];
if (!empty($specializations_ids) && is_array($specializations_ids)) {
    foreach ($specializations_ids as $spec_id) {
        $spec = $wpdb->get_row($wpdb->prepare(
            "SELECT name FROM {$wpdb->prefix}skd_pl_specializations WHERE id = %d",
            $spec_id
        ));
        if ($spec) {
            $specializations[] = $spec->name;
        }
    }
}

// Get portfolio items


// Fetch all portfolio items for the user (match dashboard logic)
$portfolio_items = $wpdb->get_results($wpdb->prepare(
    "SELECT p.*, c.name as category_name
     FROM {$wpdb->prefix}skd_pl_user_portfolio p
     LEFT JOIN {$wpdb->prefix}skd_pl_portfolio_categories c ON p.category_id = c.id
     WHERE p.user_id = %d AND p.status = 'published'
     ORDER BY p.sort_order ASC, p.created_at DESC",
    $vda_id
));

// Enhance each portfolio item with decoded fields (tags, software_used, images)
foreach ($portfolio_items as &$item) {
    $item->tags = !empty($item->tags) ? json_decode($item->tags, true) : [];
    $item->software_used = !empty($item->software_used) ? json_decode($item->software_used, true) : [];
    $item->images = !empty($item->images) ? json_decode($item->images, true) : [];
}

// Enhance each portfolio item with decoded fields
foreach ($portfolio_items as &$item) {
    // Decode JSON fields
    $item->specialization_ids = !empty($item->specialization_ids) ? json_decode($item->specialization_ids, true) : [];
    $item->software_used = !empty($item->software_used) ? json_decode($item->software_used, true) : [];
    $item->services_provided = !empty($item->services_provided) ? json_decode($item->services_provided, true) : [];
    $item->additional_images = !empty($item->additional_images) ? json_decode($item->additional_images, true) : [];

    // Get project type name if available
    $item->project_type_name = $item->project_type;
    // Get category name if available
    if (!empty($item->category_id)) {
        $cat = $wpdb->get_row($wpdb->prepare(
            "SELECT name FROM {$wpdb->prefix}skd_pl_portfolio_categories WHERE id = %d",
            $item->category_id
        ));
        $item->category_name = $cat ? $cat->name : '';
    } else {
        $item->category_name = '';
    }
    // Get service names
    $item->service_names = [];
    if (!empty($item->services_provided) && is_array($item->services_provided)) {
        foreach ($item->services_provided as $sid) {
            $srv = $wpdb->get_row($wpdb->prepare(
                "SELECT name FROM {$wpdb->prefix}skd_pl_services WHERE id = %d",
                $sid
            ));
            if ($srv) $item->service_names[] = $srv->name;
        }
    }
}

// Get timezone
$timezone_display = 'Remote';
if (!empty($profile->timezone)) {
    $tz = $wpdb->get_row($wpdb->prepare(
        "SELECT name, offset FROM {$wpdb->prefix}skd_pl_timezones WHERE id = %d",
        $profile->timezone
    ));
    if ($tz) {
        $timezone_display = $tz->name . ' • ' . $tz->offset;
    }
}

// Get availability status name
$availability_display = '';
if (!empty($profile->availability_status)) {
    $avail = $wpdb->get_row($wpdb->prepare(
        "SELECT name FROM {$wpdb->prefix}skd_pl_availability_types WHERE id = %d AND status = 'active'",
        $profile->availability_status
    ));
    if ($avail) {
        $availability_display = $avail->name;
    }
}

// Get response time name
$response_time_display = '';
if (!empty($profile->response_time)) {
    $resp = $wpdb->get_row($wpdb->prepare(
        "SELECT name FROM {$wpdb->prefix}skd_pl_response_times WHERE id = %d AND status = 'active'",
        $profile->response_time
    ));
    if ($resp) {
        $response_time_display = $resp->name;
    }
}

// Get languages spoken
$languages_display = '';
if (!empty($profile->languages_spoken)) {
    $languages_array = json_decode($profile->languages_spoken, true);
    if (is_array($languages_array) && !empty($languages_array)) {
        // Fetch language names from database
        $language_names = [];
        foreach ($languages_array as $lang_id) {
            $lang = $wpdb->get_row($wpdb->prepare(
                "SELECT name FROM {$wpdb->prefix}skd_pl_languages WHERE id = %d AND status = 'active'",
                $lang_id
            ));
            if ($lang) {
                $language_names[] = $lang->name;
            }
        }
        $languages_display = !empty($language_names) ? implode(', ', $language_names) : '';
    }
}

// Calculate stats
$total_projects = $profile->total_projects ?? 47;
$avg_rating = $profile->rating ?? 4.9;
$total_reviews = $profile->total_reviews ?? 20;
$completion_rate = $profile->job_success_rate ?? 98;

?>

<div class="skd-vda-profile-page">
    <div class="skd-vda-profile-container">

        <!-- Left Sidebar -->
        <aside class="skd-vda-sidebar">

            <!-- Pricing Card -->
            <div class="skd-vda-card skd-pricing-card">
                <h3>PRICING</h3>

                <?php if ($profile->hourly_rate): ?>
                    <div class="skd-pricing-item">
                        <span class="skd-pricing-label">Hourly Rate</span>
                        <span class="skd-pricing-value">$<?php echo number_format($profile->hourly_rate, 0); ?><span class="skd-pricing-unit">/hour</span></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($availability_display)): ?>
                    <div class="skd-pricing-item">
                        <span class="skd-pricing-label">Availability</span>
                        <span class="skd-pricing-value"><?php echo esc_html($availability_display); ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($response_time_display)): ?>
                    <div class="skd-pricing-item">
                        <span class="skd-pricing-label">Response Time</span>
                        <span class="skd-pricing-value"><?php echo esc_html($response_time_display); ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Skills & Software Card -->
            <?php if (!empty($skills)): ?>
                <div class="skd-vda-card">
                    <h3>SKILLS & SOFTWARE</h3>
                    <div class="skd-tags-list">
                        <?php foreach ($skills as $skill): ?>
                            <span class="skd-tag"><?php echo esc_html($skill); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Services Offered Card -->
            <?php if (!empty($services)): ?>
                <div class="skd-vda-card">
                    <h3>SERVICES OFFERED</h3>
                    <div class="skd-tags-list">
                        <?php foreach ($services as $service): ?>
                            <span class="skd-tag"><?php echo esc_html($service); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Project Types Card -->
            <?php if (!empty($project_types)): ?>
                <div class="skd-vda-card">
                    <h3>PROJECT TYPES</h3>
                    <div class="skd-project-types-list">
                        <?php foreach ($project_types as $type): ?>
                            <div class="skd-project-type-item">
                                <?php if (!empty($type['icon'])): ?>
                                    <iconify-icon icon="<?php echo esc_attr($type['icon']); ?>"></iconify-icon>
                                <?php endif; ?>
                                <span><?php echo esc_html($type['name']); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Service Types Card -->
            <?php if (!empty($service_types)): ?>
                <div class="skd-vda-card">
                    <h3>SERVICE TYPES</h3>
                    <div class="skd-tags-list">
                        <?php foreach ($service_types as $type): ?>
                            <span class="skd-tag"><?php echo esc_html($type); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?> <!-- Certifications Card -->
            <?php if (!empty($certifications)): ?>
                <div class="skd-vda-card">
                    <h3>CERTIFICATIONS</h3>
                    <div class="skd-cert-list">
                        <?php foreach ($certifications as $cert): ?>
                            <div class="skd-cert-item">
                                <iconify-icon icon="mdi:check-decagram" class="skd-cert-icon"></iconify-icon>
                                <span><?php echo esc_html($cert->certification_name ?? $cert->title); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Experience Card -->
            <div class="skd-vda-card">
                <h3>EXPERIENCE</h3>

                <?php if ($profile->experience_level && $profile->experience_level !== 'not-set'): ?>
                    <div class="skd-exp-item">
                        <span class="skd-exp-label">Level</span>
                        <span class="skd-exp-value"><?php echo esc_html(ucfirst(str_replace('-', ' ', $profile->experience_level))); ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($profile->years_experience): ?>
                    <div class="skd-exp-item">
                        <span class="skd-exp-label">Years</span>
                        <span class="skd-exp-value"><?php echo esc_html($profile->years_experience); ?>+ Year<?php echo $profile->years_experience > 1 ? 's' : ''; ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($profile->education_level)): ?>
                    <div class="skd-exp-item">
                        <span class="skd-exp-label">Education</span>
                        <span class="skd-exp-value"><?php echo esc_html($profile->education_level); ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($languages_display)): ?>
                    <div class="skd-exp-item">
                        <span class="skd-exp-label">Languages</span>
                        <span class="skd-exp-value"><?php echo esc_html($languages_display); ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Specializations Card -->
            <?php if (!empty($specializations)): ?>
                <div class="skd-vda-card">
                    <h3>SPECIALIZATIONS</h3>
                    <div class="skd-tags-list">
                        <?php foreach ($specializations as $spec): ?>
                            <span class="skd-tag skd-tag-spec"><?php echo esc_html($spec); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Social Links Card -->
            <?php if (!empty($profile->linkedin_url) || !empty($profile->behance_url) || !empty($profile->instagram_url) || !empty($profile->portfolio_url)): ?>
                <div class="skd-vda-card">
                    <h3>LINKS</h3>
                    <div class="skd-social-links">
                        <?php if (!empty($profile->portfolio_url)): ?>
                            <a href="<?php echo esc_url($profile->portfolio_url); ?>" target="_blank" class="skd-social-link">
                                <iconify-icon icon="mdi:briefcase"></iconify-icon>
                                <span>Portfolio</span>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($profile->linkedin_url)): ?>
                            <a href="<?php echo esc_url($profile->linkedin_url); ?>" target="_blank" class="skd-social-link">
                                <iconify-icon icon="mdi:linkedin"></iconify-icon>
                                <span>LinkedIn</span>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($profile->behance_url)): ?>
                            <a href="<?php echo esc_url($profile->behance_url); ?>" target="_blank" class="skd-social-link">
                                <iconify-icon icon="mdi:behance"></iconify-icon>
                                <span>Behance</span>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($profile->instagram_url)): ?>
                            <a href="<?php echo esc_url($profile->instagram_url); ?>" target="_blank" class="skd-social-link">
                                <iconify-icon icon="mdi:instagram"></iconify-icon>
                                <span>Instagram</span>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($profile->pinterest_url)): ?>
                            <a href="<?php echo esc_url($profile->pinterest_url); ?>" target="_blank" class="skd-social-link">
                                <iconify-icon icon="mdi:pinterest"></iconify-icon>
                                <span>Pinterest</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

        </aside>

        <!-- Main Content -->
        <main class="skd-vda-main">

            <!-- Profile Header -->
            <div class="skd-vda-header">
                <div class="skd-vda-header-left">
                    <div class="skd-vda-avatar">
                        <?php if (!empty($profile->avatar_url)): ?>
                            <img src="<?php echo esc_url($profile->avatar_url); ?>" alt="<?php echo esc_attr($user->display_name); ?>">
                        <?php else: ?>
                            <div class="skd-avatar-placeholder">
                                <?php echo esc_html(strtoupper(substr($user->display_name, 0, 1))); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="skd-vda-header-info">
                        <h1><?php echo esc_html($user->display_name); ?>
                            <?php if ($profile->is_verified): ?>
                                <iconify-icon icon="mdi:check-decagram" class="skd-verified-icon"></iconify-icon>
                            <?php endif; ?>
                        </h1>
                        <p class="skd-vda-tagline"><?php echo esc_html($profile->tagline ?? 'Professional Designer'); ?></p>

                        <!-- Badges -->
                        <?php if ($profile->is_featured || $profile->is_top_rated): ?>
                            <div class="skd-vda-badges">
                                <?php if ($profile->is_featured): ?>
                                    <span class="skd-badge skd-badge-featured">
                                        <iconify-icon icon="mdi:star"></iconify-icon> Featured
                                    </span>
                                <?php endif; ?>
                                <?php if ($profile->is_top_rated): ?>
                                    <span class="skd-badge skd-badge-top-rated">
                                        <iconify-icon icon="mdi:trophy"></iconify-icon> Top Rated
                                    </span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <div class="skd-vda-meta">
                            <span class="skd-vda-location">
                                <iconify-icon icon="mdi:map-marker"></iconify-icon>
                                <?php echo esc_html($timezone_display); ?>
                            </span>
                            <span class="skd-vda-rate">
                                <iconify-icon icon="mdi:currency-usd"></iconify-icon>
                                $<?php echo number_format($profile->hourly_rate ?? 0, 0); ?>/hour
                            </span>
                        </div>
                    </div>
                </div>
                <div class="skd-vda-header-right">
                    <button class="skd-btn skd-btn-message">
                        <iconify-icon icon="mdi:message"></iconify-icon>
                        Message
                    </button>
                    <button class="skd-btn skd-btn-save">
                        <iconify-icon icon="mdi:bookmark-outline"></iconify-icon>
                        Save
                    </button>
                </div>
            </div>

            <!-- Short Description (Under Avatar) -->
            <?php if (!empty($profile->short_description)): ?>
                <div class="skd-vda-section">
                    <p class="skd-vda-bio"><?php echo nl2br(esc_html($profile->short_description)); ?></p>
                </div>
            <?php endif; ?>

            <!-- Stats Bar -->
            <div class="skd-vda-stats-bar">
                <div class="skd-stat">
                    <div class="skd-stat-value"><?php echo $total_projects; ?></div>
                    <div class="skd-stat-label">Projects</div>
                </div>
                <div class="skd-stat">
                    <div class="skd-stat-value"><?php echo number_format($avg_rating, 1); ?></div>
                    <div class="skd-stat-label">Ratings</div>
                </div>
                <div class="skd-stat">
                    <div class="skd-stat-value"><?php echo $completion_rate; ?>%</div>
                    <div class="skd-stat-label">Job Success</div>
                </div>
            </div>

            <!-- About Me Section -->
            <?php if (!empty($profile->bio)): ?>
                <div class="skd-vda-section">
                    <div class="skd-section-header">
                        <iconify-icon icon="mdi:account" class="skd-section-icon"></iconify-icon>
                        <h2>About Me</h2>
                    </div>
                    <div class="skd-about-content">
                        <?php echo wpautop($profile->bio); ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- What I Offer Section -->
            <?php if (!empty($profile->what_i_offer)): ?>
                <div class="skd-vda-section">
                    <div class="skd-section-header">
                        <iconify-icon icon="mdi:star" class="skd-section-icon"></iconify-icon>
                        <h2>What I Offer</h2>
                    </div>
                    <div class="skd-offer-content">
                        <?php echo wpautop($profile->what_i_offer); ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Portfolio Section -->
            <?php if (!empty($portfolio_items)): ?>
                <div class="skd-vda-section">
                    <div class="skd-section-header">
                        <iconify-icon icon="mdi:briefcase" class="skd-section-icon"></iconify-icon>
                        <h2>Portfolio</h2>
                    </div>
                    <div class="skd-portfolio-grid">
                        <?php foreach ($portfolio_items as $item): ?>
                            <?php
                            $placeholderSvg = 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22300%22%3E%3Crect fill=%22%23ddd%22 width=%22400%22 height=%22300%22/%3E%3Ctext fill=%22%23999%22 font-family=%22sans-serif%22 font-size=%2220%22 dy=%2210.5%22 font-weight=%22bold%22 x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22%3ENo Image%3C/text%3E%3C/svg%3E';
                            $imageUrl = !empty($item->featured_image) ? $item->featured_image : (!empty($item->images[0]) ? $item->images[0] : $placeholderSvg);
                            $desc = '';
                            if (!empty($item->description)) {
                                $stripped = strip_tags($item->description);
                                $truncated = mb_substr($stripped, 0, 120);
                                $ellipsis = mb_strlen($stripped) > 120 ? '...' : '';
                                $desc = '<p class="vda-portfolio-desc">' . esc_html($truncated . $ellipsis) . '</p>';
                            }
                            $tagsHtml = '';
                            if (!empty($item->tags) && is_array($item->tags)) {
                                $tagsHtml = '<div class="vda-portfolio-tags">';
                                $shown = 0;
                                foreach ($item->tags as $tag) {
                                    if ($shown < 3) {
                                        $tagsHtml .= '<span class="vda-tag">' . esc_html($tag) . '</span>';
                                    }
                                    $shown++;
                                }
                                if ($shown > 3) {
                                    $tagsHtml .= '<span class="vda-tag">+' . ($shown - 3) . '</span>';
                                }
                                $tagsHtml .= '</div>';
                            }
                            $yearHtml = !empty($item->year) ? '<span><iconify-icon icon="material-symbols:calendar-month-outline"></iconify-icon> ' . esc_html($item->year) . '</span>' : '';
                            $categoryBadge = !empty($item->category_name) ? '<span class="vda-category-badge">' . esc_html($item->category_name) . '</span>' : '';
                            ?>
                            <div class="vda-portfolio-card">
                                <div class="vda-portfolio-image">
                                    <img src="<?php echo esc_attr($imageUrl); ?>" alt="<?php echo esc_attr($item->title); ?>">
                                    <?php echo $categoryBadge; ?>
                                </div>
                                <div class="vda-portfolio-content">
                                    <h4><?php echo esc_html($item->title); ?></h4>
                                    <?php echo $desc; ?>
                                    <?php echo $tagsHtml; ?>
                                    <div class="vda-portfolio-meta">
                                        <?php echo $yearHtml; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Reviews Section -->
            <div class="skd-vda-section">
                <div class="skd-section-header">
                    <iconify-icon icon="mdi:star-box" class="skd-section-icon"></iconify-icon>
                    <h2>Reviews (<?php echo $total_reviews; ?>)</h2>
                </div>

                <!-- Reviews List -->
                <div class="skd-reviews-list">
                    <?php
                    $sample_reviews = [
                        [
                            'name' => 'Michelle Rodriguez',
                            'date' => '2 weeks ago',
                            'rating' => 5,
                            'text' => 'Sarah was absolutely amazing to work with! Her 3D renderings helped us visualize our kitchen remodel perfectly. She was responsive, professional, and delivered exactly what we wanted. Highly recommend!'
                        ],
                        [
                            'name' => 'James Lee',
                            'date' => '1 month ago',
                            'rating' => 5,
                            'text' => 'Professional, creative, and talented! Sarah created beautiful renderings for our home office renovation. Her attention to detail and design sense are top-notch. Will definitely hire again.'
                        ],
                        [
                            'name' => 'Amanda Chen',
                            'date' => '2 months ago',
                            'rating' => 5,
                            'text' => 'Sarah transformed our outdated bathroom into a modern spa-like retreat. Her 3D visualizations were so realistic, and the final result matched perfectly. Great communication throughout the project!'
                        ],
                    ];

                    foreach ($sample_reviews as $review): ?>
                        <div class="skd-review-card">
                            <div class="skd-review-header">
                                <div class="skd-reviewer-avatar">
                                    <?php echo esc_html(strtoupper(substr($review['name'], 0, 1))); ?>
                                </div>
                                <div class="skd-reviewer-info">
                                    <h4 class="skd-reviewer-name"><?php echo esc_html($review['name']); ?></h4>
                                    <div class="skd-review-meta">
                                        <div class="skd-review-stars">
                                            <?php for ($i = 0; $i < 5; $i++): ?>
                                                <iconify-icon icon="<?php echo $i < $review['rating'] ? 'mdi:star' : 'mdi:star-outline'; ?>"></iconify-icon>
                                            <?php endfor; ?>
                                        </div>
                                        <span class="skd-review-date"><?php echo esc_html($review['date']); ?></span>
                                    </div>
                                </div>
                            </div>
                            <p class="skd-review-text"><?php echo esc_html($review['text']); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </main>

    </div>
</div>