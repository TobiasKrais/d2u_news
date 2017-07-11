<?php
$sql = rex_sql::factory();
// Install database
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". rex::getTablePrefix() ."d2u_news_fairs (
	fair_id int(10) unsigned NOT NULL auto_increment,
	name varchar(255) collate utf8_general_ci default NULL,
	city varchar(255) collate utf8_general_ci default NULL,
	country_code varchar(3) collate utf8_general_ci default NULL,
	date_start varchar(10) collate utf8_general_ci default NULL,
	date_end varchar(10) collate utf8_general_ci default NULL,
	updatedate int(11) default NULL,
	updateuser varchar(255) collate utf8_general_ci default NULL,
	PRIMARY KEY (fair_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;");