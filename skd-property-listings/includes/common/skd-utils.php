<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function skd_encrypt_id($id)
{
    $key = 'skd0o0o1'; // Change this to a random string
    return urlencode(base64_encode($id . '|' . hash_hmac('sha256', $id, $key)));
}

function skd_decrypt_id($encrypted_id)
{
    $key = 'skd0o0o1'; // Same key as above
    $decoded = base64_decode(urldecode($encrypted_id));

    if ($decoded) {
        list($id, $hash) = explode('|', $decoded);
        if ($hash === hash_hmac('sha256', $id, $key)) {
            return $id; // Return the original ID if hash matches
        }
    }

    return false; // Invalid decryption
}
