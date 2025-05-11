<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

const IBLOCK_ID_REVIEWS = 5;

$reviewsORM = CIBlockElement::GetList(
	[
		"SORT"=>"ASC", 
		"PROPERTY_PRIORITY"=>"ASC"
	], 
	[
		"IBLOCK_ID"=>IBLOCK_ID_REVIEWS, 
		"ACTIVE"=>"Y", 
	], 
	[
		"ID",
		"NAME",
		"PROPERTY_AUTHOR",
		"PROPERTY_PRODUCT"
	]
);

$reviewsAr =[];
while($reviews = $reviewsORM->Fetch()) {
	$reviewsAr[] = $reviews;
}

$count = 0;

foreach ($arResult['ITEMS'] as $key => $arItem) {
	$arItem['PRICES']['PRICE']['PRINT_VALUE'] = number_format((float)$arItem['PRICES']['PRICE']['PRINT_VALUE'], 0, '.', ' ');
	$arItem['PRICES']['PRICE']['PRINT_VALUE'] .= ' '.$arItem['PROPERTIES']['PRICECURRENCY']['VALUE_ENUM'];

	foreach($reviewsAr as $reviewsItem) {
		if($reviewsItem['PROPERTY_PRODUCT_VALUE'] == intval($arItem['ID'])) {
			$arItem['REVIEWS'][] = $reviewsItem;
			++$count;
		}
	}

	$arResult['ITEMS'][$key] = $arItem;
}

$arResult['COUNT_REVIEWS'] = $count;
$arResult['FIRST_REVIEWS'] = reset($reviewsAr);

$this->__component->SetResultCacheKeys(['COUNT_REVIEWS', 'FIRST_REVIEWS']);
