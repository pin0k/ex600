<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Mail\Event;
use Bitrix\Main\EventManager;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$eventManager = \Bitrix\Main\EventManager::getInstance(); 
$eventManager->addEventHandler("iblock", "OnBeforeIBlockElementUpdate", [Ex2590::class, "CheckLenghtTextPreview"]);
$eventManager->addEventHandler("iblock", "OnAfterIBlockElementUpdate", [Ex2590::class, "NotationLog"]);

class Ex2590 {
  private const IBLOCK_ID_REVIEWS = 5;
  private const LENGTH_REVIEWS_PREVIEW_TEXT = 5;
  private const PROPERTY_AUTHOR_ID = 9;
  private const PROPERTY_AUTHOR_TYPE_ID = 154;
  private static $oldAuthor = '';

	public static function CheckLenghtTextPreview(&$arFields) {
    if(intval($arFields['IBLOCK_ID']) !== self::IBLOCK_ID_REVIEWS) {
      return true;
    }

    $elementsORM = CIBlockElement::GetList(
      [],
      [
          "IBLOCK_ID"=>intval($arFields["IBLOCK_ID"]),
          'ID' => intval($arFields["ID"])
      ],
      false,
      false,
      ['PROPERTY_AUTHOR']
    );

    while($element = $elementsORM->Fetch()) {
      static::$oldAuthor = $element["PROPERTY_AUTHOR_VALUE"];            
    }

    if(mb_strlen($arFields['PREVIEW_TEXT']) < self::LENGTH_REVIEWS_PREVIEW_TEXT) {
      global $APPLICATION;
			$APPLICATION->throwException(Loc::getMessage('ERROR_MESSAGE', [
        '#LENGTH#' => mb_strlen($arFields['PREVIEW_TEXT']),
      ]));
			return false;
    }

    if(str_contains($arFields['PREVIEW_TEXT'], '#del#')) {
      $arFields['PREVIEW_TEXT'] = str_replace($arFields['PREVIEW_TEXT'], '', $arFields['PREVIEW_TEXT']);
    }
	}

  public static function NotationLog(&$arFields) {
    if($arFields["RESULT"]) {
      
      $newAuthor = reset(array_column($arFields['PROPERTY_VALUES'][self::PROPERTY_AUTHOR_ID], 'VALUE'));
      
      if(intval($newAuthor) != intval(static::$oldAuthor)) {
        CEventLog::Add([
          "SEVERITY" => "INFO",
          "AUDIT_TYPE_ID" => "ex2_590",
          "MODULE_ID" => "IBlock",
          "DESCRIPTION" => Loc::getMessage('SEND_NOTATION_LOG', [
            '#ID#' => $arFields['ID'],
            '#OLD_AUTHOR#' => static::$oldAuthor,
            '#NEW_AUTHOR#' => $newAuthor,
          ]),
        ]);
      }
    }
  }
}