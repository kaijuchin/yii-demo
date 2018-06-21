<?php
use yii\helpers\Html;
?>
<?php foreach ($data as $item): ?>
    <?= str_repeat('--', $item['depath']) . $item['id'] . '<br />'; ?>
<?php endforeach; ?>