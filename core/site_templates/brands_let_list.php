<?php
/*echo "<pre>";
print_r($arResult);
echo "</pre>";*/
if(!empty($arResult["BRENDS"])):?>
    <div class="brends-list">
		<?if(is_array($arResult["BRENDS"])):
			foreach($arResult["BRENDS"] as $key=>$brends):?>
				<?if($brends):?>
                    <div class="brends-list__item opnElements" data-brand='<?=$brends?>' <?if($arResult["GROUP_ID"]):?>data-id='<?=$arResult["GROUP_ID"]?>'<?endif;?>><?=$brends?></div>
				<?endif;?>
			<?endforeach;?>
		<?else:?>
            <div class="brends-list__item opnElements" data-brand='<?=$arResult["BRENDS"]?>'><?=$arResult["BRENDS"]?></div>
		<?endif;?>
    </div>
<?endif?>
    <div class="items-list">
    </div>