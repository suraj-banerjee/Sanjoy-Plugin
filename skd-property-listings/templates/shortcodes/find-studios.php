<?php

/**
 * Template for Find Studios Directory
 * Displays Studio/Agency profiles with filtering and search
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get filter data
global $wpdb;

// Get skills for filters
$skills_table = $wpdb->prefix . 'skd_pl_skills';
$skills = $wpdb->get_results("SELECT * FROM $skills_table WHERE status = 'active' ORDER BY name ASC");

// Get services for filters  
$services_table = $wpdb->prefix . 'skd_pl_services';
$services = $wpdb->get_results("SELECT * FROM $services_table WHERE status = 'active' ORDER BY name ASC");

// Get specializations for filters
$specializations_table = $wpdb->prefix . 'skd_pl_specializations';
$specializations = $wpdb->get_results("SELECT * FROM $specializations_table WHERE status = 'active' ORDER BY name ASC");

// Get locations for filters
$locations_table = $wpdb->prefix . 'skd_pl_locations';
$locations = $wpdb->get_results("SELECT * FROM $locations_table ORDER BY name ASC");

// Initial studios load
$listings_table = $wpdb->prefix . 'skd_pl_listings';
$initial_studios = $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM $listings_table 
     WHERE listing_status = 'publish' AND user_role = 'studio' 
     ORDER BY is_feature DESC, created_at DESC 
     LIMIT %d",
    $atts['per_page']
));

$total_count = $wpdb->get_var("SELECT COUNT(*) FROM $listings_table WHERE listing_status = 'publish' AND user_role = 'studio'");
?>

<div class="skd-find-studios-wrapper">
    <!-- Hero Section -->
    <div class="skd-hero-section">
        <div class="skd-hero-content">
            <h1>Find Design Studios & Agencies</h1>
            <p>Connect with professional design studios offering comprehensive design services and team-based solutions.</p>
        </div>
    </div>

    <div class="skd-directory-layout">
        <!-- Sidebar Filters -->
        <aside class="skd-filters-sidebar">
            <button class="skd-filter-toggle mobile-only" onclick="toggleFilters()">
                <span class="dashicons dashicons-filter"></span>
                Show Filters
            </button>

            <div class="skd-filters" id="studioFilters">
                <div class="skd-filter-header">
                    <h3>Filter Studios</h3>
                    <button class="skd-clear-filters" onclick="clearAllFilters()">Clear All</button>
                </div>

                <!-- Search -->
                <div class="skd-filter-section">
                    <h4>Search</h4>
                    <input type="text" id="searchInput" class="skd-filter-input" placeholder="Search studios, services, or specializations...">
                </div>

                <!-- Studio Size -->
                <div class="skd-filter-section">
                    <h4>Studio Size</h4>
                    <div class="skd-filter-group">
                        <label class="skd-filter-radio">
                            <input type="radio" name="team_size" value="small">
                            <span class="radiomark"></span>
                            Small (1-5 people)
                        </label>
                        <label class="skd-filter-radio">
                            <input type="radio" name="team_size" value="medium">
                            <span class="radiomark"></span>
                            Medium (6-20 people)
                        </label>
                        <label class="skd-filter-radio">
                            <input type="radio" name="team_size" value="large">
                            <span class="radiomark"></span>
                            Large (20+ people)
                        </label>
                    </div>
                </div>

                <!-- Services -->
                <div class="skd-filter-section">
                    <h4>Services Offered</h4>
                    <div class="skd-filter-group skd-checkbox-group" data-max-height="200">
                        <?php foreach ($services as $service): ?>
                            <label class="skd-filter-checkbox">
                                <input type="checkbox" name="services[]" value="<?php echo esc_attr($service->id); ?>">
                                <span class="checkmark"></span>
                                <?php echo esc_html($service->name); ?>
                                <?php if ($service->is_popular): ?>
                                    <span class="popular-badge">Popular</span>
                                <?php endif; ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Specializations -->
                <div class="skd-filter-section">
                    <h4>Design Specializations</h4>
                    <div class="skd-filter-group skd-checkbox-group" data-max-height="200">
                        <?php foreach ($specializations as $spec): ?>
                            <label class="skd-filter-checkbox">
                                <input type="checkbox" name="specializations[]" value="<?php echo esc_attr($spec->id); ?>">
                                <span class="checkmark"></span>
                                <?php echo esc_html($spec->name); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Founded Year Range -->
                <div class="skd-filter-section">
                    <h4>Experience (Years in Business)</h4>
                    <div class="skd-filter-group">
                        <label class="skd-filter-radio">
                            <input type="radio" name="experience_years" value="new">
                            <span class="radiomark"></span>
                            New (0-2 years)
                        </label>
                        <label class="skd-filter-radio">
                            <input type="radio" name="experience_years" value="established">
                            <span class="radiomark"></span>
                            Established (3-10 years)
                        </label>
                        <label class="skd-filter-radio">
                            <input type="radio" name="experience_years" value="veteran">
                            <span class="radiomark"></span>
                            Veteran (10+ years)
                        </label>
                    </div>
                </div>

                <!-- Location -->
                <div class="skd-filter-section">
                    <h4>Location</h4>
                    <select id="locationFilter" class="skd-filter-select">
                        <option value="">All Locations</option>
                        <?php foreach ($locations as $location): ?>
                            <option value="<?php echo esc_attr($location->id); ?>">
                                <?php echo esc_html($location->name); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Apply Filters Button -->
                <button class="skd-apply-filters-btn" onclick="applyFilters()">
                    Apply Filters
                </button>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="skd-main-content">
            <!-- Active Filters Display -->
            <div class="skd-active-filters" id="activeFilters"></div>

            <!-- Sort Bar -->
            <div class="skd-sort-bar">
                <div class="skd-results-count">
                    <span id="resultsCount">Showing <strong><?php echo count($initial_studios); ?></strong> of <strong><?php echo $total_count; ?></strong> studios</span>
                </div>
                <div class="skd-sort-controls">
                    <label for="sortSelect">Sort by:</label>
                    <select id="sortSelect" class="skd-sort-select">
                        <option value="recent">Most Recent</option>
                        <option value="rating">Highest Rated</option>
                        <option value="experience">Most Experienced</option>
                        <option value="team_size">Largest Team</option>
                        <option value="projects">Most Projects</option>
                    </select>
                </div>
            </div>

            <!-- Studios Grid -->
            <div class="skd-studios-grid" id="studiosGrid">
                <?php foreach ($initial_studios as $studio):
                    $skills_data = json_decode($studio->skills, true) ?: [];
                    $services_data = json_decode($studio->services, true) ?: [];
                    $portfolio_images = json_decode($studio->portfolio_images, true) ?: [];
                    $years_in_business = $studio->founded_year ? date('Y') - $studio->founded_year : 0;
                ?>
                    <div class="skd-studio-card" data-studio-id="<?php echo $studio->id; ?>">
                        <div class="skd-card-header">
                            <div class="skd-studio-logo">
                                <?php if ($studio->skd_logo): ?>
                                    <img src="<?php echo esc_url($studio->skd_logo); ?>" alt="<?php echo esc_attr($studio->listing_title); ?>">
                                <?php else: ?>
                                    <div class="skd-logo-placeholder">
                                        <?php echo strtoupper(substr($studio->listing_title, 0, 2)); ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="skd-studio-badges">
                                <?php if ($studio->is_verified): ?>
                                    <span class="skd-badge verified">
                                        <span class="dashicons dashicons-yes-alt"></span>
                                        Verified
                                    </span>
                                <?php endif; ?>

                                <?php if ($studio->is_feature): ?>
                                    <span class="skd-badge featured">
                                        <span class="dashicons dashicons-star-filled"></span>
                                        Featured
                                    </span>
                                <?php endif; ?>

                                <?php if ($studio->total_projects >= 100): ?>
                                    <span class="skd-badge top-rated">
                                        <span class="dashicons dashicons-awards"></span>
                                        Top Studio
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="skd-card-body">
                            <h3 class="skd-studio-name">
                                <a href="<?php echo home_url("/studio/{$studio->slug}/"); ?>">
                                    <?php echo esc_html($studio->listing_title); ?>
                                </a>
                            </h3>

                            <div class="skd-studio-meta">
                                <div class="skd-studio-info">
                                    <span class="skd-team-size">
                                        <span class="dashicons dashicons-groups"></span>
                                        <?php echo esc_html($studio->team_size); ?> team members
                                    </span>

                                    <?php if ($years_in_business > 0): ?>
                                        <span class="skd-experience">
                                            <span class="dashicons dashicons-calendar-alt"></span>
                                            <?php echo $years_in_business; ?> years in business
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <div class="skd-rating">
                                    <?php if ($studio->rating > 0): ?>
                                        <span class="skd-stars">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <span class="dashicons <?php echo $i <= $studio->rating ? 'dashicons-star-filled' : 'dashicons-star-empty'; ?>"></span>
                                            <?php endfor; ?>
                                        </span>
                                        <span class="skd-rating-text"><?php echo number_format($studio->rating, 1); ?> (<?php echo $studio->total_reviews; ?> reviews)</span>
                                    <?php else: ?>
                                        <span class="skd-rating-text">No reviews yet</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="skd-location-projects">
                                <span class="skd-location">
                                    <span class="dashicons dashicons-location"></span>
                                    <?php echo esc_html($studio->list_address ?: 'Remote'); ?>
                                </span>
                                <span class="skd-projects">
                                    <span class="dashicons dashicons-portfolio"></span>
                                    <?php echo $studio->total_projects; ?> projects completed
                                </span>
                            </div>

                            <p class="skd-description">
                                <?php echo esc_html(wp_trim_words($studio->listing_description, 25)); ?>
                            </p>

                            <!-- Services Offered -->
                            <?php if (!empty($services_data)): ?>
                                <div class="skd-services-offered">
                                    <h4>Services:</h4>
                                    <div class="skd-services-tags">
                                        <?php
                                        $displayed_services = array_slice($services_data, 0, 4);
                                        foreach ($displayed_services as $service_id):
                                            $service = $wpdb->get_row($wpdb->prepare("SELECT name FROM $services_table WHERE id = %d", $service_id));
                                            if ($service):
                                        ?>
                                                <span class="skd-service-tag"><?php echo esc_html($service->name); ?></span>
                                            <?php
                                            endif;
                                        endforeach;
                                        if (count($services_data) > 4):
                                            ?>
                                            <span class="skd-service-tag more">+<?php echo count($services_data) - 4; ?> more</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Portfolio Preview -->
                            <?php if (!empty($portfolio_images)): ?>
                                <div class="skd-portfolio-preview studio-portfolio">
                                    <?php
                                    $preview_images = array_slice($portfolio_images, 0, 4);
                                    foreach ($preview_images as $image):
                                    ?>
                                        <div class="skd-portfolio-thumb">
                                            <img src="<?php echo esc_url($image); ?>" alt="Studio portfolio">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <!-- Pricing Info -->
                            <div class="skd-pricing-info">
                                <?php if ($studio->hourly_rate): ?>
                                    <span class="skd-rate-range">
                                        Starting from $<?php echo number_format($studio->hourly_rate, 0); ?>/<?php echo esc_html($studio->rate_type); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="skd-rate-range">Custom pricing available</span>
                                <?php endif; ?>
                            </div>

                            <!-- Action Buttons -->
                            <div class="skd-card-actions">
                                <a href="<?php echo home_url("/studio/{$studio->slug}/"); ?>" class="skd-btn skd-btn-primary">
                                    View Studio
                                </a>
                                <button class="skd-btn skd-btn-secondary" onclick="contactStudio(<?php echo $studio->id; ?>)">
                                    <span class="dashicons dashicons-email"></span>
                                    Get Quote
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <div class="skd-pagination" id="pagination">
                <!-- Will be populated by JavaScript -->
            </div>

            <!-- Loading Indicator -->
            <div class="skd-loading" id="loadingIndicator" style="display: none;">
                <div class="skd-loading-spinner"></div>
                <p>Loading studios...</p>
            </div>
        </main>
    </div>
</div>

<!-- Contact Modal -->
<div id="contactModal" class="skd-modal" style="display: none;">
    <div class="skd-modal-content">
        <div class="skd-modal-header">
            <h3>Contact Studio</h3>
            <button class="skd-modal-close" onclick="closeContactModal()">&times;</button>
        </div>
        <div class="skd-modal-body">
            <form id="contactForm">
                <div class="skd-form-group">
                    <label>Project Description:</label>
                    <textarea id="contactMessage" placeholder="Tell us about your project requirements..."></textarea>
                </div>
                <div class="skd-form-group">
                    <label>Estimated Budget:</label>
                    <select id="budgetRange">
                        <option value="">Select budget range</option>
                        <option value="under-5k">Under $5,000</option>
                        <option value="5k-15k">$5,000 - $15,000</option>
                        <option value="15k-50k">$15,000 - $50,000</option>
                        <option value="50k-plus">$50,000+</option>
                    </select>
                </div>
                <div class="skd-form-actions">
                    <button type="button" class="skd-btn skd-btn-secondary" onclick="closeContactModal()">Cancel</button>
                    <button type="submit" class="skd-btn skd-btn-primary">Request Quote</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Initialize find studios functionality
    document.addEventListener('DOMContentLoaded', function() {
        initializeFindStudios();
    });

    function initializeFindStudios() {
        setupFilters();
        setupSearch();
        setupSort();
    }

    function setupFilters() {
        document.querySelectorAll('.skd-filters input[type="checkbox"], .skd-filters input[type="radio"]').forEach(input => {
            input.addEventListener('change', debounce(applyFilters, 500));
        });
        document.getElementById('locationFilter').addEventListener('change', applyFilters);
    }

    function setupSearch() {
        document.getElementById('searchInput').addEventListener('input', debounce(applyFilters, 500));
    }

    function setupSort() {
        document.getElementById('sortSelect').addEventListener('change', applyFilters);
    }

    function applyFilters() {
        const formData = new FormData();

        formData.append('action', 'skd_filter_professionals');
        formData.append('user_role', 'studio');
        formData.append('search', document.getElementById('searchInput').value);

        // Services  
        document.querySelectorAll('input[name="services[]"]:checked').forEach(cb => {
            formData.append('services[]', cb.value);
        });

        // Specializations
        document.querySelectorAll('input[name="specializations[]"]:checked').forEach(cb => {
            formData.append('specializations[]', cb.value);
        });

        // Team size, experience, location filters
        const teamSizeInput = document.querySelector('input[name="team_size"]:checked');
        if (teamSizeInput) formData.append('team_size', teamSizeInput.value);

        const experienceInput = document.querySelector('input[name="experience_years"]:checked');
        if (experienceInput) formData.append('experience_years', experienceInput.value);

        const location = document.getElementById('locationFilter').value;
        if (location) formData.append('location', location);

        formData.append('sort_by', document.getElementById('sortSelect').value);
        formData.append('page', 1);

        // Show loading
        document.getElementById('loadingIndicator').style.display = 'block';
        document.getElementById('studiosGrid').style.opacity = '0.5';

        // Make AJAX request
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateStudiosGrid(data.data.professionals);
                    updateResultsCount(data.data.total_count, data.data.professionals.length);
                    updatePagination(data.data.current_page, data.data.total_pages);
                    updateActiveFilters();
                }
            })
            .catch(error => {
                console.error('Request error:', error);
            })
            .finally(() => {
                document.getElementById('loadingIndicator').style.display = 'none';
                document.getElementById('studiosGrid').style.opacity = '1';
            });
    }

    function updateStudiosGrid(studios) {
        const grid = document.getElementById('studiosGrid');
        grid.innerHTML = studios.map(studio => createStudioCard(studio)).join('');
    }

    function updateResultsCount(total, current) {
        document.getElementById('resultsCount').innerHTML = `Showing <strong>${current}</strong> of <strong>${total}</strong> studios`;
    }

    function updatePagination(currentPage, totalPages) {
        // Pagination implementation
    }

    function updateActiveFilters() {
        // Active filters implementation
    }

    function createStudioCard(studio) {
        // Create HTML for studio card
        return `<div class="skd-studio-card" data-studio-id="${studio.id}">
        <!-- Studio card HTML structure -->
    </div>`;
    }

    function contactStudio(studioId) {
        document.getElementById('contactModal').style.display = 'block';
    }

    function closeContactModal() {
        document.getElementById('contactModal').style.display = 'none';
    }

    function clearAllFilters() {
        document.querySelectorAll('.skd-filters input[type="checkbox"], .skd-filters input[type="radio"]').forEach(input => {
            input.checked = false;
        });

        document.getElementById('searchInput').value = '';
        document.getElementById('locationFilter').value = '';

        applyFilters();
    }

    function toggleFilters() {
        const filters = document.getElementById('studioFilters');
        filters.classList.toggle('show');
    }

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
</script>