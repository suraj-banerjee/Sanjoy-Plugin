<?php
if (!defined('ABSPATH')) exit;

global $wpdb;
$table = $wpdb->prefix . 'skd_pl_timezones';
$timezones = $wpdb->get_results("SELECT * FROM $table ORDER BY sort_order ASC, name ASC");
?>

<div class="wrap">
    <h1 class="wp-heading-inline">Timezone Management</h1>
    <a href="#" class="page-title-action" id="add-new-item">Add New</a>
    <hr class="wp-header-end">

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th width="5%">ID</th>
                <th width="35%">Name</th>
                <th width="20%">Timezone Identifier</th>
                <th width="12%">Offset</th>
                <th width="10%">Status</th>
                <th width="13%">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($timezones)): ?>
                <tr>
                    <td colspan="6">No timezones found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($timezones as $item): ?>
                    <tr>
                        <td><?php echo esc_html($item->id); ?></td>
                        <td><strong><?php echo esc_html($item->name); ?></strong></td>
                        <td><code><?php echo esc_html($item->value); ?></code></td>
                        <td><?php echo esc_html($item->offset); ?></td>
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
        <h2 id="modal-title">Add New Timezone</h2>
        <form id="item-form">
            <input type="hidden" id="item-id" name="id">
            <p>
                <label for="item-name">Display Name * <small>(e.g., (GMT+05:30) Mumbai, Kolkata, New Delhi)</small></label>
                <input type="text" id="item-name" name="name" class="regular-text" required placeholder="(GMT+05:30) Mumbai, Kolkata, New Delhi">
            </p>
            <p>
                <label for="item-value">Timezone Identifier * <small>(e.g., Asia/Kolkata - internal use only)</small></label>
                <input type="text" id="item-value" name="value" class="regular-text" required placeholder="Asia/Kolkata">
            </p>
            <p>
                <label for="item-offset">GMT Offset * <small>(e.g., GMT+05:30)</small></label>
                <input type="text" id="item-offset" name="offset" class="regular-text" required placeholder="GMT+05:30">
            </p>
            <p>
                <label for="item-status">Status</label>
                <select id="item-status" name="status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </p>
            <p>
                <button type="submit" class="button button-primary">Save Timezone</button>
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

        $('#add-new-item').click(function(e) {
            e.preventDefault();
            $('#item-form')[0].reset();
            $('#item-id').val('');
            $('#modal-title').text('Add New Timezone');
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
                    action: 'skd_get_timezones',
                    id: id,
                    nonce: nonce
                },
                success: function(response) {
                    if (response.success && response.data.timezones.length > 0) {
                        const item = response.data.timezones[0];
                        $('#modal-title').text('Edit Timezone');
                        $('#item-id').val(item.id);
                        $('#item-name').val(item.name);
                        $('#item-value').val(item.value);
                        $('#item-offset').val(item.offset);
                        $('#item-status').val(item.status);
                        $('#item-modal').show();
                    }
                }
            });
        });

        $('#item-form').submit(function(e) {
            e.preventDefault();
            const action = $('#item-id').val() ? 'skd_update_timezone' : 'skd_add_timezone';
            $.post(ajaxurl, $(this).serialize() + '&action=' + action + '&nonce=' + nonce, function(response) {
                alert(response.data.message);
                if (response.success) location.reload();
            });
        });

        $('.delete-item').click(function(e) {
            e.preventDefault();
            if (!confirm('Delete this timezone?')) return;
            $.post(ajaxurl, {
                action: 'skd_delete_timezone',
                id: $(this).data('id'),
                nonce
            }, function(response) {
                alert(response.data.message);
                if (response.success) location.reload();
            });
        });
    });
</script>