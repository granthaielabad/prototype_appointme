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
});
