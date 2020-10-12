<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'cart.php';
require_once MODEL_PATH . 'user.php';


session_start();

// トークンの照合
$post_token = get_post('token');
if(is_valid_csrf_token($post_token) === false) {
  set_error('不正なリクエストです。');
  redirect_to(ADMIN_URL);
}

if(is_logined() === false) {
  redirect_to(LOGIN_URL);
}

$db = get_db_connect();

$user = get_login_user($db);

$order_number = get_post('order_number');

$history = get_history_by_order_number($db, $order_number);

$details = get_details($db, $order_number);

$total_price = sum_carts($details);


include_once VIEW_PATH . 'details_view.php';