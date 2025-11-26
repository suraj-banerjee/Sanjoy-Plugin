<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<style>
    .skd-user-listings table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .skd-user-listings th,
    .skd-user-listings td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left;
    }

    .skd-user-listings th {
        background: #f4f4f4;
    }

    .skd-add-listing-btn {
        background: #28a745;
        color: #fff;
        padding: 6px 12px;
        text-decoration: none;
        border-radius: 4px;
    }

    .listingSlug {
        font-size: 15px;
        font-weight: 600;
        line-height: 1;
        color: #28a745 !important;
        transition: 0.3s all ease-in-out;
    }

    .listingSlug:hover {
        color: #92d509 !important;
    }
</style>

<div class="skd-user-listings">
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Status</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($listings)) : ?>
                <tr>
                    <td colspan="4">No listings found.</td>
                </tr>
            <?php endif; ?>
            <?php foreach ($listings as $listing) : ?>
                <tr>
                    <td>
                        <a class="listingSlug" href="<?php echo esc_url(site_url('/single-detail/' . $listing->slug)); ?>"><?php echo esc_html($listing->listing_title); ?></a>
                    </td>
                    <td><?php echo ucfirst($listing->listing_status); ?></td>
                    <td><?php echo date('d-m-Y', strtotime($listing->created_at)); ?></td>
                    <td>
                        <a href="<?php echo site_url('add-new-listing/edit/' . $listing->id);
                                    ?>" class="skd-add-listing-btn">Edit</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>