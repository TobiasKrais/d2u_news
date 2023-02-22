<?php

$sql = rex_sql::factory();

// Delete tables
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_news_types');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'd2u_news_types_lang');

\rex_sql_table::get(
    \rex::getTable('d2u_news_news'))
    ->removeColumn('type_ids')
    ->ensure();
