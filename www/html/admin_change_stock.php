<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';

// ログインしているかどうかチェックのためセッションスタート
session_start();

// ログインチェックの関数利用
if(is_logined() === false){
  // してなければログインページへ
  redirect_to(LOGIN_URL);
}

// PDOを取得
$db = get_db_connect();

// ログインしているユーザー情報を取得
$user = get_login_user($db);

// 管理者でなければ
if(is_admin($user) === false){
  // ログインページへ
  redirect_to(LOGIN_URL);
}

// POST送信された商品IDを取得
$item_id = get_post('item_id');
// POST送信された在庫数を取得
$stock = get_post('stock');
// データベースに在庫数を登録したら
if(update_item_stock($db, $item_id, $stock)){
  // 完了メッセージを設定
  set_message('在庫数を変更しました。');
} else {
  // 失敗したら、エラーメッセージを設定
  set_error('在庫数の変更に失敗しました。');
}

// 商品管理ページにリダイレクト
redirect_to(ADMIN_URL);