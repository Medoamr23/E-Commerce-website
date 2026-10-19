<?php $cur = basename($_SERVER['PHP_SELF']); ?>
<h1>Admin dashboard</h1>
<div class="admin-nav">
<?php foreach (['index.php'=>'Overview','products.php'=>'Products','orders.php'=>'Orders','users.php'=>'Users','comments.php'=>'Comments','categories.php'=>'Categories','account.php'=>'My account'] as $nf=>$nl): ?>
  <a href="<?= $nf ?>" class="<?= $cur === $nf ? 'on' : '' ?>"><?= $nl ?></a>
<?php endforeach; ?>
</div>
