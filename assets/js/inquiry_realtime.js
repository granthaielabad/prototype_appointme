/**
 * Real-time Inquiry Updates
 * Polls for new inquiries every 5 seconds and updates the table dynamically
 */

let lastTableState = ''; // Hash of last known state to detect changes
let pollInterval;

function initRealtimeUpdates() {
    // Initialize the last known state
    updateTableStateHash();

    // Start polling for new inquiries every 5 seconds
    pollInterval = setInterval(fetchAndUpdateInquiries, 5000);
}

function getTableStateHash() {
    // Create a hash of all current inquiry IDs in the table
    const rows = Array.from(document.querySelectorAll('tbody tr[data-inquiry]')).map(row => {
        try {
            const data = JSON.parse(row.dataset.inquiry);
            return data.inquiry_id;
        } catch {
            return null;
        }
    }).filter(id => id !== null);

    return JSON.stringify(rows);
}

function updateTableStateHash() {
    lastTableState = getTableStateHash();
}

function fetchAndUpdateInquiries() {
    const currentFilter = new URLSearchParams(window.location.search).get('filter') || 'all';

    fetch(`/admin/inquiries/fetch?filter=${currentFilter}`)
        .then(response => response.json())
        .then(data => {
            if (!data.success || !data.inquiries) {
                console.error('Failed to fetch inquiries:', data.error);
                return;
            }

            const inquiries = data.inquiries;
            const tbody = document.querySelector('tbody');
            const serverIds = inquiries.map(inq => inq.inquiry_id);
            const currentRows = Array.from(tbody.querySelectorAll('tr[data-inquiry]')).map(row => {
                try {
                    return JSON.parse(row.dataset.inquiry);
                } catch {
                    return null;
                }
            }).filter(d => d !== null);

            const currentRowIds = currentRows.map(d => d.inquiry_id);

            // Add new inquiries (in server data but not in DOM)
            inquiries.forEach(inquiry => {
                if (!currentRowIds.includes(inquiry.inquiry_id)) {
                    addInquiryRow(inquiry, tbody);
                }
            });

            // Remove inquiries not in server (deleted or filtered out)
            currentRows.forEach(rowData => {
                if (!serverIds.includes(rowData.inquiry_id)) {
                    const row = Array.from(tbody.querySelectorAll('tr[data-inquiry]')).find(tr => {
                        try {
                            return JSON.parse(tr.dataset.inquiry).inquiry_id === rowData.inquiry_id;
                        } catch {
                            return false;
                        }
                    });
                    if (row) row.remove();
                }
            });

            // Update read status for existing inquiries
            inquiries.forEach(inquiry => {
                const row = Array.from(tbody.querySelectorAll('tr[data-inquiry]')).find(tr => {
                    try {
                        return JSON.parse(tr.dataset.inquiry).inquiry_id === inquiry.inquiry_id;
                    } catch {
                        return false;
                    }
                });

                if (row) {
                    try {
                        const rowData = JSON.parse(row.dataset.inquiry);
                        if (rowData.is_read !== inquiry.is_read) {
                            updateInquiryRow(inquiry, row);
                        }
                    } catch (e) {
                        console.error('Error comparing read status:', e);
                    }
                }
            });

            // Show/hide empty state
            updateEmptyState();
        })
        .catch(err => console.error('Error polling inquiries:', err));
}

function addInquiryRow(inquiry, tbody) {
    // Check if row already exists to prevent duplicates
    const existingRow = Array.from(tbody.querySelectorAll('tr[data-inquiry]')).find(row => {
        try {
            const data = JSON.parse(row.dataset.inquiry);
            return data.inquiry_id === inquiry.inquiry_id;
        } catch {
            return false;
        }
    });

    if (existingRow) {
        console.warn('Inquiry already exists, skipping duplicate:', inquiry.inquiry_id);
        return;
    }

    const rows = tbody.querySelectorAll('tr[data-inquiry]');
    const rowCount = rows.length + 1;
    
    const row = document.createElement('tr');
    row.setAttribute('data-inquiry', JSON.stringify(inquiry));
    
    // Add unread styling if new
    if (!inquiry.is_read) {
        row.classList.add('table-light', 'fw-bold');
    }

    const dateFormatted = new Date(inquiry.created_at).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });

    row.innerHTML = `
        <td>${rowCount}</td>
        <td>${escapeHtml(inquiry.full_name)}${!inquiry.is_read ? ' <span class="badge bg-warning text-dark ms-2">NEW</span>' : ''}</td>
        <td>${escapeHtml(inquiry.email)}</td>
        <td>${dateFormatted}</td>
        <td>
            <button class="text-purple me-2 openInquiryModal" style="border:0;background:none;">
                <i class="bi bi-eye"></i>
            </button>
            <a href="/admin/inquiries/delete?id=${inquiry.inquiry_id}" 
               class="text-purple"
               onclick="return confirm('Delete this inquiry?')">
                <i class="bi bi-trash"></i>
            </a>
        </td>
    `;

    // Insert at top for new inquiries
    if (tbody.firstElementChild && !tbody.firstElementChild.classList.contains('text-center')) {
        tbody.insertBefore(row, tbody.firstElementChild);
    } else {
        tbody.appendChild(row);
    }

    // Reattach event listeners to new button
    row.querySelector('.openInquiryModal').addEventListener('click', openInquiryModal);
}

function updateInquiryRow(inquiry, row) {
    if (!row) return;

    row.setAttribute('data-inquiry', JSON.stringify(inquiry));
    
    // Update read status styling
    if (inquiry.is_read) {
        row.classList.remove('table-light', 'fw-bold');
        const badge = row.querySelector('.badge');
        if (badge) badge.remove();
    } else {
        row.classList.add('table-light', 'fw-bold');
        if (!row.querySelector('.badge')) {
            const nameCell = row.querySelector('td:nth-child(2)');
            nameCell.innerHTML += ' <span class="badge bg-warning text-dark ms-2">NEW</span>';
        }
    }
}

function updateEmptyState() {
    const tbody = document.querySelector('tbody');
    const rows = tbody.querySelectorAll('tr[data-inquiry]');
    let emptyRow = tbody.querySelector('tr.text-center');

    if (rows.length === 0) {
        if (!emptyRow) {
            emptyRow = document.createElement('tr');
            emptyRow.innerHTML = '<td colspan="5" class="text-center text-muted py-4">No inquiries found.</td>';
            tbody.appendChild(emptyRow);
        }
    } else {
        if (emptyRow) {
            emptyRow.remove();
        }
    }
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

// Stop polling when user leaves page
window.addEventListener('beforeunload', () => {
    if (pollInterval) {
        clearInterval(pollInterval);
    }
});

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', initRealtimeUpdates);
