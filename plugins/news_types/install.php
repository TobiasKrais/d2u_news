<?php

\rex_sql_table::get(\rex::getTable('d2u_news_types'))
    ->ensureColumn(new rex_sql_column('type_id', 'INT(11) unsigned', false, null, 'auto_increment'))
    ->setPrimaryKey('type_id')
    ->ensureColumn(new \rex_sql_column('priority', 'INT(10)', true))
    ->ensure();
\rex_sql_table::get(\rex::getTable('d2u_news_types_lang'))
    ->ensureColumn(new rex_sql_column('type_id', 'INT(11)', false))
    ->ensureColumn(new \rex_sql_column('clang_id', 'INT(11)', false, 1))
    ->setPrimaryKey(['type_id', 'clang_id'])
    ->ensureColumn(new \rex_sql_column('name', 'VARCHAR(255)'))
    ->ensureColumn(new \rex_sql_column('translation_needs_update', 'VARCHAR(7)'))
    ->ensure();

// Alter news table
\rex_sql_table::get(
    \rex::getTable('d2u_news_news'))
    ->ensureColumn(new \rex_sql_column('type_ids', 'VARCHAR(255)'))
    ->alter();
