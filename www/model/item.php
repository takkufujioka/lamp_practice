<?php
// 共通の関数ファイル読み込み
require_once MODEL_PATH . 'functions.php';
// データベースに関する関数ファイル読み込み
require_once MODEL_PATH . 'db.php';

// DB利用

// 指定の商品情報取得
function get_item($db, $item_id){
  $sql = "
    SELECT
      item_id, 
      name,
      stock,
      price,
      image,
      status
    FROM
      items
    WHERE
      item_id = ?
  ";

  return fetch_query($db, $sql, [$item_id]);
}


function get_items($db, $is_open = false){
  $sql = '
    SELECT
      item_id, 
      name,
      stock,
      price,
      image,
      status
    FROM
      items
  ';
  if($is_open === true){
    $sql .= '
      WHERE status = 1
    ';
  }

  return fetch_all_query($db, $sql);
}

// 商品一覧を取得
function get_all_items($db){
  return get_items($db);
}

// 公開商品一覧を取得
function get_open_items($db){
  return get_items($db, true);
}

// 新規商品を登録
function regist_item($db, $name, $price, $stock, $status, $image){
  $filename = get_upload_filename($image);
  if(validate_item($name, $price, $stock, $filename, $status) === false){
    return false;
  }
  return regist_item_transaction($db, $name, $price, $stock, $status, $image, $filename);
}

// 商品を登録するためのトランザクション処理
function regist_item_transaction($db, $name, $price, $stock, $status, $image, $filename){
  $db->beginTransaction();
  if(insert_item($db, $name, $price, $stock, $filename, $status) 
    && save_image($image, $filename)){
    $db->commit();
    return true;
  }
  $db->rollback();
  return false;
  
}

// データベースに商品を登録
function insert_item($db, $name, $price, $stock, $filename, $status){
  $status_value = PERMITTED_ITEM_STATUSES[$status];
  $sql = "
    INSERT INTO
      items(
        name,
        price,
        stock,
        image,
        status
      )
    VALUES(?, ?, ?, ?, ?);
  ";

  return execute_query($db, $sql, [$name, $price, $stock, $filename, $status_value]);
}

// 商品のステータスを変更
function update_item_status($db, $item_id, $status){
  $sql = "
    UPDATE
      items
    SET
      status = ?
    WHERE
      item_id = ?
    LIMIT 1
  ";
  
  return execute_query($db, $sql, [$status, $item_id]);
}

// 在庫数を変更
function update_item_stock($db, $item_id, $stock){
  $sql = "
    UPDATE
      items
    SET
      stock = ?
    WHERE
      item_id = ?
    LIMIT 1
  ";
  
  return execute_query($db, $sql, [$stock, $item_id]);
}


function destroy_item($db, $item_id){
  $item = get_item($db, $item_id);
  if($item === false){
    return false;
  }
  $db->beginTransaction();
  if(delete_item($db, $item['item_id'])
    && delete_image($item['image'])){
    $db->commit();
    return true;
  }
  $db->rollback();
  return false;
}

// データベースから商品を削除
function delete_item($db, $item_id){
  $sql = "
    DELETE FROM
      items
    WHERE
      item_id = ?
    LIMIT 1
  ";
  
  return execute_query($db, $sql, [$item_id]);
}


// 非DB

function is_open($item){
  return $item['status'] === 1;
}

function validate_item($name, $price, $stock, $filename, $status){
  $is_valid_item_name = is_valid_item_name($name);
  $is_valid_item_price = is_valid_item_price($price);
  $is_valid_item_stock = is_valid_item_stock($stock);
  $is_valid_item_filename = is_valid_item_filename($filename);
  $is_valid_item_status = is_valid_item_status($status);

  return $is_valid_item_name
    && $is_valid_item_price
    && $is_valid_item_stock
    && $is_valid_item_filename
    && $is_valid_item_status;
}

// 商品名が既定の文字数かどうかチェック
function is_valid_item_name($name){
  $is_valid = true;
  if(is_valid_length($name, ITEM_NAME_LENGTH_MIN, ITEM_NAME_LENGTH_MAX) === false){
    set_error('商品名は'. ITEM_NAME_LENGTH_MIN . '文字以上、' . ITEM_NAME_LENGTH_MAX . '文字以内にしてください。');
    $is_valid = false;
  }
  return $is_valid;
}

// 商品の価格が既定の形式かどうかチェック
function is_valid_item_price($price){
  $is_valid = true;
  if(is_positive_integer($price) === false){
    set_error('価格は0以上の整数で入力してください。');
    $is_valid = false;
  }
  return $is_valid;
}

// 在庫数が既定の形式かどうかチェック
function is_valid_item_stock($stock){
  $is_valid = true;
  if(is_positive_integer($stock) === false){
    set_error('在庫数は0以上の整数で入力してください。');
    $is_valid = false;
  }
  return $is_valid;
}

// 商品画像ファイル名が空でないかチェック
function is_valid_item_filename($filename){
  $is_valid = true;
  if($filename === ''){
    $is_valid = false;
  }
  return $is_valid;
}


function is_valid_item_status($status){
  $is_valid = true;
  if(isset(PERMITTED_ITEM_STATUSES[$status]) === false){
    $is_valid = false;
  }
  return $is_valid;
}