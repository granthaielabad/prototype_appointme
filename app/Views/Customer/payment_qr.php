<?php
$checkoutUrl = $checkoutUrl ?? '';
$escapedUrl  = htmlspecialchars($checkoutUrl, ENT_QUOTES, 'UTF-8');
$qrData      = urlencode($checkoutUrl);
?>
<link rel="stylesheet" href="/assets/css/payment_qr.css">

<div class="payment-qr-page">
    <div class="payment-card">
        <?php if (!empty($checkoutUrl)): ?>
            <div>
                <div class="subheading">Pay with E-Wallet</div>

                <div class="qr-wrapper">
                    <img
                        src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=<?= $qrData ?>"
                        alt="Payment QR Code"
                    >
                </div>

                <div class="merchant">8th Avenue Salon</div>
                <div class="details">Mobile No: +639999999999</div>
                <div class="details">User Id: 00000000000</div>

                <div class="actions">
                    <a
                        href="<?= $escapedUrl ?>"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="pay-btn"
                    >
                        Pay
                    </a>

                    <a href="/my-appointments" class="back-link" id="back-to-appointments">
                        Back to My Appointments
                    </a>
                </div>
            </div>

            <script>
              window.PAYMENT_QR_CONFIG = { cancelUrl: '/payment/cancel-session' };
            </script>
            <script src="/assets/js/payment_qr.js"></script>
        <?php else: ?>
            <div class="error-card">
                <div class="payment-title">Payment Session Missing</div>
                <div class="payment-meta">No active payment session found.</div>
                <div class="payment-meta">Please try booking again.</div>
                <a href="/my-appointments" class="back-link" id="back-to-appointments">
                    Back to My Appointments
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>
