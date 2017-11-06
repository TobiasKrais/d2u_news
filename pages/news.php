<?php
$func = rex_request('func', 'string');
$entry_id = rex_request('entry_id', 'int');
$message = rex_get('message', 'string');

// Print comments
if($message != "") {
	print rex_view::success(rex_i18n::msg($message));
}

// save settings
if (filter_input(INPUT_POST, "btn_save") == 1 || filter_input(INPUT_POST, "btn_apply") == 1) {
	$form = (array) rex_post('form', 'array', []);

	// Media fields and links need special treatment
	$input_media = (array) rex_post('REX_INPUT_MEDIA', 'array', array());
	$link_ids = filter_input_array(INPUT_POST, array('REX_INPUT_LINK'=> array('filter' => FILTER_VALIDATE_INT, 'flags' => FILTER_REQUIRE_ARRAY)));

	$success = TRUE;
	$news = FALSE;
	$news_id = $form['news_id'];
	foreach(rex_clang::getAll() as $rex_clang) {
		if($news === FALSE) {
			$news = new News($news_id, $rex_clang->getId());
			$news->news_id = $news_id; // Ensure correct ID in case first language has no object
			$news->picture = $input_media[1];
			$news->article_id = $link_ids["REX_INPUT_LINK"][1];
			$news->date = $form['date'];
			$news->online_status = array_key_exists('online_status', $form) ? "online" : "offline";
		}
		else {
			$news->clang_id = $rex_clang->getId();
		}
		$news->name = $form['lang'][$rex_clang->getId()]['name'];
		$news->teaser = $form['lang'][$rex_clang->getId()]['teaser'];
		$news->translation_needs_update = $form['lang'][$rex_clang->getId()]['translation_needs_update'];
		
		if($news->translation_needs_update == "delete") {
			$news->delete(FALSE);
		}
		else if($news->save() > 0){
			$success = FALSE;
		}
		else {
			// remember id, for each database lang object needs same id
			$news_id = $news->news_id;
		}
	}

	// message output
	$message = 'form_save_error';
	if($success) {
		$message = 'form_saved';
	}
	
	// Redirect to make reload and thus double save impossible
	if(filter_input(INPUT_POST, "btn_apply") == 1 && $news !== FALSE) {
		header("Location: ". rex_url::currentBackendPage(array("entry_id"=>$news->news_id, "func"=>'edit', "message"=>$message), FALSE));
	}
	else {
		header("Location: ". rex_url::currentBackendPage(array("message"=>$message), FALSE));
	}
	exit;
}
// Delete
else if(filter_input(INPUT_POST, "btn_delete") == 1 || $func == 'delete') {
	$news_id = $entry_id;
	if($news_id == 0) {
		$form = (array) rex_post('form', 'array', []);
		$news_id = $form['news_id'];
	}
	$news = new News($news_id, rex_config::get("d2u_helper", "default_lang"));
	$news->news_id = $news_id; // Ensure correct ID in case first language has no object
	$news->delete();
	
	$func = '';
}
// Change online status of news
else if($func == 'changestatus') {
	$news_id = $entry_id;
	$news = new News($news_id, rex_config::get("d2u_helper", "default_lang"));
	$news->news_id = $news_id; // Ensure correct ID in case first language has no object
	$news->changeStatus();
	
	header("Location: ". rex_url::currentBackendPage());
	exit;
}

// Form
if ($func == 'edit' || $func == 'add') {
?>
	<form action="<?php print rex_url::currentBackendPage(); ?>" method="post">
		<div class="panel panel-edit">
			<header class="panel-heading"><div class="panel-title"><?php print rex_i18n::msg('d2u_news_news'); ?></div></header>
			<div class="panel-body">
				<input type="hidden" name="form[news_id]" value="<?php echo $entry_id; ?>">
				<?php
					foreach(rex_clang::getAll() as $rex_clang) {
						$news = new News($entry_id, $rex_clang->getId());
						$required = $rex_clang->getId() == rex_config::get("d2u_helper", "default_lang") ? TRUE : FALSE;
						
						$readonly_lang = TRUE;
						if(rex::getUser()->isAdmin() || (rex::getUser()->hasPerm('d2u_news[edit_lang]') && rex::getUser()->getComplexPerm('clang')->hasPerm($rex_clang->getId()))) {
							$readonly_lang = FALSE;
						}
				?>
					<fieldset>
						<legend><?php echo rex_i18n::msg('d2u_helper_text_lang') .' "'. $rex_clang->getName() .'"'; ?></legend>
						<div class="panel-body-wrapper slide">
							<?php
								if($rex_clang->getId() != rex_config::get("d2u_helper", "default_lang")) {
									$options_translations = [];
									$options_translations["yes"] = rex_i18n::msg('d2u_helper_translation_needs_update');
									$options_translations["no"] = rex_i18n::msg('d2u_helper_translation_is_uptodate');
									$options_translations["delete"] = rex_i18n::msg('d2u_helper_translation_delete');
									d2u_addon_backend_helper::form_select('d2u_helper_translation', 'form[lang]['. $rex_clang->getId() .'][translation_needs_update]', $options_translations, array($news->translation_needs_update), 1, FALSE, $readonly_lang);
								}
								else {
									print '<input type="hidden" name="form[lang]['. $rex_clang->getId() .'][translation_needs_update]" value="">';
								}
								
								d2u_addon_backend_helper::form_input('d2u_news_name', "form[lang][". $rex_clang->getId() ."][name]", $news->name, $required, $readonly_lang, "text");
								d2u_addon_backend_helper::form_textarea('d2u_news_teaser', "form[lang][". $rex_clang->getId() ."][teaser]", $news->teaser, 5, FALSE, $readonly_lang, TRUE);
							?>
						</div>
					</fieldset>
				<?php
					}
				?>
				<fieldset>
					<legend><?php echo rex_i18n::msg('d2u_helper_data_all_lang'); ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
							// Do not use last object from translations, because you don't know if it exists in DB
							$news = new News($entry_id, rex_config::get("d2u_helper", "default_lang"));
							$readonly = TRUE;
							if(rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_news[edit_data]')) {
								$readonly = FALSE;
							}
							
							d2u_addon_backend_helper::form_mediafield('d2u_helper_picture', '1', $news->picture, $readonly);
							if(rex_addon::get('d2u_machinery')->isAvailable() && count(Machine::getAll(rex_config::get("d2u_helper", "default_lang"), TRUE)) > 0) {
								$options_link_type = [];
								$options_link_type["none"] = rex_i18n::msg('d2u_news_no_link');
								$options_link_type["article"] = rex_i18n::msg('d2u_news_article');
								$options_link_type["machine"] = rex_i18n::msg('d2u_news_machine');
								d2u_addon_backend_helper::form_select('d2u_news_link_type', 'form[link_type]', $options_link_type, array($news->link_type), 1, FALSE, $readonly_lang);
								$options_machines = [];
								foreach(Machine::getAll(rex_config::get("d2u_helper", "default_lang"), TRUE) as $machine) {
									$options_machines[$machine->machine_id] = $machine->name;
								}
								d2u_addon_backend_helper::form_select('d2u_news_machine', 'form[d2u_machines_machine_id]', $options_machines, array($news->d2u_machines_machine_id), 1, FALSE, $readonly_lang);
						?>
						<script>
							function changeType() {
								if($('select[name="form\\[link_type\\]"]').val() === "article") {
									$('#LINK_1').show();
									$('#form\\[d2u_machines_machine_id\\]').hide();
								}
								else if($('select[name="form\\[link_type\\]"]').val() === "machine") {
									$('#LINK_1').hide();
									$('#form\\[d2u_machines_machine_id\\]').show();
								}
								else if($('select[name="form\\[link_type\\]"]').val() === "none") {
									$('#LINK_1').hide();
									$('#form\\[d2u_machines_machine_id\\]').hide();
								}
							}
							
							// On init
							changeType();
							// On change
							$('select[name="form\\[link_type\\]"]').on('change', function() {
								changeType();
							});
						</script>
						<?php
							}
							else {
								print '<input type="hidden" name="form[link_type]" value="article">';
							}
							d2u_addon_backend_helper::form_linkfield('d2u_news_article', '1', $news->article_id, rex_config::get("d2u_helper", "default_lang", rex_clang::getStartId()));
							d2u_addon_backend_helper::form_checkbox('d2u_helper_online_status', 'form[online_status]', 'online', $news->online_status == "online", $readonly);
							d2u_addon_backend_helper::form_input('d2u_news_date', "form[date]", $news->date, TRUE, $readonly, "date");
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
						<button class="btn btn-delete" type="submit" name="btn_delete" formnovalidate="formnovalidate" data-confirm="<?php echo rex_i18n::msg('form_delete'); ?>?" value="1"><?php echo rex_i18n::msg('form_delete'); ?></button>
					</div>
				</div>
			</footer>
		</div>
	</form>
	<br>
	<?php
		print d2u_addon_backend_helper::getCSS();
		print d2u_addon_backend_helper::getJS();
}

if ($func == '') {
	$query = 'SELECT refs.news_id, name, online_status, `date` '
		. 'FROM '. rex::getTablePrefix() .'d2u_news_news AS refs '
		. 'LEFT JOIN '. rex::getTablePrefix() .'d2u_news_news_lang AS lang '
			. 'ON refs.news_id = lang.news_id AND lang.clang_id = '. rex_config::get("d2u_helper", "default_lang") .' '
		.'ORDER BY `date` DESC';
    $list = rex_list::factory($query);

    $list->addTableAttribute('class', 'table-striped table-hover');

    $tdIcon = '<i class="rex-icon fa-newspaper-o"></i>';
	$thIcon = "";
	if(rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_news[edit_data]')) {
	    $thIcon = '<a href="' . $list->getUrl(['func' => 'add']) . '" title="' . rex_i18n::msg('add') . '"><i class="rex-icon rex-icon-add-module"></i></a>';
	}
    $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['func' => 'edit', 'entry_id' => '###news_id###']);

    $list->setColumnLabel('news_id', rex_i18n::msg('id'));
    $list->setColumnLayout('news_id', ['<th class="rex-table-id">###VALUE###</th>', '<td class="rex-table-id">###VALUE###</td>']);

    $list->setColumnLabel('name', rex_i18n::msg('d2u_news_name'));
    $list->setColumnParams('name', ['func' => 'edit', 'entry_id' => '###news_id###']);

    $list->setColumnLabel('date', rex_i18n::msg('d2u_news_date'));
   
    $list->addColumn(rex_i18n::msg('module_functions'), '<i class="rex-icon rex-icon-edit"></i> ' . rex_i18n::msg('system_update'));
    $list->setColumnLayout(rex_i18n::msg('module_functions'), ['<th class="rex-table-action" colspan="2">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('module_functions'), ['func' => 'edit', 'entry_id' => '###news_id###']);

	$list->removeColumn('online_status');
	if(rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_news[edit_data]')) {
		$list->addColumn(rex_i18n::msg('status_online'), '<a class="rex-###online_status###" href="' . rex_url::currentBackendPage(['func' => 'changestatus']) . '&entry_id=###news_id###"><i class="rex-icon rex-icon-###online_status###"></i> ###online_status###</a>');
		$list->setColumnLayout(rex_i18n::msg('status_online'), ['', '<td class="rex-table-action">###VALUE###</td>']);

		$list->addColumn(rex_i18n::msg('delete_module'), '<i class="rex-icon rex-icon-delete"></i> ' . rex_i18n::msg('delete'));
		$list->setColumnLayout(rex_i18n::msg('delete_module'), ['', '<td class="rex-table-action">###VALUE###</td>']);
		$list->setColumnParams(rex_i18n::msg('delete_module'), ['func' => 'delete', 'entry_id' => '###news_id###']);
		$list->addLinkAttribute(rex_i18n::msg('delete_module'), 'data-confirm', rex_i18n::msg('d2u_helper_confirm_delete'));
	}

    $list->setNoRowsMessage(rex_i18n::msg('d2u_news_no_news_found'));

    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('d2u_news_news'), false);
    $fragment->setVar('content', $list->get(), false);
    echo $fragment->parse('core/page/section.php');
}