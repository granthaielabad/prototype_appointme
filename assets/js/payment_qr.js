(() => {
  const cfg = window.PAYMENT_QR_CONFIG || {};
  const cancelUrl = cfg.cancelUrl || '/payment/cancel-session';
  let cancelled = false;

  const sendCancel = () => {
    if (cancelled) return;
    cancelled = true;

    const payload = new Blob([JSON.stringify({})], { type: 'application/json' });

    if (navigator.sendBeacon) {
      navigator.sendBeacon(cancelUrl, payload);
    } else {
      fetch(cancelUrl, {
        method: 'POST',
        body: payload,
        keepalive: true,
        headers: { 'Content-Type': 'application/json' },
      });
    }

    const payBtn = document.querySelector('a.btn.btn-primary');
    if (payBtn) {
      payBtn.removeAttribute('href');
      payBtn.classList.add('disabled');
      payBtn.setAttribute('aria-disabled', 'true');
    }
  };

  // Cancel on navigation 
  window.addEventListener('pagehide', sendCancel);

  // Cancel on "Back to My Appointments" 
  const backBtn = document.getElementById('back-to-appointments');
  if (backBtn) backBtn.addEventListener('click', sendCancel, { once: true });
})();
