<?php
if (!defined('ABSPATH')) exit;

// Get all service types
global $wpdb;
$service_types_table = $wpdb->prefix . 'skd_pl_service_types';
$service_types = $wpdb->get_results("SELECT * FROM $service_types_table ORDER BY sort_order ASC, name ASC");
?>

<div class="wrap">
    <h1 class="wp-heading-inline">Service Types Management</h1>
    <a href="#" class="page-title-action" id="add-new-service-type">Add New</a>
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
                <?php if (empty($service_types)): ?>
                    <tr>
                        <td colspan="7">No service types found. <a href="#" id="add-first-service-type">Add your first service type</a></td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($service_types as $service_type): ?>
                        <tr>
                            <td><?php echo esc_html($service_type->id); ?></td>
                            <td><strong><?php echo esc_html($service_type->name); ?></strong></td>
                            <td><?php echo esc_html($service_type->slug); ?></td>
                            <td><?php echo esc_html(wp_trim_words($service_type->description, 10)); ?></td>
                            <td><?php echo esc_html($service_type->sort_order); ?></td>
                            <td><?php echo esc_html(ucfirst($service_type->status)); ?></td>
                            <td>
                                <a href="#" class="edit-service-type" data-id="<?php echo $service_type->id; ?>">Edit</a> |
                                <a href="#" class="delete-service-type" data-id="<?php echo $service_type->id; ?>" style="color: red;">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit Modal -->
<div id="service-type-modal" style="display:none;">
    <div class="skd-modal-overlay"></div>
    <div class="skd-modal-content">
        <h2 id="modal-title">Add New Service Type</h2>
        <form id="service-type-form">
            <input type="hidden" id="service-type-id" name="id" value="">

            <p>
                <label for="service-type-name">Name *</label>
                <input type="text" id="service-type-name" name="name" class="regular-text" required>
            </p>

            <p>
                <label for="service-type-slug">Slug</label>
                <input type="text" id="service-type-slug" name="slug" class="regular-text">
                <small>Leave blank to auto-generate from name</small>
            </p>

            <p>
                <label for="service-type-description">Description</label>
                <textarea id="service-type-description" name="description" class="large-text" rows="3"></textarea>
            </p>

            <p>
                <label for="service-type-icon">Icon URL</label>
                <input type="text" id="service-type-icon" name="icon_url" class="regular-text">
            </p>

            <p>
                <label for="service-type-sort-order">Sort Order</label>
                <input type="number" id="service-type-sort-order" name="sort_order" class="small-text" value="0" min="0">
            </p>

            <p>
                <label for="service-type-status">Status</label>
                <select id="service-type-status" name="status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </p>

            <p>
                <button type="submit" class="button button-primary">Save Service Type</button>
                <button type="button" class="button" id="cancel-service-type">Cancel</button>
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
        $('#service-type-name').on('input', function() {
            if (!$('#service-type-id').val()) {
                const slug = $(this).val()
                    .toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                $('#service-type-slug').val(slug);
            }
        });

        // Open modal for new service type
        $('#add-new-service-type, #add-first-service-type').on('click', function(e) {
            e.preventDefault();
            $('#modal-title').text('Add New Service Type');
            $('#service-type-form')[0].reset();
            $('#service-type-id').val('');
            $('#service-type-modal').show();
        });

        // Close modal
        $('#cancel-service-type').on('click', function() {
            $('#service-type-modal').hide();
        });

        // Edit service type
        $('.edit-service-type').on('click', function(e) {
            e.preventDefault();
            const id = $(this).data('id');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'skd_get_service_types',
                    id: id,
                    nonce: nonce
                },
                success: function(response) {
                    if (response.success && response.data.service_types.length > 0) {
                        const st = response.data.service_types[0];
                        $('#modal-title').text('Edit Service Type');
                        $('#service-type-id').val(st.id);
                        $('#service-type-name').val(st.name);
                        $('#service-type-slug').val(st.slug);
                        $('#service-type-description').val(st.description);
                        $('#service-type-icon').val(st.icon_url);
                        $('#service-type-sort-order').val(st.sort_order);
                        $('#service-type-status').val(st.status);
                        $('#service-type-modal').show();
                    }
                }
            });
        });

        // Submit form
        $('#service-type-form').on('submit', function(e) {
            e.preventDefault();

            const id = $('#service-type-id').val();
            const action = id ? 'skd_update_service_type' : 'skd_add_service_type';

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

        // Delete service type
        $('.delete-service-type').on('click', function(e) {
            e.preventDefault();

            if (!confirm('Are you sure you want to delete this service type?')) {
                return;
            }

            const id = $(this).data('id');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'skd_delete_service_type',
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