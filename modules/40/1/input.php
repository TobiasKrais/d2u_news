<?php
	$sprog = rex_addon::get("sprog");
	$tag_open = $sprog->getConfig('wildcard_open_tag');
	$tag_close = $sprog->getConfig('wildcard_close_tag');
?>
<div class="row">
	<div class="col-xs-12 col-sm-6">Überschrift (optional, Standard: "<?php print \Sprog\Wildcard::get('d2u_news_news'); ?>")</div>
	<div class="col-xs-12 col-sm-6">
		<input type="text" name="REX_INPUT_VALUE[4]" value="REX_VALUE[4]" class="form-control" />
	</div>
</div>
<div class="row">
	<div class="col-xs-12"><br></div>
</div>
<div class="row">
	<div class="col-xs-12 col-sm-6">In welchem Artikel ist eine komplette Übersicht über News und Messen zu finden?</div>
	<div class="col-xs-12 col-sm-6">REX_LINK[id=1 widget=1]</div>
</div>
<div class="row">
	<div class="col-xs-12"><br></div>
</div>
<div class="row">
	<div class="col-xs-12 col-sm-6">Wie viele News sollen angezeigt werden?</div>
	<div class="col-xs-12 col-sm-6">
		<input type="number" size="3" name="REX_INPUT_VALUE[1]" value="REX_VALUE[1]" class="form-control" style="max-width: 100px"/>
	</div>
</div>
<div class="row">
	<div class="col-xs-12"><br></div>
</div>
<?php
	$categories = \D2U_News\Category::getAll(rex_clang::getCurrentId(), true);
	if (count($categories) > 0) {
?>
<div class="row">
	<div class="col-xs-12 col-sm-6">Welche News Kategorie soll angezeigt werden?</div>
	<div class="col-xs-12 col-sm-6">
		<?php
			print '<select name="REX_INPUT_VALUE[2]" class="form-control">';
			print '<option value="0">Nachrichten aller Kategorien anzeigen</option>';
			foreach ($categories as $category) {
				echo '<option value="'. $category->category_id .'" ';
				if ("REX_VALUE[2]" == $category->category_id) {
					echo 'selected="selected" ';
				}
				echo '>'. $category->name .'</option>';
			}
			print '</select>';
		?>
	</div>
</div>
<div class="row">
	<div class="col-xs-12"><br></div>
</div>
<?php
	}
	if(rex_plugin::get('d2u_news', 'news_types')->isAvailable()) {
?>
<div class="row">
	<div class="col-xs-12 col-sm-6">Welche Nachrichtenarten sollen herausgefiltert werden?<br>(Ohne Auswahl werden alle Nachrichten angezeigt.)</div>
	<div class="col-xs-12 col-sm-6">
		<?php
			$selected_types = rex_var::toArray("REX_VALUE[3]");
			$types = \D2U_News\Type::getAll(rex_clang::getCurrentId(), true);
			if (count($types) > 0) {
				print '<select name="REX_INPUT_VALUE[3][]" multiple="multiple" style="width: 100%" size="5" class="form-control">';
				foreach ($types as $type) {
					echo '<option value="'. $type->type_id .'" ';

					if (in_array($type->type_id, $selected_types)) {
						echo 'selected="selected" ';
					}
					echo '>'. $type->name .'</option>';
				}
				print '</select>';
			}
		?>
	</div>
</div>
<div class="row">
	<div class="col-xs-12"><br></div>
</div>
<?php
	}
?>
<div class="row">
	<div class="col-xs-12">
		<p>Alle weiteren Änderungen bitte im <a href="index.php?page=d2u_news/news">D2U News Addon</a> vornehmen.</p>
	</div>
</div>