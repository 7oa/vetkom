<?
use Core\Main\Catalog,
    Core\Main\User;
$USER_ID = User::getID();
$userInfo = User::getByID($USER_ID);
$docs=User::getInstance()->getResult('GetManagerPhoto', array('id' => $userInfo["EXTERNAL"]));
if($docs["bindata"]) {
    $img = $_SERVER['DOCUMENT_ROOT'] . '/images/avatar_'. $USER_ID. '.' . $docs["ext"];
    $file = iconv('utf-8', 'windows-1251', $img);
    $decode=base64_decode($docs["bindata"]);
    file_put_contents($file, $decode);
    $path = 'images/avatar_'. $USER_ID. '.' . $docs["ext"];
}
?>
<div class="col-xs-3 leftMenu">
    <div class="all-brends">
        <div class="all-brands-link ajax-all-brands">Все бренды</div>
    </div>
    <form class="group-search ajax-search-group">
        <div class="group-search__input">
            <input type="text" class="form-control" id="search-brend" placeholder="Поиск по группе">
            <input type="reset" class="btn btn-primary ajax-reset-brend" value="Сброс">
        </div>
    </form>
    <div>Все группы</div>
    <ul class="nav catalogMenu">
        <?
		$sections = Catalog::getInstance()->getResult('GetGroupList', array('id' =>'', 'Input' => ''));
        foreach ($sections as $value):?>
            <li>
                <a href="#" data-id="<?= $value['id']; ?>" data-group="<?=$value['isGroup']?>" class="openCatalog opnBrends<?if(!$value['isGroup']):?> ajax-brend-alph<?endif;?>">
                    <?= $value['name']; ?>
                    <? if ($value['isGroup']) { ?>
                        <span class="caret"></span>
                    <? } ?>
                </a>
            </li>
        <?endforeach; ?>
    </ul>

    <div class="youManager">
        Ваш менеджер<br><strong><?= $userInfo["MANAGER"] ?></strong><br>
        <?if($path):?><img src="<?=$path?>" alt="" width="200" class="managerFoto">
		<?else:?><img src="/core/templates/main/images/avatar_noname.png" alt="" width="200" class="managerFoto">
		<?endif;?><br>
        <div class="managerPhone"><?= $userInfo["MANAGERPHONE"] ?></div>
    </div>
</div>
<div class="col-xs-9 rightContent">
    <div class="search">
        <form class="form-horizontal">
            <div class="row">
                <div class="col-sm-10"><input type="text" class="form-control" id="searchInput"></div>
                <div class="col-sm-2"><input type="submit" class="btn btn-primary button100 serchButton" value="Найти"></div>
            </div>
        </form>
    </div>
    <div>
    	<div id="catalogKey" style="display: none"></div>
<!--        <label>
        	<input id="areVal" class="catalogCheck" type="checkbox" value="Y" />
        	Показывать только товары в наличии
        </label>-->
    </div>
    <div class="brends-wrapper ajax-brends"></div>
    <div class="divTable">
        <?if (((date(G)<10)||(date(G)>=19))):?>
            <div class="alert alert-info"><strong>Обращаем Ваше внимание:</strong> не все цены на данный момент актуальны, проверяйте обновление прайса в 10.00 утра (время Мск).</div>
        <?endif;?>
		<div class="catalogHelp">Выберите товар из каталога<br>или воспользуйтесь поиском</div>
    </div>

</div>
<div class="modal fade" id="analogModal" tabindex="-1" role="dialog" aria-labelledby="analogModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="analogModalLabel">Аналоги</h4>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>