<? if ($arResult):
    foreach ($arResult as $value):?>
    <li>
        <a href="#" data-id="<?= $value['id']; ?>" class="opnElements openCatalog opnBrends">
			<?= $value['name']; ?>
        </a>
    </li>
	<?endforeach; ?>
<?else:?>
    <li>Ничего не найдено</li>
<? endif; ?>
