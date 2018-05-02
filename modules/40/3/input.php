<?php
	if(rex_plugin::get('d2u_news', 'fairs')) {
?>
<div class="row">
	<div class="col-xs-12">
		<p>In welchem Artikel ist eine komplette Übersicht der Messen zu finden? REX_LINK[id=1 widget=1]</p>
		<br />
	</div>
</div>
<?php
	}
?>
<div class="row">
	<div class="col-xs-12">
		Wie viele News sollen angezeigt werden?
		<input type="number" size="3" name="REX_INPUT_VALUE[1]" value="REX_VALUE[1]" style="max-width: 150px"/>
		<br /><br />
	</div>
</div><div class="row">
	<div class="col-xs-12">
		<?php
			$categories = \D2U_News\Category::getAll(rex_clang::getCurrentId(), TRUE);
			if (count($categories) > 0) {
				print 'Welche News Kategorie soll angezeigt werden? <select name="REX_INPUT_VALUE[2]" style="max-width: 500px;">';
				print '<option value="0">Nachrichten aller Kategorien anzeigen</option>';
				foreach ($categories as $category) {
					echo '<option value="'. $category->category_id .'" ';

					if ("REX_VALUE[2]" == $category->category_id) {
						echo 'selected="selected" ';
					}
					echo '>'. $category->name .'</option>';
				}
				print '</select>';
			}
			print "<br /><br />";
		?>
	</div>
</div>
<div class="row">
	<div class="col-xs-12">
		<p>Alle weiteren Änderungen bitte im <a href="index.php?page=d2u_news/news">D2U News Addon</a> vornehmen.</p>
	</div>
</div>