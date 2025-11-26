<?php

/**
 * Template for Post Job Form
 * Allows employers/studios to post new design jobs
 */

if (!defined('ABSPATH')) {
    exit;
}

global $wpdb;

// Get required data for form options
$specializations_table = $wpdb->prefix . 'skd_pl_specializations';
$skills_table = $wpdb->prefix . 'skd_pl_skills';

$specializations = $wpdb->get_results("SELECT * FROM $specializations_table ORDER BY name");
$skills = $wpdb->get_results("SELECT * FROM $skills_table ORDER BY name");

// Job form options
$job_types = [
    'full-time' => 'Full-time Position',
    'part-time' => 'Part-time Position',
    'contract' => 'Contract Work',
    'freelance' => 'Freelance Project',
    'temporary' => 'Temporary Position'
];

$experience_levels = [
    'entry' => 'Entry Level (0-2 years)',
    'mid' => 'Mid Level (2-5 years)',
    'senior' => 'Senior Level (5-10 years)',
    'executive' => 'Executive (10+ years)'
];

$urgency_levels = [
    'low' => 'No Rush - 30+ days',
    'medium' => 'Standard - 2-4 weeks',
    'high' => 'Urgent - 1-2 weeks',
    'asap' => 'ASAP - Within days'
];

$project_durations = [
    '1-week' => '1 week or less',
    '1-month' => '1 month',
    '3-months' => '2-3 months',
    '6-months' => '3-6 months',
    '1-year' => '6-12 months',
    'ongoing' => 'Ongoing/Long-term'
];
?>

<div class="skd-post-job-wrapper">
    <!-- Post Job Header -->
    <div class="skd-post-job-header">
        <div class="skd-header-content">
            <h1>Post a Design Job</h1>
            <p>Connect with talented interior design professionals. Post your project or position to reach thousands of qualified candidates.</p>

            <!-- Pricing Preview -->
            <div class="skd-pricing-preview">
                <div class="skd-pricing-option active">
                    <span class="skd-price">Free</span>
                    <span class="skd-duration">30 days</span>
                    <span class="skd-features">Basic listing</span>
                </div>
                <div class="skd-pricing-option">
                    <span class="skd-price">$49</span>
                    <span class="skd-duration">60 days</span>
                    <span class="skd-features">Featured + promoted</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Job Post Form -->
    <div class="skd-job-form-container">
        <form id="postJobForm" class="skd-job-form">

            <!-- Step 1: Basic Information -->
            <div class="skd-form-step active" data-step="1">
                <div class="skd-step-header">
                    <h2>Basic Information</h2>
                    <p>Tell us about the position or project you're looking to fill.</p>
                </div>

                <div class="skd-form-row">
                    <div class="skd-form-group full-width">
                        <label>Job Title *</label>
                        <input type="text" id="jobTitle" required placeholder="e.g. Senior Interior Designer, 3D Visualization Artist">
                        <small>Be specific and descriptive to attract the right candidates</small>
                    </div>
                </div>

                <div class="skd-form-row">
                    <div class="skd-form-group">
                        <label>Job Type *</label>
                        <select id="jobType" required>
                            <option value="">Select job type</option>
                            <?php foreach ($job_types as $value => $label): ?>
                                <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="skd-form-group">
                        <label>Experience Level *</label>
                        <select id="experienceLevel" required>
                            <option value="">Select experience level</option>
                            <?php foreach ($experience_levels as $value => $label): ?>
                                <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="skd-form-row">
                    <div class="skd-form-group">
                        <label>Location *</label>
                        <input type="text" id="jobLocation" required placeholder="City, State/Country or 'Remote'">
                        <small>Specify if position is remote, hybrid, or on-site</small>
                    </div>
                    <div class="skd-form-group">
                        <label>
                            <input type="checkbox" id="isRemote">
                            This is a remote position
                        </label>
                    </div>
                </div>

                <div class="skd-form-group full-width">
                    <label>Company/Organization Name *</label>
                    <input type="text" id="companyName" required placeholder="Your company or studio name">
                </div>

                <div class="skd-form-group full-width">
                    <label>Short Description *</label>
                    <textarea id="jobSummary" required placeholder="Brief overview of the position/project (2-3 sentences)" maxlength="300"></textarea>
                    <small><span id="summaryCount">0</span>/300 characters</small>
                </div>
            </div>

            <!-- Step 2: Job Details -->
            <div class="skd-form-step" data-step="2">
                <div class="skd-step-header">
                    <h2>Job Details</h2>
                    <p>Provide detailed information about the role and requirements.</p>
                </div>

                <div class="skd-form-group full-width">
                    <label>Detailed Job Description *</label>
                    <div class="skd-editor-toolbar">
                        <button type="button" onclick="formatText('bold')"><strong>B</strong></button>
                        <button type="button" onclick="formatText('italic')"><em>I</em></button>
                        <button type="button" onclick="formatText('insertUnorderedList')">• List</button>
                        <button type="button" onclick="formatText('insertOrderedList')">1. List</button>
                    </div>
                    <div id="jobDescription" contenteditable="true" class="skd-rich-editor" placeholder="Describe the role, responsibilities, and what you're looking for in detail..."></div>
                    <small>Include key responsibilities, day-to-day tasks, and company culture</small>
                </div>

                <div class="skd-form-group full-width">
                    <label>Requirements & Qualifications</label>
                    <textarea id="jobRequirements" placeholder="• Minimum 3 years experience in residential design&#10;• Proficiency in AutoCAD and SketchUp&#10;• Strong communication skills&#10;• Portfolio of completed projects"></textarea>
                </div>

                <div class="skd-form-row">
                    <div class="skd-form-group">
                        <label>Specialization Areas</label>
                        <div class="skd-multi-select">
                            <?php foreach ($specializations as $spec): ?>
                                <label class="skd-checkbox-label">
                                    <input type="checkbox" name="specializations[]" value="<?php echo esc_attr($spec->id); ?>">
                                    <span class="skd-checkbox-custom"></span>
                                    <?php echo esc_html($spec->name); ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="skd-form-group">
                        <label>Required Skills/Software</label>
                        <div class="skd-multi-select">
                            <?php foreach ($skills as $skill): ?>
                                <label class="skd-checkbox-label">
                                    <input type="checkbox" name="skills[]" value="<?php echo esc_attr($skill->id); ?>">
                                    <span class="skd-checkbox-custom"></span>
                                    <?php echo esc_html($skill->name); ?>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div class="skd-form-row">
                    <div class="skd-form-group">
                        <label>Project Duration</label>
                        <select id="projectDuration">
                            <option value="">Select duration</option>
                            <?php foreach ($project_durations as $value => $label): ?>
                                <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="skd-form-group">
                        <label>Urgency</label>
                        <select id="jobUrgency">
                            <option value="medium">Standard - 2-4 weeks</option>
                            <?php foreach ($urgency_levels as $value => $label): ?>
                                <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Step 3: Compensation & Benefits -->
            <div class="skd-form-step" data-step="3">
                <div class="skd-step-header">
                    <h2>Compensation & Benefits</h2>
                    <p>Set competitive compensation to attract top talent.</p>
                </div>

                <div class="skd-compensation-options">
                    <div class="skd-compensation-type">
                        <label class="skd-radio-label">
                            <input type="radio" name="compensationType" value="hourly" onchange="toggleCompensationFields()">
                            <span class="skd-radio-custom"></span>
                            Hourly Rate
                        </label>
                    </div>
                    <div class="skd-compensation-type">
                        <label class="skd-radio-label">
                            <input type="radio" name="compensationType" value="salary" onchange="toggleCompensationFields()">
                            <span class="skd-radio-custom"></span>
                            Annual Salary
                        </label>
                    </div>
                    <div class="skd-compensation-type">
                        <label class="skd-radio-label">
                            <input type="radio" name="compensationType" value="project" onchange="toggleCompensationFields()">
                            <span class="skd-radio-custom"></span>
                            Project Fee
                        </label>
                    </div>
                    <div class="skd-compensation-type">
                        <label class="skd-radio-label">
                            <input type="radio" name="compensationType" value="negotiate" onchange="toggleCompensationFields()">
                            <span class="skd-radio-custom"></span>
                            Negotiable
                        </label>
                    </div>
                </div>

                <div class="skd-salary-range" id="salaryRange" style="display: none;">
                    <div class="skd-form-row">
                        <div class="skd-form-group">
                            <label>Minimum Amount</label>
                            <div class="skd-currency-input">
                                <span class="skd-currency-symbol">$</span>
                                <input type="number" id="salaryMin" placeholder="0" min="0">
                            </div>
                        </div>
                        <div class="skd-form-group">
                            <label>Maximum Amount</label>
                            <div class="skd-currency-input">
                                <span class="skd-currency-symbol">$</span>
                                <input type="number" id="salaryMax" placeholder="0" min="0">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="skd-form-group full-width">
                    <label>Benefits & Perks</label>
                    <div class="skd-benefits-grid">
                        <label class="skd-checkbox-label">
                            <input type="checkbox" name="benefits[]" value="health-insurance">
                            <span class="skd-checkbox-custom"></span>
                            Health Insurance
                        </label>
                        <label class="skd-checkbox-label">
                            <input type="checkbox" name="benefits[]" value="flexible-hours">
                            <span class="skd-checkbox-custom"></span>
                            Flexible Hours
                        </label>
                        <label class="skd-checkbox-label">
                            <input type="checkbox" name="benefits[]" value="remote-work">
                            <span class="skd-checkbox-custom"></span>
                            Remote Work Options
                        </label>
                        <label class="skd-checkbox-label">
                            <input type="checkbox" name="benefits[]" value="professional-development">
                            <span class="skd-checkbox-custom"></span>
                            Professional Development
                        </label>
                        <label class="skd-checkbox-label">
                            <input type="checkbox" name="benefits[]" value="equipment">
                            <span class="skd-checkbox-custom"></span>
                            Equipment/Software Provided
                        </label>
                        <label class="skd-checkbox-label">
                            <input type="checkbox" name="benefits[]" value="vacation">
                            <span class="skd-checkbox-custom"></span>
                            Paid Time Off
                        </label>
                    </div>
                </div>

                <div class="skd-form-group full-width">
                    <label>Additional Benefits</label>
                    <textarea id="additionalBenefits" placeholder="Describe any other benefits, perks, or unique aspects of working with your company..."></textarea>
                </div>
            </div>

            <!-- Step 4: Application Process -->
            <div class="skd-form-step" data-step="4">
                <div class="skd-step-header">
                    <h2>Application Process</h2>
                    <p>Configure how candidates can apply and contact you.</p>
                </div>

                <div class="skd-form-row">
                    <div class="skd-form-group">
                        <label>Contact Email *</label>
                        <input type="email" id="contactEmail" required placeholder="jobs@yourcompany.com">
                    </div>
                    <div class="skd-form-group">
                        <label>Contact Phone</label>
                        <input type="tel" id="contactPhone" placeholder="+1 (555) 123-4567">
                    </div>
                </div>

                <div class="skd-form-group">
                    <label>Application Deadline</label>
                    <input type="date" id="applicationDeadline" min="<?php echo date('Y-m-d'); ?>">
                    <small>Leave blank for no deadline</small>
                </div>

                <div class="skd-form-group">
                    <label>How to Apply</label>
                    <div class="skd-application-methods">
                        <label class="skd-radio-label">
                            <input type="radio" name="applicationMethod" value="platform" checked>
                            <span class="skd-radio-custom"></span>
                            Through interiAssist platform
                        </label>
                        <label class="skd-radio-label">
                            <input type="radio" name="applicationMethod" value="email">
                            <span class="skd-radio-custom"></span>
                            Direct email contact
                        </label>
                        <label class="skd-radio-label">
                            <input type="radio" name="applicationMethod" value="external">
                            <span class="skd-radio-custom"></span>
                            External application link
                        </label>
                    </div>
                </div>

                <div class="skd-form-group" id="externalLinkGroup" style="display: none;">
                    <label>External Application URL</label>
                    <input type="url" id="externalApplicationUrl" placeholder="https://your-website.com/apply">
                </div>

                <div class="skd-form-group">
                    <label>Application Instructions</label>
                    <textarea id="applicationInstructions" placeholder="Any specific instructions for applicants, such as what to include in their portfolio or cover letter..."></textarea>
                </div>

                <div class="skd-form-group">
                    <label>Screening Questions (Optional)</label>
                    <div class="skd-screening-questions">
                        <div class="skd-question-item">
                            <input type="text" placeholder="e.g. How many years of commercial design experience do you have?">
                            <button type="button" class="skd-btn skd-btn-small" onclick="removeQuestion(this)">Remove</button>
                        </div>
                        <button type="button" class="skd-btn skd-btn-outline" onclick="addScreeningQuestion()">
                            <span class="dashicons dashicons-plus"></span>
                            Add Question
                        </button>
                    </div>
                </div>
            </div>

            <!-- Step 5: Review & Publish -->
            <div class="skd-form-step" data-step="5">
                <div class="skd-step-header">
                    <h2>Review & Publish</h2>
                    <p>Review your job posting before publishing it live.</p>
                </div>

                <!-- Job Preview -->
                <div class="skd-job-preview" id="jobPreview">
                    <!-- Preview will be generated here -->
                </div>

                <!-- Publishing Options -->
                <div class="skd-publishing-options">
                    <h3>Publishing Options</h3>

                    <div class="skd-pricing-cards">
                        <div class="skd-pricing-card" data-plan="free">
                            <div class="skd-plan-header">
                                <h4>Free Listing</h4>
                                <div class="skd-plan-price">$0</div>
                            </div>
                            <ul class="skd-plan-features">
                                <li>30 days active</li>
                                <li>Standard search visibility</li>
                                <li>Basic application management</li>
                                <li>Email notifications</li>
                            </ul>
                            <label class="skd-radio-label">
                                <input type="radio" name="publishingPlan" value="free" checked>
                                <span class="skd-radio-custom"></span>
                                Choose Free
                            </label>
                        </div>

                        <div class="skd-pricing-card featured" data-plan="featured">
                            <div class="skd-plan-badge">Most Popular</div>
                            <div class="skd-plan-header">
                                <h4>Featured Listing</h4>
                                <div class="skd-plan-price">$49</div>
                            </div>
                            <ul class="skd-plan-features">
                                <li>60 days active</li>
                                <li>Featured in search results</li>
                                <li>Highlighted with badge</li>
                                <li>Premium application tools</li>
                                <li>Priority support</li>
                                <li>Analytics dashboard</li>
                            </ul>
                            <label class="skd-radio-label">
                                <input type="radio" name="publishingPlan" value="featured">
                                <span class="skd-radio-custom"></span>
                                Choose Featured
                            </label>
                        </div>
                    </div>
                </div>

                <div class="skd-form-group">
                    <label class="skd-checkbox-label">
                        <input type="checkbox" id="agreeToTerms" required>
                        <span class="skd-checkbox-custom"></span>
                        I agree to the <a href="/terms/" target="_blank">Terms of Service</a> and <a href="/posting-guidelines/" target="_blank">Job Posting Guidelines</a>
                    </label>
                </div>
            </div>

            <!-- Form Navigation -->
            <div class="skd-form-navigation">
                <button type="button" class="skd-btn skd-btn-secondary" id="prevStep" onclick="previousStep()" style="display: none;">
                    <span class="dashicons dashicons-arrow-left-alt"></span>
                    Previous
                </button>

                <div class="skd-step-indicator">
                    <span class="skd-step active">1</span>
                    <span class="skd-step">2</span>
                    <span class="skd-step">3</span>
                    <span class="skd-step">4</span>
                    <span class="skd-step">5</span>
                </div>

                <button type="button" class="skd-btn skd-btn-primary" id="nextStep" onclick="nextStep()">
                    Next
                    <span class="dashicons dashicons-arrow-right-alt"></span>
                </button>

                <button type="submit" class="skd-btn skd-btn-primary skd-btn-large" id="submitJob" style="display: none;">
                    <span class="dashicons dashicons-megaphone"></span>
                    Publish Job
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="skd-modal" style="display: none;">
    <div class="skd-modal-content">
        <div class="skd-modal-header">
            <h3>Job Posted Successfully!</h3>
        </div>
        <div class="skd-modal-body">
            <div class="skd-success-content">
                <span class="dashicons dashicons-yes-alt skd-success-icon"></span>
                <p>Your job has been posted and is now live on interiAssist. You'll start receiving applications shortly!</p>

                <div class="skd-success-actions">
                    <a href="/manage-jobs/" class="skd-btn skd-btn-primary">Manage My Jobs</a>
                    <a href="/job-board/" class="skd-btn skd-btn-secondary">View Job Board</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentStep = 1;
    const totalSteps = 5;

    document.addEventListener('DOMContentLoaded', function() {
        setupFormValidation();
        setupCharacterCount();
        setupRichEditor();
        updateStepVisibility();
    });

    function nextStep() {
        if (validateCurrentStep()) {
            if (currentStep < totalSteps) {
                currentStep++;
                updateStepVisibility();
                updateStepIndicator();

                if (currentStep === 5) {
                    generateJobPreview();
                }
            }
        }
    }

    function previousStep() {
        if (currentStep > 1) {
            currentStep--;
            updateStepVisibility();
            updateStepIndicator();
        }
    }

    function updateStepVisibility() {
        // Hide all steps
        document.querySelectorAll('.skd-form-step').forEach(step => {
            step.classList.remove('active');
        });

        // Show current step
        document.querySelector(`[data-step="${currentStep}"]`).classList.add('active');

        // Update navigation buttons
        document.getElementById('prevStep').style.display = currentStep > 1 ? 'block' : 'none';
        document.getElementById('nextStep').style.display = currentStep < totalSteps ? 'block' : 'none';
        document.getElementById('submitJob').style.display = currentStep === totalSteps ? 'block' : 'none';
    }

    function updateStepIndicator() {
        document.querySelectorAll('.skd-step-indicator .skd-step').forEach((indicator, index) => {
            indicator.classList.toggle('active', index < currentStep);
            indicator.classList.toggle('completed', index < currentStep - 1);
        });
    }

    function validateCurrentStep() {
        const currentStepElement = document.querySelector(`[data-step="${currentStep}"]`);
        const requiredFields = currentStepElement.querySelectorAll('[required]');

        for (const field of requiredFields) {
            if (!field.value.trim()) {
                field.focus();
                field.classList.add('error');
                return false;
            }
            field.classList.remove('error');
        }

        return true;
    }

    function toggleCompensationFields() {
        const compensationType = document.querySelector('input[name="compensationType"]:checked').value;
        const salaryRange = document.getElementById('salaryRange');

        if (compensationType === 'negotiate') {
            salaryRange.style.display = 'none';
        } else {
            salaryRange.style.display = 'block';

            // Update labels based on compensation type
            const labels = salaryRange.querySelectorAll('label');
            if (compensationType === 'hourly') {
                labels[0].textContent = 'Minimum Hourly Rate';
                labels[1].textContent = 'Maximum Hourly Rate';
            } else if (compensationType === 'salary') {
                labels[0].textContent = 'Minimum Annual Salary';
                labels[1].textContent = 'Maximum Annual Salary';
            } else if (compensationType === 'project') {
                labels[0].textContent = 'Minimum Project Fee';
                labels[1].textContent = 'Maximum Project Fee';
            }
        }
    }

    function setupCharacterCount() {
        const summaryField = document.getElementById('jobSummary');
        const countElement = document.getElementById('summaryCount');

        summaryField.addEventListener('input', function() {
            const count = this.value.length;
            countElement.textContent = count;

            if (count > 300) {
                countElement.style.color = '#d93025';
            } else {
                countElement.style.color = '#5f6368';
            }
        });
    }

    function setupRichEditor() {
        const editor = document.getElementById('jobDescription');

        editor.addEventListener('focus', function() {
            if (this.textContent.trim() === 'Describe the role, responsibilities, and what you\'re looking for in detail...') {
                this.textContent = '';
            }
        });

        editor.addEventListener('blur', function() {
            if (this.textContent.trim() === '') {
                this.textContent = 'Describe the role, responsibilities, and what you\'re looking for in detail...';
            }
        });
    }

    function formatText(command) {
        document.execCommand(command, false, null);
        document.getElementById('jobDescription').focus();
    }

    function addScreeningQuestion() {
        const container = document.querySelector('.skd-screening-questions');
        const addButton = container.querySelector('.skd-btn-outline');

        const questionDiv = document.createElement('div');
        questionDiv.className = 'skd-question-item';
        questionDiv.innerHTML = `
        <input type="text" placeholder="Enter your screening question...">
        <button type="button" class="skd-btn skd-btn-small" onclick="removeQuestion(this)">Remove</button>
    `;

        container.insertBefore(questionDiv, addButton);
    }

    function removeQuestion(button) {
        button.parentElement.remove();
    }

    function generateJobPreview() {
        const preview = document.getElementById('jobPreview');

        // Get form data
        const title = document.getElementById('jobTitle').value;
        const company = document.getElementById('companyName').value;
        const location = document.getElementById('jobLocation').value;
        const type = document.getElementById('jobType').value;
        const summary = document.getElementById('jobSummary').value;

        // Generate preview HTML
        preview.innerHTML = `
        <div class="skd-job-preview-card">
            <div class="skd-job-preview-header">
                <h3>${title}</h3>
                <div class="skd-job-meta">
                    <span class="skd-company">${company}</span>
                    <span class="skd-location">${location}</span>
                    <span class="skd-job-type">${type}</span>
                </div>
            </div>
            <div class="skd-job-preview-body">
                <p>${summary}</p>
                <div class="skd-job-actions-preview">
                    <button class="skd-btn skd-btn-primary" disabled>Apply Now</button>
                    <button class="skd-btn skd-btn-outline" disabled>Save Job</button>
                </div>
            </div>
        </div>
    `;
    }

    function setupFormValidation() {
        // Application method change handler
        document.querySelectorAll('input[name="applicationMethod"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const externalGroup = document.getElementById('externalLinkGroup');
                externalGroup.style.display = this.value === 'external' ? 'block' : 'none';
            });
        });
    }

    // Form submission
    document.getElementById('postJobForm').addEventListener('submit', function(e) {
        e.preventDefault();

        if (!validateCurrentStep()) {
            return;
        }

        // Gather all form data
        const formData = new FormData();
        formData.append('action', 'skd_submit_job_post');

        // Basic information
        formData.append('job_title', document.getElementById('jobTitle').value);
        formData.append('job_type', document.getElementById('jobType').value);
        formData.append('experience_level', document.getElementById('experienceLevel').value);
        formData.append('location', document.getElementById('jobLocation').value);
        formData.append('is_remote', document.getElementById('isRemote').checked);
        formData.append('company_name', document.getElementById('companyName').value);
        formData.append('job_summary', document.getElementById('jobSummary').value);

        // Job details
        formData.append('job_description', document.getElementById('jobDescription').innerHTML);
        formData.append('job_requirements', document.getElementById('jobRequirements').value);
        formData.append('project_duration', document.getElementById('projectDuration').value);
        formData.append('job_urgency', document.getElementById('jobUrgency').value);

        // Specializations and skills
        const specializations = Array.from(document.querySelectorAll('input[name="specializations[]"]:checked')).map(cb => cb.value);
        const skills = Array.from(document.querySelectorAll('input[name="skills[]"]:checked')).map(cb => cb.value);
        formData.append('specializations', JSON.stringify(specializations));
        formData.append('skills', JSON.stringify(skills));

        // Compensation
        const compensationType = document.querySelector('input[name="compensationType"]:checked').value;
        formData.append('compensation_type', compensationType);
        formData.append('salary_min', document.getElementById('salaryMin').value);
        formData.append('salary_max', document.getElementById('salaryMax').value);

        // Benefits
        const benefits = Array.from(document.querySelectorAll('input[name="benefits[]"]:checked')).map(cb => cb.value);
        formData.append('benefits', JSON.stringify(benefits));
        formData.append('additional_benefits', document.getElementById('additionalBenefits').value);

        // Application process
        formData.append('contact_email', document.getElementById('contactEmail').value);
        formData.append('contact_phone', document.getElementById('contactPhone').value);
        formData.append('application_deadline', document.getElementById('applicationDeadline').value);
        formData.append('application_method', document.querySelector('input[name="applicationMethod"]:checked').value);
        formData.append('external_application_url', document.getElementById('externalApplicationUrl').value);
        formData.append('application_instructions', document.getElementById('applicationInstructions').value);

        // Screening questions
        const screeningQuestions = Array.from(document.querySelectorAll('.skd-question-item input')).map(input => input.value).filter(q => q.trim());
        formData.append('screening_questions', JSON.stringify(screeningQuestions));

        // Publishing plan
        formData.append('publishing_plan', document.querySelector('input[name="publishingPlan"]:checked').value);

        // Submit the form
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('successModal').style.display = 'block';
                } else {
                    alert('Error posting job: ' + (data.data || 'Please try again.'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error posting job. Please try again.');
            });
    });
</script>