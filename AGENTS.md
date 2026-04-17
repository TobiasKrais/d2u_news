# D2U News - Agent Notes

Nur projektspezifische Regeln, die für KI-Arbeit relevant sind.

## Kernregeln

- Namespaces: `D2U_News` für Datenklassen, `TobiasKrais\D2UNews` für Modulklassen
- Einrückung: 4 Spaces in PHP-Klassen, Tabs in Moduldateien
- Kommentare nur auf Englisch
- Frontend-Labels über `Sprog\Wildcard::get()`, Backend-Labels über `rex_i18n::msg()` mit Keys aus `lang/`

## Wichtige Projekthinweise

- Backend-Translation-Keys müssen in allen Sprachdateien unter `lang/` synchron bleiben.
- Wenn Module unter `modules/40/*` geändert werden, Changelog in `pages/help.changelog.php` prüfen oder aktualisieren und die Revisionsnummer in `lib/d2u_news_module_manager.php` nur einmal pro Release erhöhen.
- Versionshinweise für Module: Wenn die Zielversion im Changelog bereits `-DEV` trägt, innerhalb derselben Entwicklungsphase keine weitere Revisionsnummer für dasselbe Modul hochzählen. Erst mit der nächsten Release-Version wieder erneut erhöhen.
- In Changelog-Dateien, AGENTS.md und README.md sind Umlaute erlaubt und müssen nicht auf ASCII umgeschrieben werden.

## Pflege

- Diese Datei kurz und handlungsorientiert halten.
- Neue Einträge nur aufnehmen, wenn sie wiederkehrende Stolperfallen, verbindliche Projektkonventionen oder agentenrelevante Workflows betreffen.
