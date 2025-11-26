<?php

/**
 * Template for User Messages Interface
 * Professional messaging system for interiAssist
 */

if (!defined('ABSPATH')) {
    exit;
}

// Check if user is logged in
if (!is_user_logged_in()) {
    echo '<p>Please <a href="' . wp_login_url() . '">login</a> to access messages.</p>';
    return;
}

global $wpdb;
$current_user = wp_get_current_user();
$user_id = $current_user->ID;

// Get conversations for current user
$messages_table = $wpdb->prefix . 'skd_pl_messages';
$conversations = $wpdb->get_results($wpdb->prepare(
    "SELECT 
        CASE 
            WHEN sender_id = %d THEN recipient_id 
            ELSE sender_id 
        END as contact_id,
        MAX(created_at) as last_message_time,
        COUNT(CASE WHEN recipient_id = %d AND is_read = 0 THEN 1 END) as unread_count
     FROM $messages_table 
     WHERE sender_id = %d OR recipient_id = %d 
     GROUP BY contact_id 
     ORDER BY last_message_time DESC",
    $user_id,
    $user_id,
    $user_id,
    $user_id
));

// Get contact details for conversations
$contacts = [];
if (!empty($conversations)) {
    $contact_ids = array_column($conversations, 'contact_id');
    $placeholders = implode(',', array_fill(0, count($contact_ids), '%d'));

    $contact_details = $wpdb->get_results($wpdb->prepare(
        "SELECT u.ID, u.display_name, l.listing_title, l.skd_logo, l.user_role
         FROM {$wpdb->users} u
         LEFT JOIN {$wpdb->prefix}skd_pl_listings l ON u.ID = l.user_id
         WHERE u.ID IN ($placeholders)",
        ...$contact_ids
    ));

    foreach ($contact_details as $contact) {
        $contacts[$contact->ID] = $contact;
    }
}
?>

<div class="skd-messages-wrapper">
    <div class="skd-messages-layout">
        <!-- Conversations Sidebar -->
        <div class="skd-conversations-sidebar">
            <div class="skd-conversations-header">
                <h3>Conversations</h3>
                <button class="skd-btn skd-btn-primary skd-btn-small" onclick="openNewConversationModal()">
                    <span class="dashicons dashicons-plus"></span>
                    New
                </button>
            </div>

            <div class="skd-conversations-search">
                <input type="text" id="conversationSearch" placeholder="Search conversations..." onkeyup="filterConversations()">
                <span class="dashicons dashicons-search"></span>
            </div>

            <div class="skd-conversations-list" id="conversationsList">
                <?php if (empty($conversations)): ?>
                    <div class="skd-empty-conversations">
                        <span class="dashicons dashicons-email"></span>
                        <p>No conversations yet</p>
                        <button class="skd-btn skd-btn-outline skd-btn-small" onclick="openNewConversationModal()">
                            Start a conversation
                        </button>
                    </div>
                <?php else: ?>
                    <?php foreach ($conversations as $conv): ?>
                        <?php
                        $contact = $contacts[$conv->contact_id] ?? null;
                        if (!$contact) continue;

                        // Get last message
                        $last_message = $wpdb->get_row($wpdb->prepare(
                            "SELECT * FROM $messages_table 
                             WHERE (sender_id = %d AND recipient_id = %d) 
                                OR (sender_id = %d AND recipient_id = %d) 
                             ORDER BY created_at DESC LIMIT 1",
                            $user_id,
                            $conv->contact_id,
                            $conv->contact_id,
                            $user_id
                        ));
                        ?>
                        <div class="skd-conversation-item <?php echo $conv->unread_count > 0 ? 'has-unread' : ''; ?>"
                            data-contact-id="<?php echo $conv->contact_id; ?>"
                            onclick="openConversation(<?php echo $conv->contact_id; ?>)">

                            <div class="skd-conversation-avatar">
                                <?php if ($contact->skd_logo): ?>
                                    <img src="<?php echo esc_url($contact->skd_logo); ?>" alt="<?php echo esc_attr($contact->display_name); ?>">
                                <?php else: ?>
                                    <div class="skd-avatar-placeholder">
                                        <?php echo strtoupper(substr($contact->display_name, 0, 2)); ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ($conv->unread_count > 0): ?>
                                    <span class="skd-unread-badge"><?php echo $conv->unread_count; ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="skd-conversation-info">
                                <div class="skd-conversation-header">
                                    <h4><?php echo esc_html($contact->listing_title ?: $contact->display_name); ?></h4>
                                    <span class="skd-conversation-time">
                                        <?php echo human_time_diff(strtotime($conv->last_message_time)); ?> ago
                                    </span>
                                </div>

                                <?php if ($contact->user_role): ?>
                                    <div class="skd-contact-role">
                                        <?php
                                        echo $contact->user_role === 'studio' ? 'Design Studio' : ($contact->user_role === 'vda' ? 'Virtual Design Assistant' : 'Professional');
                                        ?>
                                    </div>
                                <?php endif; ?>

                                <?php if ($last_message): ?>
                                    <div class="skd-conversation-preview">
                                        <span class="skd-message-sender">
                                            <?php echo $last_message->sender_id === $user_id ? 'You' : 'Them'; ?>:
                                        </span>
                                        <?php echo esc_html(wp_trim_words($last_message->message_text, 8)); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Messages Content Area -->
        <div class="skd-messages-content">
            <div class="skd-no-conversation-selected" id="noConversationSelected">
                <div class="skd-empty-state">
                    <span class="dashicons dashicons-email"></span>
                    <h3>Select a conversation</h3>
                    <p>Choose a conversation from the sidebar to view messages</p>
                    <button class="skd-btn skd-btn-primary" onclick="openNewConversationModal()">
                        <span class="dashicons dashicons-plus"></span>
                        Start New Conversation
                    </button>
                </div>
            </div>

            <div class="skd-conversation-view" id="conversationView" style="display: none;">
                <!-- Conversation Header -->
                <div class="skd-conversation-header-bar">
                    <div class="skd-contact-info">
                        <div class="skd-contact-avatar" id="contactAvatar"></div>
                        <div class="skd-contact-details">
                            <h3 id="contactName"></h3>
                            <span id="contactRole"></span>
                        </div>
                    </div>

                    <div class="skd-conversation-actions">
                        <button class="skd-btn skd-btn-outline skd-btn-small" onclick="viewContactProfile()" id="viewProfileBtn">
                            <span class="dashicons dashicons-admin-users"></span>
                            View Profile
                        </button>
                        <button class="skd-btn skd-btn-secondary skd-btn-small" onclick="archiveConversation()" title="Archive">
                            <span class="dashicons dashicons-archive"></span>
                        </button>
                    </div>
                </div>

                <!-- Messages Area -->
                <div class="skd-messages-area" id="messagesArea">
                    <!-- Messages will be loaded here -->
                </div>

                <!-- Message Input -->
                <div class="skd-message-input-area">
                    <form id="messageForm" onsubmit="sendMessage(event)">
                        <div class="skd-message-input-container">
                            <textarea id="messageInput" placeholder="Type your message..." rows="3" required></textarea>
                            <div class="skd-message-actions">
                                <button type="button" class="skd-btn skd-btn-secondary skd-btn-small" onclick="attachFile()" title="Attach File">
                                    <span class="dashicons dashicons-paperclip"></span>
                                </button>
                                <button type="submit" class="skd-btn skd-btn-primary">
                                    <span class="dashicons dashicons-paperplane"></span>
                                    Send
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Conversation Modal -->
<div id="newConversationModal" class="skd-modal" style="display: none;">
    <div class="skd-modal-content">
        <div class="skd-modal-header">
            <h3>Start New Conversation</h3>
            <button class="skd-modal-close" onclick="closeNewConversationModal()">&times;</button>
        </div>
        <div class="skd-modal-body">
            <div class="skd-contact-search">
                <input type="text" id="contactSearch" placeholder="Search for professionals..." onkeyup="searchContacts()">
                <div class="skd-search-results" id="searchResults">
                    <!-- Search results will appear here -->
                </div>
            </div>

            <form id="newConversationForm" style="display: none;">
                <input type="hidden" id="selectedContactId" value="">
                <div class="skd-selected-contact" id="selectedContact">
                    <!-- Selected contact will appear here -->
                </div>

                <div class="skd-form-group">
                    <label>Subject</label>
                    <input type="text" id="conversationSubject" placeholder="Enter subject..." required>
                </div>

                <div class="skd-form-group">
                    <label>Message</label>
                    <textarea id="conversationMessage" placeholder="Type your message..." rows="6" required></textarea>
                </div>

                <div class="skd-form-actions">
                    <button type="button" class="skd-btn skd-btn-secondary" onclick="closeNewConversationModal()">Cancel</button>
                    <button type="submit" class="skd-btn skd-btn-primary">Send Message</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let currentContactId = null;
    let messagePollingInterval = null;

    document.addEventListener('DOMContentLoaded', function() {
        // Auto-refresh messages every 30 seconds
        messagePollingInterval = setInterval(refreshCurrentConversation, 30000);
    });

    function openConversation(contactId) {
        currentContactId = contactId;

        // Update UI
        document.getElementById('noConversationSelected').style.display = 'none';
        document.getElementById('conversationView').style.display = 'flex';

        // Mark conversation as active
        document.querySelectorAll('.skd-conversation-item').forEach(item => {
            item.classList.remove('active');
        });
        document.querySelector(`[data-contact-id="${contactId}"]`).classList.add('active');

        // Load contact info and messages
        loadContactInfo(contactId);
        loadMessages(contactId);
        markMessagesAsRead(contactId);
    }

    function loadContactInfo(contactId) {
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=skd_get_contact_info&contact_id=${contactId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const contact = data.data;

                    document.getElementById('contactName').textContent = contact.name;
                    document.getElementById('contactRole').textContent = contact.role;

                    const avatar = document.getElementById('contactAvatar');
                    if (contact.avatar) {
                        avatar.innerHTML = `<img src="${contact.avatar}" alt="${contact.name}">`;
                    } else {
                        avatar.innerHTML = `<div class="skd-avatar-placeholder">${contact.name.charAt(0).toUpperCase()}</div>`;
                    }

                    // Set up profile view button
                    document.getElementById('viewProfileBtn').onclick = () => {
                        if (contact.profile_url) {
                            window.open(contact.profile_url, '_blank');
                        }
                    };
                }
            });
    }

    function loadMessages(contactId) {
        const messagesArea = document.getElementById('messagesArea');
        messagesArea.innerHTML = '<div class="skd-loading-messages">Loading messages...</div>';

        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=skd_get_conversation_messages&contact_id=${contactId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    messagesArea.innerHTML = data.data.html;
                    scrollToBottom();
                } else {
                    messagesArea.innerHTML = '<div class="skd-error">Failed to load messages.</div>';
                }
            });
    }

    function sendMessage(event) {
        event.preventDefault();

        const messageText = document.getElementById('messageInput').value.trim();
        if (!messageText || !currentContactId) return;

        const formData = new FormData();
        formData.append('action', 'skd_send_message');
        formData.append('recipient_id', currentContactId);
        formData.append('message_text', messageText);

        // Disable send button
        const sendBtn = event.target.querySelector('button[type="submit"]');
        sendBtn.disabled = true;
        sendBtn.innerHTML = '<span class="dashicons dashicons-update spin"></span> Sending...';

        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('messageInput').value = '';
                    loadMessages(currentContactId);
                    refreshConversationsList();
                } else {
                    alert('Error sending message: ' + (data.data || 'Please try again.'));
                }
            })
            .finally(() => {
                sendBtn.disabled = false;
                sendBtn.innerHTML = '<span class="dashicons dashicons-paperplane"></span> Send';
            });
    }

    function markMessagesAsRead(contactId) {
        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=skd_mark_messages_read&contact_id=${contactId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update unread count in sidebar
                    const conversationItem = document.querySelector(`[data-contact-id="${contactId}"]`);
                    if (conversationItem) {
                        conversationItem.classList.remove('has-unread');
                        const unreadBadge = conversationItem.querySelector('.skd-unread-badge');
                        if (unreadBadge) {
                            unreadBadge.remove();
                        }
                    }
                }
            });
    }

    function scrollToBottom() {
        const messagesArea = document.getElementById('messagesArea');
        messagesArea.scrollTop = messagesArea.scrollHeight;
    }

    function refreshCurrentConversation() {
        if (currentContactId) {
            loadMessages(currentContactId);
        }
        refreshConversationsList();
    }

    function refreshConversationsList() {
        // Reload the entire conversations list
        location.reload(); // Simple approach - could be optimized with AJAX
    }

    function openNewConversationModal() {
        document.getElementById('newConversationModal').style.display = 'block';
        document.getElementById('newConversationForm').style.display = 'none';
        document.getElementById('contactSearch').value = '';
        document.getElementById('searchResults').innerHTML = '';
    }

    function closeNewConversationModal() {
        document.getElementById('newConversationModal').style.display = 'none';
    }

    function searchContacts() {
        const searchTerm = document.getElementById('contactSearch').value.trim();

        if (searchTerm.length < 2) {
            document.getElementById('searchResults').innerHTML = '';
            return;
        }

        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=skd_search_contacts&search=${encodeURIComponent(searchTerm)}`
            })
            .then(response => response.json())
            .then(data => {
                const resultsContainer = document.getElementById('searchResults');

                if (data.success && data.data.length > 0) {
                    let resultsHtml = '';
                    data.data.forEach(contact => {
                        resultsHtml += `
                    <div class="skd-search-result-item" onclick="selectContact(${contact.id}, '${contact.name}', '${contact.role}', '${contact.avatar}')">
                        <div class="skd-result-avatar">
                            ${contact.avatar ? 
                                `<img src="${contact.avatar}" alt="${contact.name}">` : 
                                `<div class="skd-avatar-placeholder">${contact.name.charAt(0).toUpperCase()}</div>`
                            }
                        </div>
                        <div class="skd-result-info">
                            <h4>${contact.name}</h4>
                            <span>${contact.role}</span>
                        </div>
                    </div>
                `;
                    });
                    resultsContainer.innerHTML = resultsHtml;
                } else {
                    resultsContainer.innerHTML = '<div class="skd-no-results">No contacts found</div>';
                }
            });
    }

    function selectContact(contactId, contactName, contactRole, contactAvatar) {
        document.getElementById('selectedContactId').value = contactId;

        const selectedContactHtml = `
        <div class="skd-selected-contact-item">
            <div class="skd-contact-avatar">
                ${contactAvatar ? 
                    `<img src="${contactAvatar}" alt="${contactName}">` : 
                    `<div class="skd-avatar-placeholder">${contactName.charAt(0).toUpperCase()}</div>`
                }
            </div>
            <div class="skd-contact-info">
                <h4>${contactName}</h4>
                <span>${contactRole}</span>
            </div>
        </div>
    `;

        document.getElementById('selectedContact').innerHTML = selectedContactHtml;
        document.getElementById('searchResults').innerHTML = '';
        document.getElementById('newConversationForm').style.display = 'block';
    }

    // New conversation form submission
    document.getElementById('newConversationForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData();
        formData.append('action', 'skd_start_conversation');
        formData.append('recipient_id', document.getElementById('selectedContactId').value);
        formData.append('subject', document.getElementById('conversationSubject').value);
        formData.append('message_text', document.getElementById('conversationMessage').value);

        fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeNewConversationModal();
                    location.reload(); // Reload to show new conversation
                } else {
                    alert('Error starting conversation: ' + (data.data || 'Please try again.'));
                }
            });
    });

    function filterConversations() {
        const searchTerm = document.getElementById('conversationSearch').value.toLowerCase();
        const conversations = document.querySelectorAll('.skd-conversation-item');

        conversations.forEach(conversation => {
            const name = conversation.querySelector('h4').textContent.toLowerCase();
            const preview = conversation.querySelector('.skd-conversation-preview');
            const previewText = preview ? preview.textContent.toLowerCase() : '';

            if (name.includes(searchTerm) || previewText.includes(searchTerm)) {
                conversation.style.display = 'flex';
            } else {
                conversation.style.display = 'none';
            }
        });
    }

    function archiveConversation() {
        if (confirm('Are you sure you want to archive this conversation?')) {
            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=skd_archive_conversation&contact_id=${currentContactId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error archiving conversation.');
                    }
                });
        }
    }

    function attachFile() {
        // Create file input dynamically
        const fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.accept = 'image/*,.pdf,.doc,.docx';
        fileInput.onchange = function(e) {
            const file = e.target.files[0];
            if (file) {
                // Handle file attachment (implementation would depend on requirements)
                console.log('File selected:', file.name);
            }
        };
        fileInput.click();
    }

    // Keyboard shortcuts
    document.getElementById('messageInput').addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 'Enter') {
            document.getElementById('messageForm').dispatchEvent(new Event('submit'));
        }
    });

    // Cleanup on page unload
    window.addEventListener('beforeunload', function() {
        if (messagePollingInterval) {
            clearInterval(messagePollingInterval);
        }
    });
</script>