<?php
/*
 * Modules
 */
$d2u_module_manager = new D2UModuleManager(D2UNewsModules::getModules(), "modules/", "d2u_news");

// D2UModuleManager actions
$d2u_module_id = rex_request('d2u_module_id', 'string');
$paired_module = rex_request('pair_'. $d2u_module_id, 'int');
$function = rex_request('function', 'string');
if($d2u_module_id != "") {
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
<p>1.1.2-DEV:</p>
<ul>
	<li>...</li>
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