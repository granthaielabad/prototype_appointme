<?php
use App\Core\Session;
$flashKeys = ['success','error','info'];
foreach (['success','error','info'] as $k) {
    $f = Session::getFlash($k);
    if ($f) {
        $cls = $f['type'] ?? ($k === 'success' ? 'success' : 'danger');
        echo '<div class="container mt-3"><div class="alert alert-'.$cls.'">'.$f['msg'].'</div></div>';
    }
}
?>
