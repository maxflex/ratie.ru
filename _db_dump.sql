CREATE TABLE `comments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_adjective` int(10) unsigned DEFAULT NULL COMMENT 'ID прилагательного, которое комментируется',
  `id_user` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'ID проголосовавшего пользователя (0 - аноним)',
  `ip` varchar(15) DEFAULT NULL COMMENT 'IP адрес комментатора',
  `comment` varchar(255) DEFAULT NULL COMMENT 'Комментарий',
  `time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Комментарии прилагательных' AUTO_INCREMENT=1 ;