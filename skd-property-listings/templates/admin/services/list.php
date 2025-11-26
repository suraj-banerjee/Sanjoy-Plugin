<?php
if (!defined('ABSPATH')) exit;

global $wpdb;
$services_table = $wpdb->prefix . 'skd_pl_services';
$services = $wpdb->get_results("SELECT * FROM $services_table ORDER BY sort_order ASC, name ASC");
?>

<div class="wrap">
    <h1 class="wp-heading-inline">Services Management</h1>
    <a href="#" class="page-title-action" id="add-new-service">Add New</a>
    <hr class="wp-header-end">

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($services)): ?>
                <tr>
                    <td colspan="5">No services found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($services as $service): ?>
                    <tr>
                        <td><?php echo esc_html($service->id); ?></td>
                        <td><strong><?php echo esc_html($service->name); ?></strong></td>
                        <td><?php echo esc_html(wp_trim_words($service->description, 10)); ?></td>
                        <td><?php echo esc_html(ucfirst($service->status)); ?></td>
                        <td>
                            <a href="#" class="edit-service" data-id="<?php echo $service->id; ?>">Edit</a> |
                            <a href="#" class="delete-service" data-id="<?php echo $service->id; ?>" style="color: red;">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div id="service-modal" style="display:none;">
    <div class="skd-modal-overlay"></div>
    <div class="skd-modal-content">
        <h2 id="modal-title">Add New Service</h2>
        <form id="service-form">
            <input type="hidden" id="service-id" name="id">
            <p>
                <label for="service-name">Name *</label>
                <input type="text" id="service-name" name="name" class="regular-text" required>
            </p>
            <p>
                <label for="service-description">Description</label>
                <textarea id="service-description" name="description" class="large-text" rows="3"></textarea>
            </p>
            <p>
                <label for="service-status">Status</label>
                <select id="service-status" name="status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </p>
            <p>
                <button type="submit" class="button button-primary">Save Service</button>
                <button type="button" class="button" id="cancel-service">Cancel</button>
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

        $('#add-new-service').click(function(e) {
            e.preventDefault();
            $('#service-form')[0].reset();
            $('#service-id').val('');
            $('#modal-title').text('Add New Service');
            $('#service-modal').show();
        });

        $('#cancel-service').click(function() {
            $('#service-modal').hide();
        });

        $('#service-form').submit(function(e) {
            e.preventDefault();
            const action = $('#service-id').val() ? 'skd_update_service' : 'skd_add_service';
            $.post(ajaxurl, $(this).serialize() + '&action=' + action + '&nonce=' + nonce, function(response) {
                alert(response.data.message);
                if (response.success) location.reload();
            });
        });

        $('.delete-service').click(function(e) {
            e.preventDefault();
            if (!confirm('Delete this service?')) return;
            $.post(ajaxurl, {
                action: 'skd_delete_service',
                id: $(this).data('id'),
                nonce
            }, function(response) {
                alert(response.data.message);
                if (response.success) location.reload();
            });
        });
    });
</script>