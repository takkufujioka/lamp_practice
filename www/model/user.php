<?php
// 共通関数ファイルの読み込み
require_once MODEL_PATH . 'functions.php';
// データベースに関する関数ファイルの読み込み
require_once MODEL_PATH . 'db.php';

// 指定のユーザーIDのユーザー情報の取得
function get_user($db, $user_id){
  $sql = "
    SELECT
      user_id, 
      name,
      password,
      type
    FROM
      users
    WHERE
      user_id = ?
    LIMIT 1
  ";

  return fetch_query($db, $sql, [$user_id]);
}

// 指定のユーザー名のユーザー情報を取得
function get_user_by_name($db, $name){
  $sql = "
    SELECT
      user_id, 
      name,
      password,
      type
    FROM
      users
    WHERE
      name = ?
    LIMIT 1
  ";

  return fetch_query($db, $sql, [$name]);
}

// 指定のユーザー名もしくはパスワードを存在するかチェックし、存在すればセッションにユーザーIDを登録
function login_as($db, $name, $password){
  $user = get_user_by_name($db, $name);
  if($user === false || $user['password'] !== $password){
    return false;
  }
  set_session('user_id', $user['user_id']);
  return $user;
}

// セッションからユーザーIDを取得し、ユーザー情報を取得
function get_login_user($db){
  $login_user_id = get_session('user_id');

  return get_user($db, $login_user_id);
}

// ユーザー名、パスワードのチェックに問題がなければデータベースにユーザー情報を登録
function regist_user($db, $name, $password, $password_confirmation) {
  if( is_valid_user($name, $password, $password_confirmation) === false){
    return false;
  }
  
  return insert_user($db, $name, $password);
}

function is_admin($user){
  return $user['type'] === USER_TYPE_ADMIN;
}

// ユーザー名、パスワードが既定の形式であるかチェック
function is_valid_user($name, $password, $password_confirmation){
  // 短絡評価を避けるため一旦代入。
  $is_valid_user_name = is_valid_user_name($name);
  $is_valid_password = is_valid_password($password, $password_confirmation);
  return $is_valid_user_name && $is_valid_password ;
}

// ユーザー名が既定の形式かどうかチェック
function is_valid_user_name($name) {
  $is_valid = true;
  if(is_valid_length($name, USER_NAME_LENGTH_MIN, USER_NAME_LENGTH_MAX) === false){
    set_error('ユーザー名は'. USER_NAME_LENGTH_MIN . '文字以上、' . USER_NAME_LENGTH_MAX . '文字以内にしてください。');
    $is_valid = false;
  }
  if(is_alphanumeric($name) === false){
    set_error('ユーザー名は半角英数字で入力してください。');
    $is_valid = false;
  }
  return $is_valid;
}

// パスワードが既定の形式かどうか、またパスワードと確認用パスワードが一致するかどうかチェック
function is_valid_password($password, $password_confirmation){
  $is_valid = true;
  if(is_valid_length($password, USER_PASSWORD_LENGTH_MIN, USER_PASSWORD_LENGTH_MAX) === false){
    set_error('パスワードは'. USER_PASSWORD_LENGTH_MIN . '文字以上、' . USER_PASSWORD_LENGTH_MAX . '文字以内にしてください。');
    $is_valid = false;
  }
  if(is_alphanumeric($password) === false){
    set_error('パスワードは半角英数字で入力してください。');
    $is_valid = false;
  }
  if($password !== $password_confirmation){
    set_error('パスワードがパスワード(確認用)と一致しません。');
    $is_valid = false;
  }
  return $is_valid;
}

// ユーザー情報を登録
function insert_user($db, $name, $password){
  $sql = "
    INSERT INTO
      users(name, password)
    VALUES (?, ?);
  ";

  return execute_query($db, $sql, [$name, $password]);
}

