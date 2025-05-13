<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Mail\Event;
use Bitrix\Main\EventManager;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$eventManager = \Bitrix\Main\EventManager::getInstance();

$eventManager->addEventHandler("main", "OnBeforeUserUpdate", [Ex2_600::class, "OnBeforeUserUpdateHandler"]);
$eventManager->addEventHandler("main", "OnAfterUserUpdate", [Ex2_600::class, "OnAfterUserUpdateHandler"]);

class Ex2_600 {
  private static $oldUserClass = '';
  private const CONST_SITE_ID = 's1';

	public static function OnBeforeUserUpdateHandler(&$arFields) {
    $usersORM = CUser::GetList(
      [],
      [],
      [
        'ID' => $arFields['ID'],
      ], 
      [
        'SELECT' => ['UF_USER_CLASS'],
      ]
    );

    while($arUser = $usersORM->Fetch()) {
      static::$oldUserClass = $arUser['UF_USER_CLASS'];
    }
	}

  public static function OnAfterUserUpdateHandler(&$arFields) {
		if($arFields["RESULT"]) {
      if(intval($arFields['UF_USER_CLASS']) != intval(static::$oldUserClass)) {
        Event::send([
          'EVENT_NAME' => 'EX2_AUTHOR_INFO',
          'LID' => self::CONST_SITE_ID,
          'C_FIELDS' => [
            'OLD_USER_CLASS' => static::$oldUserClass,
            'NEW_USER_CLASS' => $arFields['UF_USER_CLASS'],
            'NAME' => $arFields['LAST_NAME'].' '.$arFields['NAME'],
            'EMAIL' => Loc::getMessage('EMAIL'),
            'EMAIL_TO' => Loc::getMessage('EMAIL_TO'),
          ],
        ]);
      }
    }
  }
}