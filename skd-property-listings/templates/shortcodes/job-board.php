<?php

/**
 * Template for Job Board
 * Displays available design jobs with filtering and search
 */

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

// Get job-related data
$jobs_table = $wpdb->prefix . 'skd_pl_jobs';
$job_applications_table = $wpdb->prefix . 'skd_pl_job_applications';
$specializations_table = $wpdb->prefix . 'skd_pl_specializations';

// Job types and categories
$job_types = [
    'full-time' => 'Full-time',
    'part-time' => 'Part-time',
    'contract' => 'Contract',
    'freelance' => 'Freelance',
    'temporary' => 'Temporary'
];

$experience_levels = [
    'entry' => 'Entry Level',
    'mid' => 'Mid Level',
    'senior' => 'Senior Level',
    'executive' => 'Executive'
];

$budget_ranges = [
    'under-1k' => 'Under $1,000',
    '1k-5k' => '$1,000 - $5,000',
    '5k-15k' => '$5,000 - $15,000',
    '15k-50k' => '$15,000 - $50,000',
    '50k-plus' => '$50,000+'
];

// Get specializations for filtering
$specializations = $wpdb->get_results("SELECT * FROM $specializations_table ORDER BY name");
?>

<div class="skd-job-board-wrapper">
    <!-- Job Board Header -->
    <div class="skd-job-board-header">
        <div class="skd-header-content">
            <h1>Design Job Board</h1>
            <p>Discover exciting opportunities in interior design. Connect with studios, agencies, and clients looking for talented professionals.</p>

            <div class="skd-header-actions">
                <a href="/post-job/" class="skd-btn skd-btn-primary skd-btn-large">
                    <span class="dashicons dashicons-plus"></span>
                    Post a Job
                </a>
                <button class="skd-btn skd-btn-secondary" onclick="toggleJobAlerts()">
                    <span class="dashicons dashicons-bell"></span>
                    Job Alerts
                </button>
            </div>
        </div>
    </div>

    <!-- Job Stats -->
    <div class="skd-job-stats">
        <div class="skd-stat-card">
            <span class="skd-stat-number" id="totalJobs">0</span>
            <span class="skd-stat-label">Active Jobs</span>
        </div>
        <div class="skd-stat-card">
            <span class="skd-stat-number">156</span>
            <span class="skd-stat-label">Jobs This Week</span>
        </div>
        <div class="skd-stat-card">
            <span class="skd-stat-number">89%</span>
            <span class="skd-stat-label">Response Rate</span>
        </div>
        <div class="skd-stat-card">
            <span class="skd-stat-number">$65k</span>
            <span class="skd-stat-label">Average Salary</span>
        </div>
    </div>

    <div class="skd-job-board-content">
        <!-- Job Filters Sidebar -->
        <div class="skd-job-filters">
            <div class="skd-filter-header">
                <h3>Filter Jobs</h3>
                <button class="skd-clear-filters" onclick="clearAllFilters()">Clear All</button>
            </div>

            <!-- Search -->
            <div class="skd-filter-section">
                <div class="skd-search-group">
                    <input type="text" id="jobSearch" placeholder="Search jobs..." onkeyup="filterJobs()">
                    <span class="dashicons dashicons-search"></span>
                </div>
            </div>

            <!-- Job Type -->
            <div class="skd-filter-section">
                <h4>Job Type</h4>
                <div class="skd-checkbox-group">
                    <?php foreach ($job_types as $value => $label): ?>
                        <label class="skd-checkbox-label">
                            <input type="checkbox" value="<?php echo esc_attr($value); ?>" onchange="filterJobs()">
                            <span class="skd-checkbox-custom"></span>
                            <?php echo esc_html($label); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Experience Level -->
            <div class="skd-filter-section">
                <h4>Experience Level</h4>
                <div class="skd-checkbox-group">
                    <?php foreach ($experience_levels as $value => $label): ?>
                        <label class="skd-checkbox-label">
                            <input type="checkbox" value="<?php echo esc_attr($value); ?>" onchange="filterJobs()">
                            <span class="skd-checkbox-custom"></span>
                            <?php echo esc_html($label); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Specialization -->
            <div class="skd-filter-section">
                <h4>Specialization</h4>
                <div class="skd-checkbox-group">
                    <?php foreach ($specializations as $spec): ?>
                        <label class="skd-checkbox-label">
                            <input type="checkbox" value="<?php echo esc_attr($spec->id); ?>" onchange="filterJobs()">
                            <span class="skd-checkbox-custom"></span>
                            <?php echo esc_html($spec->name); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Budget Range -->
            <div class="skd-filter-section">
                <h4>Budget Range</h4>
                <div class="skd-checkbox-group">
                    <?php foreach ($budget_ranges as $value => $label): ?>
                        <label class="skd-checkbox-label">
                            <input type="checkbox" value="<?php echo esc_attr($value); ?>" onchange="filterJobs()">
                            <span class="skd-checkbox-custom"></span>
                            <?php echo esc_html($label); ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Location -->
            <div class="skd-filter-section">
                <h4>Location</h4>
                <div class="skd-filter-group">
                    <input type="text" id="locationFilter" placeholder="Enter location..." onkeyup="filterJobs()">
                </div>
                <div class="skd-checkbox-group">
                    <label class="skd-checkbox-label">
                        <input type="checkbox" value="remote" onchange="filterJobs()">
                        <span class="skd-checkbox-custom"></span>
                        Remote Only
                    </label>
                </div>
            </div>

            <!-- Posted Date -->
            <div class="skd-filter-section">
                <h4>Posted Date</h4>
                <div class="skd-radio-group">
                    <label class="skd-radio-label">
                        <input type="radio" name="datePosted" value="any" checked onchange="filterJobs()">
                        <span class="skd-radio-custom"></span>
                        Any Time
                    </label>
                    <label class="skd-radio-label">
                        <input type="radio" name="datePosted" value="24h" onchange="filterJobs()">
                        <span class="skd-radio-custom"></span>
                        Last 24 Hours
                    </label>
                    <label class="skd-radio-label">
                        <input type="radio" name="datePosted" value="7d" onchange="filterJobs()">
                        <span class="skd-radio-custom"></span>
                        Last 7 Days
                    </label>
                    <label class="skd-radio-label">
                        <input type="radio" name="datePosted" value="30d" onchange="filterJobs()">
                        <span class="skd-radio-custom"></span>
                        Last 30 Days
                    </label>
                </div>
            </div>
        </div>

        <!-- Job Listings -->
        <div class="skd-job-listings">
            <!-- Sort Options -->
            <div class="skd-job-sort">
                <div class="skd-sort-left">
                    <span id="jobCount">Loading...</span> jobs found
                </div>
                <div class="skd-sort-right">
                    <label>Sort by:</label>
                    <select id="jobSort" onchange="filterJobs()">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="budget_high">Highest Budget</option>
                        <option value="budget_low">Lowest Budget</option>
                        <option value="title">Job Title</option>
                    </select>
                </div>
            </div>

            <!-- Job Cards Container -->
            <div class="skd-job-cards" id="jobCardsContainer">
                <!-- Jobs will be loaded here via AJAX -->
            </div>

            <!-- Load More Button -->
            <div class="skd-load-more-container">
                <button class="skd-btn skd-btn-outline skd-btn-large" id="loadMoreJobs" onclick="loadMoreJobs()">
                    Load More Jobs
                </button>
            </div>

            <!-- No Jobs Found -->
            <div class="skd-no-jobs" id="noJobsFound" style="display: none;">
                <div class="skd-no-jobs-content">
                    <span class="dashicons dashicons-search"></span>
                    <h3>No jobs found</h3>
                    <p>Try adjusting your filters or search terms to find more opportunities.</p>
                    <button class="skd-btn skd-btn-primary" onclick="clearAllFilters()">Clear All Filters</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Job Application Modal -->
<div id="jobApplicationModal" class="skd-modal" style="display: none;">
    <div class="skd-modal-content skd-modal-large">
        <div class="skd-modal-header">
            <h3>Apply for Job</h3>
            <button class="skd-modal-close" onclick="closeJobApplication()">&times;</button>
        </div>
        <div class="skd-modal-body">
            <form id="jobApplicationForm">
                <input type="hidden" id="applyJobId" value="">

                <div class="skd-job-summary" id="jobSummaryForApplication">
                    <!-- Job summary will be populated here -->
                </div>

                <div class="skd-form-row">
                    <div class="skd-form-group">
                        <label>Full Name *</label>
                        <input type="text" id="applicantName" required>
                    </div>
                    <div class="skd-form-group">
                        <label>Email Address *</label>
                        <input type="email" id="applicantEmail" required>
                    </div>
                </div>

                <div class="skd-form-row">
                    <div class="skd-form-group">
                        <label>Phone Number</label>
                        <input type="tel" id="applicantPhone">
                    </div>
                    <div class="skd-form-group">
                        <label>Location</label>
                        <input type="text" id="applicantLocation" placeholder="City, State/Country">
                    </div>
                </div>

                <div class="skd-form-group">
                    <label>Cover Letter *</label>
                    <textarea id="coverLetter" required placeholder="Why are you interested in this position? What makes you a great fit?"></textarea>
                </div>

                <div class="skd-form-group">
                    <label>Resume/Portfolio Link</label>
                    <input type="url" id="portfolioLink" placeholder="Link to your resume, portfolio, or LinkedIn profile">
                </div>

                <div class="skd-form-group">
                    <label>Expected Rate</label>
                    <div class="skd-rate-input">
                        <input type="number" id="expectedRate" placeholder="0" min="0">
                        <select id="rateType">
                            <option value="hourly">per hour</option>
                            <option value="daily">per day</option>
                            <option value="weekly">per week</option>
                            <option value="monthly">per month</option>
                            <option value="project">per project</option>
                        </select>
                    </div>
                </div>

                <div class="skd-form-group">
                    <label>Availability</label>
                    <select id="applicantAvailability">
                        <option value="">Select availability</option>
                        <option value="immediately">Available Immediately</option>
                        <option value="1-week">Available in 1 week</option>
                        <option value="2-weeks">Available in 2 weeks</option>
                        <option value="1-month">Available in 1 month</option>
                        <option value="flexible">Flexible</option>
                    </select>
                </div>

                <div class="skd-form-group">
                    <label class="skd-checkbox-label">
                        <input type="checkbox" id="agreeToTerms" required>
                        <span class="skd-checkbox-custom"></span>
                        I agree to the <a href="/terms/" target="_blank">Terms of Service</a> and <a href="/privacy/" target="_blank">Privacy Policy</a>
                    </label>
                </div>

                <div class="skd-form-actions">
                    <button type="button" class="skd-btn skd-btn-secondary" onclick="closeJobApplication()">Cancel</button>
                    <button type="submit" class="skd-btn skd-btn-primary skd-btn-large">
                        <span class="dashicons dashicons-email"></span>
                        Submit Application
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Job Alerts Modal -->
<div id="jobAlertsModal" class="skd-modal" style="display: none;">
    <div class="skd-modal-content">
        <div class="skd-modal-header">
            <h3>Job Alerts</h3>
            <button class="skd-modal-close" onclick="toggleJobAlerts()">&times;</button>
        </div>
        <div class="skd-modal-body">
            <p>Get notified when new jobs matching your criteria are posted.</p>

            <form id="jobAlertsForm">
                <div class="skd-form-group">
                    <label>Email Address *</label>
                    <input type="email" id="alertEmail" required>
                </div>

                <div class="skd-form-group">
                    <label>Keywords</label>
                    <input type="text" id="alertKeywords" placeholder="e.g. residential, commercial, 3D rendering">
                </div>

                <div class="skd-form-group">
                    <label>Job Type</label>
                    <select id="alertJobType" multiple>
                        <?php foreach ($job_types as $value => $label): ?>
                            <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="skd-form-group">
                    <label>Location</label>
                    <input type="text" id="alertLocation" placeholder="City, State/Country or 'Remote'">
                </div>

                <div class="skd-form-group">
                    <label>Alert Frequency</label>
                    <select id="alertFrequency">
                        <option value="immediate">Immediate</option>
                        <option value="daily">Daily Digest</option>
                        <option value="weekly">Weekly Digest</option>
                    </select>
                </div>

                <div class="skd-form-actions">
                    <button type="button" class="skd-btn skd-btn-secondary" onclick="toggleJobAlerts()">Cancel</button>
                    <button type="submit" class="skd-btn skd-btn-primary">Set Up Alerts</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let currentPage = 1;
    let loading = false;
    let allJobsLoaded = false;

    document.addEventListener('DOMContentLoaded', function() {
        loadJobs();
    });

    function loadJobs(page = 1) {
        if (loading) return;

        loading = true;

        const formData = new FormData();
        formData.append('action', 'skd_fetch_jobs');
        formData.append('page', page);
        formData.append('per_page', 10);

        // Add filter values
        const filters = getFilterValues();
        for (const [key, value] of Object.entries(filters)) {
            if (Array.isArray(value)) {
                value.forEach(v => formData.append(key + '[]', v));
            } else {
                formData.append(key, value);
            }
        }

        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (page === 1) {
                        document.getElementById('jobCardsContainer').innerHTML = data.data.html;
                    } else {
                        document.getElementById('jobCardsContainer').innerHTML += data.data.html;
                    }

                    document.getElementById('jobCount').textContent = data.data.total;
                    document.getElementById('totalJobs').textContent = data.data.total;

                    if (data.data.total === 0) {
                        document.getElementById('noJobsFound').style.display = 'block';
                        document.querySelector('.skd-load-more-container').style.display = 'none';
                    } else {
                        document.getElementById('noJobsFound').style.display = 'none';
                        document.querySelector('.skd-load-more-container').style.display = data.data.has_more ? 'block' : 'none';
                    }

                    allJobsLoaded = !data.data.has_more;
                }

                loading = false;
            })
            .catch(error => {
                console.error('Error loading jobs:', error);
                loading = false;
            });
    }

    function filterJobs() {
        currentPage = 1;
        allJobsLoaded = false;
        loadJobs(1);
    }

    function loadMoreJobs() {
        if (!allJobsLoaded) {
            currentPage++;
            loadJobs(currentPage);
        }
    }

    function getFilterValues() {
        const filters = {};

        // Search term
        filters.search = document.getElementById('jobSearch').value;

        // Location
        filters.location = document.getElementById('locationFilter').value;

        // Sort
        filters.sort = document.getElementById('jobSort').value;

        // Date posted
        filters.date_posted = document.querySelector('input[name="datePosted"]:checked').value;

        // Checkboxes
        filters.job_types = Array.from(document.querySelectorAll('.skd-filter-section:nth-child(3) input[type="checkbox"]:checked')).map(cb => cb.value);
        filters.experience_levels = Array.from(document.querySelectorAll('.skd-filter-section:nth-child(4) input[type="checkbox"]:checked')).map(cb => cb.value);
        filters.specializations = Array.from(document.querySelectorAll('.skd-filter-section:nth-child(5) input[type="checkbox"]:checked')).map(cb => cb.value);
        filters.budget_ranges = Array.from(document.querySelectorAll('.skd-filter-section:nth-child(6) input[type="checkbox"]:checked')).map(cb => cb.value);

        // Remote only
        filters.remote_only = document.querySelector('input[value="remote"]:checked') ? true : false;

        return filters;
    }

    function clearAllFilters() {
        // Clear search inputs
        document.getElementById('jobSearch').value = '';
        document.getElementById('locationFilter').value = '';

        // Clear checkboxes
        document.querySelectorAll('.skd-filter-section input[type="checkbox"]').forEach(cb => cb.checked = false);

        // Reset radio buttons
        document.querySelector('input[name="datePosted"][value="any"]').checked = true;

        // Reset sort
        document.getElementById('jobSort').value = 'newest';

        filterJobs();
    }

    function applyForJob(jobId) {
        // Fetch job details and show application modal
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=skd_get_job_details&job_id=' + jobId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('applyJobId').value = jobId;
                    document.getElementById('jobSummaryForApplication').innerHTML = data.data.summary_html;
                    document.getElementById('jobApplicationModal').style.display = 'block';
                }
            });
    }

    function closeJobApplication() {
        document.getElementById('jobApplicationModal').style.display = 'none';
        document.getElementById('jobApplicationForm').reset();
    }

    function toggleJobAlerts() {
        const modal = document.getElementById('jobAlertsModal');
        modal.style.display = modal.style.display === 'block' ? 'none' : 'block';
    }

    function saveJob(jobId, button) {
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=skd_save_job&job_id=' + jobId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    button.classList.toggle('saved');
                    button.innerHTML = button.classList.contains('saved') ?
                        '<span class="dashicons dashicons-heart"></span> Saved' :
                        '<span class="dashicons dashicons-heart-empty"></span> Save';
                }
            });
    }

    // Form submissions
    document.getElementById('jobApplicationForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData();
        formData.append('action', 'skd_submit_job_application');
        formData.append('job_id', document.getElementById('applyJobId').value);
        formData.append('name', document.getElementById('applicantName').value);
        formData.append('email', document.getElementById('applicantEmail').value);
        formData.append('phone', document.getElementById('applicantPhone').value);
        formData.append('location', document.getElementById('applicantLocation').value);
        formData.append('cover_letter', document.getElementById('coverLetter').value);
        formData.append('portfolio_link', document.getElementById('portfolioLink').value);
        formData.append('expected_rate', document.getElementById('expectedRate').value);
        formData.append('rate_type', document.getElementById('rateType').value);
        formData.append('availability', document.getElementById('applicantAvailability').value);

        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Application submitted successfully!');
                    closeJobApplication();
                } else {
                    alert('Error submitting application. Please try again.');
                }
            });
    });

    document.getElementById('jobAlertsForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        formData.append('action', 'skd_set_job_alerts');

        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Job alerts set up successfully!');
                    toggleJobAlerts();
                    this.reset();
                } else {
                    alert('Error setting up alerts. Please try again.');
                }
            });
    });
</script>