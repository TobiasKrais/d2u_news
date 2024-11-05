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
    ->ensureColumn(new rex_sql_column('news_id', 'INT(11)', false))
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
    ->ensureColumn(new rex_sql_column('category_id', 'INT(11)', false))
    ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false, 1))
    ->setPrimaryKey(['category_id', 'clang_id'])
    ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)'))
    ->ensure();

// Update language replacements
if (!class_exists(d2u_news_lang_helper::class)) {
    // Load class in case addon is deactivated
    require_once 'lib/d2u_news_lang_helper.php';
}
d2u_news_lang_helper::factory()->install();

// remove default lang setting
if ($this->hasConfig('default_lang')) {
    $this->removeConfig('default_lang');
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

// Update modules
if (class_exists(TobiasKrais\D2UHelper\ModuleManager::class)) {
    $modules = [];
    $modules[] = new \TobiasKrais\D2UHelper\Module('40-1',
        'D2U News - Ausgabe News',
        7);
    $modules[] = new \TobiasKrais\D2UHelper\Module('40-2',
        'D2U News - Ausgabe Messen',
        1);
    $modules[] = new \TobiasKrais\D2UHelper\Module('40-3',
        'D2U News - Ausgabe News und Messen',
        6);
    $d2u_module_manager = new \TobiasKrais\D2UHelper\ModuleManager($modules, '', 'd2u_news');
    $d2u_module_manager->autoupdate();
}
