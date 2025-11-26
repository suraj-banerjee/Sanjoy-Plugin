<?php
if (!defined('ABSPATH')) exit;

// Get all portfolio categories
global $wpdb;
$categories_table = $wpdb->prefix . 'skd_pl_portfolio_categories';
$categories = $wpdb->get_results("SELECT * FROM $categories_table ORDER BY sort_order ASC, name ASC");
?>

<div class="wrap">
    <h1 class="wp-heading-inline">Portfolio Categories Management</h1>
    <hr class="wp-header-end">

    <div class="skd-admin-actions" style="margin: 20px 0;">
        <a href="#" class="button button-primary" id="add-new-category">Add New Category</a>
    </div>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th width="50">ID</th>
                <th>Icon</th>
                <th>Name</th>
                <th>Slug</th>
                <th>Description</th>
                <th width="80">Order</th>
                <th width="80">Status</th>
                <th width="120">Actions</th>
            </tr>
        </thead>
        <tbody id="sortable-categories">
            <?php if (empty($categories)): ?>
                <tr>
                    <td colspan="8">No categories found. <a href="#" id="add-first-category">Add your first category</a></td>
                </tr>
            <?php else: ?>
                <?php foreach ($categories as $category): ?>
                    <tr data-id="<?php echo $category->id; ?>" class="sortable-row">
                        <td>
                            <span class="dashicons dashicons-move" style="cursor: move; color: #999;" title="Drag to reorder"></span>
                            <?php echo esc_html($category->id); ?>
                        </td>
                        <td>
                            <?php if ($category->icon_url): ?>
                                <img src="<?php echo esc_url($category->icon_url); ?>" alt="Icon" style="width: 40px; height: 40px; object-fit: contain;">
                            <?php else: ?>
                                <span style="font-size: 30px;">📁</span>
                            <?php endif; ?>
                        </td>
                        <td><strong><?php echo esc_html($category->name); ?></strong></td>
                        <td><code><?php echo esc_html($category->slug); ?></code></td>
                        <td><?php echo esc_html(wp_trim_words($category->description, 10)); ?></td>
                        <td><?php echo esc_html($category->sort_order); ?></td>
                        <td>
                            <span class="skd-badge <?php echo $category->status === 'active' ? 'skd-badge-success' : 'skd-badge-inactive'; ?>">
                                <?php echo esc_html(ucfirst($category->status)); ?>
                            </span>
                        </td>
                        <td>
                            <a href="#" class="edit-category" data-id="<?php echo $category->id; ?>">Edit</a> |
                            <a href="#" class="delete-category" data-id="<?php echo $category->id; ?>" style="color: red;">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Add/Edit Category Modal -->
<div id="category-modal" class="skd-modal" style="display: none;">
    <div class="skd-modal-content">
        <span class="skd-modal-close">&times;</span>
        <h2 id="modal-title">Add New Category</h2>

        <form id="category-form" enctype="multipart/form-data">
            <input type="hidden" id="category-id" name="id" value="">

            <table class="form-table">
                <tr>
                    <th><label for="category-name">Category Name *</label></th>
                    <td>
                        <input type="text" id="category-name" name="name" class="regular-text" required>
                        <p class="description">e.g., Residential Interior, 3D Visualization, Kitchen Design</p>
                    </td>
                </tr>

                <tr>
                    <th><label for="category-slug">Slug</label></th>
                    <td>
                        <input type="text" id="category-slug" name="slug" class="regular-text">
                        <p class="description">URL-friendly version. Leave blank to auto-generate from name.</p>
                    </td>
                </tr>

                <tr>
                    <th><label for="category-description">Description</label></th>
                    <td>
                        <textarea id="category-description" name="description" class="large-text" rows="3"></textarea>
                        <p class="description">Brief description of this portfolio category.</p>
                    </td>
                </tr>

                <tr>
                    <th><label for="category-icon">Icon/Image</label></th>
                    <td>
                        <div id="icon-preview" style="margin-bottom: 10px;">
                            <img id="icon-preview-img" src="" alt="Icon" style="max-width: 80px; max-height: 80px; display: none;">
                        </div>
                        <input type="file" id="category-icon" name="icon_image" accept="image/*">
                        <input type="hidden" id="current-icon-url" name="current_icon_url" value="">
                        <p class="description">Upload an icon or image for this category (optional).</p>
                    </td>
                </tr>

                <tr>
                    <th><label for="category-sort-order">Sort Order</label></th>
                    <td>
                        <input type="number" id="category-sort-order" name="sort_order" class="small-text" value="0" min="0">
                        <p class="description">Lower numbers appear first. You can also drag rows to reorder.</p>
                    </td>
                </tr>

                <tr>
                    <th><label for="category-status">Status</label></th>
                    <td>
                        <select id="category-status" name="status">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </td>
                </tr>
            </table>

            <div class="skd-modal-actions">
                <button type="submit" class="button button-primary">Save Category</button>
                <button type="button" class="button" id="cancel-category">Cancel</button>
            </div>
        </form>
    </div>
</div>

<style>
    .skd-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .skd-badge-success {
        background: #d1fae5;
        color: #065f46;
    }

    .skd-badge-inactive {
        background: #e5e7eb;
        color: #6b7280;
    }

    .skd-modal {
        position: fixed;
        z-index: 100000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.6);
    }

    .skd-modal-content {
        background-color: #fff;
        margin: 3% auto;
        padding: 30px;
        border: 1px solid #ddd;
        border-radius: 8px;
        width: 90%;
        max-width: 700px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    }

    .skd-modal-close {
        color: #999;
        float: right;
        font-size: 32px;
        font-weight: bold;
        line-height: 20px;
        cursor: pointer;
    }

    .skd-modal-close:hover {
        color: #333;
    }

    .skd-modal-actions {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #ddd;
        text-align: right;
    }

    .skd-modal-actions .button {
        margin-left: 10px;
    }

    #sortable-categories .sortable-row {
        cursor: move;
    }

    #sortable-categories .ui-sortable-helper {
        background-color: #f0f0f0;
        opacity: 0.8;
    }
</style>

<script>
    jQuery(document).ready(function($) {
        // Initialize sortable
        $('#sortable-categories').sortable({
            handle: '.dashicons-move',
            axis: 'y',
            cursor: 'move',
            placeholder: 'ui-state-highlight',
            update: function(event, ui) {
                const order = [];
                $('#sortable-categories tr').each(function() {
                    const id = $(this).data('id');
                    if (id) {
                        order.push(id);
                    }
                });

                // Save order via AJAX
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'skd_pl_reorder_portfolio_categories',
                        order: order,
                        nonce: '<?php echo wp_create_nonce('skd_pl_reorder_portfolio_categories'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Update sort_order display
                            $('#sortable-categories tr').each(function(index) {
                                $(this).find('td:nth-child(6)').text(index + 1);
                            });
                        }
                    }
                });
            }
        });

        // Modal controls
        function openModal(title, category = null) {
            $('#modal-title').text(title);
            $('#category-modal').fadeIn();

            if (category) {
                // Edit mode
                $('#category-id').val(category.id);
                $('#category-name').val(category.name);
                $('#category-slug').val(category.slug);
                $('#category-description').val(category.description);
                $('#category-sort-order').val(category.sort_order);
                $('#category-status').val(category.status);
                $('#current-icon-url').val(category.icon_url || '');

                if (category.icon_url) {
                    $('#icon-preview-img').attr('src', category.icon_url).show();
                } else {
                    $('#icon-preview-img').hide();
                }
            } else {
                // Add mode
                $('#category-form')[0].reset();
                $('#category-id').val('');
                $('#icon-preview-img').hide();
                $('#current-icon-url').val('');
            }
        }

        function closeModal() {
            $('#category-modal').fadeOut();
            $('#category-form')[0].reset();
        }

        // Add new category
        $('#add-new-category, #add-first-category').on('click', function(e) {
            e.preventDefault();
            openModal('Add New Category');
        });

        // Edit category
        $(document).on('click', '.edit-category', function(e) {
            e.preventDefault();
            const id = $(this).data('id');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'skd_pl_get_portfolio_category',
                    id: id
                },
                success: function(response) {
                    if (response.success) {
                        openModal('Edit Category', response.data);
                    }
                }
            });
        });

        // Close modal
        $('.skd-modal-close, #cancel-category').on('click', function() {
            closeModal();
        });

        // Close modal on outside click
        $(window).on('click', function(e) {
            if ($(e.target).is('#category-modal')) {
                closeModal();
            }
        });

        // Preview icon on file select
        $('#category-icon').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#icon-preview-img').attr('src', e.target.result).show();
                };
                reader.readAsDataURL(file);
            }
        });

        // Submit form
        $('#category-form').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const isEdit = $('#category-id').val() !== '';

            formData.append('action', isEdit ? 'skd_pl_update_portfolio_category' : 'skd_pl_add_portfolio_category');
            formData.append('nonce', '<?php echo wp_create_nonce('skd_pl_manage_portfolio_categories'); ?>');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        closeModal();
                        location.reload();
                    } else {
                        alert(response.data || 'Error saving category');
                    }
                },
                error: function() {
                    alert('Error communicating with server');
                }
            });
        });

        // Delete category
        $(document).on('click', '.delete-category', function(e) {
            e.preventDefault();

            if (!confirm('Are you sure you want to delete this category? This action cannot be undone.')) {
                return;
            }

            const id = $(this).data('id');
            const $row = $(this).closest('tr');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'skd_pl_delete_portfolio_category',
                    id: id,
                    nonce: '<?php echo wp_create_nonce('skd_pl_manage_portfolio_categories'); ?>'
                },
                success: function(response) {
                    if (response.success) {
                        $row.fadeOut(300, function() {
                            $(this).remove();
                        });
                    } else {
                        alert(response.data || 'Error deleting category');
                    }
                }
            });
        });
    });
</script>