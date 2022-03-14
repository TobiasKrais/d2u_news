<?php
// use path relative to __DIR__ to get correct path in update temp dir
$this->includeFile(__DIR__.'/install.php');

// Update modules
if(class_exists('D2UModuleManager')) {
	$modules = [];
	$modules[] = new D2UModule("40-1",
		"D2U News - Ausgabe News",
		6);
	$modules[] = new D2UModule("40-2",
		"D2U News - Ausgabe Messen",
		1);
	$modules[] = new D2UModule("40-3",
		"D2U News - Ausgabe News und Messen",
		5);
	$d2u_module_manager = new D2UModuleManager($modules, "", "d2u_news");
	$d2u_module_manager->autoupdate();
}

// Remove old columns
\rex_sql_table::get(
    \rex::getTable('d2u_news_news_lang'))
    ->removeColumn('updatedate')
    ->removeColumn('updateuser')
    ->ensure();
\rex_sql_table::get(
    \rex::getTable('d2u_news_categories_lang'))
    ->removeColumn('updatedate')
    ->removeColumn('updateuser')
    ->ensure();

// remove default lang setting
if ($this->hasConfig('default_lang')) {
	$this->removeConfig('default_lang');
}