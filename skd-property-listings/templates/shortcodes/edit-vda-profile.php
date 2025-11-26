<?php

/**
 * Template: Edit VDA Profile
 * Shortcode: [skd_edit_vda_profile]
 */

if (!defined('ABSPATH')) exit;

if (!is_user_logged_in()) {
    echo '<p>Please <a href="' . home_url('/login/') . '">login</a> to edit your profile.</p>';
    return;
}

global $wpdb;
$user_id = get_current_user_id();
$user = wp_get_current_user();
$user_type = get_user_meta($user_id, 'skd_user_type', true);

if ($user_type !== 'vda') {
    echo '<p>This page is only for VDA users.</p>';
    return;
}

$profile = SKD_PL_VDA_Profile::get_user_profile($user_id);
$skills = json_decode($profile->skills ?? '[]', true) ?: [];
$services = json_decode($profile->services_offered ?? '[]', true) ?: [];
$specializations = json_decode($profile->specializations ?? '[]', true) ?: [];
$languages = json_decode($profile->languages_spoken ?? '[]', true) ?: [];
?>

<div class="skd-profile-edit-wrapper">
    <div class="skd-profile-header">
        <h1>Edit Your Profile</h1>
        <div class="skd-profile-completeness">
            <span>Profile Completeness: <strong><?php echo $profile->profile_completeness ?? 0; ?>%</strong></span>
            <div class="progress-bar">
                <div class="progress-fill" style="width: <?php echo $profile->profile_completeness ?? 0; ?>%"></div>
            </div>
        </div>
    </div>

    <div class="skd-profile-tabs">
        <nav class="skd-tab-nav">
            <button class="skd-tab-btn active" data-tab="basic">Basic Info</button>
            <button class="skd-tab-btn" data-tab="skills">Skills & Software</button>
            <button class="skd-tab-btn" data-tab="services">Services</button>
            <button class="skd-tab-btn" data-tab="specializations">Specializations</button>
            <button class="skd-tab-btn" data-tab="experience">Experience</button>
            <button class="skd-tab-btn" data-tab="portfolio">Portfolio</button>
            <button class="skd-tab-btn" data-tab="rates">Rates & Availability</button>
            <button class="skd-tab-btn" data-tab="social">Social Links</button>
        </nav>

        <div class="skd-tab-content">
            <!-- Basic Info Tab -->
            <div class="skd-tab-pane active" id="tab-basic">
                <h2>Basic Information</h2>
                <form id="basic-info-form" class="skd-profile-form">
                    <div class="skd-form-row">
                        <div class="skd-form-group">
                            <label>Profile Picture</label>
                            <div class="skd-avatar-upload">
                                <!-- <img src="<?php //echo esc_url($profile->avatar_url ?: 'https://via.placeholder.com/150'); 
                                                ?>" alt="Avatar" id="avatar-preview"> -->
                                <input type="file" id="avatar-upload" accept="image/*">
                                <button type="button" class="skd-btn" id="upload-avatar-btn">Upload New Picture</button>
                            </div>
                        </div>
                    </div>

                    <div class="skd-form-row skd-two-col">
                        <div class="skd-form-group">
                            <label>First Name *</label>
                            <input type="text" name="first_name" value="<?php echo esc_attr($user->first_name); ?>" required>
                        </div>
                        <div class="skd-form-group">
                            <label>Last Name *</label>
                            <input type="text" name="last_name" value="<?php echo esc_attr($user->last_name); ?>" required>
                        </div>
                    </div>

                    <div class="skd-form-group">
                        <label>Professional Tagline</label>
                        <input type="text" name="tagline" value="<?php echo esc_attr($profile->tagline ?? ''); ?>" placeholder="e.g., 3D Rendering Specialist">
                    </div>

                    <div class="skd-form-group">
                        <label>Short Description</label>
                        <textarea name="short_description" rows="3" maxlength="300" placeholder="Brief description about you (max 300 characters)"><?php echo esc_textarea($profile->short_description ?? ''); ?></textarea>
                        <small>This appears right below your name on your profile. Keep it concise.</small>
                    </div>

                    <div class="skd-form-group">
                        <label>About Me (Bio)</label>
                        <?php
                        wp_editor(
                            $profile->bio ?? '',
                            'bio_editor',
                            array(
                                'textarea_name' => 'bio',
                                'textarea_rows' => 8,
                                'media_buttons' => false,
                                'teeny' => true,
                                'quicktags' => true,
                            )
                        );
                        ?>
                        <small>This is your full professional bio shown in the "About Me" section.</small>
                    </div>

                    <div class="skd-form-group">
                        <label>What I Offer</label>
                        <?php
                        wp_editor(
                            $profile->what_i_offer ?? '',
                            'what_i_offer_editor',
                            array(
                                'textarea_name' => 'what_i_offer',
                                'textarea_rows' => 6,
                                'media_buttons' => false,
                                'teeny' => true,
                                'quicktags' => true,
                            )
                        );
                        ?>
                        <small>List the services and offerings you provide to clients.</small>
                    </div>

                    <div class="skd-form-row skd-two-col">
                        <div class="skd-form-group">
                            <label>Country</label>
                            <input type="text" name="country" value="<?php echo esc_attr($profile->country ?? ''); ?>">
                        </div>
                        <div class="skd-form-group">
                            <label>City</label>
                            <input type="text" name="city" value="<?php echo esc_attr($profile->city ?? ''); ?>">
                        </div>
                    </div>

                    <div class="skd-form-group">
                        <label>Timezone</label>
                        <select name="timezone">
                            <option value="">Select Timezone</option>
                            <?php
                            $timezones = SKD_PL_Registration::get_timezones();
                            foreach ($timezones as $value => $label) {
                                $selected = ($profile->timezone ?? '') == $value ? 'selected' : '';
                                echo "<option value='$value' $selected>$label</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <button type="submit" class="skd-btn skd-btn-primary">Save Basic Info</button>
                </form>
            </div>

            <!-- Skills Tab -->
            <div class="skd-tab-pane" id="tab-skills">
                <h2>Skills & Software</h2>
                <form id="skills-form" class="skd-profile-form">
                    <div class="skd-form-group">
                        <label>Select Your Skills</label>
                        <select name="skills[]" id="skills-select" multiple class="skd-select2" style="width: 100%">
                            <?php
                            $all_skills = SKD_PL_VDA_Skills::get_skills(['status' => 'active']);
                            foreach ($all_skills as $skill) {
                                $selected = in_array($skill->id, $skills) ? 'selected' : '';
                                echo "<option value='{$skill->id}' $selected>{$skill->name}</option>";
                            }
                            ?>
                        </select>
                        <small>You can add new skills by typing them</small>
                    </div>
                    <button type="submit" class="skd-btn skd-btn-primary">Save Skills</button>
                </form>
            </div>

            <!-- Services Tab -->
            <div class="skd-tab-pane" id="tab-services">
                <h2>Services Offered</h2>
                <form id="services-form" class="skd-profile-form">
                    <div class="skd-form-group">
                        <label>Select Services You Offer</label>
                        <select name="services[]" id="services-select" multiple class="skd-select2" style="width: 100%">
                            <?php
                            $all_services = SKD_PL_VDA_Services::get_services(['status' => 'active']);
                            foreach ($all_services as $service) {
                                $selected = in_array($service->id, $services) ? 'selected' : '';
                                echo "<option value='{$service->id}' $selected>{$service->name}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" class="skd-btn skd-btn-primary">Save Services</button>
                </form>
            </div>

            <!-- Specializations Tab -->
            <div class="skd-tab-pane" id="tab-specializations">
                <h2>Specializations</h2>
                <form id="specializations-form" class="skd-profile-form">
                    <div class="skd-form-group">
                        <label>Your Specializations</label>
                        <select name="specializations[]" id="specializations-select" multiple class="skd-select2" style="width: 100%">
                            <?php
                            $all_specs = SKD_PL_VDA_Specializations::get_specializations(['status' => 'active']);
                            foreach ($all_specs as $spec) {
                                $selected = in_array($spec->id, $specializations) ? 'selected' : '';
                                echo "<option value='{$spec->id}' $selected>{$spec->name}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <button type="submit" class="skd-btn skd-btn-primary">Save Specializations</button>
                </form>
            </div>

            <!-- Experience Tab -->
            <div class="skd-tab-pane" id="tab-experience">
                <h2>Experience & Education</h2>
                <form id="experience-form" class="skd-profile-form">
                    <div class="skd-form-row skd-two-col">
                        <div class="skd-form-group">
                            <label>Years of Experience</label>
                            <input type="number" name="years_experience" value="<?php echo esc_attr($profile->years_experience ?? ''); ?>" min="0">
                        </div>
                        <div class="skd-form-group">
                            <label>Experience Level</label>
                            <select name="experience_level">
                                <option value="">Select Level</option>
                                <option value="junior" <?php selected($profile->experience_level ?? '', 'junior'); ?>>Junior (0-2 yrs)</option>
                                <option value="mid" <?php selected($profile->experience_level ?? '', 'mid'); ?>>Mid-Level (3-5 yrs)</option>
                                <option value="senior" <?php selected($profile->experience_level ?? '', 'senior'); ?>>Senior (5+ yrs)</option>
                                <option value="expert" <?php selected($profile->experience_level ?? '', 'expert'); ?>>Expert (10+ yrs)</option>
                            </select>
                        </div>
                    </div>

                    <div class="skd-form-group">
                        <label>Education Level</label>
                        <select name="education_level">
                            <option value="">Select Education</option>
                            <option value="High School" <?php selected($profile->education_level ?? '', 'High School'); ?>>High School</option>
                            <option value="Associate Degree" <?php selected($profile->education_level ?? '', 'Associate Degree'); ?>>Associate Degree</option>
                            <option value="Bachelor's Degree" <?php selected($profile->education_level ?? '', "Bachelor's Degree"); ?>>Bachelor's Degree</option>
                            <option value="Master's Degree" <?php selected($profile->education_level ?? '', "Master's Degree"); ?>>Master's Degree</option>
                            <option value="PhD" <?php selected($profile->education_level ?? '', 'PhD'); ?>>PhD</option>
                        </select>
                    </div>

                    <div class="skd-form-group">
                        <label>Languages Spoken</label>
                        <select name="languages[]" id="languages-select" multiple class="skd-select2" style="width: 100%">
                            <?php
                            $common_languages = ['English', 'Spanish', 'French', 'German', 'Italian', 'Portuguese', 'Chinese', 'Japanese', 'Korean', 'Arabic', 'Hindi', 'Russian'];
                            foreach ($common_languages as $lang) {
                                $selected = in_array($lang, $languages) ? 'selected' : '';
                                echo "<option value='$lang' $selected>$lang</option>";
                            }
                            foreach ($languages as $lang) {
                                if (!in_array($lang, $common_languages)) {
                                    echo "<option value='$lang' selected>$lang</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <button type="submit" class="skd-btn skd-btn-primary">Save Experience</button>
                </form>
            </div>

            <!-- Portfolio Tab -->
            <div class="skd-tab-pane" id="tab-portfolio">
                <h2>Portfolio</h2>
                <button type="button" class="skd-btn skd-btn-primary" id="add-portfolio-btn">Add New Project</button>
                <div id="portfolio-items" class="skd-portfolio-grid">
                    <!-- Portfolio items will be loaded here -->
                </div>
            </div>

            <!-- Rates Tab -->
            <div class="skd-tab-pane" id="tab-rates">
                <h2>Rates & Availability</h2>
                <form id="rates-form" class="skd-profile-form">
                    <div class="skd-form-row skd-two-col">
                        <div class="skd-form-group">
                            <label>Hourly Rate (USD)</label>
                            <input type="number" name="hourly_rate" value="<?php echo esc_attr($profile->hourly_rate ?? ''); ?>" min="0" step="0.01">
                        </div>
                        <div class="skd-form-group">
                            <label>Pricing Model</label>
                            <select name="pricing_model">
                                <option value="hourly" <?php selected($profile->pricing_model ?? '', 'hourly'); ?>>Hourly</option>
                                <option value="fixed" <?php selected($profile->pricing_model ?? '', 'fixed'); ?>>Fixed Price</option>
                                <option value="both" <?php selected($profile->pricing_model ?? '', 'both'); ?>>Both</option>
                                <option value="negotiable" <?php selected($profile->pricing_model ?? '', 'negotiable'); ?>>Negotiable</option>
                            </select>
                        </div>
                    </div>

                    <div class="skd-form-row skd-two-col">
                        <div class="skd-form-group">
                            <label>Availability Status</label>
                            <select name="availability_status">
                                <option value="available" <?php selected($profile->availability_status ?? '', 'available'); ?>>Available</option>
                                <option value="busy" <?php selected($profile->availability_status ?? '', 'busy'); ?>>Busy</option>
                                <option value="unavailable" <?php selected($profile->availability_status ?? '', 'unavailable'); ?>>Unavailable</option>
                            </select>
                        </div>
                        <div class="skd-form-group">
                            <label>Response Time</label>
                            <select name="response_time">
                                <option value="">Select</option>
                                <option value="Within 1 hour" <?php selected($profile->response_time ?? '', 'Within 1 hour'); ?>>Within 1 hour</option>
                                <option value="Within 2 hours" <?php selected($profile->response_time ?? '', 'Within 2 hours'); ?>>Within 2 hours</option>
                                <option value="Within 24 hours" <?php selected($profile->response_time ?? '', 'Within 24 hours'); ?>>Within 24 hours</option>
                                <option value="Within 48 hours" <?php selected($profile->response_time ?? '', 'Within 48 hours'); ?>>Within 48 hours</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="skd-btn skd-btn-primary">Save Rates</button>
                </form>
            </div>

            <!-- Social Links Tab -->
            <div class="skd-tab-pane" id="tab-social">
                <h2>Social Links & Portfolio</h2>
                <form id="social-form" class="skd-profile-form">
                    <div class="skd-form-group">
                        <label>Website URL</label>
                        <input type="url" name="website_url" value="<?php echo esc_url($profile->website_url ?? ''); ?>" placeholder="https://yourwebsite.com">
                    </div>
                    <div class="skd-form-group">
                        <label>LinkedIn URL</label>
                        <input type="url" name="linkedin_url" value="<?php echo esc_url($profile->linkedin_url ?? ''); ?>" placeholder="https://linkedin.com/in/yourprofile">
                    </div>
                    <div class="skd-form-group">
                        <label>Behance URL</label>
                        <input type="url" name="behance_url" value="<?php echo esc_url($profile->behance_url ?? ''); ?>" placeholder="https://behance.net/yourprofile">
                    </div>
                    <div class="skd-form-group">
                        <label>Instagram URL</label>
                        <input type="url" name="instagram_url" value="<?php echo esc_url($profile->instagram_url ?? ''); ?>" placeholder="https://instagram.com/yourprofile">
                    </div>
                    <div class="skd-form-group">
                        <label>Pinterest URL</label>
                        <input type="url" name="pinterest_url" value="<?php echo esc_url($profile->pinterest_url ?? ''); ?>" placeholder="https://pinterest.com/yourprofile">
                    </div>
                    <div class="skd-form-group">
                        <label>Portfolio URL</label>
                        <input type="url" name="portfolio_url" value="<?php echo esc_url($profile->portfolio_url ?? ''); ?>" placeholder="https://yourportfolio.com">
                    </div>
                    <button type="submit" class="skd-btn skd-btn-primary">Save Social Links</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .skd-profile-edit-wrapper {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    .skd-profile-header {
        margin-bottom: 30px;
    }

    .skd-profile-header h1 {
        margin-bottom: 15px;
    }

    .skd-profile-completeness {
        margin-bottom: 10px;
    }

    .progress-bar {
        width: 100%;
        height: 10px;
        background: #e0e0e0;
        border-radius: 5px;
        overflow: hidden;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #667eea, #764ba2);
        transition: width 0.3s;
    }

    .skd-tab-nav {
        display: flex;
        gap: 10px;
        border-bottom: 2px solid #e0e0e0;
        margin-bottom: 30px;
        overflow-x: auto;
    }

    .skd-tab-btn {
        padding: 12px 20px;
        background: none;
        border: none;
        border-bottom: 3px solid transparent;
        cursor: pointer;
        font-size: 15px;
        white-space: nowrap;
    }

    .skd-tab-btn:hover {
        background: #f5f5f5;
    }

    .skd-tab-btn.active {
        border-bottom-color: #667eea;
        color: #667eea;
        font-weight: 600;
    }

    .skd-tab-pane {
        display: none;
    }

    .skd-tab-pane.active {
        display: block;
    }

    .skd-profile-form {
        max-width: 800px;
    }

    .skd-form-row {
        margin-bottom: 20px;
    }

    .skd-form-row.skd-two-col {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .skd-form-group {
        margin-bottom: 20px;
    }

    .skd-form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 600;
    }

    .skd-form-group input,
    .skd-form-group textarea,
    .skd-form-group select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
    }

    .skd-avatar-upload {
        text-align: center;
    }

    .skd-avatar-upload img {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 15px;
        border: 3px solid #e0e0e0;
    }

    .skd-avatar-upload input[type="file"] {
        display: none;
    }

    .skd-btn {
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 15px;
    }

    .skd-btn-primary {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
    }

    .skd-btn-primary:hover {
        opacity: 0.9;
    }

    .skd-portfolio-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    @media (max-width: 768px) {
        .skd-form-row.skd-two-col {
            grid-template-columns: 1fr;
        }

        .skd-tab-nav {
            flex-wrap: nowrap;
        }
    }
</style>

<script>
    jQuery(document).ready(function($) {
        const nonce = '<?php echo wp_create_nonce('skd_ajax_nonce'); ?>';

        // Tab switching
        $('.skd-tab-btn').click(function() {
            const tab = $(this).data('tab');
            $('.skd-tab-btn').removeClass('active');
            $(this).addClass('active');
            $('.skd-tab-pane').removeClass('active');
            $('#tab-' + tab).addClass('active');
        });

        // Initialize Select2 with tags and AJAX
        $('.skd-select2').select2({
            tags: true,
            tokenSeparators: [','],
            ajax: {
                url: skd_ajax_object.ajax_url,
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term,
                        action: 'skd_get_' + $(this).attr('id').replace('-select', '')
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results || []
                    };
                }
            }
        });

        // Form submissions
        $('#basic-info-form').submit(function(e) {
            e.preventDefault();
            submitForm(this, 'skd_update_profile_basic', 'Basic info updated!');
        });

        $('#skills-form').submit(function(e) {
            e.preventDefault();
            submitForm(this, 'skd_update_profile_skills', 'Skills updated!');
        });

        $('#services-form').submit(function(e) {
            e.preventDefault();
            submitForm(this, 'skd_update_profile_services', 'Services updated!');
        });

        $('#specializations-form').submit(function(e) {
            e.preventDefault();
            submitForm(this, 'skd_update_profile_specializations', 'Specializations updated!');
        });

        $('#experience-form').submit(function(e) {
            e.preventDefault();
            submitForm(this, 'skd_update_profile_experience', 'Experience updated!');
        });

        $('#rates-form').submit(function(e) {
            e.preventDefault();
            submitForm(this, 'skd_update_profile_rates', 'Rates updated!');
        });

        $('#social-form').submit(function(e) {
            e.preventDefault();
            submitForm(this, 'skd_update_profile_social', 'Social links updated!');
        });

        function submitForm(form, action, successMsg) {
            const $btn = $(form).find('button[type="submit"]');
            $btn.prop('disabled', true).text('Saving...');

            $.post(skd_ajax_object.ajax_url, $(form).serialize() + '&action=' + action + '&nonce=' + nonce, function(response) {
                $btn.prop('disabled', false).text('Save');
                if (response.success) {
                    Swal.fire('Success', successMsg, 'success');
                    location.reload();
                } else {
                    Swal.fire('Error', response.data.message, 'error');
                }
            });
        }

        // Avatar upload
        $('#upload-avatar-btn').click(function() {
            $('#avatar-upload').click();
        });

        $('#avatar-upload').change(function() {
            const file = this.files[0];
            if (file) {
                const formData = new FormData();
                formData.append('avatar', file);
                formData.append('action', 'skd_upload_avatar');
                formData.append('nonce', nonce);

                $.ajax({
                    url: skd_ajax_object.ajax_url,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            $('#avatar-preview').attr('src', response.data.url);
                            Swal.fire('Success', 'Avatar uploaded!', 'success');
                        }
                    }
                });
            }
        });

        // Load portfolio items
        function loadPortfolio() {
            $.post(skd_ajax_object.ajax_url, {
                action: 'skd_get_portfolio_items',
                nonce: nonce
            }, function(response) {
                if (response.success && response.data.items) {
                    $('#portfolio-items').html('');
                    response.data.items.forEach(function(item) {
                        const images = JSON.parse(item.images || '[]');
                        const featuredImg = item.featured_image || images[0] || 'https://via.placeholder.com/300x200';

                        const html = `
                        <div class="portfolio-card" data-id="${item.id}">
                            <img src="${featuredImg}" alt="${item.title}">
                            <div class="portfolio-info">
                                <h3>${item.title}</h3>
                                <p>${item.description ? item.description.substring(0, 80) + '...' : ''}</p>
                                <div class="portfolio-actions">
                                    <button class="edit-portfolio-btn" data-id="${item.id}">Edit</button>
                                    <button class="delete-portfolio-btn" data-id="${item.id}">Delete</button>
                                </div>
                            </div>
                        </div>
                    `;
                        $('#portfolio-items').append(html);
                    });
                }
            });
        }

        // Show portfolio modal for adding
        $('#add-portfolio-btn').click(function() {
            showPortfolioModal();
        });

        function showPortfolioModal(itemData = null) {
            Swal.fire({
                title: itemData ? 'Edit Project' : 'Add New Project',
                html: `
                <form id="portfolio-form" style="text-align: left;">
                    <input type="hidden" name="portfolio_id" value="${itemData ? itemData.id : ''}">
                    <div class="form-group">
                        <label>Project Title *</label>
                        <input type="text" name="title" class="swal2-input" value="${itemData ? itemData.title : ''}" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="swal2-textarea">${itemData ? itemData.description : ''}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Project Type</label>
                        <select name="project_type" class="swal2-select">
                            <option value="residential">Residential</option>
                            <option value="commercial">Commercial</option>
                            <option value="hospitality">Hospitality</option>
                            <option value="retail">Retail</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <input type="text" name="category" class="swal2-input" value="${itemData ? itemData.category : ''}">
                    </div>
                    <div class="form-group">
                        <label>Software Used (comma separated)</label>
                        <input type="text" name="software_used" class="swal2-input" placeholder="3ds Max, V-Ray, Photoshop" value="${itemData ? (itemData.software_used ? JSON.parse(itemData.software_used).join(', ') : '') : ''}">
                    </div>
                    <div class="form-group">
                        <label>Year</label>
                        <input type="number" name="year" class="swal2-input" value="${itemData ? itemData.year : new Date().getFullYear()}">
                    </div>
                    <div class="form-group">
                        <label>Project URL</label>
                        <input type="url" name="project_url" class="swal2-input" value="${itemData ? itemData.project_url : ''}">
                    </div>
                </form>
            `,
                showCancelButton: true,
                confirmButtonText: 'Save',
                preConfirm: () => {
                    const formData = new FormData($('#portfolio-form')[0]);
                    const data = {
                        title: formData.get('title'),
                        description: formData.get('description'),
                        project_type: formData.get('project_type'),
                        category: formData.get('category'),
                        software_used: formData.get('software_used').split(',').map(s => s.trim()).filter(s => s),
                        year: formData.get('year'),
                        project_url: formData.get('project_url')
                    };

                    if (!data.title) {
                        Swal.showValidationMessage('Title is required');
                        return false;
                    }

                    return data;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const action = itemData ? 'skd_update_portfolio_item' : 'skd_add_portfolio_item';
                    const postData = {
                        ...result.value,
                        action: action,
                        nonce: nonce
                    };

                    if (itemData) {
                        postData.portfolio_id = itemData.id;
                    }

                    $.post(skd_ajax_object.ajax_url, postData, function(response) {
                        if (response.success) {
                            Swal.fire('Success', itemData ? 'Project updated!' : 'Project added!', 'success');
                            loadPortfolio();
                        } else {
                            Swal.fire('Error', response.data.message, 'error');
                        }
                    });
                }
            });
        }

        // Edit portfolio item
        $(document).on('click', '.edit-portfolio-btn', function() {
            const id = $(this).data('id');
            $.post(skd_ajax_object.ajax_url, {
                action: 'skd_get_portfolio_items',
                nonce: nonce
            }, function(response) {
                if (response.success && response.data.items) {
                    const item = response.data.items.find(i => i.id == id);
                    if (item) {
                        showPortfolioModal(item);
                    }
                }
            });
        });

        // Delete portfolio item
        $(document).on('click', '.delete-portfolio-btn', function() {
            const id = $(this).data('id');
            Swal.fire({
                title: 'Delete Project?',
                text: 'This action cannot be undone',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.post(skd_ajax_object.ajax_url, {
                        action: 'skd_delete_portfolio_item',
                        portfolio_id: id,
                        nonce: nonce
                    }, function(response) {
                        if (response.success) {
                            Swal.fire('Deleted!', 'Project removed', 'success');
                            loadPortfolio();
                        } else {
                            Swal.fire('Error', response.data.message, 'error');
                        }
                    });
                }
            });
        });

        // Load portfolio on tab switch
        $('.skd-tab-btn[data-tab="portfolio"]').click(function() {
            loadPortfolio();
        });
    });
</script>

<style>
    .portfolio-card {
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
        transition: transform 0.2s;
    }

    .portfolio-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .portfolio-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .portfolio-info {
        padding: 15px;
    }

    .portfolio-info h3 {
        margin: 0 0 10px;
        font-size: 16px;
    }

    .portfolio-info p {
        margin: 0 0 15px;
        color: #666;
        font-size: 14px;
    }

    .portfolio-actions {
        display: flex;
        gap: 10px;
    }

    .portfolio-actions button {
        flex: 1;
        padding: 8px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 13px;
    }

    .edit-portfolio-btn {
        background: #667eea;
        color: white;
    }

    .delete-portfolio-btn {
        background: #dc3545;
        color: white;
    }
</style>