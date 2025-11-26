<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class SKD_PL_Seed_Data
{
    /**
     * Insert initial seed data for interiAssist
     */
    public static function insert_seed_data()
    {
        global $wpdb;

        self::create_user_roles();
        self::insert_design_categories();
        self::insert_design_skills();
        self::insert_design_services();
        self::insert_design_specializations();
        self::insert_design_certifications();
        self::insert_portfolio_categories();
        self::insert_design_locations();
        self::insert_sample_pricing_plans();
        self::insert_project_types();
        self::insert_service_types();
        self::insert_availability_types();
        self::insert_timezones();
        self::insert_experience_levels();
        self::insert_response_times();
        self::insert_languages();
        self::insert_demo_vdas();
    }

    /**
     * Create custom user roles for interiAssist platform
     */
    private static function create_user_roles()
    {
        // Ensure we have access to WordPress user role functions
        if (!function_exists('add_role')) {
            return;
        }

        // Remove existing custom roles if they exist
        if (function_exists('remove_role')) {
            remove_role('vda_user');
            remove_role('studio_user');
            remove_role('employer_user');
        }

        // Base capabilities for all custom roles
        $base_capabilities = [
            'read' => true,
            'edit_posts' => false,
            'delete_posts' => false,
        ];

        // 🧑‍🎨 Virtual Design Assistant (VDA) Role
        $vda_capabilities = array_merge($base_capabilities, [
            'create_vda_profile' => true,
            'edit_own_vda_profile' => true,
            'apply_to_jobs' => true,
            'join_agency' => true,
            'manage_own_portfolio' => true,
            'view_job_listings' => true,
            'receive_messages' => true,
            'send_messages' => true,
            'upload_files' => true,
        ]);

        add_role('vda_user', 'Virtual Design Assistant', $vda_capabilities);

        // 🏢 Studio/Agency Role
        $studio_capabilities = array_merge($base_capabilities, [
            'create_studio_profile' => true,
            'edit_own_studio_profile' => true,
            'manage_team_members' => true,
            'invite_vda_members' => true,
            'post_projects' => true,
            'manage_own_projects' => true,
            'view_vda_profiles' => true,
            'hire_vdas' => true,
            'manage_client_relationships' => true,
            'receive_messages' => true,
            'send_messages' => true,
            'upload_files' => true,
            'manage_portfolio' => true,
        ]);

        add_role('studio_user', 'Studio/Agency', $studio_capabilities);

        // 🛋️ Interior Designer/Employer Role
        $employer_capabilities = array_merge($base_capabilities, [
            'create_employer_profile' => true,
            'edit_own_employer_profile' => true,
            'post_job_listings' => true,
            'manage_own_jobs' => true,
            'view_vda_profiles' => true,
            'view_studio_profiles' => true,
            'hire_vdas' => true,
            'hire_studios' => true,
            'manage_projects' => true,
            'receive_messages' => true,
            'send_messages' => true,
            'upload_files' => true,
            'make_payments' => true,
        ]);

        add_role('employer_user', 'Interior Designer/Employer', $employer_capabilities);

        // Add capabilities to administrator role for managing the platform
        if (function_exists('get_role')) {
            $admin_role = get_role('administrator');
            if ($admin_role) {
                $admin_capabilities = [
                    'manage_all_vdas',
                    'manage_all_studios',
                    'manage_all_employers',
                    'manage_all_jobs',
                    'manage_all_projects',
                    'moderate_platform',
                    'view_platform_analytics',
                ];

                foreach ($admin_capabilities as $capability) {
                    $admin_role->add_cap($capability);
                }
            }
        }
    }

    /**
     * Insert design-specific categories
     */
    private static function insert_design_categories()
    {
        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_categories';

        $categories = [
            ['name' => 'Residential Design', 'slug' => 'residential-design', 'parent_id' => 0, 'description' => 'Home interior design services'],
            ['name' => 'Commercial Design', 'slug' => 'commercial-design', 'parent_id' => 0, 'description' => 'Office and commercial space design'],
            ['name' => 'Hospitality Design', 'slug' => 'hospitality-design', 'parent_id' => 0, 'description' => 'Hotels, restaurants, and hospitality spaces'],
            ['name' => 'Retail Design', 'slug' => 'retail-design', 'parent_id' => 0, 'description' => 'Retail stores and showrooms'],
            ['name' => 'Healthcare Design', 'slug' => 'healthcare-design', 'parent_id' => 0, 'description' => 'Medical facilities and healthcare spaces'],
            ['name' => 'Educational Design', 'slug' => 'educational-design', 'parent_id' => 0, 'description' => 'Schools, universities, and learning spaces'],
        ];

        foreach ($categories as $category) {
            $existing = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM $table WHERE slug = %s",
                $category['slug']
            ));

            if (!$existing) {
                $wpdb->insert($table, $category);
            }
        }
    }

    /**
     * Insert design skills and software
     */
    private static function insert_design_skills()
    {
        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_skills';

        $skills = [
            // 3D & Modeling Software
            ['name' => 'SketchUp', 'slug' => 'sketchup', 'category' => 'software', 'is_featured' => 1],
            ['name' => 'Layout', 'slug' => 'layout', 'category' => 'software'],
            ['name' => '3ds Max', 'slug' => '3ds-max', 'category' => 'software', 'is_featured' => 1],
            ['name' => 'Blender', 'slug' => 'blender', 'category' => 'software'],
            ['name' => 'Cinema 4D', 'slug' => 'cinema-4d', 'category' => 'software'],
            ['name' => 'Rhino', 'slug' => 'rhino', 'category' => 'software'],

            // CAD Software
            ['name' => 'AutoCAD', 'slug' => 'autocad', 'category' => 'software', 'is_featured' => 1],
            ['name' => 'Revit', 'slug' => 'revit', 'category' => 'software', 'is_featured' => 1],
            ['name' => 'Chief Architect', 'slug' => 'chief-architect', 'category' => 'software'],
            ['name' => '2020 Design', 'slug' => '2020-design', 'category' => 'software'],
            ['name' => 'ArchiCAD', 'slug' => 'archicad', 'category' => 'software'],

            // Design & Graphics Software
            ['name' => 'Photoshop', 'slug' => 'photoshop', 'category' => 'software'],
            ['name' => 'Illustrator', 'slug' => 'illustrator', 'category' => 'software'],
            ['name' => 'Canva', 'slug' => 'canva', 'category' => 'software'],
            ['name' => 'Procreate', 'slug' => 'procreate', 'category' => 'software'],

            // Interior Design Software
            ['name' => 'Coohom', 'slug' => 'coohom', 'category' => 'software'],
            ['name' => 'Homestyler', 'slug' => 'homestyler', 'category' => 'software'],
            ['name' => 'Foyr Neo', 'slug' => 'foyr-neo', 'category' => 'software'],
            ['name' => 'DesignFiles', 'slug' => 'designfiles', 'category' => 'software'],

            // Rendering Software
            ['name' => 'D5 Render', 'slug' => 'd5-render', 'category' => 'software'],
            ['name' => 'Enscape', 'slug' => 'enscape', 'category' => 'software'],
            ['name' => 'Twinmotion', 'slug' => 'twinmotion', 'category' => 'software'],
            ['name' => 'V-Ray', 'slug' => 'vray', 'category' => 'software', 'is_featured' => 1],
            ['name' => 'Lumion', 'slug' => 'lumion', 'category' => 'software'],
            ['name' => 'Maxwell Render', 'slug' => 'maxwell-render', 'category' => 'software'],
            ['name' => 'Corona Renderer', 'slug' => 'corona-renderer', 'category' => 'software'],
            ['name' => 'SU Podium', 'slug' => 'su-podium', 'category' => 'software'],
            ['name' => 'Keyshot', 'slug' => 'keyshot', 'category' => 'software'],

            // Floor Plan & Space Planning Software
            ['name' => 'Floorplanner', 'slug' => 'floorplanner', 'category' => 'software'],
            ['name' => 'RoomSketcher', 'slug' => 'roomsketcher', 'category' => 'software'],
            ['name' => 'RoomStyler', 'slug' => 'roomstyler', 'category' => 'software'],
            ['name' => 'Planner 5D', 'slug' => 'planner-5d', 'category' => 'software'],
            ['name' => 'HomeByMe', 'slug' => 'homebyme', 'category' => 'software'],

            // Design Skills
            ['name' => 'Space Planning', 'slug' => 'space-planning', 'category' => 'design_skill'],
            ['name' => 'Color Theory', 'slug' => 'color-theory', 'category' => 'design_skill'],
            ['name' => 'Lighting Design', 'slug' => 'lighting-design', 'category' => 'design_skill'],
            ['name' => 'Material Selection', 'slug' => 'material-selection', 'category' => 'design_skill'],
            ['name' => 'Furniture Selection', 'slug' => 'furniture-selection', 'category' => 'design_skill'],
            ['name' => 'Concept Development', 'slug' => 'concept-development', 'category' => 'design_skill'],

            // Technical Skills
            ['name' => '3D Modeling', 'slug' => '3d-modeling', 'category' => 'technical_skill'],
            ['name' => '3D Rendering', 'slug' => '3d-rendering', 'category' => 'technical_skill'],
            ['name' => 'Technical Drawing', 'slug' => 'technical-drawing', 'category' => 'technical_skill'],
            ['name' => 'Construction Documentation', 'slug' => 'construction-documentation', 'category' => 'technical_skill'],
            ['name' => 'BIM Modeling', 'slug' => 'bim-modeling', 'category' => 'technical_skill'],

            // Soft Skills
            ['name' => 'Client Communication', 'slug' => 'client-communication', 'category' => 'soft_skill'],
            ['name' => 'Project Management', 'slug' => 'project-management', 'category' => 'soft_skill'],
            ['name' => 'Time Management', 'slug' => 'time-management', 'category' => 'soft_skill'],
            ['name' => 'Team Collaboration', 'slug' => 'team-collaboration', 'category' => 'soft_skill'],
        ];

        foreach ($skills as $skill) {
            $existing = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM $table WHERE slug = %s",
                $skill['slug']
            ));

            if (!$existing) {
                if (!isset($skill['is_featured'])) {
                    $skill['is_featured'] = 0;
                }
                $wpdb->insert($table, $skill);
            }
        }
    }

    /**
     * Insert design services
     */
    private static function insert_design_services()
    {
        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_services';

        $services = [
            ['name' => '3D Interior Rendering', 'slug' => '3d-interior-rendering', 'category' => 'visualization'],
            ['name' => '3D Exterior Rendering', 'slug' => '3d-exterior-rendering', 'category' => 'visualization'],
            ['name' => 'Animation Walkthrough', 'slug' => 'animation-walkthrough', 'category' => 'visualization'],
            ['name' => 'Virtual Reality (VR)', 'slug' => 'virtual-reality', 'category' => 'visualization'],
            ['name' => 'CAD Drafting', 'slug' => 'cad-drafting', 'category' => 'technical'],
            ['name' => 'Floor Plan Creation', 'slug' => 'floor-plan-creation', 'category' => 'technical'],
            ['name' => 'Construction Documents', 'slug' => 'construction-documents', 'category' => 'technical'],
            ['name' => 'As-Built Drawings', 'slug' => 'as-built-drawings', 'category' => 'technical'],
            ['name' => 'Concept Design', 'slug' => 'concept-design', 'category' => 'design'],
            ['name' => 'Space Planning', 'slug' => 'space-planning', 'category' => 'design'],
            ['name' => 'Furniture Layout', 'slug' => 'furniture-layout', 'category' => 'design'],
            ['name' => 'Material & Finish Selection', 'slug' => 'material-finish-selection', 'category' => 'design'],
            ['name' => 'Color Consultation', 'slug' => 'color-consultation', 'category' => 'design'],
            ['name' => 'Lighting Design', 'slug' => 'lighting-design', 'category' => 'design'],
            ['name' => 'Mood Boards', 'slug' => 'mood-boards', 'category' => 'presentation'],
            ['name' => 'Design Presentation', 'slug' => 'design-presentation', 'category' => 'presentation'],
            ['name' => 'Project Management', 'slug' => 'project-management', 'category' => 'management'],
            ['name' => 'Design Consultation', 'slug' => 'design-consultation', 'category' => 'consultation'],
        ];

        foreach ($services as $service) {
            $existing = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM $table WHERE slug = %s",
                $service['slug']
            ));

            if (!$existing) {
                $service['is_popular'] = in_array($service['name'], ['3D Interior Rendering', 'CAD Drafting', 'Space Planning', 'Concept Design']) ? 1 : 0;
                $wpdb->insert($table, $service);
            }
        }
    }

    /**
     * Insert design specializations
     */
    private static function insert_design_specializations()
    {
        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_specializations';

        $specializations = [
            ['name' => 'Kitchen Design', 'slug' => 'kitchen-design', 'parent_id' => 0],
            ['name' => 'Bathroom Design', 'slug' => 'bathroom-design', 'parent_id' => 0],
            ['name' => 'Living Room Design', 'slug' => 'living-room-design', 'parent_id' => 0],
            ['name' => 'Bedroom Design', 'slug' => 'bedroom-design', 'parent_id' => 0],
            ['name' => 'Office Design', 'slug' => 'office-design', 'parent_id' => 0],
            ['name' => 'Restaurant Design', 'slug' => 'restaurant-design', 'parent_id' => 0],
            ['name' => 'Hotel Design', 'slug' => 'hotel-design', 'parent_id' => 0],
            ['name' => 'Retail Store Design', 'slug' => 'retail-store-design', 'parent_id' => 0],
            ['name' => 'Healthcare Facility Design', 'slug' => 'healthcare-facility-design', 'parent_id' => 0],
            ['name' => 'Educational Space Design', 'slug' => 'educational-space-design', 'parent_id' => 0],
            ['name' => 'Sustainable Design', 'slug' => 'sustainable-design', 'parent_id' => 0],
            ['name' => 'Universal Design', 'slug' => 'universal-design', 'parent_id' => 0],
            ['name' => 'Luxury Design', 'slug' => 'luxury-design', 'parent_id' => 0],
            ['name' => 'Minimalist Design', 'slug' => 'minimalist-design', 'parent_id' => 0],
            ['name' => 'Traditional Design', 'slug' => 'traditional-design', 'parent_id' => 0],
            ['name' => 'Modern Design', 'slug' => 'modern-design', 'parent_id' => 0],
            ['name' => 'Contemporary Design', 'slug' => 'contemporary-design', 'parent_id' => 0],
        ];

        foreach ($specializations as $spec) {
            $existing = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM $table WHERE slug = %s",
                $spec['slug']
            ));

            if (!$existing) {
                $wpdb->insert($table, $spec);
            }
        }
    }

    /**
     * Insert design certifications and badges
     */
    private static function insert_design_certifications()
    {
        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_certifications';

        $certifications = [
            ['name' => 'SketchUp Certified', 'slug' => 'sketchup-certified', 'issuer' => 'SketchUp', 'verification_required' => 1],
            ['name' => 'AutoCAD Certified Professional', 'slug' => 'autocad-certified-professional', 'issuer' => 'Autodesk', 'verification_required' => 1],
            ['name' => 'Revit Architecture Professional', 'slug' => 'revit-architecture-professional', 'issuer' => 'Autodesk', 'verification_required' => 1],
            ['name' => '3ds Max Certified Professional', 'slug' => '3ds-max-certified-professional', 'issuer' => 'Autodesk', 'verification_required' => 1],
            ['name' => 'V-Ray Certified Professional', 'slug' => 'vray-certified-professional', 'issuer' => 'Chaos Group', 'verification_required' => 1],
            ['name' => 'NCIDQ Certificate', 'slug' => 'ncidq-certificate', 'issuer' => 'NCIDQ', 'verification_required' => 1],
            ['name' => 'LEED AP', 'slug' => 'leed-ap', 'issuer' => 'GBCI', 'verification_required' => 1],
            ['name' => 'Certified Interior Designer', 'slug' => 'certified-interior-designer', 'issuer' => 'Various', 'verification_required' => 1],
            ['name' => 'Adobe Certified Expert', 'slug' => 'adobe-certified-expert', 'issuer' => 'Adobe', 'verification_required' => 1],
            ['name' => 'interiAssist Verified', 'slug' => 'interiassist-verified', 'issuer' => 'interiAssist', 'verification_required' => 0],
            ['name' => 'Portfolio Reviewed', 'slug' => 'portfolio-reviewed', 'issuer' => 'interiAssist', 'verification_required' => 0],
            ['name' => 'Top Rated Professional', 'slug' => 'top-rated-professional', 'issuer' => 'interiAssist', 'verification_required' => 0],
        ];

        foreach ($certifications as $cert) {
            $existing = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM $table WHERE slug = %s",
                $cert['slug']
            ));

            if (!$existing) {
                $wpdb->insert($table, $cert);
            }
        }
    }

    /**
     * Insert portfolio categories for project classification
     */
    private static function insert_portfolio_categories()
    {
        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_portfolio_categories';

        $categories = [
            ['name' => 'Residential Interior', 'slug' => 'residential-interior', 'description' => 'Home interior design projects'],
            ['name' => 'Commercial Interior', 'slug' => 'commercial-interior', 'description' => 'Office, retail, and commercial spaces'],
            ['name' => 'Hospitality Design', 'slug' => 'hospitality-design', 'description' => 'Hotels, restaurants, and leisure spaces'],
            ['name' => '3D Visualization', 'slug' => '3d-visualization', 'description' => '3D renders and visualizations'],
            ['name' => 'Kitchen Design', 'slug' => 'kitchen-design', 'description' => 'Kitchen interior and layout design'],
            ['name' => 'Bathroom Design', 'slug' => 'bathroom-design', 'description' => 'Bathroom interior design'],
            ['name' => 'Living Room', 'slug' => 'living-room', 'description' => 'Living room and lounge design'],
            ['name' => 'Bedroom Design', 'slug' => 'bedroom-design', 'description' => 'Bedroom interior design'],
            ['name' => 'Office Design', 'slug' => 'office-design', 'description' => 'Workspace and office interior'],
            ['name' => 'Retail Design', 'slug' => 'retail-design', 'description' => 'Retail store and showroom design'],
            ['name' => 'Restaurant Design', 'slug' => 'restaurant-design', 'description' => 'Restaurant and cafe interior'],
            ['name' => 'Outdoor & Landscape', 'slug' => 'outdoor-landscape', 'description' => 'Outdoor spaces and landscape design'],
            ['name' => 'Floor Plans', 'slug' => 'floor-plans', 'description' => 'Floor plans and space planning'],
            ['name' => 'Furniture Design', 'slug' => 'furniture-design', 'description' => 'Custom furniture design'],
            ['name' => 'Lighting Design', 'slug' => 'lighting-design', 'description' => 'Lighting plans and design'],
            ['name' => 'Concept Design', 'slug' => 'concept-design', 'description' => 'Concept boards and mood boards'],
        ];

        foreach ($categories as $index => $category) {
            $existing = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM $table WHERE slug = %s",
                $category['slug']
            ));

            if (!$existing) {
                $wpdb->insert($table, array_merge($category, ['sort_order' => $index + 1]));
            }
        }
    }

    /**
     * Insert global locations for remote work
     */
    private static function insert_design_locations()
    {
        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_locations';

        $locations = [
            // Major regions
            ['name' => 'North America', 'slug' => 'north-america', 'parent_id' => 0],
            ['name' => 'Europe', 'slug' => 'europe', 'parent_id' => 0],
            ['name' => 'Asia Pacific', 'slug' => 'asia-pacific', 'parent_id' => 0],
            ['name' => 'South America', 'slug' => 'south-america', 'parent_id' => 0],
            ['name' => 'Africa', 'slug' => 'africa', 'parent_id' => 0],
            ['name' => 'Middle East', 'slug' => 'middle-east', 'parent_id' => 0],

            // Major countries (can be expanded)
            ['name' => 'United States', 'slug' => 'united-states', 'parent_id' => 1],
            ['name' => 'Canada', 'slug' => 'canada', 'parent_id' => 1],
            ['name' => 'United Kingdom', 'slug' => 'united-kingdom', 'parent_id' => 2],
            ['name' => 'Germany', 'slug' => 'germany', 'parent_id' => 2],
            ['name' => 'Australia', 'slug' => 'australia', 'parent_id' => 3],
            ['name' => 'India', 'slug' => 'india', 'parent_id' => 3],
            ['name' => 'Singapore', 'slug' => 'singapore', 'parent_id' => 3],

            // Special location
            ['name' => 'Remote Worldwide', 'slug' => 'remote-worldwide', 'parent_id' => 0],
        ];

        foreach ($locations as $location) {
            $existing = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM $table WHERE slug = %s",
                $location['slug']
            ));

            if (!$existing) {
                $wpdb->insert($table, $location);
            }
        }
    }

    /**
     * Insert sample pricing plans for agencies/studios with member limits
     */
    private static function insert_sample_pricing_plans()
    {
        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_price_plans';

        $pricing_plans = [
            [
                'plan_name' => 'Solo VDA',
                'slug' => 'solo-vda',
                'plan_description' => 'Perfect for individual Virtual Design Assistants starting their freelance journey',
                'plan_type' => 'individual',
                'price' => 0.00,
                'is_free' => 1,
                'listing_duration' => 30,
                'duration_unit' => 'days',
                'no_of_listing' => 3,
                'mark_as_unlimited' => 0,
                'no_of_feature_listing' => 1,
                'mark_feature_unlimited' => 0,
                'plan_status' => 'active',
                'max_team_members' => 1, // Solo only
                'contact_owner' => 'yes',
                'customer_review' => 'yes',
            ],
            [
                'plan_name' => 'Studio Starter',
                'slug' => 'studio-starter',
                'plan_description' => 'For small studios with up to 2 team members',
                'plan_type' => 'studio',
                'price' => 29.00,
                'is_free' => 0,
                'listing_duration' => 60,
                'duration_unit' => 'days',
                'no_of_listing' => 10,
                'mark_as_unlimited' => 0,
                'no_of_feature_listing' => 3,
                'mark_feature_unlimited' => 0,
                'plan_status' => 'active',
                'max_team_members' => 2,
                'contact_owner' => 'yes',
                'customer_review' => 'yes',
                'featured_the_list' => 'yes',
            ],
            [
                'plan_name' => 'Studio Professional',
                'slug' => 'studio-professional',
                'plan_description' => 'For growing studios with up to 5 team members',
                'plan_type' => 'studio',
                'price' => 79.00,
                'is_free' => 0,
                'listing_duration' => 90,
                'duration_unit' => 'days',
                'no_of_listing' => 25,
                'mark_as_unlimited' => 0,
                'no_of_feature_listing' => 10,
                'mark_feature_unlimited' => 0,
                'plan_status' => 'active',
                'max_team_members' => 5,
                'contact_owner' => 'yes',
                'customer_review' => 'yes',
                'featured_the_list' => 'yes',
                'recomend_plan' => 'yes', // Recommended plan
            ],
            [
                'plan_name' => 'Agency Enterprise',
                'slug' => 'agency-enterprise',
                'plan_description' => 'For established agencies with unlimited team members',
                'plan_type' => 'agency',
                'price' => 199.00,
                'is_free' => 0,
                'listing_duration' => 365,
                'duration_unit' => 'days',
                'mark_as_unlimited' => 1, // Unlimited listings
                'mark_feature_unlimited' => 1, // Unlimited featured listings
                'plan_status' => 'active',
                'max_team_members' => -1, // Unlimited members
                'contact_owner' => 'yes',
                'customer_review' => 'yes',
                'featured_the_list' => 'yes',
            ],
            [
                'plan_name' => 'Employer Basic',
                'slug' => 'employer-basic',
                'plan_description' => 'For interior designers and employers posting jobs',
                'plan_type' => 'employer',
                'price' => 19.00,
                'is_free' => 0,
                'listing_duration' => 30,
                'duration_unit' => 'days',
                'no_of_listing' => 5,
                'mark_as_unlimited' => 0,
                'no_of_feature_listing' => 2,
                'mark_feature_unlimited' => 0,
                'plan_status' => 'active',
                'max_team_members' => 1, // Solo employer
                'contact_owner' => 'yes',
                'customer_review' => 'yes',
            ],
        ];

        foreach ($pricing_plans as $plan) {
            $existing = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM $table WHERE slug = %s",
                $plan['slug']
            ));

            if (!$existing) {
                // Add default values for required fields
                $plan = array_merge([
                    'add_gst_rate' => 0,
                    'gst_rate' => 0.00,
                    'never_expire' => 0,
                    'featured_the_list' => 'no',
                    'featured_the_list_hide' => 0,
                    'enable_subscription' => 'no',
                    'enable_subscription_hide' => 0,
                    'mark_feature_unlimited' => 0,
                    'contact_owner' => 'no',
                    'contact_owner_hide' => 0,
                    'customer_review' => 'no',
                    'customer_review_hide' => 0,
                    'mark_as_sold' => 'no',
                    'mark_as_sold_hide' => 0,
                    'recomend_plan' => 'no',
                    'hide_from_plan' => 'no',
                    'listing_sorting_order' => 0,
                    'business_name' => 'yes',
                    'business_name_hide' => 0,
                    'pricing_fld' => 'yes',
                    'pricing_fld_hide' => 0,
                    'location_fld' => 'yes',
                    'location_fld_hide' => 0,
                    'tag_fld' => 'yes',
                    'tag_fld_hide' => 0,
                    'category_fld' => 'yes',
                    'category_fld_hide' => 0,
                    'phone_fld' => 'yes',
                    'phone_fld_hide' => 0,
                    'email_fld' => 'yes',
                    'email_fld_hide' => 0,
                    'website_fld' => 'yes',
                    'website_fld_hide' => 0,
                ], $plan);

                $wpdb->insert($table, $plan);
            }
        }
    }

    /**
     * Insert project types
     */
    private static function insert_project_types()
    {
        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_project_types';

        // Check if data already exists
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table");
        if ($count > 0) {
            return;
        }

        $project_types = [
            ['name' => 'Residential', 'slug' => 'residential', 'description' => 'Residential interior design projects', 'sort_order' => 1],
            ['name' => 'Commercial', 'slug' => 'commercial', 'description' => 'Commercial interior design projects', 'sort_order' => 2],
            ['name' => 'Hospitality', 'slug' => 'hospitality', 'description' => 'Hotels, restaurants, and hospitality projects', 'sort_order' => 3],
            ['name' => 'Retail', 'slug' => 'retail', 'description' => 'Retail store and showroom design', 'sort_order' => 4],
            ['name' => 'Kitchen & Bath', 'slug' => 'kitchen-bath', 'description' => 'Kitchen and bathroom design projects', 'sort_order' => 5],
            ['name' => 'New Construction', 'slug' => 'new-construction', 'description' => 'New construction interior design', 'sort_order' => 6],
            ['name' => 'E-Design', 'slug' => 'e-design', 'description' => 'Online/virtual interior design services', 'sort_order' => 7],
            ['name' => 'Landscape/Outdoor', 'slug' => 'landscape-outdoor', 'description' => 'Outdoor and landscape design', 'sort_order' => 8],
            ['name' => '3D Rendering Projects', 'slug' => '3d-rendering-projects', 'description' => '3D visualization and rendering projects', 'sort_order' => 9],
            ['name' => 'CAD Drafting Projects', 'slug' => 'cad-drafting-projects', 'description' => 'CAD drafting and technical drawing projects', 'sort_order' => 10],
            ['name' => 'Concept Design Projects', 'slug' => 'concept-design-projects', 'description' => 'Conceptual design and planning projects', 'sort_order' => 11],
        ];

        foreach ($project_types as $project_type) {
            $project_type['status'] = 'active';
            $wpdb->insert($table, $project_type);
        }
    }

    /**
     * Insert service types
     */
    private static function insert_service_types()
    {
        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_service_types';

        // Check if data already exists
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table");
        if ($count > 0) {
            return;
        }

        $service_types = [
            ['name' => 'CAD Drafting', 'slug' => 'cad-drafting', 'description' => 'Computer-aided design drafting services', 'sort_order' => 1],
            ['name' => '2D Floor Plans', 'slug' => '2d-floor-plans', 'description' => '2D floor plan creation', 'sort_order' => 2],
            ['name' => '3D Modeling', 'slug' => '3d-modeling', 'description' => '3D model creation and design', 'sort_order' => 3],
            ['name' => '3D Rendering', 'slug' => '3d-rendering', 'description' => 'Photorealistic 3D rendering services', 'sort_order' => 4],
            ['name' => 'BIM Modeling', 'slug' => 'bim-modeling', 'description' => 'Building Information Modeling services', 'sort_order' => 5],
            ['name' => 'Moodboards', 'slug' => 'moodboards', 'description' => 'Creative moodboard design', 'sort_order' => 6],
            ['name' => 'Concept Boards', 'slug' => 'concept-boards', 'description' => 'Concept board development', 'sort_order' => 7],
            ['name' => 'Material Boards', 'slug' => 'material-boards', 'description' => 'Material selection and board creation', 'sort_order' => 8],
            ['name' => 'Space Planning', 'slug' => 'space-planning', 'description' => 'Space planning and layout services', 'sort_order' => 9],
            ['name' => 'Furniture Layouts', 'slug' => 'furniture-layouts', 'description' => 'Furniture arrangement and layout planning', 'sort_order' => 10],
            ['name' => 'Construction Documentation', 'slug' => 'construction-documentation', 'description' => 'Construction documents and specifications', 'sort_order' => 11],
            ['name' => 'Design Presentations', 'slug' => 'design-presentations', 'description' => 'Professional design presentations', 'sort_order' => 12],
            ['name' => 'Client Presentation Decks', 'slug' => 'client-presentation-decks', 'description' => 'Client presentation deck creation', 'sort_order' => 13],
            ['name' => 'Presentation Design', 'slug' => 'presentation-design', 'description' => 'Presentation design and formatting', 'sort_order' => 14],
        ];

        foreach ($service_types as $service_type) {
            $service_type['status'] = 'active';
            $wpdb->insert($table, $service_type);
        }
    }

    /**
     * Insert availability types
     */
    private static function insert_availability_types()
    {
        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_availability_types';

        // Check if data already exists
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table");
        if ($count > 0) {
            return;
        }

        $availability_types = [
            ['name' => 'Available Now', 'slug' => 'available-now', 'description' => 'Ready to start immediately', 'sort_order' => 1],
            ['name' => 'Available This Week', 'slug' => 'available-this-week', 'description' => 'Can start within this week', 'sort_order' => 2],
            ['name' => 'Part-Time', 'slug' => 'part-time', 'description' => 'Part-time availability', 'sort_order' => 3],
            ['name' => 'Full-Time', 'slug' => 'full-time', 'description' => 'Full-time availability', 'sort_order' => 4],
            ['name' => 'Project-Based', 'slug' => 'project-based', 'description' => 'Available for project-based work', 'sort_order' => 5],
            ['name' => 'Weekends', 'slug' => 'weekends', 'description' => 'Available on weekends', 'sort_order' => 6],
            ['name' => 'Evenings', 'slug' => 'evenings', 'description' => 'Available in the evenings', 'sort_order' => 7],
            ['name' => 'Flexible Hours', 'slug' => 'flexible-hours', 'description' => 'Flexible availability', 'sort_order' => 8],
        ];

        foreach ($availability_types as $type) {
            $type['status'] = 'active';
            $wpdb->insert($table, $type);
        }
    }

    /**
     * Insert timezones
     */
    private static function insert_timezones()
    {
        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_timezones';

        // Check if data already exists
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table");
        if ($count > 0) {
            return;
        }

        $timezones = [
            ['name' => '(GMT-12:00) International Date Line West', 'value' => 'Etc/GMT+12', 'offset' => 'GMT-12:00', 'sort_order' => 1],
            ['name' => '(GMT-11:00) Midway Island, Samoa', 'value' => 'Pacific/Samoa', 'offset' => 'GMT-11:00', 'sort_order' => 2],
            ['name' => '(GMT-10:00) Hawaii', 'value' => 'Pacific/Honolulu', 'offset' => 'GMT-10:00', 'sort_order' => 3],
            ['name' => '(GMT-09:00) Alaska', 'value' => 'America/Anchorage', 'offset' => 'GMT-09:00', 'sort_order' => 4],
            ['name' => '(GMT-08:00) Pacific Time (US & Canada)', 'value' => 'America/Los_Angeles', 'offset' => 'GMT-08:00', 'sort_order' => 5],
            ['name' => '(GMT-07:00) Mountain Time (US & Canada)', 'value' => 'America/Denver', 'offset' => 'GMT-07:00', 'sort_order' => 6],
            ['name' => '(GMT-06:00) Central Time (US & Canada)', 'value' => 'America/Chicago', 'offset' => 'GMT-06:00', 'sort_order' => 7],
            ['name' => '(GMT-05:00) Eastern Time (US & Canada)', 'value' => 'America/New_York', 'offset' => 'GMT-05:00', 'sort_order' => 8],
            ['name' => '(GMT-04:00) Atlantic Time (Canada)', 'value' => 'America/Halifax', 'offset' => 'GMT-04:00', 'sort_order' => 9],
            ['name' => '(GMT-03:30) Newfoundland', 'value' => 'America/St_Johns', 'offset' => 'GMT-03:30', 'sort_order' => 10],
            ['name' => '(GMT-03:00) Buenos Aires, Georgetown', 'value' => 'America/Argentina/Buenos_Aires', 'offset' => 'GMT-03:00', 'sort_order' => 11],
            ['name' => '(GMT-02:00) Mid-Atlantic', 'value' => 'Atlantic/South_Georgia', 'offset' => 'GMT-02:00', 'sort_order' => 12],
            ['name' => '(GMT-01:00) Azores, Cape Verde Islands', 'value' => 'Atlantic/Azores', 'offset' => 'GMT-01:00', 'sort_order' => 13],
            ['name' => '(GMT+00:00) London, Dublin, Lisbon', 'value' => 'Europe/London', 'offset' => 'GMT+00:00', 'sort_order' => 14],
            ['name' => '(GMT+01:00) Paris, Berlin, Rome', 'value' => 'Europe/Paris', 'offset' => 'GMT+01:00', 'sort_order' => 15],
            ['name' => '(GMT+02:00) Athens, Istanbul, Cairo', 'value' => 'Europe/Athens', 'offset' => 'GMT+02:00', 'sort_order' => 16],
            ['name' => '(GMT+03:00) Moscow, Kuwait, Riyadh', 'value' => 'Europe/Moscow', 'offset' => 'GMT+03:00', 'sort_order' => 17],
            ['name' => '(GMT+03:30) Tehran', 'value' => 'Asia/Tehran', 'offset' => 'GMT+03:30', 'sort_order' => 18],
            ['name' => '(GMT+04:00) Abu Dhabi, Muscat, Baku', 'value' => 'Asia/Dubai', 'offset' => 'GMT+04:00', 'sort_order' => 19],
            ['name' => '(GMT+04:30) Kabul', 'value' => 'Asia/Kabul', 'offset' => 'GMT+04:30', 'sort_order' => 20],
            ['name' => '(GMT+05:00) Islamabad, Karachi, Tashkent', 'value' => 'Asia/Karachi', 'offset' => 'GMT+05:00', 'sort_order' => 21],
            ['name' => '(GMT+05:30) Mumbai, Kolkata, New Delhi', 'value' => 'Asia/Kolkata', 'offset' => 'GMT+05:30', 'sort_order' => 22],
            ['name' => '(GMT+05:45) Kathmandu', 'value' => 'Asia/Kathmandu', 'offset' => 'GMT+05:45', 'sort_order' => 23],
            ['name' => '(GMT+06:00) Dhaka, Almaty', 'value' => 'Asia/Dhaka', 'offset' => 'GMT+06:00', 'sort_order' => 24],
            ['name' => '(GMT+06:30) Yangon, Cocos Islands', 'value' => 'Asia/Yangon', 'offset' => 'GMT+06:30', 'sort_order' => 25],
            ['name' => '(GMT+07:00) Bangkok, Hanoi, Jakarta', 'value' => 'Asia/Bangkok', 'offset' => 'GMT+07:00', 'sort_order' => 26],
            ['name' => '(GMT+08:00) Beijing, Singapore, Hong Kong', 'value' => 'Asia/Shanghai', 'offset' => 'GMT+08:00', 'sort_order' => 27],
            ['name' => '(GMT+08:00) Perth', 'value' => 'Australia/Perth', 'offset' => 'GMT+08:00', 'sort_order' => 28],
            ['name' => '(GMT+09:00) Tokyo, Seoul, Osaka', 'value' => 'Asia/Tokyo', 'offset' => 'GMT+09:00', 'sort_order' => 29],
            ['name' => '(GMT+09:30) Adelaide', 'value' => 'Australia/Adelaide', 'offset' => 'GMT+09:30', 'sort_order' => 30],
            ['name' => '(GMT+10:00) Sydney, Melbourne, Brisbane', 'value' => 'Australia/Sydney', 'offset' => 'GMT+10:00', 'sort_order' => 31],
            ['name' => '(GMT+10:00) Guam, Port Moresby', 'value' => 'Pacific/Guam', 'offset' => 'GMT+10:00', 'sort_order' => 32],
            ['name' => '(GMT+11:00) Solomon Islands', 'value' => 'Pacific/Guadalcanal', 'offset' => 'GMT+11:00', 'sort_order' => 33],
            ['name' => '(GMT+12:00) Auckland, Wellington, Fiji', 'value' => 'Pacific/Auckland', 'offset' => 'GMT+12:00', 'sort_order' => 34],
        ];

        foreach ($timezones as $timezone) {
            $timezone['status'] = 'active';
            $wpdb->insert($table, $timezone);
        }
    }

    /**
     * Insert experience levels
     */
    private static function insert_experience_levels()
    {
        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_experience_levels';

        // Check if data already exists
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table");
        if ($count > 0) {
            return;
        }

        $experience_levels = [
            ['name' => 'Entry Level', 'slug' => 'entry-level', 'years_min' => 0, 'years_max' => 1, 'description' => 'Just starting in the field', 'sort_order' => 1],
            ['name' => 'Junior', 'slug' => 'junior', 'years_min' => 1, 'years_max' => 3, 'description' => '1-3 years of experience', 'sort_order' => 2],
            ['name' => 'Mid-Level', 'slug' => 'mid-level', 'years_min' => 3, 'years_max' => 5, 'description' => '3-5 years of experience', 'sort_order' => 3],
            ['name' => 'Senior', 'slug' => 'senior', 'years_min' => 5, 'years_max' => 10, 'description' => '5-10 years of experience', 'sort_order' => 4],
            ['name' => 'Expert', 'slug' => 'expert', 'years_min' => 10, 'years_max' => null, 'description' => '10+ years of experience', 'sort_order' => 5],
        ];

        foreach ($experience_levels as $level) {
            $level['status'] = 'active';
            $wpdb->insert($table, $level);
        }
    }

    /**
     * Insert response times
     */
    private static function insert_response_times()
    {
        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_response_times';

        // Check if data already exists
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table");
        if ($count > 0) {
            return;
        }

        $response_times = [
            ['name' => 'Within 1 Hour', 'slug' => 'within-1-hour', 'description' => 'Responds within 1 hour', 'sort_order' => 1],
            ['name' => 'Within a Few Hours', 'slug' => 'within-few-hours', 'description' => 'Responds within a few hours', 'sort_order' => 2],
            ['name' => 'Within 24 Hours', 'slug' => 'within-24-hours', 'description' => 'Responds within 24 hours', 'sort_order' => 3],
            ['name' => 'Within 48 Hours', 'slug' => 'within-48-hours', 'description' => 'Responds within 48 hours', 'sort_order' => 4],
            ['name' => 'A Few Days', 'slug' => 'few-days', 'description' => 'Responds within a few days', 'sort_order' => 5],
        ];

        foreach ($response_times as $time) {
            $time['status'] = 'active';
            $wpdb->insert($table, $time);
        }
    }

    /**
     * Insert languages
     */
    private static function insert_languages()
    {
        global $wpdb;
        $table = $wpdb->prefix . 'skd_pl_languages';

        // Check if data already exists
        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table");
        if ($count > 0) {
            return;
        }

        $languages = [
            ['name' => 'English', 'code' => 'en', 'native_name' => 'English', 'sort_order' => 1],
            ['name' => 'Spanish', 'code' => 'es', 'native_name' => 'Español', 'sort_order' => 2],
            ['name' => 'French', 'code' => 'fr', 'native_name' => 'Français', 'sort_order' => 3],
            ['name' => 'German', 'code' => 'de', 'native_name' => 'Deutsch', 'sort_order' => 4],
            ['name' => 'Italian', 'code' => 'it', 'native_name' => 'Italiano', 'sort_order' => 5],
            ['name' => 'Portuguese', 'code' => 'pt', 'native_name' => 'Português', 'sort_order' => 6],
            ['name' => 'Russian', 'code' => 'ru', 'native_name' => 'Русский', 'sort_order' => 7],
            ['name' => 'Chinese (Mandarin)', 'code' => 'zh', 'native_name' => '中文', 'sort_order' => 8],
            ['name' => 'Japanese', 'code' => 'ja', 'native_name' => '日本語', 'sort_order' => 9],
            ['name' => 'Korean', 'code' => 'ko', 'native_name' => '한국어', 'sort_order' => 10],
            ['name' => 'Arabic', 'code' => 'ar', 'native_name' => 'العربية', 'sort_order' => 11],
            ['name' => 'Hindi', 'code' => 'hi', 'native_name' => 'हिन्दी', 'sort_order' => 12],
            ['name' => 'Bengali', 'code' => 'bn', 'native_name' => 'বাংলা', 'sort_order' => 13],
            ['name' => 'Urdu', 'code' => 'ur', 'native_name' => 'اردو', 'sort_order' => 14],
            ['name' => 'Turkish', 'code' => 'tr', 'native_name' => 'Türkçe', 'sort_order' => 15],
            ['name' => 'Dutch', 'code' => 'nl', 'native_name' => 'Nederlands', 'sort_order' => 16],
            ['name' => 'Polish', 'code' => 'pl', 'native_name' => 'Polski', 'sort_order' => 17],
            ['name' => 'Swedish', 'code' => 'sv', 'native_name' => 'Svenska', 'sort_order' => 18],
            ['name' => 'Norwegian', 'code' => 'no', 'native_name' => 'Norsk', 'sort_order' => 19],
            ['name' => 'Danish', 'code' => 'da', 'native_name' => 'Dansk', 'sort_order' => 20],
            ['name' => 'Finnish', 'code' => 'fi', 'native_name' => 'Suomi', 'sort_order' => 21],
            ['name' => 'Greek', 'code' => 'el', 'native_name' => 'Ελληνικά', 'sort_order' => 22],
            ['name' => 'Hebrew', 'code' => 'he', 'native_name' => 'עברית', 'sort_order' => 23],
            ['name' => 'Thai', 'code' => 'th', 'native_name' => 'ไทย', 'sort_order' => 24],
            ['name' => 'Vietnamese', 'code' => 'vi', 'native_name' => 'Tiếng Việt', 'sort_order' => 25],
            ['name' => 'Indonesian', 'code' => 'id', 'native_name' => 'Bahasa Indonesia', 'sort_order' => 26],
            ['name' => 'Malay', 'code' => 'ms', 'native_name' => 'Bahasa Melayu', 'sort_order' => 27],
            ['name' => 'Tagalog', 'code' => 'tl', 'native_name' => 'Tagalog', 'sort_order' => 28],
            ['name' => 'Swahili', 'code' => 'sw', 'native_name' => 'Kiswahili', 'sort_order' => 29],
        ];

        foreach ($languages as $language) {
            $language['status'] = 'active';
            $wpdb->insert($table, $language);
        }
    }

    /**
     * Insert demo VDA users for testing
     */
    private static function insert_demo_vdas()
    {
        global $wpdb;

        $demo_vdas = [
            [
                'username' => 'sarah_renders',
                'email' => 'sarah.renders@demo.com',
                'display_name' => 'Sarah Mitchell',
                'first_name' => 'Sarah',
                'last_name' => 'Mitchell',
                'tagline' => 'Expert 3D Rendering Specialist | Photorealistic Visualizations',
                'bio' => 'With over 8 years of experience in architectural visualization, I specialize in creating stunning photorealistic 3D renders for interior design projects. My expertise includes V-Ray, Corona Renderer, and advanced post-production techniques.',
                'hourly_rate' => 45.00,
                'experience_level' => 'expert',
                'timezone' => 1, // EST
                'availability' => 3, // Full-time
                'skills' => [2, 11, 17, 6], // 3ds Max, V-Ray, Photoshop, AutoCAD
                'specializations' => [11, 16], // Sustainable Design, Modern Design
                'project_types' => [1, 2], // Residential, Commercial
                'service_types' => [4, 3], // 3D Rendering, 3D Modeling
                'is_verified' => 1,
                'is_featured' => 1,
                'rating' => 4.9,
                'years_experience' => 8
            ],
            [
                'username' => 'mike_autocad',
                'email' => 'mike.cad@demo.com',
                'display_name' => 'Mike Johnson',
                'first_name' => 'Mike',
                'last_name' => 'Johnson',
                'tagline' => 'AutoCAD Expert | Technical Drawing Specialist',
                'bio' => 'Precision-focused CAD technician with 6 years of experience in creating detailed technical drawings and floor plans. I help interior designers bring their concepts to life with accurate and professional CAD documentation.',
                'hourly_rate' => 35.00,
                'experience_level' => 'mid',
                'timezone' => 2, // PST
                'availability' => 2, // Part-time
                'skills' => [6, 1, 7], // AutoCAD, SketchUp, Revit
                'specializations' => [5, 16], // Office Design, Modern Design
                'project_types' => [1, 2], // Residential, Commercial
                'service_types' => [1, 2], // CAD Drafting, 2D Floor Plans
                'is_verified' => 1,
                'is_featured' => 0,
                'rating' => 4.7,
                'years_experience' => 6
            ],
            [
                'username' => 'emma_sketchup',
                'email' => 'emma.design@demo.com',
                'display_name' => 'Emma Rodriguez',
                'first_name' => 'Emma',
                'last_name' => 'Rodriguez',
                'tagline' => 'SketchUp Pro | 3D Space Planning Enthusiast',
                'bio' => 'Creative 3D modeler specializing in SketchUp with 5 years of experience. I create detailed 3D models and space planning solutions that help clients visualize their dream interiors before implementation.',
                'hourly_rate' => 28.00,
                'experience_level' => 'mid',
                'timezone' => 3, // CST
                'availability' => 3, // Full-time
                'skills' => [1, 11, 17, 13], // SketchUp, V-Ray, Photoshop, Lumion
                'specializations' => [3, 7], // Living Room Design, Hotel Design
                'project_types' => [1, 3], // Residential, Hospitality
                'service_types' => [3, 9], // 3D Modeling, Space Planning
                'is_verified' => 0,
                'is_featured' => 0,
                'rating' => 4.5,
                'years_experience' => 5
            ],
            [
                'username' => 'david_lumion',
                'email' => 'david.viz@demo.com',
                'display_name' => 'David Chen',
                'first_name' => 'David',
                'last_name' => 'Chen',
                'tagline' => 'Lumion Specialist | Architectural Animation Expert',
                'bio' => '10+ years creating stunning architectural visualizations and animations. I specialize in Lumion, bringing static designs to life with cinematic walkthroughs and immersive 360° presentations.',
                'hourly_rate' => 55.00,
                'experience_level' => 'expert',
                'timezone' => 4, // GMT
                'availability' => 3, // Full-time
                'skills' => [13, 1, 2, 17], // Lumion, SketchUp, 3ds Max, Photoshop
                'specializations' => [6, 7, 13], // Restaurant Design, Hotel Design, Luxury Design
                'project_types' => [2, 3, 4], // Commercial, Hospitality, Retail
                'service_types' => [4, 3], // 3D Rendering, 3D Modeling
                'is_verified' => 1,
                'is_featured' => 1,
                'rating' => 5.0,
                'years_experience' => 10
            ],
            [
                'username' => 'lisa_revit',
                'email' => 'lisa.bim@demo.com',
                'display_name' => 'Lisa Anderson',
                'first_name' => 'Lisa',
                'last_name' => 'Anderson',
                'tagline' => 'Revit BIM Coordinator | Interior Architecture',
                'bio' => 'BIM-certified professional with 7 years of experience in Revit. I create intelligent 3D models with accurate material specifications, helping designers streamline their workflow and reduce errors.',
                'hourly_rate' => 42.00,
                'experience_level' => 'mid',
                'timezone' => 1, // EST
                'availability' => 3, // Full-time
                'skills' => [7, 6, 1], // Revit, AutoCAD, SketchUp
                'specializations' => [5, 16], // Office Design, Modern Design
                'project_types' => [2], // Commercial
                'service_types' => [5, 1], // BIM Modeling, CAD Drafting
                'is_verified' => 1,
                'is_featured' => 0,
                'rating' => 4.8,
                'years_experience' => 7
            ],
            [
                'username' => 'carlos_corona',
                'email' => 'carlos.render@demo.com',
                'display_name' => 'Carlos Martinez',
                'first_name' => 'Carlos',
                'last_name' => 'Martinez',
                'tagline' => 'Corona Renderer Expert | Realistic Lighting Specialist',
                'bio' => 'Passionate about photorealism with 6 years of experience using Corona Renderer. I create warm, inviting visualizations that showcase materials and lighting with exceptional realism.',
                'hourly_rate' => 38.00,
                'experience_level' => 'mid',
                'timezone' => 2, // PST
                'availability' => 2, // Part-time
                'skills' => [2, 12, 17], // 3ds Max, Corona Renderer, Photoshop
                'specializations' => [1, 7, 13], // Kitchen Design, Hotel Design, Luxury Design
                'project_types' => [1, 3], // Residential, Hospitality
                'service_types' => [4, 3], // 3D Rendering, 3D Modeling
                'is_verified' => 0,
                'is_featured' => 0,
                'rating' => 4.6,
                'years_experience' => 6
            ],
            [
                'username' => 'priya_enscape',
                'email' => 'priya.design@demo.com',
                'display_name' => 'Priya Sharma',
                'first_name' => 'Priya',
                'last_name' => 'Sharma',
                'tagline' => 'Enscape & VR Visualization Expert',
                'bio' => 'Real-time rendering specialist with 4 years of experience in Enscape. I create interactive VR experiences that allow clients to walk through their designs in real-time.',
                'hourly_rate' => 32.00,
                'experience_level' => 'junior',
                'timezone' => 5, // IST
                'availability' => 3, // Full-time
                'skills' => [14, 7, 1], // Enscape, Revit, SketchUp
                'specializations' => [4, 16], // Bedroom Design, Modern Design
                'project_types' => [1, 2], // Residential, Commercial
                'service_types' => [4, 3], // 3D Rendering, 3D Modeling
                'is_verified' => 1,
                'is_featured' => 0,
                'rating' => 4.4,
                'years_experience' => 4
            ],
            [
                'username' => 'alex_photoshop',
                'email' => 'alex.post@demo.com',
                'display_name' => 'Alex Thompson',
                'first_name' => 'Alex',
                'last_name' => 'Thompson',
                'tagline' => 'Post-Production Artist | Photoshop Wizard',
                'bio' => 'Specialized in post-production and image enhancement with 5 years of experience. I take good renders and make them extraordinary through advanced Photoshop techniques and artistic composition.',
                'hourly_rate' => 30.00,
                'experience_level' => 'mid',
                'timezone' => 1, // EST
                'availability' => 2, // Part-time
                'skills' => [17, 18, 19], // Photoshop, Illustrator, InDesign
                'specializations' => [8, 14], // Retail Store Design, Minimalist Design
                'project_types' => [1, 2, 4], // Residential, Commercial, Retail
                'service_types' => [12, 14], // Design Presentations, Presentation Design
                'is_verified' => 0,
                'is_featured' => 0,
                'rating' => 4.3,
                'years_experience' => 5
            ]
        ];

        foreach ($demo_vdas as $vda_data) {
            // Check if user already exists
            $user = get_user_by('email', $vda_data['email']);

            if ($user) {
                $user_id = $user->ID;
                // Check if profile exists
                $profile_exists = $wpdb->get_var($wpdb->prepare(
                    "SELECT COUNT(*) FROM {$wpdb->prefix}skd_pl_user_profiles WHERE user_id = %d",
                    $user_id
                ));

                if ($profile_exists) {
                    continue; // Skip if both user and profile exist
                }
            } else {
                // Create WordPress user
                $user_id = wp_create_user(
                    $vda_data['username'],
                    '12345678',
                    $vda_data['email']
                );

                if (is_wp_error($user_id)) {
                    continue;
                }

                // Update user meta
                wp_update_user([
                    'ID' => $user_id,
                    'display_name' => $vda_data['display_name'],
                    'first_name' => $vda_data['first_name'],
                    'last_name' => $vda_data['last_name']
                ]);

                // Assign Virtual Design Assistant role
                $user = new WP_User($user_id);
                $user->remove_role('subscriber'); // Remove default subscriber role
                $user->add_role('vda_user'); // Add VDA role

                update_user_meta($user_id, 'skd_user_type', 'vda');
            }

            // Create user profile - using actual table structure with JSON arrays
            $wpdb->insert(
                $wpdb->prefix . 'skd_pl_user_profiles',
                [
                    'user_id' => $user_id,
                    'user_type' => 'vda',
                    'tagline' => $vda_data['tagline'],
                    'bio' => $vda_data['bio'],
                    'short_description' => wp_trim_words($vda_data['bio'], 30),
                    'hourly_rate' => $vda_data['hourly_rate'],
                    'experience_level' => strtolower($vda_data['experience_level']),
                    'timezone' => $vda_data['timezone'],
                    'availability_status' => $vda_data['availability'],
                    'skills' => json_encode($vda_data['skills']),
                    'specializations' => json_encode($vda_data['specializations']),
                    'project_types' => json_encode($vda_data['project_types']),
                    'service_types' => json_encode($vda_data['service_types']),
                    'is_verified' => $vda_data['is_verified'],
                    'is_featured' => $vda_data['is_featured'],
                    'rating' => $vda_data['rating'],
                    'total_reviews' => rand(5, 50),
                    'total_projects' => rand(10, 100),
                    'years_experience' => $vda_data['years_experience'],
                    'profile_completeness' => 85,
                    'created_at' => current_time('mysql'),
                    'updated_at' => current_time('mysql'),
                    'joined_date' => current_time('mysql')
                ]
            );
        }
    }
}
