<?php
require_once '../conf/const.php';
require_once MODEL_PATH . 'functions.php';
require_once MODEL_PATH . 'user.php';

session_start();

// トークンの照合
$post_token = get_post('token');
if(is_valid_csrf_token($post_token) === false) {
  set_error('不正なリクエストです。');
  redirect_to(SIGNUP_URL);
}

if(is_logined() === true){
  redirect_to(HOME_URL);
}

$name = get_post('name');
$password = get_post('password');
$password_confirmation = get_post('password_confirmation');
$hash = password_hash($password, PASSWORD_DEFAULT);

$db = get_db_connect();

try{
  $result = regist_user($db, $name, $password, $password_confirmation, $hash);
  if( $result=== false){
    set_error('ユーザー登録に失敗しました。');
    redirect_to(SIGNUP_URL);
  }
}catch(PDOException $e){
  set_error('ユーザー登録に失敗しました。');
  redirect_to(SIGNUP_URL);
}

set_message('ユーザー登録が完了しました。');
login_as($db, $name, $hash);

// トークンを破棄
unset($_SESSION['csrf_token']);

redirect_to(HOME_URL);