<?php
$pageTitle = "Inquiry";
$activePage = "inquiry";
?>

<div class="admin-section">

    <!-- TITLE -->
    <div class="mb-4">
        <h6 class="section-title">Customer Inquiries</h6>
        <small class="section-subtitle text-muted">Manage customer questions and requests</small>
    </div>

    <!-- FILTER + DROPDOWN -->
    <div class="d-flex justify-content-end mb-3">
        <div class="dropdown">
            <button class="btn dropdown-toggle filter-btn" type="button" data-bs-toggle="dropdown">
                <?php
                    $filterLabels = [
                        'all' => 'All Inquiry',
                        'read' => 'Read Inquiry',
                        'unread' => 'Unread Inquiry'
                    ];
                    echo $filterLabels[$currentFilter] ?? 'All Inquiry';
                ?>
            </button>
            <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="?filter=all">All Inquiry</a></li>
                <li><a class="dropdown-item" href="?filter=read">Read Inquiry</a></li>
                <li><a class="dropdown-item" href="?filter=unread">Unread Inquiry</a></li>
            </ul>
        </div>
    </div>

    <!-- CONTENT CARD -->
    <div class="card content-card p-3">

        <h6 class="mb-3 fw-semibold">Read Enquiry:</h6>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Enquiry Date</th>
                        <th style="width: 90px;">Action</th>
                    </tr>
                </thead>
                <tbody>

                    <?php if (empty($inquiries)): ?>
                        <tr data-empty-state="true">
                            <td colspan="5" class="text-center text-muted py-4">No inquiries found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($inquiries as $i => $inq): ?>
                            <tr data-inquiry='<?= json_encode($inq) ?>' class="<?= !$inq['is_read'] ? 'table-light fw-bold' : '' ?>">
                                <td><?= $i + 1 ?></td>
                                <td><?= htmlspecialchars($inq['full_name']) ?><?= !$inq['is_read'] ? ' <span class="badge bg-warning text-dark ms-2">NEW</span>' : '' ?></td>
                                <td><?= htmlspecialchars($inq['email']) ?></td>
                                <td><?= date("M d, Y", strtotime($inq['created_at'])) ?></td>
                                <td>
                                    <button class="text-purple me-2 openInquiryModal" style="border:0;background:none;">
                                        <i class="bi bi-eye"></i>
                                    </button>

                                    <a href="/admin/inquiries/delete?id=<?= $inq['inquiry_id'] ?>" 
                                       class="text-purple"
                                       onclick="return confirm('Delete this inquiry?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>

                </tbody>
            </table>
        </div>

    </div>

</div>


<!-- INQUIRY DETAILS MODAL -->
<div class="custom-modal" id="inquiryDetailsModal">
    <div class="custom-modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h5>Inquiry Details</h5>
            <button class="close-modal">&times;</button>
        </div>

        <div class="modal-body mt-2">

            <p><strong>Name:</strong> <span id="inq_name"></span></p>
            <p><strong>Mobile Number:</strong> <span id="inq_phone"></span></p>
            <p><strong>Email Address:</strong> <span id="inq_email"></span></p>
            <p><strong>Inquiry Date:</strong> <span id="inq_date"></span></p>

            <p class="mt-3"><strong>Message:</strong></p>
            <p id="inq_message" class="p-2 bg-light rounded"></p>

        </div>
    </div>
</div>

<script src="/assets/js/inquiry_modals.js"></script>
<script src="/assets/js/inquiry_realtime.js"></script>
