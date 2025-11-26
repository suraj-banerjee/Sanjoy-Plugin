<?php
if (!defined('ABSPATH')) exit;

// Get all skills
global $wpdb;
$skills_table = $wpdb->prefix . 'skd_pl_skills';
$skills = $wpdb->get_results("SELECT * FROM $skills_table ORDER BY sort_order ASC, name ASC");
?>

<div class="wrap">
    <h1 class="wp-heading-inline">Software Management</h1>
    <a href="#" class="page-title-action" id="add-new-skill">Add New</a>
    <hr class="wp-header-end">

    <div class="skd-admin-content">
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($skills)): ?>
                    <tr>
                        <td colspan="6">No software found. <a href="#" id="add-first-skill">Add your first software</a></td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($skills as $skill): ?>
                        <tr>
                            <td><?php echo esc_html($skill->id); ?></td>
                            <td><strong><?php echo esc_html($skill->name); ?></strong></td>
                            <td><?php echo esc_html($skill->slug); ?></td>
                            <td><?php echo esc_html(wp_trim_words($skill->description, 10)); ?></td>
                            <td><?php echo esc_html(ucfirst($skill->status)); ?></td>
                            <td>
                                <a href="#" class="edit-skill" data-id="<?php echo $skill->id; ?>">Edit</a> |
                                <a href="#" class="delete-skill" data-id="<?php echo $skill->id; ?>" style="color: red;">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="skill-modal" style="display:none;">
    <div class="skd-modal-overlay"></div>
    <div class="skd-modal-content">
        <h2 id="modal-title">Add New Software</h2>
        <form id="skill-form">
            <input type="hidden" id="skill-id" name="id" value="">

            <p>
                <label for="skill-name">Name *</label>
                <input type="text" id="skill-name" name="name" class="regular-text" required>
            </p>

            <p>
                <label for="skill-slug">Slug</label>
                <input type="text" id="skill-slug" name="slug" class="regular-text">
                <small>Leave blank to auto-generate from name</small>
            </p>

            <p>
                <label for="skill-description">Description</label>
                <textarea id="skill-description" name="description" class="large-text" rows="3"></textarea>
            </p>

            <p>
                <label for="skill-status">Status</label>
                <select id="skill-status" name="status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </p>

            <p>
                <button type="submit" class="button button-primary">Save Software</button>
                <button type="button" class="button" id="cancel-skill">Cancel</button>
            </p>
        </form>
    </div>
</div>

<style>
    .skd-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        z-index: 100000;
    }

    .skd-modal-content {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        padding: 20px;
        border-radius: 5px;
        max-width: 600px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        z-index: 100001;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }

    .skd-modal-content h2 {
        margin-top: 0;
    }

    .skd-modal-content label {
        display: block;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .skd-modal-content input[type="text"],
    .skd-modal-content textarea,
    .skd-modal-content select {
        width: 100%;
    }
</style>

<script>
    jQuery(document).ready(function($) {
        const nonce = '<?php echo wp_create_nonce('skd_admin_nonce'); ?>';

        // Auto-generate slug from name
        $('#skill-name').on('input', function() {
            if (!$('#skill-id').val()) {
                const slug = $(this).val()
                    .toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                $('#skill-slug').val(slug);
            }
        });

        // Open modal for new software
        $('#add-new-skill, #add-first-skill').on('click', function(e) {
            e.preventDefault();
            $('#modal-title').text('Add New Software');
            $('#skill-form')[0].reset();
            $('#skill-id').val('');
            $('#skill-modal').show();
        });

        // Close modal
        $('#cancel-skill').on('click', function() {
            $('#skill-modal').hide();
        });

        // Edit software
        $('.edit-skill').on('click', function(e) {
            e.preventDefault();
            const id = $(this).data('id');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'skd_get_skills',
                    id: id,
                    nonce: nonce
                },
                success: function(response) {
                    if (response.success && response.data.skills.length > 0) {
                        const skill = response.data.skills[0];
                        $('#modal-title').text('Edit Software');
                        $('#skill-id').val(skill.id);
                        $('#skill-name').val(skill.name);
                        $('#skill-slug').val(skill.slug);
                        $('#skill-description').val(skill.description);
                        $('#skill-status').val(skill.status);
                        $('#skill-modal').show();
                    }
                }
            });
        });

        // Submit form
        $('#skill-form').on('submit', function(e) {
            e.preventDefault();

            const id = $('#skill-id').val();
            const action = id ? 'skd_update_skill' : 'skd_add_skill';

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: $(this).serialize() + '&action=' + action + '&nonce=' + nonce,
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        location.reload();
                    } else {
                        alert(response.data.message);
                    }
                }
            });
        });

        // Delete software
        $('.delete-skill').on('click', function(e) {
            e.preventDefault();

            if (!confirm('Are you sure you want to delete this software?')) {
                return;
            }

            const id = $(this).data('id');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'skd_delete_skill',
                    id: id,
                    nonce: nonce
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        location.reload();
                    } else {
                        alert(response.data.message);
                    }
                }
            });
        });
    });
</script>