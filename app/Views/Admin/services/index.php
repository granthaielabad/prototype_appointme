<?php
$pageTitle = "Services";
$activePage = "services";

// ENUM categories from DB
$categories = [
    "Straightening (By Length Level)",
    "Color Classic",
    "Hair Treatment",
    "Haircut",
    "Perming",
    "Other Services",
];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h6 class="section-title">Services Management</h6>
        <small class="section-subtitle text-muted">Manage your salon services and pricing</small>
    </div>

    <button class="btn add-service-btn d-flex align-items-center" id="openAddModal">
        <i class="bi bi-scissors me-2"></i> Add Service
    </button>
</div>

<!-- SERVICE CARDS -->
<div class="row gy-4">
    <?php foreach ($services as $s): ?>
        <div class="col-lg-4 col-md-6">
            <div class="service-card p-3 rounded"
                 data-service='<?= json_encode(
                     $s,
                     JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT,
                 ) ?>'>

                <div class="d-flex justify-content-between align-items-start">
                    <h5 class="fw-semibold mb-1"><?= htmlspecialchars($s["service_name"]) ?></h5>
                    <span class="badge bg-light text-dark"><?= htmlspecialchars(
                        $s["category"],
                    ) ?></span>
                </div>

                <p class="text-muted mb-2"><?= htmlspecialchars($s["description"]) ?></p>

                <h5 class="service-price">PHP <?= number_format($s["price"]) ?></h5>

                <div class="mt-3 d-flex gap-2">

                    <button 
                        class="btn btn-outline-secondary btn-sm px-3 openEditModal" 
                        data-id="<?= $s["service_id"] ?>">
                        Edit
                    </button>

                    <button 
                        class="btn btn-outline-danger btn-sm px-3 openDeleteModal"
                        data-id="<?= $s["service_id"] ?>"
                        data-name="<?= htmlspecialchars($s["service_name"]) ?>">
                        Delete
                    </button>

                </div>

            </div>
        </div>
    <?php endforeach; ?>
</div>


<!-- ADD SERVICE MODAL -->
<div class="custom-modal" id="addServiceModal">
    <div class="custom-modal-content">
        <div class="modal-header">
            <h5>Add New Service</h5>
            <button class="close-modal">&times;</button>
        </div>

        <p class="text-muted mb-3">Create a new service for your salon.</p>

        <form action="/admin/services/store" method="POST">

            <label class="modal-label">Name</label>
            <input type="text" name="service_name" class="modal-input" required>

            <label class="modal-label mt-2">Price</label>
            <input type="number" name="price" class="modal-input" required>

            <label class="modal-label mt-2">Category</label>
            <select name="category" class="modal-input" required>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat ?>"><?= $cat ?></option>
                <?php endforeach; ?>
            </select>

            <label class="modal-label mt-2">Description</label>
            <textarea name="description" class="modal-input" rows="3"></textarea>

            <button class="btn btn-primary w-100 mt-3" style="background:#CD9FFE;border:none;">
                Add Service
            </button>
        </form>
    </div>
</div>


<!-- EDIT SERVICE MODAL -->
<div class="custom-modal" id="editServiceModal">
    <div class="custom-modal-content">
        <div class="modal-header">
            <h5>Edit Service</h5>
            <button class="close-modal">&times;</button>
        </div>

        <p class="text-muted mb-3">Update this service’s details.</p>

        <form action="/admin/services/update" method="POST">

            <input type="hidden" name="id" id="edit_id">

            <label class="modal-label">Name</label>
            <input type="text" name="service_name" id="edit_name" class="modal-input" required>

            <label class="modal-label mt-2">Price</label>
            <input type="number" name="price" id="edit_price" class="modal-input" required>

            <label class="modal-label mt-2">Category</label>
            <select name="category" id="edit_category" class="modal-input" required>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat ?>"><?= $cat ?></option>
                <?php endforeach; ?>
            </select>

            <label class="modal-label mt-2">Description</label>
            <textarea name="description" id="edit_description" class="modal-input" rows="3"></textarea>

            <button class="btn btn-primary w-100 mt-3" style="background:#CD9FFE;border:none;">
                Save Changes
            </button>
        </form>
    </div>
</div>


<!-- DELETE SERVICE MODAL -->
<div class="custom-modal" id="deleteServiceModal">
    <div class="custom-modal-content" style="max-width: 400px; text-align: center;">

        <!-- Warning Icon -->
        <div style="margin-bottom: 20px;">
            <i class="bi bi-exclamation-triangle" style="font-size: 60px; color: #8b0000;"></i>
        </div>

        <!-- Warning Text -->
        <p style="font-size: 16px; margin-bottom: 20px;">
            Are you sure you want to <span style="color: #8b0000; font-weight: bold;">archive</span> this service?
        </p>

        <div class="d-flex gap-2 mt-3">
            <a id="confirmDeleteBtn" class="btn btn-outline-danger w-50" style="border: 1px solid #ccc; font-size: 16px; font-weight: 500;"><strong>Delete Service</strong></a>
            <button class="btn btn-outline-secondary w-50 close-modal" style="border: 1px solid #ccc; font-size: 16px; font-weight: 500;"><strong>No</strong></button>
        </div>
    </div>
</div>

<script src="/assets/js/services_modals.js"></script>
