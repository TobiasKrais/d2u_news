<?php
$sql = rex_sql::factory();
// Install database
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". rex::getTablePrefix() ."d2u_news_news (
	news_id int(10) unsigned NOT NULL auto_increment,
	picture varchar(255) collate utf8_general_ci default NULL,
	link_type varchar(15) collate utf8_general_ci default NULL,
	article_id int(10) default NULL,
	d2u_machines_machine_id int(10) default NULL,
	online_status varchar(10) collate utf8_general_ci default 'online',
	`date` varchar(10) collate utf8_general_ci default NULL,
	PRIMARY KEY (news_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;");
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". rex::getTablePrefix() ."d2u_news_news_lang (
	news_id int(10) NOT NULL,
	clang_id int(10) NOT NULL,
	name varchar(255) collate utf8_general_ci default NULL,
	teaser text collate utf8_general_ci default NULL,
	translation_needs_update varchar(7) collate utf8_general_ci default NULL,
	updatedate int(11) default NULL,
	updateuser varchar(255) collate utf8_general_ci default NULL,
	PRIMARY KEY (news_id, clang_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;");

// Insert frontend translations
d2u_news_lang_helper::factory()->install();

// Init Config
if (!$this->hasConfig()) {
	$this->setConfig('default_lang', rex_clang::getStartId());
}