<?php

// use path relative to __DIR__ to get correct path in update temp dir
$this->includeFile(__DIR__.'/install.php'); /** @phpstan-ignore-line */

\rex_sql_table::get(
    \rex::getTable('d2u_news_fairs'))
    ->removeColumn('updatedate')
    ->removeColumn('updateuser')
    ->ensure();
