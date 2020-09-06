<?php if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

\Bitrix\Main\Loader::includeModule('iblock');
CModule::IncludeModule('highloadblock');

$nav = new \Bitrix\Main\UI\PageNavigation("nav");
$nav->allowAllRecords(true)->setPageSize($arParams['PAGE_SIZE'])->initFromUri();
$arResult['NAV'] = $nav;

$dbItems = \Bitrix\Iblock\ElementTable::getList(array(
	'order' => array('sort' => 'ASC'),
	'select' => array('ID', 'ACTIVE', 'NAME', 'IBLOCK_ID', 'SORT', 'DETAIL_TEXT'),
	'filter' => array('IBLOCK_ID' => $arParams['IBLOCK_ID'], 'ACTIVE' => 'Y'),
	'group' => array('TAGS'),
	'count_total' => true,
    'offset' => $nav->getOffset(),
    'limit' => $nav->getLimit(),
	'runtime' => array(),
	'data_doubling' => false,
	'cache' => array(
		'ttl' => 600,
		'cache_joins' => true
	),
));
$nav->setRecordCount($dbItems->getCount());

$arResult['ITEMS'] = $dbItems->fetchAll();

$arHLBlock = Bitrix\Highloadblock\HighloadBlockTable::getById(2)->fetch();
$obEntity = Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
$strEntityDataClass = $obEntity->getDataClass();

$obCache = new \CPHPCache();
$cacheLifetime = 600; 

foreach ($arResult['ITEMS'] as $key => $item) {
	$cacheID = 'reviewCache_'.$item['ID'];
	$cachePath = '/'.$cacheID;
	if($obCache->InitCache($cacheLifetime, $cacheID, $cachePath))	{
		$arResult['ITEMS'][$key]['REVIEW'] = $obCache->GetVars();
	} elseif($obCache->StartDataCache()) {
		$dReview = $strEntityDataClass::getList(array(
	    	'select' => array('ID','UF_NAME', 'UF_SURNAME', 'UF_PROD', 'UF_REVIEW'),
	    	'filter' => array('UF_PROD' => $item['ID'], 'UF_ACTIVE' => 1),
	    	'order' => array('ID' => 'ASC'),
	    	'limit' => '50',
	    ));
	    $arResult['ITEMS'][$key]['REVIEW'] = $dReview->fetchAll();
	    $obCache->EndDataCache($arResult['ITEMS'][$key]['REVIEW']);
	}
}

$this->IncludeComponentTemplate();
?>
