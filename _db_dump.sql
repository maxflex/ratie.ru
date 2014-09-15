ALTER TABLE  `users` ADD  `friends` INT UNSIGNED NOT NULL DEFAULT  '0' COMMENT  'Количество друзей из ВК на Ratie' AFTER `subscribers` ;

CREATE TABLE `friends` (
					  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
					  `id_user` int(10) unsigned DEFAULT NULL COMMENT 'ID пользователя Ratie',
					  PRIMARY KEY (`id`)
					) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Друзья из ВК на Ratie' AUTO_INCREMENT=1 ;