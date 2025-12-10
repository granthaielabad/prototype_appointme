<?php
// views/pages/payment_qr.php


$checkoutUrl = $checkoutUrl ?? '';

$escapedUrl = htmlspecialchars($checkoutUrl, ENT_QUOTES, 'UTF-8');
$qrData     = urlencode($checkoutUrl);
?>

<div class="container my-5">
    <h2 class="mb-4 text-center">Scan to Pay</h2>

    <?php if (!empty($checkoutUrl)): ?>
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <p class="mb-3">
                    Please scan this QR code using your mobile banking or e-wallet app.
                </p>

                <img
                    src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=<?= $qrData ?>"
                    alt="Payment QR Code"
                    class="img-fluid mb-3"
                >

                <p class="mb-3">
                    Or click this button if you’re on mobile or desktop:
                </p>

                <p class="mb-3">
                    <a
                        href="<?= $escapedUrl ?>"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="btn btn-primary"
                    >
                        Open Payment Page
                    </a>
                </p>

                <a href="/my-appointments" class="btn btn-outline-secondary btn-sm">
                    Back to My Appointments
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-danger">
            No active payment session found. Please try booking again.
        </div>
        <a href="/my-appointments" class="btn btn-outline-secondary btn-sm">
            Back to My Appointments
        </a>
    <?php endif; ?>
</div>
