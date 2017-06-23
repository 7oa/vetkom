<?if(is_array($arResult["BRENDS"])):
	foreach ($arResult["BRENDS"] as $mas){
		$alph[] = mb_substr( strtoupper($mas), 0, 1, 'utf-8' );
	}
	$brands_alph = array_unique($alph);
	sort($brands_alph);
	$rus = preg_grep("/[А-Я]/u", $brands_alph);
	$norus = preg_grep("/[^А-Я]/u", $brands_alph);
	?>
    <div class="brends-alph-list">
        <div>|
			<?foreach($norus as $brends):?>
                <span class="brend-alph ajax-brend-alph" data-letter="<?=$brends?>"><?=$brends?></span> |
			<?endforeach;?>
        </div>
        <div>|
			<?foreach($rus as $brends):?>
                <span class="brend-alph ajax-brend-alph" data-letter="<?=$brends?>"><?=$brends?></span> |
			<?endforeach;?>
        </div>
    </div>
<?endif?>