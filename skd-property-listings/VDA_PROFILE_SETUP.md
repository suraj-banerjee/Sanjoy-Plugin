# VDA Profile Management System - Setup Guide

## Overview

Complete VDA (Virtual Design Assistant) profile management system with tab-based editing, portfolio management, and skill auto-creation.

## Components Created

### Backend Classes

1. **class-skd-pl-vda-skills.php** - Skills CRUD + auto-create
2. **class-skd-pl-vda-services.php** - Services CRUD + auto-create
3. **class-skd-pl-vda-specializations.php** - Specializations CRUD + auto-create
4. **class-skd-pl-vda-profile.php** - Profile management handlers

### Admin Pages

- **InteriAssist > Skills** - Manage skills with categories
- **InteriAssist > Services** - Manage available services
- **InteriAssist > Specializations** - Manage specialization areas

### Frontend Templates

- **edit-vda-profile.php** - Complete profile editor with 8 tabs

### Database Tables

- `skd_pl_skills` - Skills with categories (software, design_skill, technical_skill, soft_skill)
- `skd_pl_services` - Services offered
- `skd_pl_specializations` - Specialization areas
- `skd_pl_user_profiles` - Extended with JSON fields for arrays
- `skd_pl_user_portfolio` - Portfolio projects with images

## How to Use

### 1. Create a Profile Edit Page

Create a new WordPress page and add the shortcode:

```
[skd_edit_vda_profile]
```

### 2. Admin Management

Navigate to **InteriAssist** menu in WordPress admin:

- Add common skills that VDAs can select
- Add services offered by VDAs
- Add specialization categories

### 3. Profile Completeness

The system tracks profile completeness based on 15 fields:

- Basic Info (4 pts): Tagline, Bio, Avatar, Country
- Skills & Services (3 pts): Skills, Services, Specializations
- Experience (2 pts): Years, Level
- Rates (2 pts): Hourly Rate, Availability
- Social (2 pts): Any social link
- Portfolio (2 pts): 1+ project, 3+ projects

### 4. Auto-Create Feature

When VDAs type new skills/services/specializations in Select2 fields:

1. System checks if it exists (by slug)
2. If not found, creates it automatically
3. Returns the new ID for immediate use
4. Admin can later edit/categorize in admin panel

## AJAX Endpoints

### Profile Updates

- `skd_update_profile_basic` - First name, last name, tagline, bio, location, timezone
- `skd_update_profile_skills` - Array of skill IDs (JSON)
- `skd_update_profile_services` - Array of service IDs (JSON)
- `skd_update_profile_specializations` - Array of specialization IDs (JSON)
- `skd_update_profile_experience` - Years, level, education, languages (JSON)
- `skd_update_profile_rates` - Hourly rate, pricing model, availability, response time
- `skd_update_profile_social` - Website, LinkedIn, Behance, Instagram, Pinterest, Portfolio URLs

### Portfolio Management

- `skd_add_portfolio_item` - Create new project
- `skd_update_portfolio_item` - Update existing project (ownership verified)
- `skd_delete_portfolio_item` - Delete project (ownership verified)
- `skd_get_portfolio_items` - Fetch user's portfolio

### Image Uploads

- `skd_upload_avatar` - Upload profile picture
- `skd_upload_cover_image` - Upload cover image

### Auto-Create

- `skd_create_skill_auto` - Create skill if not exists
- `skd_create_service_auto` - Create service if not exists
- `skd_create_specialization_auto` - Create specialization if not exists

## Profile Tabs

### 1. Basic Info

- Profile picture upload
- First/Last name
- Professional tagline
- Bio
- Country, City, Timezone

### 2. Skills & Software

- Multi-select with Select2
- Tag support for adding new skills
- Auto-categorization

### 3. Services

- Services offered by the VDA
- Multi-select with auto-create

### 4. Specializations

- Areas of expertise
- Multi-select with auto-create

### 5. Experience

- Years of experience
- Experience level (Junior/Mid/Senior/Expert)
- Education level
- Languages spoken

### 6. Portfolio

- Add/Edit/Delete projects
- Project details: title, description, type, category
- Software used (tags)
- Year, client, project URL
- Image upload (multi-image support ready)

### 7. Rates & Availability

- Hourly rate
- Pricing model (Hourly/Fixed/Both/Negotiable)
- Availability status (Available/Busy/Unavailable)
- Response time

### 8. Social Links

- Website
- LinkedIn
- Behance
- Instagram
- Pinterest
- Portfolio URL

## Security

All AJAX handlers use:

- `check_ajax_referer('skd_ajax_nonce', 'nonce')` - CSRF protection
- `is_user_logged_in()` - Authentication check
- `sanitize_text_field()`, `sanitize_textarea_field()`, `esc_url_raw()` - Input sanitization
- Ownership verification on portfolio operations

## Next Steps

1. **Test the profile editor** - Create a page with `[skd_edit_vda_profile]`
2. **Populate admin data** - Add common skills/services/specializations
3. **Enhance listing page** - Add filters and search (Phase 5)
4. **Create public profile** - Display VDA profiles to visitors (Phase 6)
5. **Admin VDA management** - Dashboard for managing all VDAs (Phase 7)
6. **Verification system** - Implement verified badges and featured VDAs (Phase 8)

## Technical Notes

- Uses Select2 for enhanced multi-select with tagging
- SweetAlert2 for beautiful modals and confirmations
- Profile completeness updates automatically after each change
- JSON fields used for storing arrays (skills, services, languages, images, software)
- Prepared statements for all database queries
- WordPress file upload API for images
