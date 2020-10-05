-- テーブルの構造 'history'

CREATE TABLE `history` (
  `order_number` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `purchased` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
);

--


-- テーブルの構造 'details'

CREATE TABLE `details` (
  `order_number` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
);

--