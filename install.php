<?php
$sql = rex_sql::factory();
// Install database
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". rex::getTablePrefix() ."d2u_news_news (
	news_id int(10) unsigned NOT NULL auto_increment,
	category_ids varchar(255) collate utf8mb4_unicode_ci default NULL,
	picture varchar(255) collate utf8mb4_unicode_ci default NULL,
	link_type varchar(15) collate utf8mb4_unicode_ci default NULL,
	article_id int(10) default NULL,
	url varchar(255) collate utf8mb4_unicode_ci default NULL,
	d2u_machines_machine_id int(10) default NULL,
	online_status varchar(10) collate utf8mb4_unicode_ci default 'online',
	`date` varchar(10) collate utf8mb4_unicode_ci default NULL,
	PRIMARY KEY (news_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". rex::getTablePrefix() ."d2u_news_news_lang (
	news_id int(10) NOT NULL,
	clang_id int(10) NOT NULL,
	name varchar(255) collate utf8mb4_unicode_ci default NULL,
	teaser text collate utf8mb4_unicode_ci default NULL,
	translation_needs_update varchar(7) collate utf8mb4_unicode_ci default NULL,
	PRIMARY KEY (news_id, clang_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");

$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_news_categories (
	category_id int(10) unsigned NOT NULL auto_increment,
	priority int(10) default NULL,
	picture varchar(255) collate utf8mb4_unicode_ci default NULL,
	PRIMARY KEY (category_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_news_categories_lang (
	category_id int(10) NOT NULL,
	clang_id int(10) NOT NULL,
	name varchar(255) collate utf8mb4_unicode_ci default NULL,
	translation_needs_update varchar(7) collate utf8mb4_unicode_ci default NULL,
	PRIMARY KEY (category_id, clang_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1;");

// Insert frontend translations
if(class_exists(d2u_news_lang_helper)) {
	d2u_news_lang_helper::factory()->install();
}

// Standard settings
if (!$this->hasConfig()) {
    $this->setConfig('default_sort', "name");
}