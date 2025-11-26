<?php
if (!defined('ABSPATH')) exit;

// Get all project types
global $wpdb;
$project_types_table = $wpdb->prefix . 'skd_pl_project_types';
$project_types = $wpdb->get_results("SELECT * FROM $project_types_table ORDER BY sort_order ASC, name ASC");
?>

<div class="wrap">
    <h1 class="wp-heading-inline">Project Types Management</h1>
    <a href="#" class="page-title-action" id="add-new-project-type">Add New</a>
    <hr class="wp-header-end">

    <div class="skd-admin-content">
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Description</th>
                    <th>Sort Order</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($project_types)): ?>
                    <tr>
                        <td colspan="7">No project types found. <a href="#" id="add-first-project-type">Add your first project type</a></td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($project_types as $project_type): ?>
                        <tr>
                            <td><?php echo esc_html($project_type->id); ?></td>
                            <td><strong><?php echo esc_html($project_type->name); ?></strong></td>
                            <td><?php echo esc_html($project_type->slug); ?></td>
                            <td><?php echo esc_html(wp_trim_words($project_type->description, 10)); ?></td>
                            <td><?php echo esc_html($project_type->sort_order); ?></td>
                            <td><?php echo esc_html(ucfirst($project_type->status)); ?></td>
                            <td>
                                <a href="#" class="edit-project-type" data-id="<?php echo $project_type->id; ?>">Edit</a> |
                                <a href="#" class="delete-project-type" data-id="<?php echo $project_type->id; ?>" style="color: red;">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="project-type-modal" style="display:none;">
    <div class="skd-modal-overlay"></div>
    <div class="skd-modal-content">
        <h2 id="modal-title">Add New Project Type</h2>
        <form id="project-type-form">
            <input type="hidden" id="project-type-id" name="id" value="">

            <p>
                <label for="project-type-name">Name *</label>
                <input type="text" id="project-type-name" name="name" class="regular-text" required>
            </p>

            <p>
                <label for="project-type-slug">Slug</label>
                <input type="text" id="project-type-slug" name="slug" class="regular-text">
                <small>Leave blank to auto-generate from name</small>
            </p>

            <p>
                <label for="project-type-description">Description</label>
                <textarea id="project-type-description" name="description" class="large-text" rows="3"></textarea>
            </p>

            <p>
                <label for="project-type-icon">Icon URL</label>
                <input type="text" id="project-type-icon" name="icon_url" class="regular-text">
            </p>

            <p>
                <label for="project-type-sort-order">Sort Order</label>
                <input type="number" id="project-type-sort-order" name="sort_order" class="small-text" value="0" min="0">
            </p>

            <p>
                <label for="project-type-status">Status</label>
                <select id="project-type-status" name="status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </p>

            <p>
                <button type="submit" class="button button-primary">Save Project Type</button>
                <button type="button" class="button" id="cancel-project-type">Cancel</button>
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
    .skd-modal-content input[type="number"],
    .skd-modal-content textarea,
    .skd-modal-content select {
        width: 100%;
    }
</style>

<script>
    jQuery(document).ready(function($) {
        const nonce = '<?php echo wp_create_nonce('skd_admin_nonce'); ?>';

        // Auto-generate slug from name
        $('#project-type-name').on('input', function() {
            if (!$('#project-type-id').val()) {
                const slug = $(this).val()
                    .toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                $('#project-type-slug').val(slug);
            }
        });

        // Open modal for new project type
        $('#add-new-project-type, #add-first-project-type').on('click', function(e) {
            e.preventDefault();
            $('#modal-title').text('Add New Project Type');
            $('#project-type-form')[0].reset();
            $('#project-type-id').val('');
            $('#project-type-modal').show();
        });

        // Close modal
        $('#cancel-project-type').on('click', function() {
            $('#project-type-modal').hide();
        });

        // Edit project type
        $('.edit-project-type').on('click', function(e) {
            e.preventDefault();
            const id = $(this).data('id');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'skd_get_project_types',
                    id: id,
                    nonce: nonce
                },
                success: function(response) {
                    if (response.success && response.data.project_types.length > 0) {
                        const pt = response.data.project_types[0];
                        $('#modal-title').text('Edit Project Type');
                        $('#project-type-id').val(pt.id);
                        $('#project-type-name').val(pt.name);
                        $('#project-type-slug').val(pt.slug);
                        $('#project-type-description').val(pt.description);
                        $('#project-type-icon').val(pt.icon_url);
                        $('#project-type-sort-order').val(pt.sort_order);
                        $('#project-type-status').val(pt.status);
                        $('#project-type-modal').show();
                    }
                }
            });
        });

        // Submit form
        $('#project-type-form').on('submit', function(e) {
            e.preventDefault();

            const id = $('#project-type-id').val();
            const action = id ? 'skd_update_project_type' : 'skd_add_project_type';

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

        // Delete project type
        $('.delete-project-type').on('click', function(e) {
            e.preventDefault();

            if (!confirm('Are you sure you want to delete this project type?')) {
                return;
            }

            const id = $(this).data('id');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'skd_delete_project_type',
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