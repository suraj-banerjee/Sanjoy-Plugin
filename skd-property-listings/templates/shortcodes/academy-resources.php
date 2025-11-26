<?php

/**
 * Template for Academy/Resources/Community Section
 * Educational resources, courses, and community features
 */

if (!defined('ABSPATH')) {
    exit;
}

// Get categories for filtering
$resource_categories = [
    'courses' => 'Online Courses',
    'tutorials' => 'Video Tutorials',
    'articles' => 'Articles & Guides',
    'templates' => 'Design Templates',
    'tools' => 'Design Tools',
    'webinars' => 'Webinars',
    'certification' => 'Certification Programs'
];

$community_categories = [
    'discussions' => 'General Discussions',
    'showcase' => 'Portfolio Showcase',
    'feedback' => 'Design Feedback',
    'collaboration' => 'Collaboration Requests',
    'events' => 'Events & Networking',
    'jobs' => 'Job Opportunities'
];
?>

<div class="skd-academy-wrapper">
    <!-- Hero Section -->
    <div class="skd-academy-hero">
        <div class="skd-hero-content">
            <h1>Design Academy & Resources</h1>
            <p>Enhance your skills with our comprehensive collection of courses, tutorials, and resources designed specifically for interior design professionals.</p>

            <!-- Navigation Tabs -->
            <div class="skd-academy-tabs">
                <button class="skd-tab-btn active" onclick="showTab('academy')">
                    <span class="dashicons dashicons-book-alt"></span>
                    Academy
                </button>
                <button class="skd-tab-btn" onclick="showTab('resources')">
                    <span class="dashicons dashicons-download"></span>
                    Resources
                </button>
                <button class="skd-tab-btn" onclick="showTab('community')">
                    <span class="dashicons dashicons-groups"></span>
                    Community
                </button>
            </div>
        </div>
    </div>

    <!-- Academy Tab Content -->
    <div id="academyTab" class="skd-tab-content active">
        <div class="skd-academy-filters">
            <div class="skd-filter-group">
                <label>Category:</label>
                <select id="academyCategory" onchange="filterAcademyContent()">
                    <option value="">All Categories</option>
                    <?php foreach ($resource_categories as $value => $label): ?>
                        <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="skd-filter-group">
                <label>Level:</label>
                <select id="academyLevel" onchange="filterAcademyContent()">
                    <option value="">All Levels</option>
                    <option value="beginner">Beginner</option>
                    <option value="intermediate">Intermediate</option>
                    <option value="advanced">Advanced</option>
                </select>
            </div>

            <div class="skd-filter-group">
                <label>Duration:</label>
                <select id="academyDuration" onchange="filterAcademyContent()">
                    <option value="">Any Duration</option>
                    <option value="short">Under 1 hour</option>
                    <option value="medium">1-5 hours</option>
                    <option value="long">5+ hours</option>
                </select>
            </div>

            <div class="skd-filter-group">
                <label>Price:</label>
                <select id="academyPrice" onchange="filterAcademyContent()">
                    <option value="">All Prices</option>
                    <option value="free">Free</option>
                    <option value="paid">Paid</option>
                    <option value="premium">Premium Only</option>
                </select>
            </div>

            <div class="skd-search-group">
                <input type="text" id="academySearch" placeholder="Search courses..." onkeyup="filterAcademyContent()">
                <span class="dashicons dashicons-search"></span>
            </div>
        </div>

        <!-- Featured Courses -->
        <div class="skd-featured-section">
            <h2>Featured Courses</h2>
            <div class="skd-course-grid">
                <div class="skd-course-card featured">
                    <div class="skd-course-image">
                        <img src="https://via.placeholder.com/400x250/1a73e8/ffffff?text=3D+Rendering" alt="3D Rendering Course">
                        <span class="skd-course-badge premium">Premium</span>
                    </div>
                    <div class="skd-course-content">
                        <h3>Advanced 3D Rendering Techniques</h3>
                        <p>Master photorealistic rendering with V-Ray, Corona, and Lumion for stunning interior visualizations.</p>
                        <div class="skd-course-meta">
                            <span class="skd-instructor">by Sarah Chen</span>
                            <span class="skd-rating">
                                <span class="dashicons dashicons-star-filled"></span>
                                4.9 (234)
                            </span>
                        </div>
                        <div class="skd-course-info">
                            <span class="skd-duration">12 hours</span>
                            <span class="skd-level">Advanced</span>
                            <span class="skd-price">$299</span>
                        </div>
                    </div>
                </div>

                <div class="skd-course-card">
                    <div class="skd-course-image">
                        <img src="https://via.placeholder.com/400x250/34a853/ffffff?text=Color+Theory" alt="Color Theory Course">
                        <span class="skd-course-badge free">Free</span>
                    </div>
                    <div class="skd-course-content">
                        <h3>Interior Color Theory Fundamentals</h3>
                        <p>Learn the psychology of color and how to create harmonious color palettes for any space.</p>
                        <div class="skd-course-meta">
                            <span class="skd-instructor">by Michael Rodriguez</span>
                            <span class="skd-rating">
                                <span class="dashicons dashicons-star-filled"></span>
                                4.7 (156)
                            </span>
                        </div>
                        <div class="skd-course-info">
                            <span class="skd-duration">3 hours</span>
                            <span class="skd-level">Beginner</span>
                            <span class="skd-price">Free</span>
                        </div>
                    </div>
                </div>

                <div class="skd-course-card">
                    <div class="skd-course-image">
                        <img src="https://via.placeholder.com/400x250/ea4335/ffffff?text=AutoCAD" alt="AutoCAD Course">
                        <span class="skd-course-badge new">New</span>
                    </div>
                    <div class="skd-course-content">
                        <h3>AutoCAD for Interior Designers</h3>
                        <p>Complete guide to creating professional floor plans, elevations, and construction drawings.</p>
                        <div class="skd-course-meta">
                            <span class="skd-instructor">by Jennifer Liu</span>
                            <span class="skd-rating">
                                <span class="dashicons dashicons-star-filled"></span>
                                4.8 (89)
                            </span>
                        </div>
                        <div class="skd-course-info">
                            <span class="skd-duration">8 hours</span>
                            <span class="skd-level">Intermediate</span>
                            <span class="skd-price">$199</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Course Categories -->
        <div class="skd-categories-section">
            <h2>Browse by Category</h2>
            <div class="skd-category-grid">
                <?php foreach ($resource_categories as $category => $label): ?>
                    <div class="skd-category-card" onclick="filterByCategory('<?php echo esc_attr($category); ?>')">
                        <div class="skd-category-icon">
                            <span class="dashicons dashicons-<?php
                                                                echo $category === 'courses' ? 'book-alt' : ($category === 'tutorials' ? 'video-alt3' : ($category === 'articles' ? 'media-text' : ($category === 'templates' ? 'layout' : ($category === 'tools' ? 'admin-tools' : ($category === 'webinars' ? 'video-alt2' : 'awards')))));
                                                                ?>"></span>
                        </div>
                        <h3><?php echo esc_html($label); ?></h3>
                        <span class="skd-category-count">
                            <?php echo rand(15, 85); ?> items
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Certification Programs -->
        <div class="skd-certification-section">
            <h2>Certification Programs</h2>
            <div class="skd-cert-programs">
                <div class="skd-cert-card">
                    <div class="skd-cert-badge">
                        <img src="https://via.placeholder.com/100x100/1a73e8/ffffff?text=VDA" alt="VDA Certification">
                    </div>
                    <div class="skd-cert-content">
                        <h3>Certified Virtual Design Assistant (CVDA)</h3>
                        <p>Comprehensive certification program covering all aspects of virtual design assistance, client communication, and project management.</p>
                        <ul>
                            <li>20 hours of training content</li>
                            <li>Hands-on projects and assessments</li>
                            <li>Industry recognition certificate</li>
                            <li>1 year continuing education access</li>
                        </ul>
                        <div class="skd-cert-price">$499</div>
                        <button class="skd-btn skd-btn-primary">Enroll Now</button>
                    </div>
                </div>

                <div class="skd-cert-card">
                    <div class="skd-cert-badge">
                        <img src="https://via.placeholder.com/100x100/34a853/ffffff?text=3D" alt="3D Specialist Certification">
                    </div>
                    <div class="skd-cert-content">
                        <h3>3D Visualization Specialist</h3>
                        <p>Advanced certification in 3D rendering, virtual reality, and architectural visualization techniques.</p>
                        <ul>
                            <li>30 hours of advanced training</li>
                            <li>Software-specific modules</li>
                            <li>Portfolio development guidance</li>
                            <li>Industry networking opportunities</li>
                        </ul>
                        <div class="skd-cert-price">$799</div>
                        <button class="skd-btn skd-btn-primary">Enroll Now</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resources Tab Content -->
    <div id="resourcesTab" class="skd-tab-content">
        <div class="skd-resources-grid">
            <!-- Templates Section -->
            <div class="skd-resource-section">
                <h2>Design Templates</h2>
                <div class="skd-template-grid">
                    <div class="skd-template-card">
                        <div class="skd-template-preview">
                            <img src="https://via.placeholder.com/300x200/1a73e8/ffffff?text=Mood+Board" alt="Mood Board Template">
                        </div>
                        <h3>Mood Board Templates</h3>
                        <p>Professional mood board layouts for client presentations</p>
                        <button class="skd-btn skd-btn-outline">Download</button>
                    </div>

                    <div class="skd-template-card">
                        <div class="skd-template-preview">
                            <img src="https://via.placeholder.com/300x200/34a853/ffffff?text=Proposal" alt="Proposal Template">
                        </div>
                        <h3>Project Proposals</h3>
                        <p>Customizable proposal templates for design projects</p>
                        <button class="skd-btn skd-btn-outline">Download</button>
                    </div>

                    <div class="skd-template-card">
                        <div class="skd-template-preview">
                            <img src="https://via.placeholder.com/300x200/ea4335/ffffff?text=Invoice" alt="Invoice Template">
                        </div>
                        <h3>Invoice Templates</h3>
                        <p>Professional invoice formats for design services</p>
                        <button class="skd-btn skd-btn-outline">Download</button>
                    </div>
                </div>
            </div>

            <!-- Tools Section -->
            <div class="skd-resource-section">
                <h2>Design Tools</h2>
                <div class="skd-tools-grid">
                    <div class="skd-tool-card">
                        <div class="skd-tool-icon">
                            <span class="dashicons dashicons-admin-tools"></span>
                        </div>
                        <h3>Color Palette Generator</h3>
                        <p>Generate harmonious color schemes for your projects</p>
                        <button class="skd-btn skd-btn-primary">Launch Tool</button>
                    </div>

                    <div class="skd-tool-card">
                        <div class="skd-tool-icon">
                            <span class="dashicons dashicons-calculator"></span>
                        </div>
                        <h3>Room Measurement Calculator</h3>
                        <p>Calculate square footage and material requirements</p>
                        <button class="skd-btn skd-btn-primary">Launch Tool</button>
                    </div>

                    <div class="skd-tool-card">
                        <div class="skd-tool-icon">
                            <span class="dashicons dashicons-money-alt"></span>
                        </div>
                        <h3>Project Cost Estimator</h3>
                        <p>Estimate project costs and create budgets</p>
                        <button class="skd-btn skd-btn-primary">Launch Tool</button>
                    </div>
                </div>
            </div>

            <!-- Guides Section -->
            <div class="skd-resource-section">
                <h2>Guides & Checklists</h2>
                <div class="skd-guides-list">
                    <div class="skd-guide-item">
                        <div class="skd-guide-icon">
                            <span class="dashicons dashicons-media-document"></span>
                        </div>
                        <div class="skd-guide-content">
                            <h3>Client Onboarding Checklist</h3>
                            <p>Step-by-step guide for smooth client onboarding process</p>
                            <span class="skd-guide-type">PDF Guide</span>
                        </div>
                        <button class="skd-btn skd-btn-outline">Download</button>
                    </div>

                    <div class="skd-guide-item">
                        <div class="skd-guide-icon">
                            <span class="dashicons dashicons-media-document"></span>
                        </div>
                        <div class="skd-guide-content">
                            <h3>Space Planning Guidelines</h3>
                            <p>Comprehensive guide to effective space planning principles</p>
                            <span class="skd-guide-type">PDF Guide</span>
                        </div>
                        <button class="skd-btn skd-btn-outline">Download</button>
                    </div>

                    <div class="skd-guide-item">
                        <div class="skd-guide-icon">
                            <span class="dashicons dashicons-media-document"></span>
                        </div>
                        <div class="skd-guide-content">
                            <h3>Furniture Specification Sheet</h3>
                            <p>Template for detailed furniture and finish specifications</p>
                            <span class="skd-guide-type">Excel Template</span>
                        </div>
                        <button class="skd-btn skd-btn-outline">Download</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Community Tab Content -->
    <div id="communityTab" class="skd-tab-content">
        <div class="skd-community-header">
            <h2>Design Community</h2>
            <p>Connect, share, and learn from interior design professionals worldwide</p>
            <button class="skd-btn skd-btn-primary" onclick="openNewTopicModal()">
                <span class="dashicons dashicons-plus"></span>
                Start New Discussion
            </button>
        </div>

        <!-- Community Stats -->
        <div class="skd-community-stats">
            <div class="skd-stat-card">
                <span class="skd-stat-number">2,847</span>
                <span class="skd-stat-label">Active Members</span>
            </div>
            <div class="skd-stat-card">
                <span class="skd-stat-number">1,234</span>
                <span class="skd-stat-label">Discussions</span>
            </div>
            <div class="skd-stat-card">
                <span class="skd-stat-number">5,678</span>
                <span class="skd-stat-label">Posts This Month</span>
            </div>
            <div class="skd-stat-card">
                <span class="skd-stat-number">89%</span>
                <span class="skd-stat-label">Response Rate</span>
            </div>
        </div>

        <!-- Community Categories -->
        <div class="skd-community-categories">
            <h3>Discussion Categories</h3>
            <div class="skd-category-list">
                <?php foreach ($community_categories as $category => $label): ?>
                    <div class="skd-community-category" onclick="filterCommunityPosts('<?php echo esc_attr($category); ?>')">
                        <div class="skd-category-info">
                            <h4><?php echo esc_html($label); ?></h4>
                            <p><?php echo rand(50, 300); ?> topics • <?php echo rand(200, 1500); ?> posts</p>
                        </div>
                        <div class="skd-category-activity">
                            <span class="skd-last-post"><?php echo rand(1, 24); ?>h ago</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Recent Discussions -->
        <div class="skd-recent-discussions">
            <h3>Recent Discussions</h3>
            <div class="skd-discussion-list">
                <div class="skd-discussion-item">
                    <div class="skd-discussion-avatar">
                        <img src="https://via.placeholder.com/50x50/1a73e8/ffffff?text=SJ" alt="Sarah Johnson">
                    </div>
                    <div class="skd-discussion-content">
                        <h4><a href="#">Help with small space color schemes</a></h4>
                        <p>I'm working on a 500 sq ft apartment and struggling with color choices that won't make it feel cramped...</p>
                        <div class="skd-discussion-meta">
                            <span class="skd-author">by Sarah Johnson</span>
                            <span class="skd-category">General Discussions</span>
                            <span class="skd-time">2 hours ago</span>
                        </div>
                    </div>
                    <div class="skd-discussion-stats">
                        <span class="skd-replies">12 replies</span>
                        <span class="skd-views">156 views</span>
                    </div>
                </div>

                <div class="skd-discussion-item">
                    <div class="skd-discussion-avatar">
                        <img src="https://via.placeholder.com/50x50/34a853/ffffff?text=MR" alt="Mike Rodriguez">
                    </div>
                    <div class="skd-discussion-content">
                        <h4><a href="#">Looking for feedback on living room design</a></h4>
                        <p>Just finished this modern living room design for a client. Would love some feedback from the community...</p>
                        <div class="skd-discussion-meta">
                            <span class="skd-author">by Mike Rodriguez</span>
                            <span class="skd-category">Design Feedback</span>
                            <span class="skd-time">4 hours ago</span>
                        </div>
                    </div>
                    <div class="skd-discussion-stats">
                        <span class="skd-replies">8 replies</span>
                        <span class="skd-views">98 views</span>
                    </div>
                </div>

                <div class="skd-discussion-item">
                    <div class="skd-discussion-avatar">
                        <img src="https://via.placeholder.com/50x50/ea4335/ffffff?text=AL" alt="Anna Lee">
                    </div>
                    <div class="skd-discussion-content">
                        <h4><a href="#">Sustainable materials for commercial projects</a></h4>
                        <p>Does anyone have experience with eco-friendly materials for large commercial spaces? Looking for recommendations...</p>
                        <div class="skd-discussion-meta">
                            <span class="skd-author">by Anna Lee</span>
                            <span class="skd-category">General Discussions</span>
                            <span class="skd-time">6 hours ago</span>
                        </div>
                    </div>
                    <div class="skd-discussion-stats">
                        <span class="skd-replies">15 replies</span>
                        <span class="skd-views">234 views</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Events -->
        <div class="skd-community-events">
            <h3>Upcoming Events</h3>
            <div class="skd-events-list">
                <div class="skd-event-item">
                    <div class="skd-event-date">
                        <span class="skd-event-month">Nov</span>
                        <span class="skd-event-day">15</span>
                    </div>
                    <div class="skd-event-content">
                        <h4>Virtual Design Challenge</h4>
                        <p>Monthly design challenge focused on sustainable residential design</p>
                        <span class="skd-event-time">2:00 PM EST</span>
                    </div>
                    <button class="skd-btn skd-btn-outline">Join Event</button>
                </div>

                <div class="skd-event-item">
                    <div class="skd-event-date">
                        <span class="skd-event-month">Nov</span>
                        <span class="skd-event-day">22</span>
                    </div>
                    <div class="skd-event-content">
                        <h4>Color Theory Workshop</h4>
                        <p>Interactive webinar on advanced color theory for interior designers</p>
                        <span class="skd-event-time">7:00 PM EST</span>
                    </div>
                    <button class="skd-btn skd-btn-outline">Register</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Topic Modal -->
<div id="newTopicModal" class="skd-modal" style="display: none;">
    <div class="skd-modal-content">
        <div class="skd-modal-header">
            <h3>Start New Discussion</h3>
            <button class="skd-modal-close" onclick="closeNewTopicModal()">&times;</button>
        </div>
        <div class="skd-modal-body">
            <form id="newTopicForm">
                <div class="skd-form-group">
                    <label>Category *</label>
                    <select id="topicCategory" required>
                        <option value="">Select category</option>
                        <?php foreach ($community_categories as $value => $label): ?>
                            <option value="<?php echo esc_attr($value); ?>"><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="skd-form-group">
                    <label>Topic Title *</label>
                    <input type="text" id="topicTitle" required placeholder="Enter your topic title...">
                </div>
                <div class="skd-form-group">
                    <label>Description *</label>
                    <textarea id="topicDescription" required placeholder="Describe your question or discussion topic..."></textarea>
                </div>
                <div class="skd-form-group">
                    <label>Tags</label>
                    <input type="text" id="topicTags" placeholder="Add tags separated by commas...">
                </div>
                <div class="skd-form-actions">
                    <button type="button" class="skd-btn skd-btn-secondary" onclick="closeNewTopicModal()">Cancel</button>
                    <button type="submit" class="skd-btn skd-btn-primary">Start Discussion</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function showTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.skd-tab-content').forEach(tab => {
            tab.classList.remove('active');
        });

        // Remove active class from all tab buttons
        document.querySelectorAll('.skd-tab-btn').forEach(btn => {
            btn.classList.remove('active');
        });

        // Show selected tab content
        document.getElementById(tabName + 'Tab').classList.add('active');

        // Add active class to clicked tab button
        event.target.closest('.skd-tab-btn').classList.add('active');
    }

    function filterAcademyContent() {
        // Implement academy content filtering
        const category = document.getElementById('academyCategory').value;
        const level = document.getElementById('academyLevel').value;
        const duration = document.getElementById('academyDuration').value;
        const price = document.getElementById('academyPrice').value;
        const search = document.getElementById('academySearch').value;

        console.log('Filtering academy content:', {
            category,
            level,
            duration,
            price,
            search
        });
    }

    function filterByCategory(category) {
        document.getElementById('academyCategory').value = category;
        filterAcademyContent();
    }

    function filterCommunityPosts(category) {
        console.log('Filtering community posts by:', category);
    }

    function openNewTopicModal() {
        document.getElementById('newTopicModal').style.display = 'block';
    }

    function closeNewTopicModal() {
        document.getElementById('newTopicModal').style.display = 'none';
    }

    // New topic form submission
    document.getElementById('newTopicForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = {
            category: document.getElementById('topicCategory').value,
            title: document.getElementById('topicTitle').value,
            description: document.getElementById('topicDescription').value,
            tags: document.getElementById('topicTags').value
        };

        console.log('New topic:', formData);

        // Here you would submit to your backend
        alert('Discussion started successfully!');
        closeNewTopicModal();
        document.getElementById('newTopicForm').reset();
    });
</script>