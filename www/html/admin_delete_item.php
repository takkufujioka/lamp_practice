<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';
require_once MODEL_PATH . 'item.php';

session_start();

// トークンの照合
$post_token = get_post('token');
if(is_valid_csrf_token($post_token) === false) {
  set_error('不正なリクエストです。');
  redirect_to(ADMIN_URL);
}

if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

$db = get_db_connect();

$user = get_login_user($db);

if(is_admin($user) === false){
  redirect_to(LOGIN_URL);
}

// POST送信された商品IDを取得
$item_id = get_post('item_id');


if(destroy_item($db, $item_id) === true){
  set_message('商品を削除しました。');
} else {
  set_error('商品削除に失敗しました。');
}

// トークンを破棄
unset($_SESSION['csrf_token']);

redirect_to(ADMIN_URL);