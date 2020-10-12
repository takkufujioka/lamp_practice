<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  <title>購入明細</title>
  <!-- <link rel="stylesheet" href=""> -->
</head>
<body>
  <?php include VIEW_PATH . 'templates/header_logined.php'; ?>
  <div class="container">
    
    <h1>購入明細</h1>

    <p>注文番号：<?php print($history['order_number']); ?></p>
    <p>購入日時：<?php print($history['purchased']); ?></p>
    <p>合計金額：<?php print(number_format($total_price)); ?>円</p>

    <?php include VIEW_PATH . 'templates/messages.php'; ?>

    <table class="table table-bordered">
      <thead class="thead-light">
        <tr>
          <th>商品名</th>
          <th>価格</th>
          <th>購入数</th>
          <th>小計</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($details as $detail) { ?>
          <tr>
            <td><?php print(h($detail['name'])); ?></td>
            <td><?php print(number_format($detail['price'])); ?>円</td>
            <td><?php print($detail['amount']); ?>個</td>
            <td><?php print(number_format($detail['price'] * $detail['amount'])); ?>円</td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</body>
</html>