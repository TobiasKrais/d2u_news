<?php
// Update language replacements
d2u_references_lang_helper::factory()->install();

// Update modules
if(class_exists(D2UModuleManager) && class_exists(D2UNewsModules)) {
	$d2u_module_manager = new D2UModuleManager(D2UNewsModules::getD2UNewsModules(), "", "d2u_news");
	$d2u_module_manager->autoupdate();
}