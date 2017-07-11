<div class="row">
	<div class="col-xs-12">
		<?php
			if(rex_plugin::get('d2u_news', 'fairs')) {
		?>
				<p>In welchem Artikel ist eine komplette Übersicht der Messen zu finden? REX_LINK[id=1 widget=1]</p>
				<br />
		<?php
			}
		?>
		Wie viele News sollen angezeigt werden?
		<input type="number" size="3" name="REX_INPUT_VALUE[1]" value="REX_VALUE[1]" style="max-width: 150px"/>
		<br /><br />
		<p>Alle weiteren Änderungen bitte im <a href="index.php?page=d2u_news/news">D2U News Addon</a> vornehmen.</p>
	</div>
</div>
