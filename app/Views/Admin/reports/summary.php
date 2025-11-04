<?php
// Summary view
?>
<div class="container">
  <h1>Appointments Summary (<?=htmlspecialchars($start)?> → <?=htmlspecialchars($end)?>)</h1>
  <table class="table table-striped">
    <thead><tr><th>Date</th><th>Count</th></tr></thead>
    <tbody>
    <?php foreach($rows as $r): ?>
      <tr><td><?=htmlspecialchars($r['appointment_date'])?></td><td><?=htmlspecialchars($r['total'])?></td></tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <a class="btn btn-secondary" href="/admin/reports">Back</a>
</div>
