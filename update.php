<?php
// Update language replacements
d2u_news_lang_helper::factory()->install();

// Update modules
if(class_exists('D2UModuleManager')) {
	$modules = [];
	$modules[] = new D2UModule("40-1",
		"D2U News - Ausgabe News",
		5);
	$modules[] = new D2UModule("40-2",
		"D2U News - Ausgabe Messen",
		1);
	$modules[] = new D2UModule("40-3",
		"D2U News - Ausgabe News und Messen",
		4);
	$d2u_module_manager = new D2UModuleManager($modules, "", "d2u_news");
	$d2u_module_manager->autoupdate();
}

// 1.0.1 Update database
$sql = rex_sql::factory();
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_news_news LIKE 'url';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_news_news "
		. "ADD url varchar(255) collate utf8_general_ci default NULL AFTER article_id;");
}
// 1.1.0
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_news_categories (
	category_id int(10) unsigned NOT NULL auto_increment,
	priority int(10) default NULL,
	picture varchar(255) collate utf8_general_ci default NULL,
	PRIMARY KEY (category_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;");
$sql->setQuery("CREATE TABLE IF NOT EXISTS ". \rex::getTablePrefix() ."d2u_news_categories_lang (
	category_id int(10) NOT NULL,
	clang_id int(10) NOT NULL,
	name varchar(255) collate utf8_general_ci default NULL,
	translation_needs_update varchar(7) collate utf8_general_ci default NULL,
	updatedate int(11) default NULL,
	updateuser varchar(255) collate utf8_general_ci default NULL,
	PRIMARY KEY (category_id, clang_id)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1;");
$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_news_news LIKE 'category_ids';");
if($sql->getRows() == 0) {
	$sql->setQuery("ALTER TABLE ". \rex::getTablePrefix() ."d2u_news_news "
		. "ADD category_ids varchar(255) collate utf8_general_ci default NULL AFTER news_id;");
}

// remove default lang setting
if ($this->hasConfig('default_lang')) {
	$this->removeConfig('default_lang');
}

// Standard settings
if (!$this->hasConfig()) {
    $this->setConfig('default_sort', "name");
}