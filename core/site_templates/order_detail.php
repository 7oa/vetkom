<div class="modal-body">
    <?//echo "<pre>"; print_r($arResult); echo "</pre>";?>
    <div class="pull-right text-right">
        <button type="button" class="btn btn-default saveSampleBlockByOrder" data-toggle="modal" data-target="#save_order_template">
            Сохранить заказ как шаблон
        </button>
        <button type="button" class="btn btn-default zakazRepeat" data-number="<?= $arResult["number"] ?>" data-date="<?= $arResult["date"] ?>" data-form="custom">Повторить заказ</button>
        <br/><br/>

        <button type="button" class="btn btn-primary zakazPrint" data-number="<?= $arResult["number"] ?>" data-date="<?= $arResult["date"] ?>" data-form="ext_ReconcilementOrder"><span class="glyphicon glyphicon-print" aria-hidden="true"></span> Заказ согласование</button>
		<?if ($arResult["Protected"]!=1):?>
            <button type="button" class="btn btn-primary zakazPrint" data-number="<?= $arResult["number"] ?>" data-date="<?= $arResult["date"] ?>" data-form="paymentInvoice"><span class="glyphicon glyphicon-print" aria-hidden="true"></span> Счет на оплату </button>
            <? if ($arResult["DEF_PRICE"] == "1"): ?>
                <button type="button" class="btn btn-primary zakazPrint" data-number="<?= $arResult["number"] ?>" data-date="<?= $arResult["date"] ?>" data-form="custom_prices"><span class="glyphicon glyphicon-print" aria-hidden="true"></span> Счет на оплату с розничными ценами </button>
            <? endif; ?>
		<? endif; ?>
    </div>
    <strong>Доставка:</strong> <?= $arResult["shipmentType"] ?><br/>
    <strong>Адрес доставки:</strong> <?= $arResult["shipmentAddress"] ?><br/>
    <? /*if ($arResult["comment"]): ?><strong>Комментарий:</strong> <?= $arResult["comment"] ?><br/><? endif; */?>
    <div class="clearfix"></div>

    <? if ($arResult["DOCS"]): ?>

        <?
        if (!array_key_exists(0, $arResult["DOCS"]))
            $docs[] = $arResult["DOCS"];
        else
            $docs = $arResult["DOCS"];
        ?>
        <div>
            <h4>Документы, связанные с заказом</h4>
            <ul>
                <?php foreach ($docs as $arDocs) { ?>
                    <li>
                        <?php if (($arDocs["name"] == "РеализацияТоваровУслуг") || ($arDocs["name"] == "ПриходныйКассовыйОрдер") || ($arDocs["name"] == "ПоступлениеБезналичныхДенежныхСредств") || ($arDocs["name"] == "ОперацияПоПлатежнойКарте")) { ?>
                            <a href="#paymentsInfo" class="paymentsInfo" data-toggle="modal" data-name="<?= $arDocs["name"] ?>" data-guid="<?= $arDocs["GUID"] ?>">
                                <?= $arDocs["representation"] ?> <?= $arDocs["number"] ?> от <?= date('d.m.Y', strtotime($arDocs["date"])) ?> на сумму <?= number_format($arDocs["sum"], 2, '.', ' '); ?>
                            </a>
                        <?php } else { ?>
                            <?= $arDocs["representation"] ?> <?= $arDocs["number"] ?> от <?= date('d.m.Y', strtotime($arDocs["date"])) ?> на сумму <?= number_format($arDocs["sum"], 2, '.', ' '); ?>
                        <?php } ?>
                    </li>
                <?php } ?>
            </ul>
        </div>

    <? endif; ?>
    <br/>



    <table class="table table-bordered allTable">
        <tr>
            <th>Название</th>
            <th>Количество</th>
            <th width="100">Цена за шт.</th>
            <th width="100">Сумма</th>
        </tr>
        <?
        $sum = 0;
        foreach ($arResult['strings'] as $option):
            ?>
            <tr>
                <td>
                    <?= $option['name']; ?>
                    <br/><small class="art"><?= $option['art']; ?></small>
                </td>
                <td class="text-right"><?= $option['quantity']; ?></td>
                <td class="text-right"><?= number_format($option['price'], 2, '.', ' '); ?></td>
                <td class="text-right"><?= number_format($option['sum'], 2, '.', ' '); ?></td>
            </tr>
            <?
            $sum += $option['sum'];
        endforeach;
        ?>

    </table>
    <div class="text-right">
        <div>Итого: <span class="zakazItog"><?= number_format($sum, 2, '.', ' ') ?></span> руб.</div>
    </div>

</div>
<div class="modal-footer">
    <?if($arResult["status"]=="На согласовании"):?>
    <button type="button" class="btn btn-default order-edit-btn" data-toggle="modal" data-target="#order-edit-form">Редактировать</button>
    <?endif;?>
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
</div>


<div class="modal fade" id="save_order_template" tabindex="-1" role="dialog" aria-labelledby="userModalLabel1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close modal-close" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="userModalLabel">Добавить шаблон</h4>
            </div>
            <div class="modal-body">
                <div id="sampleStatusOrder" class="alert alert-warning sampleWarningOrder"></div>
                <input type="text" class="form-control sample-name-by-order" placeholder="Введите название шаблона" >
                <div class="alert alert-success samplesSuccessOrder"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default to-samples-by-order" data-number="<?= $arResult["number"] ?>" data-date="<?= $arResult["date"] ?>">Сохранить</button>
                <button type="button" class="btn btn-default sampleCloseOrder modal-close">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="order-edit-form" tabindex="-1" role="dialog" aria-labelledby="userModalLabel2" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-body">
                Редактирование заказа приведет к очистке состава Текущего заказа. Продолжить?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default order-edit ajax-order-edit" data-number="<?= $arResult["number"] ?>" data-date="<?= $arResult["date"] ?>" data-guid="<?=$arResult["guid"]?>">Продолжить</button>
                <button type="button" class="btn btn-default modal-close">Отмена</button>
            </div>
        </div>
    </div>
</div>