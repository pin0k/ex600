<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Mail\Event;
use Bitrix\Main\UserTable;
use Bitrix\Main\UserGroupTable;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$eventManager = \Bitrix\Main\EventManager::getInstance();
$eventManager->AddEventHandler("search", "BeforeIndex", [Ex2_630::class, "BeforeIndexHandler"]);

class Ex2_630 {
  private const REVIEWS_IBLOCK_ID = 5;
	
	public static function BeforeIndexHandler($arFields) {
		if($arFields["MODULE_ID"] == "iblock" && $arFields["PARAM2"] == self::REVIEWS_IBLOCK_ID) {
      CModule::IncludeModule("iblock");
      $reviewsORM = CIBlockElement::GetList(
        [], 
        [
          'ID' => $arFields['ITEM_ID'],
          'ACTIVE' => 'Y',
        ], 
        false,
        [], 
        [
          'PROPERTY_AUTHOR'
        ]
      );

      $classId = ''; 
      while($reviews = $reviewsORM->fetch()) {
        $classId = $reviews["PROPERTY_AUTHOR_VALUE"];
      };

      $arListProp = CUserFieldEnum::GetList([], [
        "ID" => $classId,
      ])->fetch();
      
      $arFields['TITLE'] = Loc::getMessage('NEW_TITLE', [
        '#OLD_TITLE#' => $arFields['TITLE'],
        '#CLASS_NAME#' => $arListProp['VALUE'],
      ]);
		}
    
		return $arFields;
	}
}