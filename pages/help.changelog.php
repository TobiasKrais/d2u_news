<?php

?>
<h2>Changelog</h2>
<p>1.2.0-DEV:</p>
<ul>
	<li>Neue Module 40-4 bis 40-6 als Bootstrap-5-Varianten der bestehenden Beispielmodule hinzugefügt.</li>
	<li>Module 40-1 bis 40-3 als "(BS4, deprecated)" markiert. Die BS4-Varianten werden im nächsten Major Release entfernt.</li>
	<li>Bugfix: Prioritäten werden bei Kategorien und News-Typen nach dem Speichern wieder stabil neu durchnummeriert, auch wenn in der Datenbank bereits doppelte Werte vorhanden sind.</li>
	<li>Backend-Listen sortierbar gemacht und Standardsortierungen von SQL-Queries auf <code>rex_list</code>-<code>defaultSort</code> umgestellt.</li>
	<li>Die Priorität von Kategorien und News-Typen kann in den Backend-Listen jetzt direkt per Hoch-/Runter-Buttons geändert werden.</li>
	<li>Bugfix: Extension Point für D2U Helper enthielt einen Fehler wenn das news_types Plugin nicht aktiviert war.</li>
</ul>
<p>1.1.6:</p>
<ul>
	<li>Modul 40-1 "D2U News - Ausgabe News" Link mehr Information entfernt, wenn News Hauptartikel der aktuelle Artikel ist.</li>
	<li>Modul 40-1 "D2U News - Ausgabe News" Link mehr Information für einzelne News hinzugefügt.</li>
	<li>Modul 40-1 "D2U News - Ausgabe News" Abstände zwischen News im CSS eingefügt.</li>
	<li>Anpassungen an D2U Helper Addon &gt;= 2.x.</li>
</ul>
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
	<li>Benötigt Redaxo &gt;= 5.10, da die neue Klasse rex_version verwendet wird.</li>
	<li>Spanische Frontend Übersetzungen aktualisiert.</li>
	<li>Bugfix: beim Speichern von News konnte ein Fehler auftauchen, wenn ein Artikellink entfernt wurde.</li>
	<li>Backend: Einstellungen und Setup Tabs rechts eingeordnet um sie vom Inhalt besser zu unterscheiden.</li>
</ul>
<p>1.1.2:</p>
<ul>
	<li>Methode News-&gt;getUrl() hinzugefügt. Gibt die URL der News zurück, abhängig vom Link Typ.</li>
	<li>Nicht benötigte Felder "updatedate" und "updateuser" in Datenbank entfernt".</li>
	<li>Listen im Backend werden jetzt nicht mehr in Seiten unterteilt.</li>
	<li>Konvertierung der Datenbanktabellen zu utf8mb4.</li>
	<li>Sprachdetails werden ausgeblendet, wenn Speicherung der Sprache nicht vorgesehen ist.</li>
	<li>Bugfix: Categories-&gt;getNews() sortiert jetzt auch absteigend, wie alle anderen getNews() Methoden.</li>
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