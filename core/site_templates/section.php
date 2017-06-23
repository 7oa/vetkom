<? if ($arResult):
	?>
    <ul class="nav">
        <?
        foreach ($arResult as $value => $key) {
            ?>
            <li>
                <a href='#' data-id="<?= $key[0]['id'] ?>" data-group="<?=$key[0]['isGroup']?>" class='openCatalog opnBrends<?if(!$key[0]['isGroup']):?> ajax-brend-alph<?endif;?>'>
                    <?= $value ?>
					<? if ($key[0]['isGroup']) { ?>
                        <span class="caret"></span>
					<? } ?>
                </a>
            </li>
            <?php
        }
        ?>
    </ul>
<? endif; ?>
