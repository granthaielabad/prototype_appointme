<?php
$pageTitle = "Reports";
$activePage = "reports";
?>

<div class="admin-section">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h6 class="section-title">Reports & Analytics</h6>
            <small class="section-subtitle text-muted">
                View system insights, performance, and generate downloadable reports
            </small>
        </div>

        <div class="d-flex gap-2">
            <a href="/admin/reports/export-csv" class="btn btn-light border">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i> Download CSV
            </a>
            <a href="/admin/reports/export-pdf" class="btn btn-primary" style="background:#CD9FFE;border:none;">
                <i class="bi bi-filetype-pdf me-1"></i> Download PDF
            </a>
        </div>
    </div>

    <!-- FILTER CARD -->
    <div class="card content-card p-3 mb-4">

        <h6 class="fw-semibold mb-3">Filter Reports</h6>

        <form method="GET" class="row g-3">

            <!-- Date From -->
            <div class="col-md-4">
                <label class="form-label fw-semibold">From Date</label>
                <input type="date" name="from" class="form-control"
                       value="<?= $_GET['from'] ?? '' ?>">
            </div>

            <!-- Date To -->
            <div class="col-md-4">
                <label class="form-label fw-semibold">To Date</label>
                <input type="date" name="to" class="form-control"
                       value="<?= $_GET['to'] ?? '' ?>">
            </div>

            <!-- Type -->
            <div class="col-md-4">
                <label class="form-label fw-semibold">Report Type</label>
                <select name="type" class="form-select">
                    <option value="appointments">Appointments</option>
                    <option value="sales">Sales</option>
                    <option value="customers">Customer Count</option>
                </select>
            </div>

            <div class="col-12">
                <button class="btn btn-primary mt-2" style="background:#CD9FFE;border:none;">
                    <i class="bi bi-filter-circle me-1"></i> Apply Filter
                </button>
            </div>

        </form>

    </div>

    <!-- SUMMARY CARDS -->
    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="stats-card">
                <h6>Total Appointments</h6>
                <h2><?= htmlspecialchars($summary['appointments'] ?? 0) ?></h2>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stats-card">
                <h6>Total Customers</h6>
                <h2><?= htmlspecialchars($summary['customers'] ?? 0) ?></h2>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stats-card">
                <h6>Total Revenue</h6>
                <h2>₱<?= number_format($summary['revenue'] ?? 0) ?></h2>
            </div>
        </div>

    </div>

    <!-- CHARTS -->
    <div class="card content-card p-4">
        <h6 class="fw-semibold mb-3">Monthly Trend Overview</h6>

        <canvas id="reportsChart" style="min-height:300px;"></canvas>
    </div>

</div>

<!-- CHART JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
fetch('/analytics/daily-appointments')
  .then(res => res.json())
  .then(data => {
      const labels = data.map(x => x.appointment_date).reverse();
      const totals = data.map(x => Number(x.total)).reverse();

      const ctx = document.getElementById('reportsChart').getContext('2d');

      new Chart(ctx, {
          type: 'line',
          data: {
              labels: labels,
              datasets: [{
                  label: "Daily Appointments",
                  data: totals,
                  borderColor: "#CD9FFE",
                  backgroundColor: "rgba(205,159,254,0.2)",
                  borderWidth: 2,
                  tension: 0.4,
                  fill: true
              }]
          },
          options: {
              responsive: true,
              plugins: {
                  legend: { display: true }
              },
              scales: {
                  y: { beginAtZero: true }
              }
          }
      });
  });
</script>
