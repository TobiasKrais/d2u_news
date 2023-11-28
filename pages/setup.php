<?php
/*
 * Modules
 */
$d2u_module_manager = new D2UModuleManager(D2UNewsModules::getModules(), 'modules/', 'd2u_news');

// D2UModuleManager actions
$d2u_module_id = rex_request('d2u_module_id', 'string');
$paired_module = (int) rex_request('pair_'. $d2u_module_id, 'int');
$function = rex_request('function', 'string');
if ('' !== $d2u_module_id) {
    $d2u_module_manager->doActions($d2u_module_id, $function, $paired_module);
}

// D2UModuleManager show list
$d2u_module_manager->showManagerList();

/*
 * Templates
 */
?>
<h2>Beispielseiten</h2>
<ul>
	<li>News Addon: <a href="http://www.kaltenbach.com" target="_blank">
		www.kaltenbach.com</a>.</li>
</ul>
<h2>Support</h2>
<p>Fehlermeldungen bitte im <a href="https://github.com/TobiasKrais/d2u_news" target="_blank">GitHub Repository</a> melden.</p>
<h2>Changelog</h2>
<p>1.1.5:</p>
<ul>
	<li>PHP-CS-Fixer Code Verbesserungen.</li>
	<li>Bugfix Module 40-1 und 40-3: Fehler bei Verwendung des news_types Plugins, wenn noch keine Typen angelegt waren.</li>
	<li>Ein paar erste rexstan Anpassungen.</li>
	<li>Minimale PHP Version auf 7.4 angepasst.</li>
	<li>.github Verzeichnis aus Installer Action ausgeschlossen.</li>
</ul>
<p>1.1.4:</p>
<ul>
	<li>Anpassungen an Publish Github Release to Redaxo.</li>
	<li>News können nun für einzelne Sprachen ausgeblendet werden.</li>
	<li>News können nun zu Veranstaltungen des D2U Veranstaltungen Addons verlinken.</li>
	<li>Kategoriezuordnung wird ab sofort in der Newsübersicht angezeigt.</li>
	<li>Messen Plugin: Messen haben nun auch ein Feld für ein Bild.</li>
	<li>Bugfix: Beim Löschen von Medien die vom Addon verlinkt werden wurde der Name der verlinkenden Quelle in der Warnmeldung nicht immer korrekt angegeben.</li>
	<li>Module 40-3: Eingabefeld an Redaxo 13 Dark Mode angepasst.</li>
</ul>
<p>1.1.3:</p>
<ul>
	<li>Modul 40-1 "D2U News - Ausgabe News" verwendet ein zusätzliches div um Linie unter oder über die News setzen zu können.</li>
	<li>Benötigt Redaxo >= 5.10, da die neue Klasse rex_version verwendet wird.</li>
	<li>Spanische Frontend Übersetzungen aktualisiert.</li>
	<li>Bugfix: beim Speichern von News konnte ein Fehler auftauchen, wenn ein Artikellink entfernt wurde.</li>
	<li>Backend: Einstellungen und Setup Tabs rechts eingeordnet um sie vom Inhalt besser zu unterscheiden.</li>
</ul>
<p>1.1.2:</p>
<ul>
	<li>Methode News->getUrl() hinzugefügt. Gibt die URL der News zurück, abhängig vom Link Typ.</li>
	<li>Nicht benötigte Felder "updatedate" und "updateuser" in Datenbank entfernt".</li>
	<li>Listen im Backend werden jetzt nicht mehr in Seiten unterteilt.</li>
	<li>Konvertierung der Datenbanktabellen zu utf8mb4.</li>
	<li>Sprachdetails werden ausgeblendet, wenn Speicherung der Sprache nicht vorgesehen ist.</li>
	<li>Bugfix: Categories->getNews() sortiert jetzt auch absteigend, wie alle anderen getNews() Methoden.</li>
	<li>Bugfix: Sortierung der News enthielt Fehler.</li>
	<li>Bugfix: Prioritäten wurden beim Löschen nicht reorganisiert.</li>
</ul>
<p>1.1.1:</p>
<ul>
	<li>Bugfix: Deaktiviertes Addon zu deinstallieren führte zu fatal error.</li>
	<li>In den Einstellungen gibt es jetzt eine Option, eigene Übersetzungen in SProg dauerhaft zu erhalten.</li>
	<li>Messe Plugin erlaubt im Namen und Ort nun einfache Anführungszeichen.</li>
	<li>Module 40-1 und 40-3: alt Tag des Bildes war ohne Anführungszeichen.</li>
	<li>Bugfix: Löschen von Sprachen schlug fehl.</li>
	<li>Module 40-1: Optional kann individuelle Überschrift eingegeben werden. Überschriften wurden um eine Kategorie herabgestuft.</li>
	<li>Bugfix Module: manchmal wurden weniger als die definierte Anzahl News ausgegeben.</li>
</ul>
<p>1.1.0:</p>
<ul>
	<li>Namespace "D2U_News" eingeführt. ACHTUNG: Module müssen angepasst werden!</li>
	<li>Plugin Nachrichtenarten hinzugefügt.</li>
	<li>News Kategorien hinzugefügt.</li>
</ul>
<p>1.0.3:</p>
<ul>
	<li>Bugfix: Fehler beim Speichern von Namen mit einfachem Anführungszeichen behoben.</li>
	<li>Abhängigkeit von url und yrewrite Addon aufgehoben.</li>
</ul>
<p>1.0.2:</p>
<ul>
	<li>Möglichkeit in D2U Helper Addon Liebling WYSIWYG Editor auszuwählen.</li>
	<li>Englisches Backend hinzugefügt.</li>
	<li>Link nun auch auf externe URLs möglich.</li>
</ul>
<p>1.0.1:</p>
<ul>
	<li>Übersetzungshilfe aus D2U Helper integriert.</li>
	<li>Bugfix: Speichern wenn zweite Sprache Standardsprache ist schlug fehl.</li>
	<li>Bugfix: Deinstallieren der Übersetzungen beim deinstallieren des Addons schlug fehl.</li>
	<li>Editierrechte für Übersetzer eingeschränkt.</li>
</ul>
<p>1.0.0:</p>
<ul>
	<li>Initiale Version.</li>
</ul>