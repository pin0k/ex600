<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

if(!empty($arResult['COUNT_REVIEWS']) && $arResult['COUNT_REVIEWS'] > 0) {
  $APPLICATION->SetPageProperty("ex2_meta", str_replace("#count#", $arResult['COUNT_REVIEWS'], $APPLICATION->GetProperty("ex2_meta")));
}

if(!empty($arResult['FIRST_REVIEWS'])) {
  $APPLICATION->AddViewContent('ADDITIONAL_REVIEWS', Loc::getMessage('ADDITIONAL_REVIEWS', [
    '#NAME#' => $arResult['FIRST_REVIEWS']['NAME']
  ]));
}