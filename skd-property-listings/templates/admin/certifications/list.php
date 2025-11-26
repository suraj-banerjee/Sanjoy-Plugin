<?php
if (!defined('ABSPATH')) exit;

// Get all certifications and user certifications stats
global $wpdb;
$certs_table = $wpdb->prefix . 'skd_pl_certifications';
$user_certs_table = $wpdb->prefix . 'skd_pl_user_certifications';

$certifications = $wpdb->get_results("SELECT * FROM $certs_table ORDER BY sort_order ASC, name ASC");

// Get pending count
$pending_count = $wpdb->get_var("SELECT COUNT(*) FROM $user_certs_table WHERE status = 'pending'");
?>

<div class="wrap">
    <h1 class="wp-heading-inline">Certifications & Badges Management</h1>
    <hr class="wp-header-end">

    <div class="skd-admin-tabs">
        <button class="skd-tab-btn active" data-tab="master-certs">Master Certifications</button>
        <button class="skd-tab-btn" data-tab="user-certs">
            User Certifications
            <?php if ($pending_count > 0): ?>
                <span class="skd-badge-count"><?php echo $pending_count; ?></span>
            <?php endif; ?>
        </button>
    </div>

    <!-- Master Certifications Tab -->
    <div class="skd-tab-content active" id="tab-master-certs">
        <div class="skd-admin-actions" style="margin: 20px 0;">
            <a href="#" class="button button-primary" id="add-new-certification">Add New Certification</a>
        </div>

        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th width="50">ID</th>
                    <th>Badge</th>
                    <th>Name</th>
                    <th>Issuer</th>
                    <th>Description</th>
                    <th width="120">Verification</th>
                    <th width="80">Order</th>
                    <th width="80">Status</th>
                    <th width="120">Actions</th>
                </tr>
            </thead>
            <tbody id="sortable-certs">
                <?php if (empty($certifications)): ?>
                    <tr>
                        <td colspan="9">No certifications found. <a href="#" id="add-first-cert">Add your first certification</a></td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($certifications as $cert): ?>
                        <tr data-id="<?php echo $cert->id; ?>" class="sortable-row">
                            <td>
                                <span class="dashicons dashicons-move" style="cursor: move; color: #999;" title="Drag to reorder"></span>
                                <?php echo esc_html($cert->id); ?>
                            </td>
                            <td>
                                <?php if ($cert->badge_image_url): ?>
                                    <img src="<?php echo esc_url($cert->badge_image_url); ?>" alt="Badge" style="width: 40px; height: 40px; object-fit: contain;">
                                <?php else: ?>
                                    <span style="font-size: 30px; color: #667eea;">🏆</span>
                                <?php endif; ?>
                            </td>
                            <td><strong><?php echo esc_html($cert->name); ?></strong></td>
                            <td><?php echo esc_html($cert->issuer); ?></td>
                            <td><?php echo esc_html(wp_trim_words($cert->description, 10)); ?></td>
                            <td>
                                <?php if ($cert->verification_required): ?>
                                    <span class="skd-badge skd-badge-warning">Required</span>
                                <?php else: ?>
                                    <span class="skd-badge skd-badge-success">Auto</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo esc_html($cert->sort_order); ?></td>
                            <td>
                                <span class="skd-badge <?php echo $cert->status === 'active' ? 'skd-badge-success' : 'skd-badge-inactive'; ?>">
                                    <?php echo esc_html(ucfirst($cert->status)); ?>
                                </span>
                            </td>
                            <td>
                                <a href="#" class="edit-cert" data-id="<?php echo $cert->id; ?>">Edit</a> |
                                <a href="#" class="delete-cert" data-id="<?php echo $cert->id; ?>" style="color: red;">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- User Certifications Tab -->
    <div class="skd-tab-content" id="tab-user-certs">
        <div class="skd-admin-filters" style="margin: 20px 0;">
            <label>
                <strong>Filter by Status:</strong>
                <select id="user-cert-status-filter">
                    <option value="">All</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </label>
            <button class="button" id="load-user-certs">Refresh</button>
        </div>

        <table class="wp-list-table widefat fixed striped" id="user-certs-table">
            <thead>
                <tr>
                    <th width="50">ID</th>
                    <th>User</th>
                    <th>Certification</th>
                    <th>Issuer</th>
                    <th>Certificate File</th>
                    <th>Submitted</th>
                    <th>Notes</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="user-certs-tbody">
                <tr>
                    <td colspan="9" style="text-align: center;">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit Certification Modal -->
<div id="cert-modal" style="display:none;">
    <div class="skd-modal-overlay"></div>
    <div class="skd-modal-content" style="max-width: 600px;">
        <h2 id="modal-title">Add New Certification</h2>
        <form id="cert-form" enctype="multipart/form-data">
            <input type="hidden" id="cert-id" name="id" value="">

            <p>
                <label for="cert-name">Certification Name *</label>
                <input type="text" id="cert-name" name="name" class="regular-text" required placeholder="e.g., AutoCAD Certified Professional">
            </p>

            <p>
                <label for="cert-issuer">Issuer/Organization *</label>
                <input type="text" id="cert-issuer" name="issuer" class="regular-text" required placeholder="e.g., Autodesk">
            </p>

            <p>
                <label for="cert-description">Description</label>
                <textarea id="cert-description" name="description" class="large-text" rows="3" placeholder="Brief description of what this certification represents..."></textarea>
            </p>

            <p>
                <label for="cert-badge-image">Badge Image (Optional)</label>
                <input type="file" id="cert-badge-image" name="badge_image" accept="image/*">
                <small style="display: block; margin-top: 5px; color: #666;">Upload a badge icon (PNG, JPG, SVG recommended size: 80x80px)</small>
            </p>

            <p>
                <label>
                    <input type="checkbox" id="cert-verification" name="verification_required" value="1" checked>
                    Requires Manual Verification (Admin must approve submissions)
                </label>
            </p>

            <p>
                <label for="cert-sort-order">Sort Order</label>
                <input type="number" id="cert-sort-order" name="sort_order" class="small-text" value="0" min="0">
                <small style="display: block; margin-top: 5px; color: #666;">Lower numbers appear first</small>
            </p>

            <p>
                <label for="cert-status">Status</label>
                <select id="cert-status" name="status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </p>

            <p style="border-top: 1px solid #ddd; padding-top: 15px; margin-top: 20px;">
                <button type="submit" class="button button-primary">Save Certification</button>
                <button type="button" class="button" id="cancel-cert">Cancel</button>
            </p>
        </form>
    </div>
</div>

<style>
    .skd-admin-tabs {
        display: flex;
        gap: 10px;
        margin: 20px 0;
        border-bottom: 2px solid #ddd;
    }

    .skd-tab-btn {
        padding: 10px 20px;
        background: transparent;
        border: none;
        border-bottom: 2px solid transparent;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        color: #555;
        margin-bottom: -2px;
        position: relative;
    }

    .skd-tab-btn:hover {
        color: #2271b1;
    }

    .skd-tab-btn.active {
        color: #2271b1;
        border-bottom-color: #2271b1;
    }

    .skd-badge-count {
        background: #d63638;
        color: white;
        border-radius: 10px;
        padding: 2px 8px;
        font-size: 11px;
        font-weight: 600;
        margin-left: 5px;
    }

    .skd-tab-content {
        display: none;
    }

    .skd-tab-content.active {
        display: block;
    }

    .skd-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 500;
    }

    .skd-badge-success {
        background: #d1f4e0;
        color: #0a6640;
    }

    .skd-badge-warning {
        background: #fff8e1;
        color: #f57c00;
    }

    .skd-badge-danger {
        background: #ffebee;
        color: #c62828;
    }

    .skd-badge-inactive {
        background: #e0e0e0;
        color: #666;
    }

    .skd-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 100000;
    }

    .skd-modal-content {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        padding: 30px;
        border-radius: 8px;
        max-width: 600px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
        z-index: 100001;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    }

    .skd-modal-content h2 {
        margin-top: 0;
        border-bottom: 1px solid #ddd;
        padding-bottom: 15px;
    }

    .skd-modal-content label {
        display: block;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .skd-modal-content input[type="text"],
    .skd-modal-content input[type="number"],
    .skd-modal-content select,
    .skd-modal-content textarea {
        width: 100%;
        max-width: 100%;
    }

    .sortable-row {
        cursor: move;
    }

    .sortable-row:hover {
        background-color: #f0f0f1 !important;
    }

    .ui-sortable-helper {
        background-color: #fff !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .ui-sortable-placeholder {
        background-color: #e3f2fd !important;
        border: 2px dashed #2196f3 !important;
        visibility: visible !important;
    }
</style>

<script>
    jQuery(document).ready(function($) {
        const nonce = '<?php echo wp_create_nonce('skd_admin_nonce'); ?>';

        // Initialize sortable for drag-and-drop reordering
        $('#sortable-certs').sortable({
            handle: '.dashicons-move',
            placeholder: 'ui-sortable-placeholder',
            cursor: 'move',
            update: function(event, ui) {
                // Get new order
                const order = [];
                $('#sortable-certs tr').each(function() {
                    const id = $(this).data('id');
                    if (id) {
                        order.push(id);
                    }
                });

                // Save order via AJAX
                $.post(ajaxurl, {
                    action: 'skd_reorder_certifications',
                    nonce: nonce,
                    order: order
                }, function(response) {
                    if (response.success) {
                        // Update sort_order display
                        $('#sortable-certs tr').each(function(index) {
                            $(this).find('td:eq(6)').text(index + 1);
                        });

                        // Show success message
                        $('<div class="notice notice-success is-dismissible" style="margin: 10px 0;"><p>Order updated successfully!</p></div>')
                            .insertAfter('.skd-admin-actions')
                            .delay(2000)
                            .fadeOut();
                    } else {
                        alert('Error updating order: ' + (response.data.message || 'Unknown error'));
                    }
                });
            }
        });

        // Tab switching
        $('.skd-tab-btn').click(function() {
            const tab = $(this).data('tab');
            $('.skd-tab-btn').removeClass('active');
            $(this).addClass('active');
            $('.skd-tab-content').removeClass('active');
            $('#tab-' + tab).addClass('active');

            if (tab === 'user-certs') {
                loadUserCertifications();
            }
        });

        // Add new certification
        $('#add-new-certification, #add-first-cert').click(function(e) {
            e.preventDefault();
            $('#modal-title').text('Add New Certification');
            $('#cert-form')[0].reset();
            $('#cert-id').val('');
            $('#cert-modal').show();
        });

        // Edit certification
        $(document).on('click', '.edit-cert', function(e) {
            e.preventDefault();
            const id = $(this).data('id');

            // Get certification data from the row
            const row = $(this).closest('tr');
            const name = row.find('td:eq(2)').text().trim();
            const issuer = row.find('td:eq(3)').text().trim();
            const description = row.find('td:eq(4)').text().trim();
            const sortOrder = row.find('td:eq(6)').text().trim();
            const verificationRequired = row.find('td:eq(5) .skd-badge-warning').length > 0;
            const status = row.find('td:eq(7) .skd-badge').text().trim().toLowerCase();

            $('#modal-title').text('Edit Certification');
            $('#cert-id').val(id);
            $('#cert-name').val(name);
            $('#cert-issuer').val(issuer);
            $('#cert-description').val(description);
            $('#cert-sort-order').val(sortOrder);
            $('#cert-verification').prop('checked', verificationRequired);
            $('#cert-status').val(status);
            $('#cert-modal').show();
        });

        // Cancel modal
        $('#cancel-cert').click(function() {
            $('#cert-modal').hide();
        });

        $('.skd-modal-overlay').click(function() {
            $('#cert-modal').hide();
        });

        // Save certification
        $('#cert-form').submit(function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const id = $('#cert-id').val();

            formData.append('action', id ? 'skd_update_certification_master' : 'skd_add_certification_master');
            formData.append('nonce', nonce);

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                        location.reload();
                    } else {
                        alert('Error: ' + response.data.message);
                    }
                }
            });
        });

        // Delete certification
        $(document).on('click', '.delete-cert', function(e) {
            e.preventDefault();

            if (!confirm('Are you sure you want to delete this certification? This action cannot be undone.')) {
                return;
            }

            const id = $(this).data('id');

            $.post(ajaxurl, {
                action: 'skd_delete_certification_master',
                nonce: nonce,
                id: id
            }, function(response) {
                if (response.success) {
                    alert(response.data.message);
                    location.reload();
                } else {
                    alert('Error: ' + response.data.message);
                }
            });
        });

        // Load user certifications
        function loadUserCertifications() {
            const status = $('#user-cert-status-filter').val();

            $.post(ajaxurl, {
                action: 'skd_get_user_certifications',
                nonce: nonce,
                status: status
            }, function(response) {
                if (response.success) {
                    const certs = response.data.user_certifications;
                    let html = '';

                    if (certs.length === 0) {
                        html = '<tr><td colspan="9" style="text-align:center;">No user certifications found.</td></tr>';
                    } else {
                        certs.forEach(cert => {
                            let statusBadge = '';
                            let actions = '';

                            if (cert.status === 'pending') {
                                statusBadge = '<span class="skd-badge skd-badge-warning">Pending</span>';
                                actions = `
                                <button class="button button-small approve-user-cert" data-id="${cert.id}">Approve</button>
                                <button class="button button-small reject-user-cert" data-id="${cert.id}">Reject</button>
                            `;
                            } else if (cert.status === 'approved') {
                                statusBadge = '<span class="skd-badge skd-badge-success">Approved</span>';
                                actions = `<button class="button button-small reject-user-cert" data-id="${cert.id}">Revoke</button>`;
                            } else if (cert.status === 'rejected') {
                                statusBadge = '<span class="skd-badge skd-badge-danger">Rejected</span>';
                                actions = `<button class="button button-small approve-user-cert" data-id="${cert.id}">Approve</button>`;
                            }

                            const certFile = cert.certificate_file ?
                                `<a href="${cert.certificate_file}" target="_blank" class="button button-small">View File</a>` :
                                '<em>No file</em>';

                            html += `
                            <tr>
                                <td>${cert.id}</td>
                                <td><strong>${cert.display_name}</strong><br><small>${cert.user_email}</small></td>
                                <td>${cert.cert_name}</td>
                                <td>${cert.issuer || '-'}</td>
                                <td>${certFile}</td>
                                <td>${cert.created_at}</td>
                                <td>${cert.notes || '-'}</td>
                                <td>${statusBadge}</td>
                                <td>${actions}</td>
                            </tr>
                        `;
                        });
                    }

                    $('#user-certs-tbody').html(html);
                }
            });
        }

        $('#load-user-certs').click(loadUserCertifications);
        $('#user-cert-status-filter').change(loadUserCertifications);

        // Approve user certification
        $(document).on('click', '.approve-user-cert', function() {
            const id = $(this).data('id');

            if (!confirm('Approve this certification?')) return;

            $.post(ajaxurl, {
                action: 'skd_approve_user_certification',
                nonce: nonce,
                id: id
            }, function(response) {
                if (response.success) {
                    alert(response.data.message);
                    loadUserCertifications();
                } else {
                    alert('Error: ' + response.data.message);
                }
            });
        });

        // Reject user certification
        $(document).on('click', '.reject-user-cert', function() {
            const id = $(this).data('id');

            if (!confirm('Reject this certification?')) return;

            $.post(ajaxurl, {
                action: 'skd_reject_user_certification',
                nonce: nonce,
                id: id
            }, function(response) {
                if (response.success) {
                    alert(response.data.message);
                    loadUserCertifications();
                } else {
                    alert('Error: ' + response.data.message);
                }
            });
        });
    });
</script>