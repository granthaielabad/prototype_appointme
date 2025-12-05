<?php
$pageTitle = "Archive";
$activePage = "archives";
?>

<div class="admin-section">

    <!-- HEADER -->
    <div class="mb-4">
        <h6 class="section-title">Archive</h6>
        <small class="section-subtitle text-muted">
            View, restore, or permanently remove archived records.
        </small>
    </div>

        <!-- FILTER + DROPDOWN -->
    <div class="d-flex justify-content-end mb-3">
        <div class="dropdown">
            <button class="btn dropdown-toggle filter-btn" type="button" data-bs-toggle="dropdown">
                <?php
                    $filterLabels = [
                        'all' => 'All Archive',
                        'service' => 'Service',
                        'appointment' => 'Appointment',
                        'inquiry' => 'Inquiry'
                    ];
                    echo $filterLabels[$currentFilter] ?? 'All Inquiry';
                ?>
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="?filter=all">All Archive</a></li>
                <li><a class="dropdown-item" href="?filter=service">Service</a></li>
                <li><a class="dropdown-item" href="?filter=appointment">Appointment</a></li>
                <li><a class="dropdown-item" href="?filter=inquiry">Inquiry</a></li>
            </ul>
        </div>
    </div>

    <!-- ARCHIVE ITEMS CONTAINER -->
    <div class="archive-items-container">

        <?php if (empty($items)): ?>
            <div class="alert alert-light text-center py-5">
                <p class="text-muted mb-0">No archived items found.</p>
            </div>
        <?php else: ?>
            <?php foreach ($items as $item): ?>
                <div class="card archive-item-card mb-1 p-4 border-0 shadow-sm"
                     data-archive='<?= json_encode($item, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT) ?>'>
                    <div class="d-flex justify-content-between align-items-start">
                        <!-- LEFT SIDE: Item Info -->
                        <div class="flex-grow-1">
                            <h6 class="fw-bold mb-1">
                                <?= htmlspecialchars($item['item_name']) ?>
                            </h6>
                            <small class="text-muted">
                                Archived: <?= date('m/d/Y', strtotime($item['archived_at'])) ?>
                            </small>
                        </div>

                        <!-- RIGHT SIDE: Type Badge & Actions -->
                        <div class="d-flex align-items-center gap-5">
                            <!-- Type Badge -->
                            <span class="badge bg-light text-dark">
                                <?= ucfirst(htmlspecialchars($item['item_type'])) ?>
                            </span>

                            <!-- Actions -->
                            <div class="d-flex gap-4">
                                <!-- View Details -->
                                <a href="javascript:void(0)" 
                                   class="text-purple openArchiveDetailsModal"
                                   title="View Details">
                                    <i class="bi bi-eye"></i>
                                </a>

                                <!-- Restore -->
                                <button class="text-purple btn btn-link p-0 openRestoreWarningModal"
                                   data-type="<?= $item['item_type'] ?>"
                                   data-id="<?= $item['item_id'] ?>"
                                   data-name="<?= htmlspecialchars($item['item_name']) ?>"
                                   data-filter="<?= htmlspecialchars($currentFilter) ?>"
                                   title="Restore Item">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</div>

<!-- ============================================ -->
<!-- SERVICE VIEW MODAL -->
<!-- ============================================ -->
<div class="custom-modal" id="archiveServiceViewModal">
    <div class="custom-modal-content" style="max-width: 320px;">
        <!-- Close Button -->
        <button class="close-modal" style="position: absolute; top: 15px; right: 15px; font-size: 24px; border: none; background: none; cursor: pointer;">&times;</button>

        <!-- Service Name -->
        <h4 style="font-weight: 600; margin-bottom: 1rem; margin-top: 0;" id="archive_service_name">Hair Cut</h4>

        <!-- Description -->
        <p id="archive_service_description" style="color: #666; font-size: 0.9rem; margin-bottom: 1.5rem; line-height: 1.5;"></p>

        <!-- Price -->
        <h3 style="color: #CD9FFE; font-weight: 600; margin-bottom: 1rem; font-size: 1.5rem;" id="archive_service_price">PHP 160</h3>

        <!-- Category Badge -->
        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem;">
            <span class="badge" id="archive_service_category_badge" style="background-color: #e9ecef; color: #333; padding: 0.5rem 1rem; font-size: 0.85rem; border-radius: 20px;">Hair</span>
        </div>

        <!-- Archived Date -->
        <div style="padding-top: 1rem; border-top: 1px solid #e0e0e0;">
            <small style="color: #999;"><strong>Archived Date:</strong> <span id="archive_service_archived_date"></span></small>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- APPOINTMENT VIEW MODAL -->
<!-- ============================================ -->
<div class="custom-modal" id="archiveAppointmentViewModal">
    <div class="custom-modal-content">
        <!-- Close Button -->
        <button class="close-modal" style="position: absolute; top: 15px; right: 15px; font-size: 24px; border: none; background: none; cursor: pointer;">&times;</button>

        <!-- Status Badge -->
        <div class="mb-3">
            <span class="badge" id="archive_appointment_status_badge" style="background-color: #28a745; font-size: 0.9rem; padding: 0.5rem 1rem;">Status: Pending</span>
        </div>

        <!-- Appointment Number -->
        <div class="mb-4">
            <p style="font-size: 0.9rem; color: #666; margin-bottom: 0.5rem;">Appointment Number:</p>
            <h2 style="font-size: 3rem; font-weight: bold; margin: 0;" id="archive_appointment_id">#01010</h2>
        </div>

        <!-- Booking Dates -->
        <div class="mb-4">
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 0.5rem;">
                <i class="bi bi-calendar2" style="color: #999;"></i>
                <span style="color: #999; font-size: 0.9rem;" id="archive_appointment_booked_on"></span>
            </div>
            <div style="display: flex; align-items: center; gap: 1rem;">
                <i class="bi bi-calendar2-check" style="color: #999;"></i>
                <span style="color: #999; font-size: 0.9rem;" id="archive_appointment_date_time"></span>
            </div>
        </div>

        <!-- Divider -->
        <hr style="margin: 2rem 0; border: none; border-top: 1px solid #e0e0e0;">

        <!-- Booked Services -->
        <div class="mb-4">
            <p style="font-weight: 600; margin-bottom: 1rem;">Booked Services:</p>
            <div id="archive_appointment_services" style="display: flex; gap: 1rem; flex-wrap: wrap;"></div>
        </div>

        <!-- Note -->
        <div class="mb-4">
            <p style="font-weight: 600; margin-bottom: 0.5rem;">Note:</p>
            <p id="archive_appointment_note" style="color: #666; font-size: 0.9rem; margin: 0;"></p>
        </div>

        <!-- Archived Date -->
        <div style="margin-top: 2rem; padding-top: 1rem; border-top: 1px solid #e0e0e0;">
            <small style="color: #999;"><strong>Archived Date:</strong> <span id="archive_appointment_archived_date"></span></small>
        </div>
    </div>
</div>

<!-- ============================================ -->
<!-- INQUIRY VIEW MODAL -->
<!-- ============================================ -->
<div class="custom-modal" id="archiveInquiryViewModal">
    <div class="custom-modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h5>Inquiry Details</h5>
            <button class="close-modal">&times;</button>
        </div>

        <div class="modal-body mt-2">
            <p><strong>Name:</strong> <span id="archive_inq_name"></span></p>
            <p><strong>Mobile Number:</strong> <span id="archive_inq_phone"></span></p>
            <p><strong>Email Address:</strong> <span id="archive_inq_email"></span></p>
            <p><strong>Inquiry Date:</strong> <span id="archive_inq_date"></span></p>
            <p class="mt-3"><strong>Message:</strong></p>
            <p id="archive_inq_message" class="p-2 bg-light rounded"></p>
            <p><strong>Archived Date:</strong> <span id="archive_inq_archived_date"></span></p>
        </div>
    </div>
</div>

<!-- Restore Warning Modal -->
<div class="custom-modal" id="restoreWarningModal" style="display: none;">
    <div class="custom-modal-content" style="max-width: 400px; text-align: center;">
        <!-- Restore Icon -->
        <div style="margin-bottom: 20px;">
            <i class="bi bi-arrow-counterclockwise" style="font-size: 60px; color: #28a745;"></i>
        </div>

        <!-- Warning Text -->
        <p style="font-size: 16px; margin-bottom: 20px;">
            Are you sure you want to <span style="color: #28a745; font-weight: bold;">restore</span> <span id="restoreItemName">*Name*</span> from the system?
        </p>

        <!-- Action Buttons -->
        <div style="display: flex; gap: 12px; margin-top: 20px;">
            <button class="btn btn-success w-50" id="confirmRestoreBtn" style="background:#28a745; border:none; color: white;">
                Restore
            </button>
            <button class="btn btn-outline-secondary w-50" id="cancelRestoreBtn" style="border: 1px solid #ccc;">
                No
            </button>
        </div>
    </div>
</div>

<script src="/assets/js/archive_modals.js"></script>
<script src="/assets/js/restore_warning_modal.js"></script>
