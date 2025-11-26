<?php
if (!defined('ABSPATH')) exit;

global $wpdb;
$table = $wpdb->prefix . 'skd_pl_response_times';
$times = $wpdb->get_results("SELECT * FROM $table ORDER BY sort_order ASC, name ASC");
?>

<div class="wrap">
    <h1 class="wp-heading-inline">Response Time Management</h1>
    <a href="#" class="page-title-action" id="add-new-item">Add New</a>
    <hr class="wp-header-end">

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
            <?php if (empty($times)): ?>
                <tr>
                    <td colspan="6">No response times found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($times as $item): ?>
                    <tr>
                        <td><?php echo esc_html($item->id); ?></td>
                        <td><strong><?php echo esc_html($item->name); ?></strong></td>
                        <td><?php echo esc_html($item->slug); ?></td>
                        <td><?php echo esc_html(wp_trim_words($item->description, 10)); ?></td>
                        <td><?php echo esc_html(ucfirst($item->status)); ?></td>
                        <td>
                            <a href="#" class="edit-item" data-id="<?php echo $item->id; ?>">Edit</a> |
                            <a href="#" class="delete-item" data-id="<?php echo $item->id; ?>" style="color: red;">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div id="item-modal" style="display:none;">
    <div class="skd-modal-overlay"></div>
    <div class="skd-modal-content">
        <h2 id="modal-title">Add New Response Time</h2>
        <form id="item-form">
            <input type="hidden" id="item-id" name="id">
            <p>
                <label for="item-name">Name *</label>
                <input type="text" id="item-name" name="name" class="regular-text" required>
            </p>
            <p>
                <label for="item-slug">Slug <small>(auto-generated from name)</small></label>
                <input type="text" id="item-slug" name="slug" class="regular-text">
            </p>
            <p>
                <label for="item-description">Description</label>
                <textarea id="item-description" name="description" class="large-text" rows="3"></textarea>
            </p>
            <p>
                <label for="item-status">Status</label>
                <select id="item-status" name="status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </p>
            <p>
                <button type="submit" class="button button-primary">Save Response Time</button>
                <button type="button" class="button" id="cancel-item">Cancel</button>
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

        // Auto-generate slug from name
        $('#item-name').on('input', function() {
            const name = $(this).val();
            const slug = name.toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
            $('#item-slug').val(slug);
        });

        $('#add-new-item').click(function(e) {
            e.preventDefault();
            $('#item-form')[0].reset();
            $('#item-id').val('');
            $('#modal-title').text('Add New Response Time');
            $('#item-modal').show();
        });

        $('#cancel-item').click(function() {
            $('#item-modal').hide();
        });

        // Edit
        $('.edit-item').on('click', function(e) {
            e.preventDefault();
            const id = $(this).data('id');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'skd_get_response_times',
                    id: id,
                    nonce: nonce
                },
                success: function(response) {
                    if (response.success && response.data.response_times.length > 0) {
                        const item = response.data.response_times[0];
                        $('#modal-title').text('Edit Response Time');
                        $('#item-id').val(item.id);
                        $('#item-name').val(item.name);
                        $('#item-slug').val(item.slug);
                        $('#item-description').val(item.description);
                        $('#item-status').val(item.status);
                        $('#item-modal').show();
                    }
                }
            });
        });

        $('#item-form').submit(function(e) {
            e.preventDefault();
            const action = $('#item-id').val() ? 'skd_update_response_time' : 'skd_add_response_time';
            $.post(ajaxurl, $(this).serialize() + '&action=' + action + '&nonce=' + nonce, function(response) {
                alert(response.data.message);
                if (response.success) location.reload();
            });
        });

        $('.delete-item').click(function(e) {
            e.preventDefault();
            if (!confirm('Delete this response time?')) return;
            $.post(ajaxurl, {
                action: 'skd_delete_response_time',
                id: $(this).data('id'),
                nonce
            }, function(response) {
                alert(response.data.message);
                if (response.success) location.reload();
            });
        });
    });
</script>