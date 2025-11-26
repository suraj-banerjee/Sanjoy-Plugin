<?php

/**
 * Template for Find Assistants Directory
 * Displays VDA professionals with filtering and search
 * Matches HTML design from Sample Site Layout
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get filter data
global $wpdb;

// Get skills for filters
$skills_table = $wpdb->prefix . 'skd_pl_skills';
$skills = $wpdb->get_results("SELECT * FROM $skills_table ORDER BY is_featured DESC, name ASC LIMIT 50");

// Get services for filters  
$services_table = $wpdb->prefix . 'skd_pl_services';
$services = $wpdb->get_results("SELECT * FROM $services_table ORDER BY is_popular DESC, name ASC LIMIT 30");

// Get specializations for filters
$specializations_table = $wpdb->prefix . 'skd_pl_specializations';
$specializations = $wpdb->get_results("SELECT * FROM $specializations_table ORDER BY name ASC");

// Get locations for filters
$locations_table = $wpdb->prefix . 'skd_pl_locations';
$locations = $wpdb->get_results("SELECT * FROM $locations_table WHERE parent_id = 0 ORDER BY name ASC");

// Get certifications for filters
$certifications_table = $wpdb->prefix . 'skd_pl_certifications';
$certifications = $wpdb->get_results("SELECT * FROM $certifications_table ORDER BY issuer, name ASC LIMIT 20");

// Initial professionals load - get VDA users with profiles
$profiles_table = $wpdb->prefix . 'skd_pl_user_profiles';
$users_table = $wpdb->prefix . 'users';

$initial_query = $wpdb->prepare("
    SELECT 
        p.*,
        u.display_name,
        u.user_email,
        u.ID as user_id
    FROM $profiles_table p
    INNER JOIN $users_table u ON p.user_id = u.ID
    WHERE p.user_type IN ('vda', 'studio')
    AND p.status = 'active'
    ORDER BY p.is_featured DESC, p.is_verified DESC, p.rating DESC, p.created_at DESC
    LIMIT %d
", 24);

$professionals = $wpdb->get_results($initial_query);
?>

<div class="skd-find-assistants-wrapper">
    <div class="skd-container">

        <!-- Page Header -->
        <div class="skd-directory-header">
            <h1>Find Virtual Design Assistants</h1>
            <p class="skd-subtitle">Connect with talented professionals for your interior design projects</p>
        </div>

        <div class="skd-directory-layout">

            <!-- Filter Sidebar -->
            <aside class="skd-filters-sidebar">
                <div class="skd-filters-header">
                    <h3>Filters</h3>
                    <button type="button" class="skd-clear-filters">Clear All</button>
                </div>

                <form id="skd-professionals-filter-form">

                    <!-- Search -->
                    <div class="skd-filter-section">
                        <input
                            type="text"
                            name="search"
                            id="skd-search-professionals"
                            class="skd-search-input"
                            placeholder="Search by name, skills, or location...">
                    </div>

                    <!-- Software/Skills Filter -->
                    <div class="skd-filter-section skd-collapsible">
                        <h4 class="skd-filter-title">
                            Software & Skills
                            <span class="skd-toggle-icon">−</span>
                        </h4>
                        <div class="skd-filter-content">
                            <?php if (!empty($skills)): ?>
                                <div class="skd-filter-options skd-scrollable">
                                    <?php foreach ($skills as $skill): ?>
                                        <label class="skd-filter-checkbox">
                                            <input type="checkbox" name="skills[]" value="<?php echo esc_attr($skill->id); ?>">
                                            <span class="skd-checkbox-label">
                                                <?php echo esc_html($skill->name); ?>
                                                <?php if ($skill->is_featured): ?>
                                                    <span class="skd-badge-featured">Popular</span>
                                                <?php endif; ?>
                                            </span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Experience Level Filter -->
                    <div class="skd-filter-section skd-collapsible">
                        <h4 class="skd-filter-title">
                            Experience Level
                            <span class="skd-toggle-icon">−</span>
                        </h4>
                        <div class="skd-filter-content">
                            <label class="skd-filter-checkbox">
                                <input type="checkbox" name="experience[]" value="entry">
                                <span class="skd-checkbox-label">Entry Level (0-2 years)</span>
                            </label>
                            <label class="skd-filter-checkbox">
                                <input type="checkbox" name="experience[]" value="intermediate">
                                <span class="skd-checkbox-label">Intermediate (2-5 years)</span>
                            </label>
                            <label class="skd-filter-checkbox">
                                <input type="checkbox" name="experience[]" value="expert">
                                <span class="skd-checkbox-label">Expert (5-10 years)</span>
                            </label>
                            <label class="skd-filter-checkbox">
                                <input type="checkbox" name="experience[]" value="senior">
                                <span class="skd-checkbox-label">Senior Expert (10+ years)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Pricing Filter -->
                    <div class="skd-filter-section skd-collapsible">
                        <h4 class="skd-filter-title">
                            Hourly Rate
                            <span class="skd-toggle-icon">−</span>
                        </h4>
                        <div class="skd-filter-content">
                            <div class="skd-price-range">
                                <div class="skd-price-inputs">
                                    <input
                                        type="number"
                                        name="min_rate"
                                        placeholder="Min"
                                        min="0"
                                        step="5"
                                        class="skd-price-input">
                                    <span class="skd-price-separator">-</span>
                                    <input
                                        type="number"
                                        name="max_rate"
                                        placeholder="Max"
                                        min="0"
                                        step="5"
                                        class="skd-price-input">
                                </div>
                                <div class="skd-price-presets">
                                    <button type="button" class="skd-price-preset" data-min="0" data-max="25">$0-$25</button>
                                    <button type="button" class="skd-price-preset" data-min="25" data-max="50">$25-$50</button>
                                    <button type="button" class="skd-price-preset" data-min="50" data-max="100">$50-$100</button>
                                    <button type="button" class="skd-price-preset" data-min="100" data-max="999">$100+</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Location Filter -->
                    <div class="skd-filter-section skd-collapsible">
                        <h4 class="skd-filter-title">
                            Location & Timezone
                            <span class="skd-toggle-icon">−</span>
                        </h4>
                        <div class="skd-filter-content">
                            <?php if (!empty($locations)): ?>
                                <div class="skd-filter-options skd-scrollable">
                                    <?php foreach ($locations as $location): ?>
                                        <label class="skd-filter-checkbox">
                                            <input type="checkbox" name="locations[]" value="<?php echo esc_attr($location->id); ?>">
                                            <span class="skd-checkbox-label"><?php echo esc_html($location->name); ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Verification & Badges Filter -->
                    <div class="skd-filter-section skd-collapsible">
                        <h4 class="skd-filter-title">
                            Verification & Badges
                            <span class="skd-toggle-icon">−</span>
                        </h4>
                        <div class="skd-filter-content">
                            <label class="skd-filter-checkbox">
                                <input type="checkbox" name="verified" value="1">
                                <span class="skd-checkbox-label">
                                    <iconify-icon icon="mdi:check-decagram" class="skd-icon-verified"></iconify-icon>
                                    Verified Professionals
                                </span>
                            </label>
                            <label class="skd-filter-checkbox">
                                <input type="checkbox" name="top_rated" value="1">
                                <span class="skd-checkbox-label">
                                    <iconify-icon icon="mdi:star" class="skd-icon-star"></iconify-icon>
                                    Top Rated (4.5+)
                                </span>
                            </label>
                            <label class="skd-filter-checkbox">
                                <input type="checkbox" name="featured" value="1">
                                <span class="skd-checkbox-label">
                                    <iconify-icon icon="mdi:crown" class="skd-icon-featured"></iconify-icon>
                                    Featured
                                </span>
                            </label>
                            <label class="skd-filter-checkbox">
                                <input type="checkbox" name="rising_talent" value="1">
                                <span class="skd-checkbox-label">
                                    <iconify-icon icon="mdi:trending-up" class="skd-icon-rising"></iconify-icon>
                                    Rising Talent
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Services Filter -->
                    <div class="skd-filter-section skd-collapsible">
                        <h4 class="skd-filter-title">
                            Services Offered
                            <span class="skd-toggle-icon">−</span>
                        </h4>
                        <div class="skd-filter-content">
                            <?php if (!empty($services)): ?>
                                <div class="skd-filter-options skd-scrollable">
                                    <?php foreach ($services as $service): ?>
                                        <label class="skd-filter-checkbox">
                                            <input type="checkbox" name="services[]" value="<?php echo esc_attr($service->id); ?>">
                                            <span class="skd-checkbox-label">
                                                <?php echo esc_html($service->name); ?>
                                                <?php if ($service->is_popular): ?>
                                                    <span class="skd-badge-popular">Popular</span>
                                                <?php endif; ?>
                                            </span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Specializations Filter -->
                    <div class="skd-filter-section skd-collapsible">
                        <h4 class="skd-filter-title">
                            Specializations
                            <span class="skd-toggle-icon">−</span>
                        </h4>
                        <div class="skd-filter-content">
                            <?php if (!empty($specializations)): ?>
                                <div class="skd-filter-options skd-scrollable">
                                    <?php foreach ($specializations as $specialization): ?>
                                        <label class="skd-filter-checkbox">
                                            <input type="checkbox" name="specializations[]" value="<?php echo esc_attr($specialization->id); ?>">
                                            <span class="skd-checkbox-label"><?php echo esc_html($specialization->name); ?></span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Availability Filter -->
                    <div class="skd-filter-section skd-collapsible">
                        <h4 class="skd-filter-title">
                            Availability
                            <span class="skd-toggle-icon">−</span>
                        </h4>
                        <div class="skd-filter-content">
                            <label class="skd-filter-checkbox">
                                <input type="checkbox" name="availability[]" value="available_now">
                                <span class="skd-checkbox-label">Available Now</span>
                            </label>
                            <label class="skd-filter-checkbox">
                                <input type="checkbox" name="availability[]" value="full_time">
                                <span class="skd-checkbox-label">Full-time (40+ hrs/week)</span>
                            </label>
                            <label class="skd-filter-checkbox">
                                <input type="checkbox" name="availability[]" value="part_time">
                                <span class="skd-checkbox-label">Part-time (&lt;30 hrs/week)</span>
                            </label>
                            <label class="skd-filter-checkbox">
                                <input type="checkbox" name="availability[]" value="hourly">
                                <span class="skd-checkbox-label">Hourly / As Needed</span>
                            </label>
                        </div>
                    </div>

                    <!-- User Type Filter -->
                    <div class="skd-filter-section skd-collapsible">
                        <h4 class="skd-filter-title">
                            Professional Type
                            <span class="skd-toggle-icon">−</span>
                        </h4>
                        <div class="skd-filter-content">
                            <label class="skd-filter-checkbox">
                                <input type="checkbox" name="user_type[]" value="vda">
                                <span class="skd-checkbox-label">Individual VDAs</span>
                            </label>
                            <label class="skd-filter-checkbox">
                                <input type="checkbox" name="user_type[]" value="studio">
                                <span class="skd-checkbox-label">Studios/Agencies</span>
                            </label>
                        </div>
                    </div>

                    <!-- Apply Button -->
                    <div class="skd-filter-actions">
                        <button type="submit" class="skd-btn skd-btn-primary skd-apply-filters">
                            Apply Filters
                        </button>
                    </div>

                </form>
            </aside>

            <!-- Main Content Area -->
            <main class="skd-professionals-content">

                <!-- Results Header -->
                <div class="skd-results-header">
                    <div class="skd-results-info">
                        <span class="skd-results-count" id="skd-professionals-count">
                            <?php echo count($professionals); ?> professionals found
                        </span>
                    </div>
                    <div class="skd-results-controls">
                        <select name="sort_by" id="skd-sort-professionals" class="skd-sort-select">
                            <option value="relevance">Sort by: Relevance</option>
                            <option value="rating_desc">Highest Rated</option>
                            <option value="rate_asc">Lowest Rate</option>
                            <option value="rate_desc">Highest Rate</option>
                            <option value="newest">Newest First</option>
                            <option value="experience_desc">Most Experienced</option>
                        </select>
                        <div class="skd-view-toggle">
                            <button type="button" class="skd-view-btn active" data-view="grid">
                                <iconify-icon icon="mdi:view-grid"></iconify-icon>
                            </button>
                            <button type="button" class="skd-view-btn" data-view="list">
                                <iconify-icon icon="mdi:view-list"></iconify-icon>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Professionals Grid -->
                <div class="skd-professionals-grid" id="skd-professionals-grid">
                    <?php if (!empty($professionals)): ?>
                        <?php foreach ($professionals as $professional): ?>
                            <?php
                            // Get user meta
                            $avatar_url = get_avatar_url($professional->user_id, ['size' => 200]);

                            // Get skills
                            $user_skills = $wpdb->get_results($wpdb->prepare("
                                SELECT s.name 
                                FROM {$wpdb->prefix}skd_pl_user_skills us
                                INNER JOIN {$wpdb->prefix}skd_pl_skills s ON us.skill_id = s.id
                                WHERE us.user_id = %d
                                ORDER BY us.proficiency DESC
                                LIMIT 5
                            ", $professional->user_id));

                            // Get portfolio count
                            $portfolio_count = $wpdb->get_var($wpdb->prepare("
                                SELECT COUNT(*) 
                                FROM {$wpdb->prefix}skd_pl_portfolio 
                                WHERE user_id = %d AND status = 'published'
                            ", $professional->user_id));

                            // Get sample portfolio images
                            $portfolio_samples = $wpdb->get_results($wpdb->prepare("
                                SELECT featured_image 
                                FROM {$wpdb->prefix}skd_pl_portfolio 
                                WHERE user_id = %d AND status = 'published' AND featured_image IS NOT NULL
                                ORDER BY views DESC, likes DESC
                                LIMIT 3
                            ", $professional->user_id));
                            ?>

                            <div class="skd-professional-card" data-user-id="<?php echo esc_attr($professional->user_id); ?>">

                                <!-- Card Header -->
                                <div class="skd-card-header">
                                    <div class="skd-card-avatar">
                                        <img src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr($professional->display_name); ?>">
                                        <?php if ($professional->is_verified): ?>
                                            <span class="skd-verified-badge" title="Verified Professional">
                                                <iconify-icon icon="mdi:check-decagram"></iconify-icon>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <?php if ($professional->is_featured): ?>
                                        <span class="skd-featured-ribbon">Featured</span>
                                    <?php endif; ?>
                                </div>

                                <!-- Card Body -->
                                <div class="skd-card-body">

                                    <h3 class="skd-card-name">
                                        <a href="<?php echo esc_url(add_query_arg('profile_id', $professional->user_id, get_permalink())); ?>">
                                            <?php echo esc_html($professional->display_name); ?>
                                        </a>
                                    </h3>

                                    <?php if ($professional->user_type === 'studio'): ?>
                                        <span class="skd-card-type skd-type-studio">
                                            <iconify-icon icon="mdi:office-building"></iconify-icon>
                                            Studio/Agency
                                        </span>
                                    <?php endif; ?>

                                    <?php if ($professional->tagline): ?>
                                        <p class="skd-card-tagline"><?php echo esc_html($professional->tagline); ?></p>
                                    <?php endif; ?>

                                    <!-- Rating & Stats -->
                                    <div class="skd-card-stats">
                                        <?php if ($professional->rating > 0): ?>
                                            <div class="skd-rating">
                                                <span class="skd-rating-stars">
                                                    <?php
                                                    $rating = floatval($professional->rating);
                                                    for ($i = 1; $i <= 5; $i++) {
                                                        if ($i <= floor($rating)) {
                                                            echo '<iconify-icon icon="mdi:star"></iconify-icon>';
                                                        } elseif ($i - 0.5 <= $rating) {
                                                            echo '<iconify-icon icon="mdi:star-half-full"></iconify-icon>';
                                                        } else {
                                                            echo '<iconify-icon icon="mdi:star-outline"></iconify-icon>';
                                                        }
                                                    }
                                                    ?>
                                                </span>
                                                <span class="skd-rating-value"><?php echo number_format($rating, 1); ?></span>
                                                <span class="skd-rating-count">(<?php echo intval($professional->total_reviews); ?>)</span>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Location & Experience -->
                                    <div class="skd-card-meta">
                                        <?php if ($professional->city || $professional->country): ?>
                                            <span class="skd-meta-item skd-location">
                                                <iconify-icon icon="mdi:map-marker"></iconify-icon>
                                                <?php echo esc_html(trim($professional->city . ', ' . $professional->country, ', ')); ?>
                                            </span>
                                        <?php endif; ?>

                                        <?php if ($professional->experience_level): ?>
                                            <span class="skd-meta-item skd-experience">
                                                <iconify-icon icon="mdi:briefcase"></iconify-icon>
                                                <?php echo esc_html(ucfirst(str_replace('_', ' ', $professional->experience_level))); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Hourly Rate -->
                                    <?php if ($professional->hourly_rate > 0): ?>
                                        <div class="skd-card-rate">
                                            <span class="skd-rate-amount">$<?php echo number_format($professional->hourly_rate, 0); ?></span>
                                            <span class="skd-rate-unit">/hour</span>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Skills Tags -->
                                    <?php if (!empty($user_skills)): ?>
                                        <div class="skd-card-skills">
                                            <?php foreach ($user_skills as $skill): ?>
                                                <span class="skd-skill-tag"><?php echo esc_html($skill->name); ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Portfolio Preview -->
                                    <?php if (!empty($portfolio_samples)): ?>
                                        <div class="skd-card-portfolio-preview">
                                            <?php foreach ($portfolio_samples as $sample): ?>
                                                <?php if ($sample->featured_image): ?>
                                                    <div class="skd-portfolio-thumb">
                                                        <img src="<?php echo esc_url($sample->featured_image); ?>" alt="Portfolio sample">
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            <?php if ($portfolio_count > 3): ?>
                                                <div class="skd-portfolio-more">
                                                    +<?php echo ($portfolio_count - 3); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>

                                </div>

                                <!-- Card Footer -->
                                <div class="skd-card-footer">
                                    <a href="<?php echo esc_url(home_url('/vda-profile/?vda_id=' . $professional->user_id)); ?>"
                                        class="skd-btn skd-btn-outline skd-btn-sm">
                                        View Profile
                                    </a>
                                    <button type="button"
                                        class="skd-btn skd-btn-primary skd-btn-sm skd-contact-btn"
                                        data-user-id="<?php echo esc_attr($professional->user_id); ?>">
                                        <iconify-icon icon="mdi:message-text"></iconify-icon>
                                        Contact
                                    </button>
                                </div>

                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="skd-no-results">
                            <iconify-icon icon="mdi:account-search" class="skd-no-results-icon"></iconify-icon>
                            <h3>No professionals found</h3>
                            <p>Try adjusting your filters or search criteria</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Loading State -->
                <div class="skd-loading-overlay" id="skd-professionals-loading" style="display: none;">
                    <div class="skd-spinner"></div>
                    <p>Loading professionals...</p>
                </div>

                <!-- Pagination -->
                <div class="skd-pagination" id="skd-professionals-pagination">
                    <!-- Pagination will be inserted here via AJAX -->
                </div>

            </main>

        </div>
    </div>
</div>