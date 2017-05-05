drop database if exists `db_test`;
CREATE DATABASE IF NOT EXISTS `db_test` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `db_test`;

CREATE TABLE `db_test`.`categories` ( `id` INT NOT NULL AUTO_INCREMENT , `name` VARCHAR(255) NOT NULL , PRIMARY KEY (`id`)) ENGINE = MyISAM;

INSERT INTO `categories` (`id`, `name`) VALUES (NULL, 'web'), (NULL, 'mobile');

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL
);


INSERT INTO `users` (`username`) VALUES ('admin');
INSERT INTO `users` (`username`) VALUES ('marie');


CREATE TABLE `posts` (
  `id` int(10) UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `content` text NOT NULL,
  `user_id` int(10) NOT NULL,
  `category_id` INT NULL
);


INSERT INTO `posts` (`title`, `content`, `user_id`) VALUES ('Post 1', 'Content 1', 1);
INSERT INTO `posts` (`title`, `content`, `user_id`,`category_id`) VALUES ('Post 2', 'Content 2', 1,1);
INSERT INTO `posts` (`title`, `content`, `user_id`,`category_id`) VALUES ('Post 3', 'Content 3', 1,1);
INSERT INTO `posts` (`title`, `content`, `user_id`) VALUES ('Post 4', 'Content 4', 2);
INSERT INTO `posts` (`title`, `content`, `user_id`,`category_id`) VALUES ('Post 5', 'Content 5', 2,2);
