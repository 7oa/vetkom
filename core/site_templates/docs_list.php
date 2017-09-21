<?//echo"<pre>"; print_r($arResult); echo"</pre>";?>
<div><strong>Сальдо конечное: <?= number_format($arResult["ClosingBalance"], 2, '.', ' ') ?></strong></div><br/>
<?
if (!empty($arResult["Contracts"])):
    ?>
    <table class="table table-bordered allTable">
        <tbody>
        <tr>
            <th>Документы</th>
            <th>Приход</th>
            <th>Расход</th>
        </tr>
        <?
        if (!array_key_exists(0, $arResult["Contracts"]))
            $contracts[] = $arResult["Contracts"];
        else
			$contracts = $arResult["Contracts"];
        ?>
        <? foreach ($contracts as $arContracts): ?>
            <tr>

                <td colspan="2" style="border-top-width: 3px;"><h3><?=$arContracts["Contract_name"]?></h3></td>
                <td style="border-top-width: 3px;">
                    <b>Сальдо начальное:</b><br>
                    <?= number_format($arContracts["BeginningBalance"], 2, '.', ' ') ?>
                </td>
            </tr>
            <?if($arContracts["Documents"]):
				unset($docs);
				if (!array_key_exists(0, $arContracts["Documents"]))
					$docs[] = $arContracts["Documents"];
				else
					$docs = $arContracts["Documents"];
				?>
                <? foreach ($docs as $arPay):?>
                    <tr>
                        <td>
                            <?php if (($arPay["Name"] == "РеализацияТоваровУслуг") || ($arPay["Name"] == "ПриходныйКассовыйОрдер") || ($arPay["Name"] == "ПоступлениеБезналичныхДенежныхСредств") || ($arPay["Name"] == "ОперацияПоПлатежнойКарте")) {?>
                                <a href="#paymentsInfo" class="paymentsInfo" data-toggle="modal" data-name="<?= $arPay["Name"] ?>" data-guid="<?= $arPay["GUID"] ?>">
                                    <?= $arPay["Representation"] ?>
                                </a>
                            <?php } else echo $arPay['Representation']; ?>
                        </td>
                        <td class="text-right"><?= number_format($arPay["SumIn"], 2, '.', ' ') ?></td>
                        <td class="text-right"><?= number_format($arPay["SumOut"], 2, '.', ' ') ?></td>
                    </tr>
                <? endforeach; ?>
            <?endif;?>
            <tr>
                <td colspan="2"  style="border-bottom-width: 3px;"></td>
                <td style="border-bottom-width: 3px;">
                    <b>Сальдо конечное:</b><br>
                    <?= number_format($arContracts["ClosingBalance"], 2, '.', ' ') ?>
                </td>
            </tr>
        <? endforeach; ?>

        </tbody>
    </table>
<? else: ?>
    Нет документов за выбранный период<br><br>
<? endif; ?>

<div><strong>Сальдо начальное: <?= number_format($arResult["BeginningBalance"], 2, '.', ' ') ?></strong></div><br/>
</tr>
