<?php
// Update language replacements
d2u_news_lang_helper::factory()->install();

// Update modules
if(class_exists(D2UModuleManager)) {
	$modules = [];
	$modules[] = new D2UModule("40-1",
		"D2U News - Ausgabe News",
		1);
	$modules[] = new D2UModule("40-2",
		"D2U News - Ausgabe Messen",
		1);
	$modules[] = new D2UModule("40-3",
		"D2U News - Ausgabe News und Messen",
		1);
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

// remove default lang setting
if (!$this->hasConfig()) {
	$this->removeConfig('default_lang');
}