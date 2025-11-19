<?php
// Simple reports dashboard
?>
<div class="container">
  <h1 class="mb-4">Reports</h1>
  <p>View appointment trends and revenue. Click the button to view a daily appointments chart for the last 30 days.</p>
  <a href="/admin/reports/summary" class="btn btn-primary">View summary</a>

  <hr class="my-4" />

  <h4>Quick analytics</h4>
  <canvas id="dailyChart" style="max-width:800px"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
fetch('/analytics/daily-appointments')
  .then(r => r.json())
  .then(data => {
    const labels = data.map(r => r.appointment_date).reverse();
    const totals = data.map(r => parseInt(r.total)).reverse();
    const ctx = document.getElementById('dailyChart').getContext('2d');
    new Chart(ctx, {
      type: 'line',
      data: {
        labels,
        datasets: [{
          label: 'Daily Appointments',
          data: totals,
          tension: 0.3,
          fill: true,
        }]
      },
      options: { responsive: true, plugins: { legend: { display: true } } }
    });
  })
  .catch(err => console.error(err));
</script>
