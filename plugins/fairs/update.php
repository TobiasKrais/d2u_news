<?php
$sql = rex_sql::factory();

// Update database to 1.1.2
$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_news_fairs` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

\rex_sql_table::get(
    \rex::getTable('d2u_news_fairs'))
    ->removeColumn('updatedate')
    ->removeColumn('updateuser')
    ->ensure();

\rex_sql_table::get(
    \rex::getTable('d2u_news_fairs'))
    ->ensureColumn(new \rex_sql_column('picture', 'VARCHAR(255)', TRUE))
    ->alter();