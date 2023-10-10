<?php
$MESS = [];
require_once(__DIR__ . '/function.php' );
require_once(__DIR__ . '/lang.php' );

if (isset($_GET['EXPORT'])) {
	list($data, $th) = getInfoWithTitles($arResult);
	download_send_headers("data_export.csv");
	echo array2csv($data, $th);
	die();
}

?><!DOCTYPE html>
<html lang="ru">
<head>
	<title>Отчет ElytS: загрузка товаров на сайт</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="theme-color" content="#5dcade" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
	<link href="css/style.css?<?=time()?>" rel="stylesheet">


</head>
<body>
<div class="container-fluid foto-analyze pt-3">
	<div class="h4">Загрузка товаров на сайт</div>

	<div class="filter-block mb-3">
		<div class="h5">Фильтр</div>
		<form action="" method="GET">
			<input type="hidden" name="action" value="filter" />
			<div class="form-row align-items-center mb-3">
				<div class="col-sm-3 mb-3">
				  <label for="inlineFormInputBrand">Бренд</label>
				  <input name="filter[brand]" type="text" class="form-control" id="inlineFormInputBrand" placeholder="Название бренда" value="<?=$arResult["FILTER"]["brand"]?>">
				</div>
				<div class="col-sm-3 mb-3">
				  <label for="inlineFormInputKod">Код товара</label>
				  <input name="filter[kod]" type="text" class="form-control" id="inlineFormInputKod" placeholder="Код товара" value="<?=$arResult["FILTER"]["kod"]?>">
				</div>
				<div class="col-sm-3 mb-3">
				  <label for="inlineFormInputSezon">Сезон</label>
				  <select name="filter[sezon]" id="inlineFormInputSezon" class="form-control">
				  	  <option value="">Выберите значение</option>
					  <? foreach($filterSezon as $sezon): ?>
						<option value="<?=$sezon?>"  <?=($arResult["FILTER"]['sezon'] == $sezon ? 'selected=""' : '')?>><?=$sezon?></option>
					  <? endforeach; ?>
				  </select>
				</div>
				<div hidden class="col-sm-4 mb-3">
					<label for="inlineFormInputGroupDateTo">Дата добавления изменений</label>
					<div class="input-group">
						<input name="filter[dateFrom]" type="date" class="form-control" id="inlineFormInputGroupDateTo" placeholder="от" value="<?=$arResult["FILTER"]["dateFrom"]?>">
						<input name="filter[dateTo]" type="date" class="form-control" placeholder="до" value="<?=$arResult["FILTER"]["dateTo"]?>">
					</div>
				</div>
				<?php /**/
				?>
				<div class="col-sm-2 mb-3">
				  <label for="inlineFormInputAction">Действие не совершалось</label>
				  <select id="inlineFormInputAction" name="filter[action]" class="form-control">
					<option value="">Выберите значение</option>
				  	<?foreach ($filterParams as $nameParam) :?>
						<option value="<?=$nameParam['CODE'] ?>" <?=($arResult["FILTER"]['action'] == $nameParam["CODE"] ? 'selected=""' : '')?>><?=$nameParam['NAME'] ?></option>
					<?endforeach;?>


				  </select>
				</div>

				<div class="col-sm-2 mb-3">
				  <label for="inlineFormInputAction">Действие совершалось</label>
				  <select id="inlineFormInputAction" name="filter[done_action]" class="form-control">
					<option value="">Выберите значение</option>
				  	<?foreach ($filterParams as $nameParam) :?>
						<option value="<?=$nameParam['CODE'] ?>" <?=($arResult["FILTER"]['done_action'] == $nameParam["CODE"] ? 'selected=""' : '')?>><?=$nameParam['NAME'] ?></option>
					<?endforeach;?>


				  </select>
				</div>
			</div>
			<button type="submit" class="btn btn-primary">Найти</button>
			<?if (isset($_GET["action"])) :?>
			<a href="/report_loading_items/" class="btn btn-danger ml-2">Отменить</a>
			<?endif;?>
		</form>
	</div>
	<div class="ajaxResult">
		<?if (isset($arResult["ROW"]["ITEMS"])) :?>
			<div class='mb-4 clearfix'>
			<p class='float-left'>Найдено: <?=count($arResult["ROW"]["ITEMS"])?></p>
			<?
				echo "<a class='btn btn-success float-right' href='http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]&EXPORT'>Скачать</a>";
			?>
			</div>
		<?endif;?>
		<table class="table table-sm table-hover table-bordered fixtable small">
		  <thead>
			<tr>
			  <th scope="col" style="width: 20%;">Товар</th>
			  <th scope="col"><span style="white-space: nowrap;" style="width: 9%;">% заполн.</span></th>
			  <?foreach ($arResult["COL"] as $arCol) :?>
			  <th scope="col"><span style="white-space: break-spaces;"><?=!is_null($arCol["NAME"]) ? $arCol["NAME"] : $arCol["CODE"]?></span></th>
			  <?endforeach;
			   unset($arCol);
			  ?>
			</tr>
		  </thead>
		  <tbody>
			<?if (isset($arResult["ROW"]["ITEMS"])) :?>
			  <?foreach ($arResult["ROW"]["ITEMS"] as $arItem) :?>

				<tr>
				  <td class="first"><b>код: <?=$arItem["KOD"]?></b><br><small style=""><?=$arItem["ITEM_NAME"]?> (<?=$arItem["COLOR"]?>)</small><?=$arItem["ITEM_ID"]?></td>

				  <?/*
				  $countRow = 0;
				  foreach ($arResult["COL"] as $col){
					  if ($arResult["ROW"]["ACTIONS"][$arItem["ITEM_ID"]][$col]["DATE"]) {
						  $countRow++;
					  }
				  }
				  unset($col);

				  $percent = (($countRow > 0) ? round(($countRow / count($arResult["COL"])) * 100) : 0);*/
				  $percent = (($arItem["COUNT_ACTION"] > 0) ? round(($arItem["COUNT_ACTION"] / count($arResult["COL"])) * 100) : 0);

				  ?>
				  <td><small><?=$percent?>%</small></td>
				  <?foreach ($arResult["COL"] as $arCol) :

					  $class = '';
					  if (strtotime($arResult["ROW"]["ACTIONS"][$arItem["ITEM_ID"]][$arCol["CODE"]]["DATE"]) >= strtotime($arResult["FILTER"]["dateFrom"])) {
					  	$class = 'font-weight-bold';
					  }
				  ?>
				  	<td class="<?=$class?> <?=isset($arResult["ROW"]["ACTIONS"][$arItem["ITEM_ID"]][$arCol["CODE"]]["DATE"]) ? 'bg-success' : ''?> ">
					  <?//=isset($arResult["ROW"]["ACTIONS"][$arItem["ITEM_ID"]][$arCol["CODE"]]["DATE"]) ? date('d.m.Y', strtotime($arResult["ROW"]["ACTIONS"][$arItem["ITEM_ID"]][$arCol["CODE"]]["DATE"])) : ''?>
					  <?
						if (!is_null($arResult["ROW"]["ACTIONS"][$arItem["ITEM_ID"]][$arCol["CODE"]]["DATE"])) {
							$dtstmp = strtotime($arResult["ROW"]["ACTIONS"][$arItem["ITEM_ID"]][$arCol["CODE"]]["DATE"]);
							if ($dtstmp > 0) {
								echo date('d.m.Y', $dtstmp);
							} else {
								echo ' - <br><i class="font-weight-light">auto</i>';
							}
						}
					  ?>

				  	</td>
				  <?endforeach;
				  unset($arCol);?>
				</tr>
			  <?endforeach;?>
			<?endif;?>
		  </tbody>
		</table>
	</div>
</div>



	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
	<script type="text/javascript" src="css/script.js?<?=time()?>"></script>

</body>
</html>
