(() => {
  const cfg = window.PAYMENT_QR_CONFIG || {};
  const cancelUrl = cfg.cancelUrl || '/payment/cancel-session';
  let cancelling = false;

  const sendCancel = () => {
    if (cancelling) return;
    cancelling = true;

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

    const payBtn = document.querySelector('.pay-btn');
    if (payBtn) {
      payBtn.removeAttribute('href');
      payBtn.classList.add('disabled');
      payBtn.setAttribute('aria-disabled', 'true');
    }
  };

  // Cancel on navigation away
  window.addEventListener('pagehide', sendCancel);
  document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'hidden') sendCancel();
  });

  // If the page is restored from the back/forward cache, cancel and redirect away
  window.addEventListener('pageshow', (event) => {
    const navEntry = performance.getEntriesByType('navigation')[0];
    const cameFromHistory = event.persisted || navEntry?.type === 'back_forward';
    if (cameFromHistory) {
      sendCancel();
      window.location.replace('/my-appointments');
    }
  });

  // Cancel on "Back to My Appointments"
  const backBtn = document.getElementById('back-to-appointments');
  if (backBtn) backBtn.addEventListener('click', sendCancel, { once: true });
})();
