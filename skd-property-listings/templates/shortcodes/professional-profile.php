<?php

/**
 * Template for Individual Professional Profile
 * Displays detailed professional/studio profile with portfolio, reviews, etc.
 */

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

// Get professional data (passed from shortcode)
if (!isset($professional)) {
    return '<p>Professional profile not found.</p>';
}

// Get related data
$skills_table = $wpdb->prefix . 'skd_pl_skills';
$services_table = $wpdb->prefix . 'skd_pl_services';
$specializations_table = $wpdb->prefix . 'skd_pl_specializations';
$reviews_table = $wpdb->prefix . 'skd_pl_reviews';
$certifications_table = $wpdb->prefix . 'skd_pl_certifications';
$user_certifications_table = $wpdb->prefix . 'skd_pl_user_certifications';

// Decode JSON data
$skills_data = json_decode($professional->skills, true) ?: [];
$services_data = json_decode($professional->services, true) ?: [];
$specializations_data = json_decode($professional->specializations, true) ?: [];
$portfolio_images = json_decode($professional->portfolio_images, true) ?: [];
$social_info = json_decode($professional->social_info, true) ?: [];

// Get reviews
$reviews = $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM $reviews_table WHERE listing_id = %d AND status = 'approved' ORDER BY created_at DESC LIMIT 10",
    $professional->id
));

// Get certifications
$user_certifications = $wpdb->get_results($wpdb->prepare(
    "SELECT uc.*, c.name, c.issuer, c.badge_image_url 
     FROM $user_certifications_table uc 
     JOIN $certifications_table c ON uc.certification_id = c.id 
     WHERE uc.user_id = %d AND uc.status = 'approved'",
    $professional->user_id
));

// Calculate years of experience (if founded year is available for studios)
$years_experience = 0;
if ($professional->user_role === 'studio' && $professional->founded_year) {
    $years_experience = date('Y') - $professional->founded_year;
}

$is_studio = $professional->user_role === 'studio';
?>

<div class="skd-professional-profile-wrapper">
    <!-- Back Navigation -->
    <div class="skd-back-navigation">
        <a href="<?php echo home_url($is_studio ? '/find-studios/' : '/find-assistants/'); ?>" class="skd-back-link">
            <span class="dashicons dashicons-arrow-left-alt2"></span>
            Back to <?php echo $is_studio ? 'Studios' : 'Professionals'; ?>
        </a>
    </div>

    <!-- Profile Hero Section -->
    <div class="skd-profile-hero">
        <div class="skd-hero-content">
            <div class="skd-profile-image-large">
                <?php if ($professional->skd_logo): ?>
                    <img src="<?php echo esc_url($professional->skd_logo); ?>" alt="<?php echo esc_attr($professional->listing_title); ?>">
                <?php else: ?>
                    <div class="skd-avatar-large">
                        <?php echo strtoupper(substr($professional->listing_title, 0, 2)); ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="skd-profile-info">
                <h1><?php echo esc_html($professional->listing_title); ?></h1>

                <div class="skd-profile-type">
                    <span class="skd-role-badge <?php echo esc_attr($professional->user_role); ?>">
                        <?php
                        echo $is_studio ? 'Design Studio' : 'Virtual Design Assistant';
                        if (!$is_studio) {
                            echo ' - ' . esc_html(ucfirst($professional->experience_level));
                        }
                        ?>
                    </span>
                </div>

                <div class="skd-profile-badges">
                    <?php if ($professional->is_verified): ?>
                        <span class="skd-badge verified">
                            <span class="dashicons dashicons-yes-alt"></span>
                            Verified Professional
                        </span>
                    <?php endif; ?>

                    <?php if ($professional->is_feature): ?>
                        <span class="skd-badge featured">
                            <span class="dashicons dashicons-star-filled"></span>
                            Featured
                        </span>
                    <?php endif; ?>

                    <?php if ($professional->total_projects >= 50): ?>
                        <span class="skd-badge top-rated">
                            <span class="dashicons dashicons-awards"></span>
                            Top Rated
                        </span>
                    <?php endif; ?>
                </div>

                <div class="skd-profile-stats">
                    <div class="skd-stat">
                        <span class="skd-stat-value"><?php echo number_format($professional->rating, 1); ?></span>
                        <span class="skd-stat-label">Rating</span>
                        <div class="skd-stars-small">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <span class="dashicons <?php echo $i <= $professional->rating ? 'dashicons-star-filled' : 'dashicons-star-empty'; ?>"></span>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <div class="skd-stat">
                        <span class="skd-stat-value"><?php echo $professional->total_projects; ?></span>
                        <span class="skd-stat-label">Projects Completed</span>
                    </div>

                    <div class="skd-stat">
                        <span class="skd-stat-value"><?php echo $professional->total_reviews; ?></span>
                        <span class="skd-stat-label">Reviews</span>
                    </div>

                    <?php if ($is_studio && $years_experience > 0): ?>
                        <div class="skd-stat">
                            <span class="skd-stat-value"><?php echo $years_experience; ?></span>
                            <span class="skd-stat-label">Years in Business</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="skd-profile-actions">
                <button class="skd-btn skd-btn-primary skd-btn-large" onclick="openContactForm()">
                    <span class="dashicons dashicons-email"></span>
                    <?php echo $is_studio ? 'Request Quote' : 'Contact Professional'; ?>
                </button>

                <?php if (!empty($professional->contact_website)): ?>
                    <a href="<?php echo esc_url($professional->contact_website); ?>" target="_blank" class="skd-btn skd-btn-secondary">
                        <span class="dashicons dashicons-external"></span>
                        Visit Website
                    </a>
                <?php endif; ?>

                <button class="skd-btn skd-btn-outline" onclick="saveToFavorites(<?php echo $professional->id; ?>)">
                    <span class="dashicons dashicons-heart"></span>
                    Save
                </button>
            </div>
        </div>
    </div>

    <!-- Profile Content -->
    <div class="skd-profile-content">
        <div class="skd-profile-sidebar">
            <!-- Quick Info Card -->
            <div class="skd-info-card">
                <h3>Quick Info</h3>

                <div class="skd-info-row">
                    <span class="skd-info-label">
                        <span class="dashicons dashicons-location"></span>
                        Location
                    </span>
                    <span class="skd-info-value"><?php echo esc_html($professional->list_address ?: 'Remote Worldwide'); ?></span>
                </div>

                <?php if ($professional->timezone): ?>
                    <div class="skd-info-row">
                        <span class="skd-info-label">
                            <span class="dashicons dashicons-clock"></span>
                            Timezone
                        </span>
                        <span class="skd-info-value"><?php echo esc_html($professional->timezone); ?></span>
                    </div>
                <?php endif; ?>

                <div class="skd-info-row">
                    <span class="skd-info-label">
                        <span class="dashicons dashicons-money-alt"></span>
                        Rate
                    </span>
                    <span class="skd-info-value">
                        <?php if ($professional->hourly_rate): ?>
                            $<?php echo number_format($professional->hourly_rate, 0); ?>/<?php echo esc_html($professional->rate_type); ?>
                        <?php else: ?>
                            Contact for pricing
                        <?php endif; ?>
                    </span>
                </div>

                <div class="skd-info-row">
                    <span class="skd-info-label">
                        <span class="dashicons dashicons-calendar-alt"></span>
                        Availability
                    </span>
                    <span class="skd-info-value"><?php echo esc_html(ucwords(str_replace('-', ' ', $professional->availability))); ?></span>
                </div>

                <?php if ($is_studio): ?>
                    <div class="skd-info-row">
                        <span class="skd-info-label">
                            <span class="dashicons dashicons-groups"></span>
                            Team Size
                        </span>
                        <span class="skd-info-value"><?php echo $professional->team_size; ?> members</span>
                    </div>
                <?php endif; ?>

                <?php if ($professional->response_time): ?>
                    <div class="skd-info-row">
                        <span class="skd-info-label">
                            <span class="dashicons dashicons-clock"></span>
                            Response Time
                        </span>
                        <span class="skd-info-value">
                            <?php
                            if ($professional->response_time < 24) {
                                echo $professional->response_time . ' hours';
                            } else {
                                echo ceil($professional->response_time / 24) . ' days';
                            }
                            ?>
                        </span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Skills & Software Card -->
            <?php if (!empty($skills_data)): ?>
                <div class="skd-info-card">
                    <h3>Skills & Software</h3>
                    <div class="skd-skills-list">
                        <?php
                        foreach ($skills_data as $skill_id):
                            $skill = $wpdb->get_row($wpdb->prepare("SELECT * FROM $skills_table WHERE id = %d", $skill_id));
                            if ($skill):
                        ?>
                                <span class="skd-skill-badge <?php echo esc_attr($skill->category); ?>" title="<?php echo esc_attr($skill->description); ?>">
                                    <?php echo esc_html($skill->name); ?>
                                    <span class="skd-skill-level <?php echo esc_attr($skill->category); ?>"></span>
                                </span>
                        <?php
                            endif;
                        endforeach;
                        ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Certifications Card -->
            <?php if (!empty($user_certifications)): ?>
                <div class="skd-info-card">
                    <h3>Certifications</h3>
                    <div class="skd-certifications-list">
                        <?php foreach ($user_certifications as $cert): ?>
                            <div class="skd-certification-item">
                                <?php if ($cert->badge_image_url): ?>
                                    <img src="<?php echo esc_url($cert->badge_image_url); ?>" alt="<?php echo esc_attr($cert->name); ?>" class="skd-cert-badge">
                                <?php endif; ?>
                                <div class="skd-cert-info">
                                    <strong><?php echo esc_html($cert->name); ?></strong>
                                    <span class="skd-cert-issuer"><?php echo esc_html($cert->issuer); ?></span>
                                    <?php if ($cert->verification_date): ?>
                                        <span class="skd-cert-date"><?php echo date('Y', strtotime($cert->verification_date)); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Contact Info Card -->
            <div class="skd-info-card">
                <h3>Contact Information</h3>

                <?php if ($professional->contact_email): ?>
                    <div class="skd-contact-item">
                        <span class="dashicons dashicons-email"></span>
                        <a href="mailto:<?php echo esc_attr($professional->contact_email); ?>">
                            <?php echo esc_html($professional->contact_email); ?>
                        </a>
                    </div>
                <?php endif; ?>

                <?php if ($professional->contact_phone): ?>
                    <div class="skd-contact-item">
                        <span class="dashicons dashicons-phone"></span>
                        <a href="tel:<?php echo esc_attr($professional->contact_phone); ?>">
                            <?php echo esc_html($professional->contact_phone); ?>
                        </a>
                    </div>
                <?php endif; ?>

                <?php if ($professional->contact_website): ?>
                    <div class="skd-contact-item">
                        <span class="dashicons dashicons-admin-site"></span>
                        <a href="<?php echo esc_url($professional->contact_website); ?>" target="_blank">
                            Visit Website
                        </a>
                    </div>
                <?php endif; ?>

                <!-- Social Links -->
                <?php if (!empty($social_info)): ?>
                    <div class="skd-social-links">
                        <?php foreach ($social_info as $platform => $url): ?>
                            <?php if ($url): ?>
                                <a href="<?php echo esc_url($url); ?>" target="_blank" class="skd-social-link">
                                    <span class="dashicons dashicons-<?php echo esc_attr($platform); ?>"></span>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="skd-profile-main">
            <!-- About Section -->
            <div class="skd-content-section">
                <h2>About <?php echo $is_studio ? 'Studio' : 'Professional'; ?></h2>
                <div class="skd-about-content">
                    <?php echo wpautop(esc_html($professional->listing_description)); ?>
                </div>

                <?php if ($professional->tagline): ?>
                    <div class="skd-tagline">
                        <blockquote>
                            <em>"<?php echo esc_html($professional->tagline); ?>"</em>
                        </blockquote>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Services Section -->
            <?php if (!empty($services_data)): ?>
                <div class="skd-content-section">
                    <h2>Services Offered</h2>
                    <div class="skd-services-grid">
                        <?php
                        foreach ($services_data as $service_id):
                            $service = $wpdb->get_row($wpdb->prepare("SELECT * FROM $services_table WHERE id = %d", $service_id));
                            if ($service):
                        ?>
                                <div class="skd-service-item">
                                    <h4><?php echo esc_html($service->name); ?></h4>
                                    <?php if ($service->description): ?>
                                        <p><?php echo esc_html($service->description); ?></p>
                                    <?php endif; ?>
                                </div>
                        <?php
                            endif;
                        endforeach;
                        ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Portfolio Section -->
            <?php if (!empty($portfolio_images)): ?>
                <div class="skd-content-section">
                    <h2>Portfolio</h2>
                    <div class="skd-portfolio-gallery">
                        <?php foreach ($portfolio_images as $index => $image): ?>
                            <div class="skd-portfolio-item" onclick="openPortfolioLightbox(<?php echo $index; ?>)">
                                <img src="<?php echo esc_url($image); ?>" alt="Portfolio item <?php echo $index + 1; ?>">
                                <div class="skd-portfolio-overlay">
                                    <span class="dashicons dashicons-search"></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Reviews Section -->
            <?php if (!empty($reviews)): ?>
                <div class="skd-content-section">
                    <h2>Client Reviews</h2>
                    <div class="skd-reviews-list">
                        <?php foreach ($reviews as $review): ?>
                            <div class="skd-review-item">
                                <div class="skd-review-header">
                                    <div class="skd-reviewer-info">
                                        <div class="skd-reviewer-avatar">
                                            <?php echo strtoupper(substr($review->reviewer_name, 0, 1)); ?>
                                        </div>
                                        <div>
                                            <strong><?php echo esc_html($review->reviewer_name); ?></strong>
                                            <span class="skd-review-date"><?php echo date('F j, Y', strtotime($review->created_at)); ?></span>
                                        </div>
                                    </div>
                                    <div class="skd-review-rating">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <span class="dashicons <?php echo $i <= $review->rating ? 'dashicons-star-filled' : 'dashicons-star-empty'; ?>"></span>
                                        <?php endfor; ?>
                                    </div>
                                </div>

                                <?php if ($review->review_title): ?>
                                    <h4 class="skd-review-title"><?php echo esc_html($review->review_title); ?></h4>
                                <?php endif; ?>

                                <p class="skd-review-text"><?php echo esc_html($review->review_text); ?></p>

                                <?php if ($review->project_type): ?>
                                    <span class="skd-project-type">Project: <?php echo esc_html($review->project_type); ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Contact Modal -->
<div id="contactModal" class="skd-modal" style="display: none;">
    <div class="skd-modal-content">
        <div class="skd-modal-header">
            <h3><?php echo $is_studio ? 'Request Quote' : 'Contact Professional'; ?></h3>
            <button class="skd-modal-close" onclick="closeContactForm()">&times;</button>
        </div>
        <div class="skd-modal-body">
            <form id="contactForm">
                <div class="skd-form-group">
                    <label>Your Name *</label>
                    <input type="text" id="senderName" required>
                </div>
                <div class="skd-form-group">
                    <label>Your Email *</label>
                    <input type="email" id="senderEmail" required>
                </div>
                <div class="skd-form-group">
                    <label><?php echo $is_studio ? 'Project Description' : 'Message'; ?> *</label>
                    <textarea id="contactMessage" required placeholder="<?php echo $is_studio ? 'Tell us about your project requirements...' : 'Hi! I\'m interested in your design services...'; ?>"></textarea>
                </div>
                <?php if ($is_studio): ?>
                    <div class="skd-form-group">
                        <label>Estimated Budget</label>
                        <select id="budgetRange">
                            <option value="">Select budget range</option>
                            <option value="under-5k">Under $5,000</option>
                            <option value="5k-15k">$5,000 - $15,000</option>
                            <option value="15k-50k">$15,000 - $50,000</option>
                            <option value="50k-plus">$50,000+</option>
                        </select>
                    </div>
                    <div class="skd-form-group">
                        <label>Project Timeline</label>
                        <select id="projectTimeline">
                            <option value="">Select timeline</option>
                            <option value="asap">ASAP</option>
                            <option value="1-month">Within 1 month</option>
                            <option value="3-months">Within 3 months</option>
                            <option value="flexible">Flexible</option>
                        </select>
                    </div>
                <?php endif; ?>
                <div class="skd-form-actions">
                    <button type="button" class="skd-btn skd-btn-secondary" onclick="closeContactForm()">Cancel</button>
                    <button type="submit" class="skd-btn skd-btn-primary">
                        <?php echo $is_studio ? 'Request Quote' : 'Send Message'; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Portfolio Lightbox -->
<div id="portfolioLightbox" class="skd-lightbox" style="display: none;">
    <div class="skd-lightbox-content">
        <button class="skd-lightbox-close" onclick="closePortfolioLightbox()">&times;</button>
        <button class="skd-lightbox-prev" onclick="prevPortfolioImage()">‹</button>
        <button class="skd-lightbox-next" onclick="nextPortfolioImage()">›</button>
        <img id="lightboxImage" src="" alt="Portfolio image">
        <div class="skd-lightbox-counter">
            <span id="lightboxCounter">1 / 1</span>
        </div>
    </div>
</div>

<script>
    let currentPortfolioIndex = 0;
    const portfolioImages = <?php echo json_encode($portfolio_images); ?>;

    function openContactForm() {
        document.getElementById('contactModal').style.display = 'block';
    }

    function closeContactForm() {
        document.getElementById('contactModal').style.display = 'none';
    }

    function openPortfolioLightbox(index) {
        currentPortfolioIndex = index;
        document.getElementById('lightboxImage').src = portfolioImages[index];
        document.getElementById('lightboxCounter').textContent = `${index + 1} / ${portfolioImages.length}`;
        document.getElementById('portfolioLightbox').style.display = 'block';
    }

    function closePortfolioLightbox() {
        document.getElementById('portfolioLightbox').style.display = 'none';
    }

    function nextPortfolioImage() {
        currentPortfolioIndex = (currentPortfolioIndex + 1) % portfolioImages.length;
        openPortfolioLightbox(currentPortfolioIndex);
    }

    function prevPortfolioImage() {
        currentPortfolioIndex = (currentPortfolioIndex - 1 + portfolioImages.length) % portfolioImages.length;
        openPortfolioLightbox(currentPortfolioIndex);
    }

    function saveToFavorites(professionalId) {
        // Implement favorites functionality
        console.log('Save to favorites:', professionalId);
    }

    // Contact form submission
    document.getElementById('contactForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData();
        formData.append('action', 'skd_submit_list_contact_form');
        formData.append('listing_id', <?php echo $professional->id; ?>);
        formData.append('name', document.getElementById('senderName').value);
        formData.append('email', document.getElementById('senderEmail').value);
        formData.append('message', document.getElementById('contactMessage').value);

        <?php if ($is_studio): ?>
            const budget = document.getElementById('budgetRange').value;
            const timeline = document.getElementById('projectTimeline').value;
            if (budget) formData.append('budget', budget);
            if (timeline) formData.append('timeline', timeline);
        <?php endif; ?>

        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Message sent successfully!');
                    closeContactForm();
                    document.getElementById('contactForm').reset();
                } else {
                    alert('Error sending message. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error sending message. Please try again.');
            });
    });

    // Keyboard navigation for lightbox
    document.addEventListener('keydown', function(e) {
        const lightbox = document.getElementById('portfolioLightbox');
        if (lightbox.style.display === 'block') {
            if (e.key === 'Escape') closePortfolioLightbox();
            if (e.key === 'ArrowLeft') prevPortfolioImage();
            if (e.key === 'ArrowRight') nextPortfolioImage();
        }
    });
</script>