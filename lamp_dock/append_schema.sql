-- テーブルの構造 'history'

CREATE TABLE 'history' (
  'order_number' int(11) NOT NULL AUTO_INCREMENT,
  'user_id' int(11) NOT NULL,
  'purchased' datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UPDATE CURRENT_TIMESTAMP
);

--


-- テーブルの構造 'details'

CREATE TABLE 'details' (
  'order_number' int(11) NOT NULL,
  'cart_id' int(11) NOT NULL,
  UPDATE CURRENT_TIMESTAMP
);

--