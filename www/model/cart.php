<?php 
// 共通関数ファイルの読み込み
require_once MODEL_PATH . 'functions.php';
// データベース接続に関するファイルの読み込み
require_once MODEL_PATH . 'db.php';

// データベースから特定のユーザーのカート情報を取得
function get_user_carts($db, $user_id){
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = ?
  ";
  return fetch_all_query($db, $sql, [$user_id]);
}

// ログイン中のユーザーのカート情報を取得
function get_user_cart($db, $user_id, $item_id){
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = ?
    AND
      items.item_id = ?
  ";

  return fetch_query($db, $sql, [$user_id, $item_id]);

}

// カートに商品を追加
function add_cart($db, $user_id, $item_id ) {
  $cart = get_user_cart($db, $user_id, $item_id);
  if($cart === false){
    return insert_cart($db, $user_id, $item_id);
  }
  return update_cart_amount($db, $cart['cart_id'], $cart['amount'] + 1);
}

// カートに商品を新規登録
function insert_cart($db, $user_id, $item_id, $amount = 1){
  $sql = "
    INSERT INTO
      carts(
        item_id,
        user_id,
        amount
      )
    VALUES(?, ?, ?)
  ";

  return execute_query($db, $sql, [$item_id, $user_id, $amount]);
}

// カート内の数量を変更
function update_cart_amount($db, $cart_id, $amount){
  $sql = "
    UPDATE
      carts
    SET
      amount = ?
    WHERE
      cart_id = ?
    LIMIT 1
  ";
  return execute_query($db, $sql, [$amount, $cart_id]);
}

// カートを削除
function delete_cart($db, $cart_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      cart_id = ?
    LIMIT 1
  ";

  return execute_query($db, $sql, [$cart_id]);
}

// カートの商品を購入
function purchase_carts($db, $carts){
  if(validate_cart_purchase($carts) === false){
    return false;
  }
  purchase_carts_transaction($db, $carts[0]['user_id'], $carts);
  foreach($carts as $cart){
    if(update_item_stock(
        $db, 
        $cart['item_id'], 
        $cart['stock'] - $cart['amount']
      ) === false){
      set_error($cart['name'] . 'の購入に失敗しました。');
    }
  }
  
  delete_user_carts($db, $carts[0]['user_id']);
}

// カートテーブルから特定のユーザーの商品を削除
function delete_user_carts($db, $user_id){
  $sql = "
    DELETE FROM
      carts
    WHERE
      user_id = ?
  ";

  execute_query($db, $sql, [$user_id]);
}


function sum_carts($carts){
  $total_price = 0;
  foreach($carts as $cart){
    $total_price += $cart['price'] * $cart['amount'];
  }
  return $total_price;
}

function validate_cart_purchase($carts){
  if(count($carts) === 0){
    set_error('カートに商品が入っていません。');
    return false;
  }
  foreach($carts as $cart){
    if(is_open($cart) === false){
      set_error($cart['name'] . 'は現在購入できません。');
    }
    if($cart['stock'] - $cart['amount'] < 0){
      set_error($cart['name'] . 'は在庫が足りません。購入可能数:' . $cart['stock']);
    }
  }
  if(has_error() === true){
    return false;
  }
  return true;
}

// 購入履歴と購入明細の登録とカートからの商品の削除をトランザクション
function purchase_carts_transaction($db, $user_id, $carts) {
  $db->beginTransaction();
  if(insert_history($db, $user_id)
    && insert_details($db, $carts)){
    $db->commit();
    return true;
  }
  $db->rollback();
  return false;
}

// 購入履歴を登録
function insert_history($db, $user_id) {
  $sql = "
    INSERT INTO
      histories(
        user_id
      )
    VALUES(?)
  ";

  return execute_query($db, $sql, [$user_id]);
}

// 購入明細を登録
function insert_details($db, $carts) {
  $result = true;
  $order_number = $db->lastInsertId();
  foreach($carts as $cart) {
      $sql = "
        INSERT INTO
          details(
            order_number,
            name,
            price,
            amount
          )
        VALUES(?, ?, ?, ?)
      ";
      $tmp = execute_query($db, $sql, [$order_number, $cart['name'], $cart['price'], $cart['amount']]);
      if($tmp === false) {
        $result = $tmp;
      }
  }
  return $result;
}