<?php
use App\Core\Session;

if ($flash = Session::getFlash('success')): ?>
    <div class="alert alert-success text-center"><?= htmlspecialchars($flash['msg']) ?></div>
<?php endif; ?>

<?php if ($flash = Session::getFlash('error')): ?>
    <div class="alert alert-danger text-center"><?= htmlspecialchars($flash['msg']) ?></div>
<?php endif; ?>
