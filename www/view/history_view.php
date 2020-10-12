<!DOCTYPE html>
<html lang="ja">
<head>
  <?php include VIEW_PATH . 'templates/head.php'; ?>
  <title>購入履歴</title>
  <!-- <link rel="stylesheet" href=".css"> -->
</head>
<body>
  <?php include VIEW_PATH . 'templates/header_logined.php'; ?>

  <div class="container">
    <h1>購入履歴一覧</h1>

    <?php include VIEW_PATH . 'templates/messages.php'; ?>

    <?php if (count($user_histories) > 0) { ?>
      <table class="table table-bordered text-center">
        <thead class="thead-light">
          <tr>
            <th>注文番号</th>
            <th>購入日時</th>
            <th>合計金額</th>
            <th>操作</th>
          </tr>
        </thead>
        <tbody>
          <?php if(is_admin($user) === true) { ?>
            <?php foreach($histories as $history) { ?>
              <tr>
                <td><?php print($history['order_number']); ?></td>
                <td><?php print($history['purchased']); ?></td>
                <td><?php print(number_format(sum_details($db, $history))); ?></td>
                <td>
                  <form method="post" action="details.php">
                    <input type="submit" value="購入明細表示">
                    <input type="hidden" name="order_number" value="<?php print($history['order_number']); ?>">
                    <input type="hidden" name="token" value="<?php print($token); ?>">
                  </form>
                </td>
              </tr>
            <?php } ?>
          <?php } else { ?>
            <?php foreach($user_histories as $user_history) { ?>
              <tr>
                <td><?php print($user_history['order_number']); ?></td>
                <td><?php print($user_history['purchased']); ?></td>
                <td><?php print(number_format(sum_details($db, $user_history))); ?></td>
                <td>
                  <form method="post" action="details.php">
                    <input type="submit" value="購入明細表示">
                    <input type="hidden" name="order_number" value="<?php print($user_history['order_number']); ?>">
                    <input type="hidden" name="token" value="<?php print($token); ?>">
                  </form>
                </td>
              </tr>
            <?php } ?>
          <?php } ?>
        </tbody>
      </table>
    <?php } else { ?>
      <p>購入履歴がありません。</p>
    <?php } ?>
  </div>
</body>
</html>