<?php
if (!defined('ABSPATH')) exit;

global $wpdb;
$specs_table = $wpdb->prefix . 'skd_pl_specializations';
$specializations = $wpdb->get_results("SELECT * FROM $specs_table ORDER BY sort_order ASC, name ASC");
?>

<div class="wrap">
    <h1 class="wp-heading-inline">Specializations Management</h1>
    <a href="#" class="page-title-action" id="add-new-spec">Add New</a>
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
            <?php if (empty($specializations)): ?>
                <tr>
                    <td colspan="6">No specializations found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($specializations as $spec): ?>
                    <tr>
                        <td><?php echo esc_html($spec->id); ?></td>
                        <td><strong><?php echo esc_html($spec->name); ?></strong></td>
                        <td><?php echo esc_html($spec->slug); ?></td>
                        <td><?php echo esc_html(wp_trim_words($spec->description, 10)); ?></td>
                        <td><?php echo esc_html(ucfirst($spec->status)); ?></td>
                        <td>
                            <a href="#" class="edit-spec" data-id="<?php echo $spec->id; ?>">Edit</a> |
                            <a href="#" class="delete-spec" data-id="<?php echo $spec->id; ?>" style="color: red;">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div id="spec-modal" style="display:none;">
    <div class="skd-modal-overlay"></div>
    <div class="skd-modal-content">
        <h2 id="modal-title">Add New Specialization</h2>
        <form id="spec-form">
            <input type="hidden" id="spec-id" name="id">
            <p>
                <label for="spec-name">Name *</label>
                <input type="text" id="spec-name" name="name" class="regular-text" required>
            </p>
            <p>
                <label for="spec-slug">Slug <small>(auto-generated from name)</small></label>
                <input type="text" id="spec-slug" name="slug" class="regular-text">
            </p>
            <p>
                <label for="spec-description">Description</label>
                <textarea id="spec-description" name="description" class="large-text" rows="3"></textarea>
            </p>
            <p>
                <label for="spec-status">Status</label>
                <select id="spec-status" name="status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </p>
            <p>
                <button type="submit" class="button button-primary">Save Specialization</button>
                <button type="button" class="button" id="cancel-spec">Cancel</button>
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
        $('#spec-name').on('input', function() {
            const name = $(this).val();
            const slug = name.toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
            $('#spec-slug').val(slug);
        });

        $('#add-new-spec').click(function(e) {
            e.preventDefault();
            $('#spec-form')[0].reset();
            $('#spec-id').val('');
            $('#modal-title').text('Add New Specialization');
            $('#spec-modal').show();
        });

        $('#cancel-spec').click(function() {
            $('#spec-modal').hide();
        });

        // Edit specialization
        $('.edit-spec').on('click', function(e) {
            e.preventDefault();
            const id = $(this).data('id');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'skd_get_specializations',
                    id: id,
                    nonce: nonce
                },
                success: function(response) {
                    if (response.success && response.data.specializations.length > 0) {
                        const spec = response.data.specializations[0];
                        $('#modal-title').text('Edit Specialization');
                        $('#spec-id').val(spec.id);
                        $('#spec-name').val(spec.name);
                        $('#spec-slug').val(spec.slug);
                        $('#spec-description').val(spec.description);
                        $('#spec-status').val(spec.status);
                        $('#spec-modal').show();
                    }
                }
            });
        });

        $('#spec-form').submit(function(e) {
            e.preventDefault();
            const action = $('#spec-id').val() ? 'skd_update_specialization' : 'skd_add_specialization';
            $.post(ajaxurl, $(this).serialize() + '&action=' + action + '&nonce=' + nonce, function(response) {
                alert(response.data.message);
                if (response.success) location.reload();
            });
        });

        $('.delete-spec').click(function(e) {
            e.preventDefault();
            if (!confirm('Delete this specialization?')) return;
            $.post(ajaxurl, {
                action: 'skd_delete_specialization',
                id: $(this).data('id'),
                nonce
            }, function(response) {
                alert(response.data.message);
                if (response.success) location.reload();
            });
        });
    });
</script>