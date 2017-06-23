<? if ($arResult):
    foreach ($arResult as $value):?>
    <li>
        <a href="#" data-id="<?= $value['id']; ?>" data-group="<?=$value['isGroup']?>" class="openCatalog opnBrends<?if(!$value['isGroup']):?> ajax-brend-alph<?endif;?>">
			<?= $value['name']; ?>
			<? if ($value['isGroup']) { ?>
                <span class="caret"></span>
			<? } ?>
        </a>
    </li>
	<?endforeach; ?>
<?else:?>
    <li>Ничего не найдено</li>
<? endif; ?>
