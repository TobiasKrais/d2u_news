<?php
\rex_sql_table::get(\rex::getTable('d2u_news_news'))
	->ensureColumn(new rex_sql_column('news_id', 'int(10) unsigned', false, null, 'auto_increment'))
	->setPrimaryKey('news_id')
	->ensureColumn(new \rex_sql_column('category_ids', 'VARCHAR(255)', true))
    ->ensureColumn(new \rex_sql_column('picture', 'VARCHAR(255)', true))
    ->ensureColumn(new \rex_sql_column('link_type', 'VARCHAR(15)'))
    ->ensureColumn(new \rex_sql_column('article_id', 'INT(10)', true, 0))
    ->ensureColumn(new \rex_sql_column('url', 'VARCHAR(255)', true))
    ->ensureColumn(new \rex_sql_column('d2u_machines_machine_id', 'INT(10)', true, 0))
    ->ensureColumn(new \rex_sql_column('d2u_courses_course_id', 'INT(10)', true, 0))
    ->ensureColumn(new \rex_sql_column('online_status', 'VARCHAR(10)', true))
    ->ensureColumn(new \rex_sql_column('date', 'VARCHAR(10)'))
    ->ensure();
\rex_sql_table::get(\rex::getTable('d2u_news_news_lang'))
	->ensureColumn(new rex_sql_column('news_id', 'int(10) unsigned', false, null, 'auto_increment'))
    ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false, 1))
	->setPrimaryKey(['news_id', 'clang_id'])
    ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('teaser', 'TEXT'))
    ->ensureColumn(new \rex_sql_column('hide_this_lang', 'TINYINT(1)', true))
    ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)'))
    ->ensure();

\rex_sql_table::get(\rex::getTable('d2u_news_categories'))
	->ensureColumn(new rex_sql_column('category_id', 'int(10) unsigned', false, null, 'auto_increment'))
	->setPrimaryKey('category_id')
    ->ensureColumn(new \rex_sql_column('priority', 'INT(10)', true))
    ->ensureColumn(new \rex_sql_column('picture', 'VARCHAR(255)', true))
    ->ensure();
\rex_sql_table::get(\rex::getTable('d2u_news_categories_lang'))
	->ensureColumn(new rex_sql_column('category_id', 'int(10) unsigned', false, null, 'auto_increment'))
    ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false, 1))
	->setPrimaryKey(['category_id', 'clang_id'])
    ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)'))
    ->ensure();

// Update language replacements
if(!class_exists('d2u_news_lang_helper')) {
	// Load class in case addon is deactivated
	require_once 'lib/d2u_news_lang_helper.php';
}
d2u_news_lang_helper::factory()->install();

// Standard settings
if (!$this->hasConfig()) {
    $this->setConfig('default_sort', "name");
}