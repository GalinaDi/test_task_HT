<?php
if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();?>

<div class="items_container">
	<?php foreach($arResult['ITEMS'] as $key => $item):?>
		<div class="item">
			<div class="name"><span>ID: <?=$item['ID']?></span> - <?=$item['NAME']?></div>
			<div class="detail_text"><?=$item['DETAIL_TEXT']?></div>
			<div class="review_block">
				<?php foreach($item['REVIEW'] as $review):?>
					<div class="review_item">
						<div class="user_name"><?=$review['UF_NAME']?>&nbsp;<?=$review['UF_SURNAME']?></div>
						<div class="review"><?=$review['UF_REVIEW']?></div>
					</div>
				<?php endforeach;?>
			</div>
		</div>
	<?php endforeach;?>
</div>

<?php
$APPLICATION->IncludeComponent(
    "bitrix:main.pagenavigation",
    "",
    array(
       "NAV_OBJECT" => $arResult['NAV'],
       "SEF_MODE" => "N",
    ),
    true
);
?>
