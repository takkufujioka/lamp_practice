<?php
// 定数ファイル読み込み
require_once '../conf/const.php';
// 共通関数ファイル読み込み
require_once MODEL_PATH . 'functions.php';
// ユーザーに関する関数ファイル読み込み
require_once MODEL_PATH . 'user.php';
// 商品に関する関数ファイル読み込み
require_once MODEL_PATH . 'item.php';

// ログインチェックのため、セッション開始
session_start();

// トークンの照合
$token = get_session('csrf_token');
if(is_valid_csrf_token($token) === false) {
  set_error('不正なリクエストです。');
  redirect_to(ADMIN_URL);
}

// ログインチェック用関数利用
if(is_logined() === false){
  // ログインされていなければ、ログイン画面へ遷移
  redirect_to(LOGIN_URL);
}

// PDO取得
$db = get_db_connect();

// PDOを利用してログインしているユーザー情報を取得
$user = get_login_user($db);

// 管理者としてログインしているかどうかチェック
if(is_admin($user) === false){
  // 管理者でなければログインページへ遷移
  redirect_to(LOGIN_URL);
}

// POST送信された商品IDを取得
$item_id = get_post('item_id');
// 商品の変更情報を取得
$changes_to = get_post('changes_to');

// ステータスが公開に変更されていれば
if($changes_to === 'open'){
  // データベースのステータスを公開に更新
  update_item_status($db, $item_id, ITEM_STATUS_OPEN);
  // 完了メッセージを設定する
  set_message('ステータスを変更しました。');
  // ステータスが非公開に変更されていれば
}else if($changes_to === 'close'){
  // データベースのステータスを非公開に更新
  update_item_status($db, $item_id, ITEM_STATUS_CLOSE);
  // 完了メッセージを設定する
  set_message('ステータスを変更しました。');
  // ステータスの変更が公開、非公開以外の形で送信されてきた場合
}else {
  // エラーメッセージを設定
  set_error('不正なリクエストです。');
}

// トークンを破棄
unset($_SESSION['csrf_token']);

// 管理画面へリダイレクト
redirect_to(ADMIN_URL);