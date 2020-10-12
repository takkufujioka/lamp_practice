<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'cart.php';
require_once MODEL_PATH . 'user.php';


session_start();

// トークンを生成
$token = get_csrf_token();

if(is_logined() === false){
  redirect_to(LOGIN_URL);
}

$db = get_db_connect();

$user = get_login_user($db);

$histories = get_all_histories($db);

$user_histories = get_user_histories($db, $user['user_id']);

include_once VIEW_PATH . '/history_view.php';
?> 

