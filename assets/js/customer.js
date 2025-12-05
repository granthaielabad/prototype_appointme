document.addEventListener('DOMContentLoaded', ()=>{
  // fetch notifications count and list
  const notifCount = document.getElementById('notifCount');
  const notifBody = document.getElementById('notifBody');

  fetch('/notifications/list')
    .then(r=>r.json())
    .then(items=>{
      if (!items || !items.length){
        notifBody.innerHTML = '<p class="text-muted">No notifications.</p>';
        return;
      }
      notifCount.style.display = items.length ? 'inline-block' : 'none';
      notifCount.textContent = items.length;

      notifBody.innerHTML = items.map(i => `
        <div class="mb-2">
          <strong>${i.title}</strong>
          <div class="small text-muted">${i.message}</div>
        </div>
      `).join('');
    }).catch(()=>{});

  // invoice card to PNG download
  document.querySelectorAll('.js-download-invoice').forEach(btn => {
    btn.addEventListener('click', async (e) => {
      e.preventDefault();

      const card = btn.closest('.invoice-card');
      if (!card || typeof html2canvas !== 'function') return;

      const originalText = btn.textContent;
      btn.disabled = true;
      btn.textContent = 'Preparing...';

      try {
        const canvas = await html2canvas(card, { scale: 2, useCORS: true });
        const link = document.createElement('a');
        link.download = `invoice-${btn.dataset.invoiceId || 'card'}.png`;
        link.href = canvas.toDataURL('image/png');
        link.click();
      } catch (err) {
        console.error(err);
        alert('Could not generate image. Please try again.');
      } finally {
        btn.disabled = false;
        btn.textContent = originalText;
      }
    });
  });
});
