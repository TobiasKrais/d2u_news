<?php
// save settings
if ('save' === filter_input(INPUT_POST, 'btn_save')) {
    $settings = rex_post('settings', 'array', []);

    // Linkmap Link and media needs special treatment
    $link_ids = filter_input_array(INPUT_POST, ['REX_INPUT_LINK' => ['filter' => FILTER_VALIDATE_INT, 'flags' => FILTER_REQUIRE_ARRAY]]);
    $settings['article_id'] = is_array($link_ids['REX_INPUT_LINK']) ? $link_ids['REX_INPUT_LINK'][1] : 0;

    // Checkbox also needs special treatment if empty
    $settings['lang_wildcard_overwrite'] = array_key_exists('lang_wildcard_overwrite', $settings) ? 'true' : 'false';

    // Save settings
    if (rex_config::set('d2u_news', $settings)) {
        echo rex_view::success(rex_i18n::msg('form_saved'));

        // Install / update language replacements
        d2u_news_lang_helper::factory()->install();
    } else {
        echo rex_view::error(rex_i18n::msg('form_save_error'));
    }
}
?>
<form action="<?= rex_url::currentBackendPage() ?>" method="post">
	<div class="panel panel-edit">
		<header class="panel-heading"><div class="panel-title"><?= rex_i18n::msg('d2u_helper_settings') ?></div></header>
		<div class="panel-body">
			<fieldset>
				<legend><small><i class="rex-icon rex-icon-language"></i></small> <?= rex_i18n::msg('d2u_helper_lang_replacements') ?></legend>
				<div class="panel-body-wrapper slide">
					<?php
                        d2u_addon_backend_helper::form_checkbox('d2u_helper_lang_wildcard_overwrite', 'settings[lang_wildcard_overwrite]', 'true', 'true' === $this->getConfig('lang_wildcard_overwrite'));
                        foreach (rex_clang::getAll() as $rex_clang) {
                            echo '<dl class="rex-form-group form-group">';
                            echo '<dt><label>'. $rex_clang->getName() .'</label></dt>';
                            echo '<dd>';
                            echo '<select class="form-control" name="settings[lang_replacement_'. $rex_clang->getId() .']">';
                            $replacement_options = [
                                'd2u_helper_lang_english' => 'english',
                                'd2u_helper_lang_chinese' => 'chinese',
                                'd2u_helper_lang_czech' => 'czech',
                                'd2u_helper_lang_dutch' => 'dutch',
                                'd2u_helper_lang_french' => 'french',
                                'd2u_helper_lang_german' => 'german',
                                'd2u_helper_lang_italian' => 'italian',
                                'd2u_helper_lang_polish' => 'polish',
                                'd2u_helper_lang_portuguese' => 'portuguese',
                                'd2u_helper_lang_russian' => 'russian',
                                'd2u_helper_lang_spanish' => 'spanish',
                            ];
                            foreach ($replacement_options as $key => $value) {
                                $selected = $value == $this->getConfig('lang_replacement_'. $rex_clang->getId()) ? ' selected="selected"' : '';
                                echo '<option value="'. $value .'"'. $selected .'>'. rex_i18n::msg('d2u_helper_lang_replacements_install') .' '. rex_i18n::msg($key) .'</option>';
                            }
                            echo '</select>';
                            echo '</dl>';
                        }
                    ?>
				</div>
			</fieldset>
			<fieldset>
				<legend><small><i class="rex-icon rex-icon-open-category"></i></small> <?= rex_i18n::msg('d2u_helper_categories') ?></legend>
				<div class="panel-body-wrapper slide">
					<?php
                        $options_sort = ['name' => rex_i18n::msg('d2u_helper_name'), 'priority' => rex_i18n::msg('header_priority')];
                        d2u_addon_backend_helper::form_select('d2u_helper_sort', 'settings[default_sort]', $options_sort, [(string) $this->getConfig('default_sort')]);
                    ?>
				</div>
			</fieldset>
		</div>
		<footer class="panel-footer">
			<div class="rex-form-panel-footer">
				<div class="btn-toolbar">
					<button class="btn btn-save rex-form-aligned" type="submit" name="btn_save" value="save"><?= rex_i18n::msg('form_save') ?></button>
				</div>
			</div>
		</footer>
	</div>
</form>
<?php
    echo d2u_addon_backend_helper::getCSS();
    echo d2u_addon_backend_helper::getJS();
    echo d2u_addon_backend_helper::getJSOpenAll();
