<?php
header('Content-Type: text/html; charset=utf-8');
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL & ~E_NOTICE);

require_once(__DIR__ . '/db/db.php');
require_once(__DIR__ . '/lang.php');

$TABLE_DATA = include_once(__DIR__ . '/db/status_items_table.php');
$TABLE_NAME = $TABLE_DATA['table_name'];
$TABLE_FIELDS = $TABLE_DATA['fields_fillable'];

if ($_GET['action'] === 'filter') {
    $db = new DB();
    $limit = 50;
    $total_records = $db->get_results("SELECT COUNT(*) as count FROM $TABLE_NAME")[0]['count'];
    $total_records = intval($total_records);
    $total_pages = ceil($total_records / $limit);
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    $where = 'WHERE';

    if (isset($_GET['filter']['kod']) && $_GET['filter']['kod'] != '') {
        $kod = $db->filter($_GET['filter']['kod']);
        $where .= " KOD = $kod ";
    }

    if (isset($_GET['filter']['sezon']) && $_GET['filter']['sezon'] != '') {
        $sezon = $db->filter($_GET['filter']['sezon']);
        $where .= $where === 'WHERE' ? " SEZON = $sezon " : "AND SEZON = $sezon ";
    }

    if (isset($_GET['filter']['action']) && $_GET['filter']['action'] != '') {
        $action = $db->filter($_GET['filter']['action']);
        $where .= $where === 'WHERE' ? " $action IS NULL " : "AND $action iS NULL ";
    }

    if (isset($_GET['filter']['done_action']) && $_GET['filter']['done_action'] != '') {
        $action = $db->filter($_GET['filter']['done_action']);
        $where .= $where === 'WHERE' ? " $action IS NOT NULL " : "AND $action iS NOT NULL ";
    }

    $where  = $where === 'WHERE' ? '' : $where;
    $sql = "SELECT * FROM $TABLE_NAME $where LIMIT $limit OFFSET $offset";

    $arResult = $db->get_results($sql);
}

$sezons = (new DB())->get_results("SELECT DISTINCT SEZON  FROM $TABLE_NAME");
$sezons = array_column($sezons, 'SEZON');

?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <title>Отчет ElytS: загрузка товаров на сайт</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="theme-color" content="#5dcade" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <link href="css/style.css?<?php echo time() ?>" rel="stylesheet">
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
                        <label for="inlineFormInputKod">Код товара</label>
                        <input name="filter[kod]" type="text" class="form-control" id="inlineFormInputKod" placeholder="Код товара" value="<?php echo $_GET["filter"]["kod"] ?>">
                    </div>
                    <div class="col-sm-3 mb-3">
                        <label for="inlineFormInputSezon">Сезон</label>
                        <select name="filter[sezon]" id="inlineFormInputSezon" class="form-control">
                            <option value="">Выберите значение</option>
                            <?php foreach ($sezons  as $sezon) : ?>
                                <option value="<?php echo  $sezon ?>" <?php echo ($_GET["filter"]['sezon'] == $sezon ? 'selected=""' : '') ?>>
                                    <?php echo $sezon ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div hidden class="col-sm-4 mb-3">
                        <label for="inlineFormInputGroupDateTo">Дата добавления изменений</label>
                        <div class="input-group">
                            <input name="filter[dateFrom]" type="date" class="form-control" id="inlineFormInputGroupDateTo" placeholder="от" value="<?php echo $arResult["FILTER"]["dateFrom"] ?>">
                            <input name="filter[dateTo]" type="date" class="form-control" placeholder="до" value="<?php echo $arResult["FILTER"]["dateTo"] ?>">
                        </div>
                    </div>
                    <div class="col-sm-2 mb-3">
                        <label for="inlineFormInputAction">Действие не совершалось</label>
                        <select id="inlineFormInputAction" name="filter[action]" class="form-control">
                            <option value="">Выберите значение</option>
                            <?php foreach ($TABLE_FIELDS as $action) : ?>
                                <option value="<?php echo $action ?>" <?php echo ($_GET["filter"]['action'] == $action ? 'selected=""' : '') ?>>
                                    <?php echo $MESS[$action] ?? $action ?>
                                <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-sm-2 mb-3">
                        <label for="inlineFormInputAction">Действие совершалось</label>
                        <select id="inlineFormInputAction" name="filter[done_action]" class="form-control">
                            <option value="">Выберите значение</option>
                            <?php foreach ($TABLE_FIELDS as $action) : ?>
                                <option value="<?php echo $action ?>" <?php echo ($_GET["filter"]['done_action'] == $action ? 'selected=""' : '') ?>>
                                    <?php echo $MESS[$action] ?? $action ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Найти</button>
                <?php if (isset($_GET["action"])) : ?>
                    <a href="/report_loading_items/" class="btn btn-danger ml-2">Отменить</a>
                <?php endif; ?>
            </form>
        </div>
        <div class="ajaxResult">
            <?php if (isset($arResult["ROW"]["ITEMS"])) : ?>
                <div class='mb-4 clearfix'>
                    <p class='float-left'>Найдено: <?php echo count($arResult["ROW"]["ITEMS"]) ?></p>
                    <?php
                    echo "<a class='btn btn-success float-right' href='http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]&EXPORT'>Скачать</a>";
                    ?>
                </div>
            <?php endif; ?>
            <table class="table table-sm table-hover table-bordered fixtable small">
                <thead>
                    <tr>
                        <th scope="col" style="width: 20%;">Товар</th>
                        <th scope="col"><span style="white-space: nowrap;" style="width: 9%;">% заполн.</span></th>
                        <?php foreach ($arResult["COL"] as $arCol) : ?>
                            <th scope="col"><span style="white-space: break-spaces;"><?php echo !is_null($arCol["NAME"]) ? $arCol["NAME"] : $arCol["CODE"] ?></span></th>
                        <?php endforeach;
                        unset($arCol);
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($arResult["ROW"]["ITEMS"])) : ?>
                        <?php foreach ($arResult["ROW"]["ITEMS"] as $arItem) : ?>

                            <tr>
                                <td class="first"><b>код: <?php echo $arItem["KOD"] ?></b><br><small style=""><?php echo $arItem["ITEM_NAME"] ?> (<?php echo $arItem["COLOR"] ?>)</small><?php echo $arItem["ITEM_ID"] ?></td>

                                <?php /*
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
                                <td><small><?php echo $percent ?>%</small></td>
                                <?php foreach ($arResult["COL"] as $arCol) :

                                    $class = '';
                                    if (strtotime($arResult["ROW"]["ACTIONS"][$arItem["ITEM_ID"]][$arCol["CODE"]]["DATE"]) >= strtotime($arResult["FILTER"]["dateFrom"])) {
                                        $class = 'font-weight-bold';
                                    }
                                ?>
                                    <td class="<?php echo $class ?> <?php echo isset($arResult["ROW"]["ACTIONS"][$arItem["ITEM_ID"]][$arCol["CODE"]]["DATE"]) ? 'bg-success' : '' ?> ">
                                        <?php //=isset($arResult["ROW"]["ACTIONS"][$arItem["ITEM_ID"]][$arCol["CODE"]]["DATE"]) ? date('d.m.Y', strtotime($arResult["ROW"]["ACTIONS"][$arItem["ITEM_ID"]][$arCol["CODE"]]["DATE"])) : ''
                                        ?>
                                        <?php
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
                                <?php endforeach;
                                unset($arCol); ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if ($total_pages > 2) { ?>
            <ul class="pagination">
                <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                    <li class='page-item'>
                        <a class='page-link' href='?page=<?php echo $i ?>'>
                            <?php echo $i ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        <?php } ?>
    </div>



    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
    <script type="text/javascript" src="css/script.js?<?php echo time() ?>"></script>

</body>

</html>
