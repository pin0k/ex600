<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$eventManager = \Bitrix\Main\EventManager::getInstance(); 
$eventManager->AddEventHandler("main", "OnBuildGlobalMenu", "MyOnBuildGlobalMenu");

function MyOnBuildGlobalMenu(&$aGlobalMenu, &$aModuleMenu) {

  $ID_CONTENT_EDITOR = 5;
	$CONTENT_MANAGER_PERMISSION_SEC = 'global_menu_content';
	global $USER;

	if(!$USER->IsAdmin() && in_array($ID_CONTENT_EDITOR, CUser::GetUserGroup($USER->GetID()))) {
		foreach($aGlobalMenu as $key=>$data) {
			if($key != $CONTENT_MANAGER_PERMISSION_SEC) {
				unset($aGlobalMenu[$key]);
			}
		}

		foreach($aModuleMenu as $key=>$data) {
			if($data['parent_menu']!= $CONTENT_MANAGER_PERMISSION_SEC) {
				unset($aModuleMenu[$key]);
			}
		}

		$aGlobalMenu["fast_access"] = [
			'menu_id' => 'fast_access_glob',
			'text' => Loc::getMessage("NEW_ITEM_MENU"),
			'title' => Loc::getMessage("NEW_ITEM_MENU"),
			'sort' => 200,
			'items_id' =>'fast_access',
			'help_section' => 'fast_access',
			'items' => [
				[
					'text' => Loc::getMessage('LINK_1'),
					'parent_menu' => 'fast_access',
					'title' => Loc::getMessage('LINK_1'),
					'url' => Loc::getMessage('URL_1'),
				],
				[
					'text' => Loc::getMessage('LINK_2'),
					'parent_menu' => 'fast_access',
					'title' => Loc::getMessage('LINK_2'),
					'url' => Loc::getMessage('URL_2'),
				],
			],
		];
	}	
}