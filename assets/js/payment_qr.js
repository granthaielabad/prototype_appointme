(() => {
  const cfg = window.PAYMENT_QR_CONFIG || {};
  const cancelUrl = cfg.cancelUrl || '/payment/cancel-session';
  let cancelling = false;
  let goingToPayment = false;

  const sendCancel = () => {
    if (cancelling || goingToPayment) return; // don't cancel if user clicked Pay
    cancelling = true;

    const payload = new Blob([JSON.stringify({})], { type: 'application/json' });
    if (navigator.sendBeacon) {
      navigator.sendBeacon(cancelUrl, payload);
    } else {
      fetch(cancelUrl, { method: 'POST', body: payload, keepalive: true, headers: { 'Content-Type': 'application/json' } });
    }

    const payBtn = document.querySelector('.pay-btn');
    if (payBtn) {
      payBtn.removeAttribute('href');
      payBtn.classList.add('disabled');
      payBtn.setAttribute('aria-disabled', 'true');
    }
  };

  window.addEventListener('pagehide', sendCancel);
  document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'hidden') sendCancel();
  });

  window.addEventListener('pageshow', (event) => {
    const navEntry = performance.getEntriesByType('navigation')[0];
    const cameFromHistory = event.persisted || navEntry?.type === 'back_forward';
    if (cameFromHistory) {
      goingToPayment = false; // user came back without completing payment
      sendCancel();
      window.location.replace('/my-appointments');
    }
  });

  const backBtn = document.getElementById('back-to-appointments');
  if (backBtn) backBtn.addEventListener('click', sendCancel, { once: true });

  const payBtn = document.querySelector('.pay-btn');
  if (payBtn) {
    payBtn.addEventListener('click', () => {
      goingToPayment = true; // allow navigation to payment without cancelling
    }, { once: true });
  }
})();
