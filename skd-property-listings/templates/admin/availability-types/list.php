<?php
if (!defined('ABSPATH')) exit;

global $wpdb;
$availability_types_table = $wpdb->prefix . 'skd_pl_availability_types';
$availability_types = $wpdb->get_results("SELECT * FROM $availability_types_table ORDER BY sort_order ASC, name ASC");
?>

<div class="wrap">
    <h1 class="wp-heading-inline">Availability Types Management</h1>
    <a href="#" class="page-title-action" id="add-new-item">Add New</a>
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
                <?php if (empty($availability_types)): ?>
                    <tr>
                        <td colspan="7">No availability types found. <a href="#" id="add-first-item">Add your first availability type</a></td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($availability_types as $item): ?>
                        <tr>
                            <td><?php echo esc_html($item->id); ?></td>
                            <td><strong><?php echo esc_html($item->name); ?></strong></td>
                            <td><?php echo esc_html($item->slug); ?></td>
                            <td><?php echo esc_html(wp_trim_words($item->description, 10)); ?></td>
                            <td><?php echo esc_html($item->sort_order); ?></td>
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
</div>

<div id="item-modal" style="display:none;">
    <div class="skd-modal-overlay"></div>
    <div class="skd-modal-content">
        <h2 id="modal-title">Add New Availability Type</h2>
        <form id="item-form">
            <input type="hidden" id="item-id" name="id" value="">
            <p>
                <label for="item-name">Name *</label>
                <input type="text" id="item-name" name="name" class="regular-text" required>
            </p>
            <p>
                <label for="item-slug">Slug</label>
                <input type="text" id="item-slug" name="slug" class="regular-text">
                <small>Leave blank to auto-generate from name</small>
            </p>
            <p>
                <label for="item-description">Description</label>
                <textarea id="item-description" name="description" class="large-text" rows="3"></textarea>
            </p>
            <p>
                <label for="item-sort-order">Sort Order</label>
                <input type="number" id="item-sort-order" name="sort_order" class="small-text" value="0" min="0">
            </p>
            <p>
                <label for="item-status">Status</label>
                <select id="item-status" name="status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </p>
            <p>
                <button type="submit" class="button button-primary">Save Availability Type</button>
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

        $('#item-name').on('input', function() {
            if (!$('#item-id').val()) {
                const slug = $(this).val()
                    .toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');
                $('#item-slug').val(slug);
            }
        });

        $('#add-new-item, #add-first-item').on('click', function(e) {
            e.preventDefault();
            $('#modal-title').text('Add New Availability Type');
            $('#item-form')[0].reset();
            $('#item-id').val('');
            $('#item-modal').show();
        });

        $('#cancel-item').on('click', function() {
            $('#item-modal').hide();
        });

        $('.edit-item').on('click', function(e) {
            e.preventDefault();
            const id = $(this).data('id');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'skd_get_availability_types',
                    id: id,
                    nonce: nonce
                },
                success: function(response) {
                    if (response.success && response.data.availability_types.length > 0) {
                        const item = response.data.availability_types[0];
                        $('#modal-title').text('Edit Availability Type');
                        $('#item-id').val(item.id);
                        $('#item-name').val(item.name);
                        $('#item-slug').val(item.slug);
                        $('#item-description').val(item.description);
                        $('#item-sort-order').val(item.sort_order);
                        $('#item-status').val(item.status);
                        $('#item-modal').show();
                    }
                }
            });
        });

        $('#item-form').on('submit', function(e) {
            e.preventDefault();

            const id = $('#item-id').val();
            const action = id ? 'skd_update_availability_type' : 'skd_add_availability_type';

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

        $('.delete-item').on('click', function(e) {
            e.preventDefault();

            if (!confirm('Are you sure you want to delete this availability type?')) {
                return;
            }

            const id = $(this).data('id');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'skd_delete_availability_type',
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