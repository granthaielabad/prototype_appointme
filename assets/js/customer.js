document.addEventListener('DOMContentLoaded', () => {
  // fetch notifications count and list
  const notifCount = document.getElementById('notifCount');
  const notifBody = document.getElementById('notifBody');

  fetch('/notifications/list')
    .then((r) => r.json())
    .then((items) => {
      if (!items || !items.length) {
        if (notifBody) notifBody.innerHTML = '<p class="text-muted">No notifications.</p>';
        return;
      }
      if (notifCount) {
        notifCount.style.display = items.length ? 'inline-block' : 'none';
        notifCount.textContent = items.length;
      }
      if (notifBody) {
        notifBody.innerHTML = items
          .map(
            (i) => `
        <div class="mb-2">
          <strong>${i.title}</strong>
          <div class="small text-muted">${i.message}</div>
        </div>
      `,
          )
          .join('');
      }
    })
    .catch(() => {});

  // invoice card to PNG download
  document.querySelectorAll('.js-download-invoice').forEach((btn) => {
    btn.addEventListener('click', async (e) => {
      e.preventDefault();

      const card = btn.closest('.invoice-card');
      if (!card || typeof html2canvas !== 'function') return;

      const originalText = btn.textContent;
      btn.disabled = true;
      btn.textContent = 'Download';

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

  // profile inline edit toggle
  const editBtn = document.getElementById('editProfileBtn');
  const cancelBtn = document.getElementById('cancelEditBtn');
  const viewNodes = document.querySelectorAll('.view-mode');
  const editNodes = document.querySelectorAll('.edit-mode');

  function setEditMode(on) {
    viewNodes.forEach((n) => n.classList.toggle('d-none', on));
    editNodes.forEach((n) => n.classList.toggle('d-none', !on));
    if (editBtn) {
      editBtn.textContent = on ? 'Editing…' : 'Edit Profile';
      editBtn.disabled = on;
    }
  }

  editBtn?.addEventListener('click', () => setEditMode(true));
  cancelBtn?.addEventListener('click', () => setEditMode(false));




// profile - changed password
  const changePassForm = document.querySelector('#changePasswordModal form');
  if (changePassForm) {
    changePassForm.addEventListener('submit', (e) => {
      const pwd = changePassForm.querySelector('[name="new_password"]')?.value || '';
      const confirm = changePassForm.querySelector('[name="confirm_password"]')?.value || '';
      if (pwd !== confirm) {
        e.preventDefault();
        alert('New passwords do not match.');
      }
    });
  }

// profile - changed profile

  const photoTrigger = document.getElementById('profilePhotoTrigger');
  const photoInput = document.getElementById('profilePhotoInput');
  const photoImg = document.getElementById('profilePhoto');
  const photoForm = document.getElementById('profilePhotoForm');

  if (photoTrigger && photoInput) {
    photoTrigger.addEventListener('click', () => photoInput.click());
    photoInput.addEventListener('change', (e) => {
      const file = e.target.files?.[0];
      if (file) {
        const url = URL.createObjectURL(file);
        if (photoImg) photoImg.src = url;
        photoForm?.submit();
      }
    });
  }




});
