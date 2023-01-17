<?php
$func = rex_request('func', 'string');
$entry_id = rex_request('entry_id', 'int');
$message = rex_get('message', 'string');

// Print comments
if($message !== '') {
	print rex_view::success(rex_i18n::msg($message));
}

// save settings
if (intval(filter_input(INPUT_POST, "btn_save")) === 1 || intval(filter_input(INPUT_POST, "btn_apply")) === 1) {
	// Media fields and links need special treatment
	$input_media = rex_post('REX_INPUT_MEDIA', 'array', []);

	$form = rex_post('form', 'array', []);

	$fair = new \D2U_News\Fair($form['fair_id']);
	$fair->name = $form['name'];
	$fair->city = $form['city'];
	$fair->country_code = $form['country_code'];
	$fair->date_start = $form['date_start'];
	$fair->date_end = $form['date_end'];
	$fair->picture = $input_media[1];

	// message output
	$message = 'form_save_error';
	if($fair->save() == 0) {
		$message = 'form_saved';
	}
	
	// Redirect to make reload and thus double save impossible
	if(intval(filter_input(INPUT_POST, "btn_apply", FILTER_VALIDATE_INT)) === 1 &&$fair !== false) {
		header("Location: ". rex_url::currentBackendPage(array("entry_id"=>$fair->fair_id, "func"=>'edit', "message"=>$message), false));
	}
	else {
		header("Location: ". rex_url::currentBackendPage(["message"=>$message], false));
	}
	exit;
}
// Delete
else if(intval(filter_input(INPUT_POST, "btn_delete", FILTER_VALIDATE_INT)) === 1 || $func === 'delete') {
	$fair_id = $entry_id;
	if($fair_id === 0) {
		$form = rex_post('form', 'array', []);
		$fair_id = $form['fair_id'];
	}
	$fair = new \D2U_News\Fair($fair_id);
	$fair->delete();

	$func = '';
}

// Eingabeformular
if ($func === 'edit' || $func === 'add') {
?>
	<form action="<?php print rex_url::currentBackendPage(); ?>" method="post">
		<div class="panel panel-edit">
			<header class="panel-heading"><div class="panel-title"><?php print rex_i18n::msg('d2u_news_fairs'); ?></div></header>
			<div class="panel-body">
				<input type="hidden" name="form[fair_id]" value="<?php echo $entry_id; ?>">
				<fieldset>
					<legend><?php echo rex_i18n::msg('d2u_news_fairs'); ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
							$fair = new \D2U_News\Fair($entry_id);
							$readonly = true;
							if(rex::getUser() instanceof rex_user && (rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_news[edit_data]'))) {
								$readonly = false;
							}
							
							d2u_addon_backend_helper::form_input('d2u_news_name', 'form[name]', $fair->name, true, $readonly);
							d2u_addon_backend_helper::form_input('d2u_news_fairs_city', 'form[city]', $fair->city, true, $readonly);
							d2u_addon_backend_helper::form_input('d2u_news_fairs_country_code', 'form[country_code]', $fair->country_code, true, $readonly);
							d2u_addon_backend_helper::form_input('d2u_news_fairs_date_start', 'form[date_start]', $fair->date_start, true, $readonly, 'date');
							d2u_addon_backend_helper::form_input('d2u_news_house_date_end', 'form[date_end]', $fair->date_end, true, $readonly, 'date');
							d2u_addon_backend_helper::form_mediafield('d2u_helper_picture', '1', $fair->picture, $readonly);
						?>
					</div>
				</fieldset>
			</div>
			<footer class="panel-footer">
				<div class="rex-form-panel-footer">
					<div class="btn-toolbar">
						<button class="btn btn-save rex-form-aligned" type="submit" name="btn_save" value="1"><?php echo rex_i18n::msg('form_save'); ?></button>
						<button class="btn btn-apply" type="submit" name="btn_apply" value="1"><?php echo rex_i18n::msg('form_apply'); ?></button>
						<button class="btn btn-abort" type="submit" name="btn_abort" formnovalidate="formnovalidate" value="1"><?php echo rex_i18n::msg('form_abort'); ?></button>
						<?php
							if(rex::getUser() instanceof rex_user && (rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_news[edit_data]'))) {
								print '<button class="btn btn-delete" type="submit" name="btn_delete" formnovalidate="formnovalidate" data-confirm="'. rex_i18n::msg('form_delete') .'?" value="1">'. rex_i18n::msg('form_delete') .'</button>';
							}
						?>
					</div>
				</div>
			</footer>
		</div>
	</form>
	<br>
	<script>
		jQuery(document).ready(function($) {
			$('legend').each(function() {
				$(this).addClass('open');
				$(this).next('.panel-body-wrapper.slide').slideToggle();
			});
		});
	</script>
	<?php
		print d2u_addon_backend_helper::getCSS();
//		print d2u_addon_backend_helper::getJS();
}

if ($func === '') {
	$query = 'SELECT fair_id, name, city, date_start, date_end '
		. 'FROM '. rex::getTablePrefix() .'d2u_news_fairs '
		. 'ORDER BY date_start DESC';
    $list = rex_list::factory($query, 1000);

    $list->addTableAttribute('class', 'table-striped table-hover');

    $tdIcon = '<i class="rex-icon fa-university"></i>';
    $thIcon = '<a href="' . $list->getUrl(['func' => 'add']) . '" title="' . rex_i18n::msg('add') . '"><i class="rex-icon rex-icon-add-module"></i></a>';
    $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['func' => 'edit', 'entry_id' => '###fair_id###']);

    $list->setColumnLabel('fair_id', rex_i18n::msg('id'));
    $list->setColumnLayout('fair_id', ['<th class="rex-table-id">###VALUE###</th>', '<td class="rex-table-id">###VALUE###</td>']);

    $list->setColumnLabel('firstname', rex_i18n::msg('d2u_news_fairs_firstname'));
    $list->setColumnParams('firstname', ['func' => 'edit', 'entry_id' => '###fair_id###']);

    $list->setColumnLabel('lastname', rex_i18n::msg('d2u_news_fairs_lastname'));
    $list->setColumnParams('lastname', ['func' => 'edit', 'entry_id' => '###fair_id###']);

    $list->setColumnLabel('company', rex_i18n::msg('d2u_news_fairs_company'));

    $list->addColumn(rex_i18n::msg('module_functions'), '<i class="rex-icon rex-icon-edit"></i> ' . rex_i18n::msg('edit'));
    $list->setColumnLayout(rex_i18n::msg('module_functions'), ['<th class="rex-table-action" colspan="2">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('module_functions'), ['func' => 'edit', 'entry_id' => '###fair_id###']);

 	if(rex::getUser() instanceof rex_user && (rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_news[edit_data]'))) {
		$list->addColumn(rex_i18n::msg('delete_module'), '<i class="rex-icon rex-icon-delete"></i> ' . rex_i18n::msg('delete'));
		$list->setColumnLayout(rex_i18n::msg('delete_module'), ['', '<td class="rex-table-action">###VALUE###</td>']);
		$list->setColumnParams(rex_i18n::msg('delete_module'), ['func' => 'delete', 'entry_id' => '###fair_id###']);
		$list->addLinkAttribute(rex_i18n::msg('delete_module'), 'data-confirm', rex_i18n::msg('d2u_helper_confirm_delete'));
	}

    $list->setNoRowsMessage(rex_i18n::msg('d2u_news_fairs_no_fairs_found'));

    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('d2u_news_fairs'), false);
    $fragment->setVar('content', $list->get(), false);
    echo $fragment->parse('core/page/section.php');
}