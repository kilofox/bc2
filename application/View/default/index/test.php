<ul>
    <? foreach ($linkages as $node): ?>
    <li><?php echo isset($user->username) ? $user->username : $user->title; ?></li>
    <? endforeach; ?>
</ul>
<?php
$view = new \Bootphp\View('stats');
$view->path(SYS_PATH . '/profiler/Views/');
?>