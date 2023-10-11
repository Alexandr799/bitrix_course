<?php
header('Content-Type: text/html; charset=utf-8');
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL & ~E_NOTICE);

require_once(__DIR__ . '/db/db.php');
require_once(__DIR__ . '/lang.php');
require_once(__DIR__ . '/helpers/functions.php');

$TABLE_DATA = include_once(__DIR__ . '/db/status_items_table.php');
$TABLE_NAME = $TABLE_DATA['table_name'];
$TABLE_FIELDS_ACTION = $TABLE_DATA['fields_fillable'];
$COUNT_ACTIONS = count($TABLE_FIELDS_ACTION);

$db = new DB();
$limit = 50;
$total_records = $db->get_results("SELECT COUNT(*) as count FROM $TABLE_NAME")[0]['count'];
$total_records = intval($total_records);
$where = makeWhereRow($_GET, function ($value) use ($db) {
    return $db->filter($value);
});
$agg = makeColumnWithNullCount($TABLE_FIELDS_ACTION);

if (array_key_exists('EXPORT', $_GET)) {
    $offset = 0;

    $hash = md5(time());
    $filename = "export-$hash.csv";
    $csvFile = fopen(__DIR__ . "/$filename", 'w');
    fwrite($csvFile, "\xEF\xBB\xBF");

    do {
        $sql = "SELECT *, $agg  FROM $TABLE_NAME $where LIMIT $limit OFFSET $offset";
        $arResult = $db->get_results($sql);

        if (count($arResult) === 0) break;
        if ($offset === 0) fputcsv($csvFile, array_map(function ($value) use ($MESS) {
            return $MESS[$value] ?? $value;
        }, array_keys($arResult[0])), ";");

        foreach ($arResult as $value) {
            fputcsv($csvFile, $value, ";");
        }
        $offset += $limit;
    } while ($offset <= $total_records);
    fclose($csvFile);

    return responseCsvDownLoad(__DIR__ . "/$filename");
}

if (isset($_GET['action']) && $_GET['action'] === 'filter') {
    $total_pages = ceil($total_records / $limit);
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $offset = ($page - 1) * $limit;
    $sql = "SELECT *, $agg  FROM $TABLE_NAME $where LIMIT $limit OFFSET $offset";
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
                        <input name="filter[kod]" type="text" class="form-control" id="inlineFormInputKod" placeholder="Код товара" value="<?php echo $_GET["filter"]["kod"] ?? '' ?>">
                    </div>
                    <div class="col-sm-3 mb-3">
                        <label for="inlineFormInputSezon">Сезон</label>
                        <select name="filter[sezon]" id="inlineFormInputSezon" class="form-control">
                            <option value="">Выберите значение</option>
                            <?php foreach ($sezons  as $sezon) : ?>
                                <option value="<?php echo  $sezon ?>" <?php echo ((isset($_GET["filter"]['action']) && $_GET["filter"]['sezon'] == $sezon) ? 'selected=""' : '') ?>>
                                    <?php echo $sezon ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div hidden class="col-sm-4 mb-3">
                        <label for="inlineFormInputGroupDateTo">Дата добавления изменений</label>
                        <div class="input-group">
                            <input name="filter[dateFrom]" type="date" class="form-control" id="inlineFormInputGroupDateTo" placeholder="от" value="<?php echo $arResult["FILTER"]["dateFrom"] ?>">
                            <input name="filter[dateTo]" type="date" class="form-control" placeholder="до" value="<?php echo $arResult["FILTER"]["dateTo"] ?? '' ?>">
                        </div>
                    </div>
                    <div class="col-sm-2 mb-3">
                        <label for="inlineFormInputAction">Действие не совершалось</label>
                        <select id="inlineFormInputAction" name="filter[action]" class="form-control">
                            <option value="">Выберите значение</option>
                            <?php foreach ($TABLE_FIELDS_ACTION as $action) : ?>
                                <option value="<?php echo $action ?>" <?php echo ((isset($_GET["filter"]['action']) && ($_GET["filter"]['action'] == $action)) ? 'selected=""' : '') ?>>
                                    <?php echo $MESS[$action] ?? $action ?>
                                <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-sm-2 mb-3">
                        <label for="inlineFormInputAction">Действие совершалось</label>
                        <select id="inlineFormInputAction" name="filter[done_action]" class="form-control">
                            <option value="">Выберите значение</option>
                            <?php foreach ($TABLE_FIELDS_ACTION as $action) : ?>
                                <option value="<?php echo $action ?>" <?php echo ((isset($_GET["filter"]['action']) && $_GET["filter"]['done_action'] == $action) ? 'selected=""' : '') ?>>
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
            <?php if (isset($arResult)) : ?>
                <div class='mb-4 clearfix'>
                    <p class='float-left'>Найдено: <?php echo count($arResult) ?></p>
                    <?php
                    if (count($arResult) > 0)
                        $link = makeLinkWithQuery($_GET, "$_SERVER[HTTP_REFERER]", 'EXPORT', '');
                    echo "<a class='btn btn-success float-right' href='$link'>Скачать</a>";
                    ?>
                </div>
                <p class='float-left'>Показаны записи с: <?php echo $offset + 1 ?> по <?php echo $offset + $limit ?></p>
                <?php if (count($arResult) > 0) : ?>
                    <table class="table table-sm table-hover table-bordered fixtable small">
                        <thead>
                            <tr>
                                <?php foreach ($arResult[0] as $key => $value) : ?>
                                    <th scope="col">
                                        <span style="white-space: break-spaces;">
                                            <?php echo $MESS[$key] ?? $key ?>
                                        </span>
                                    </th>
                                <?php endforeach;
                                ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($arResult as $arItem) : ?>
                                <tr>
                                    <?php foreach ($arItem as $arCol) : ?>
                                        <td class="<?php echo validateDate($arCol) ? 'font-weight-bold' : '' ?> <?php echo isset($arCol) ? 'bg-success' : '' ?> ">
                                            <?php
                                            echo $arCol;
                                            ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
                <div class="d-flex justify-content-center">
                    <?php if ($total_pages > 1) { ?>
                        <ul class="pagination">
                            <?php
                            $current_page = isset($_GET['page']) ? $_GET['page'] : 1;
                            $pages_to_show = 5; // Количество отображаемых страниц в пагинации
                            $half = floor($pages_to_show / 2);

                            $start_page = max(1, $current_page - $half);
                            $end_page = min($total_pages, $start_page + $pages_to_show - 1);

                            if ($start_page > 1) {
                                $link = makeLinkWithQuery($_GET, "$_SERVER[HTTP_REFERER]", 'page', ($start_page - 1));
                            ?>
                                <li class='page-item'>
                                    <a class="page-link" href="<?php echo $link ?>">Предыдущая</a>";
                                </li>
                            <?php }

                            for ($i = $start_page; $i <= $end_page; $i++) {
                                $class = ($i == $current_page) ? 'active' : '';
                                $link = makeLinkWithQuery($_GET, "$_SERVER[HTTP_REFERER]", 'page', $i);
                            ?>
                                <li class='page-item  <?php echo $class ?>'>
                                    <a class="page-link" href="<?php echo $link ?>">
                                        <?php echo $i ?>
                                    </a>
                                </li>
                            <?php   }

                            if ($end_page < $total_pages) {
                                $link = makeLinkWithQuery($_GET, "$_SERVER[HTTP_REFERER]", 'page', ($end_page + 1));
                            ?>
                                <li class='page-item'>
                                    <a class="page-link" href="<?php echo $link ?>">Следующая</a>
                                </li>
                            <?php } ?>
                        </ul>
                    <?php } ?>
                </div>
            <?php endif; ?>
        </div>
    </div>



    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
    <script type="text/javascript" src="css/script.js?<?php echo time() ?>"></script>

</body>

</html>
