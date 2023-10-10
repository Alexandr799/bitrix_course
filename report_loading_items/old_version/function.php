<?php
header('Content-Type: text/html; charset=utf-8');
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL & ~E_NOTICE);

require_once(__DIR__ . '/db/db.php' );

function Debug($var, $all = true) {

	?>
		<font style="text-align: left; font-size: 12px"><pre><?var_dump($var)?></pre></font><br>
	<?php
}
//Initiate the class
Class Reports {
	private $lines = [];
	private $database;

	public function __construct()
    {
		$this->lines = file('http://pioneergroupmobile.ru/upload/1c_catalog/importFull.csv');
		$this->database = new DB();
	}

	private function arrayXmlId ($arXmlId, $action) {
		$arResult["status"] = false;
		$statusError = [];

		foreach ($arXmlId as $xmlId) {
			$arActionsItem = $this->searchItem($xmlId);

			$isCreate1C = false;
			foreach ($arActionsItem as $arAction) {

				if ($arAction["NAME"] == "ACTION_1C_CREATE") {
					$isCreate1C = true;
				}
			}

			if ($isCreate1C) {

				if ($arActionsItem) {
					$isAction = false;
					$idItem = 0;
					foreach ($arActionsItem as $arAction) {
						$idItem = $arAction["ITEM_ID"];
						if ($arAction["NAME"] == $action) {
							$isAction = true;
						}
					}

					//Add action
					if (!$isAction && $idItem) {
						$idAction = $this->addAction($idItem, $action);

						if ($idAction) {
							//$arResult["status"][$xmlId] = true;
						} else {
							$statusError[$xmlId] = 'Ошибка добавления действия ' . $xmlId . ' в отчет.';
						}
					} else {
						if ($isAction) {
							$statusError[$xmlId] = 'Повторное добавление действия "'.$action.'" ' . $xmlId . ' в отчет.';
						}

						if (!$idItem) {
							$statusError[$xmlId] = 'Товар не найден "'.$action.'" ' . $xmlId . ' в отчет.';
						}

					}

				} else {
					$arDataItem = $this->searchDataItem($xmlId);

					if ($arDataItem) {

						$idItem = $this->addItem($arDataItem);

						if ($idItem) {
							$idAction = $this->addAction($idItem, $action);

							if ($idAction) {
								//$arResult["status"][$xmlId] = true;
							} else {
								//$arResult["errorText"][$xmlId] = 'Ошибка добавления действия "'.$action.'", "' . $xmlId . '" в отчет.';
								$statusError[$xmlId] = 'Ошибка добавления действия "'.$action.'", "' . $xmlId . '" в отчет.';
							}
						} else {
							//$arResult["errorText"][$xmlId] = 'Ошибка добавления товара "'.$action.'", "' . $xmlId . '" в отчет.';
							$statusError[$xmlId] = 'Ошибка добавления товара "'.$action.'", "' . $xmlId . '" в отчет.';
						}

					} else {
						//$arResult["errorText"][$xmlId] = 'XML_ID ' . $xmlId . ' не найден  в файле.';
						$statusError[$xmlId] = 'XML_ID ' . $xmlId . ' не найден  в файле.';
					}

				}
			}
		}

		if (empty($statusError)) {
			$arResult["status"] = true;
		} else {
			$arResult["errorText"] = $statusError;
		}

		return $arResult;
	}

	public function create ($dataCreate = []) {

		$arResult["status"] = false;
		$arResult["multi"] = false;
		$idAction = 0;

		//$action = mb_strtoupper(trim($dataCreate["action"]));
		//$arXmlId = $dataCreate["xml_id"];

		if (!empty($dataCreate)) {

			$result = $this->createXmlId($dataCreate);
			if ($result["status"]) {
				$arResult["status"] = $result["status"];
			} else {
				$arResult["errorText"] = $result["errorText"];
			}


		}

		return $arResult;
	}

	private function createXmlId ($dataCreate) {
		$arResult["status"] = false;
		$action = mb_strtoupper(trim($dataCreate["action"]));
		$xmlId = $dataCreate["xml_id"];

		//Search actions for items
		$arActionsItem = $this->searchItem($xmlId);

		$isCreate1C = false;
		foreach ($arActionsItem as $arAction) {

			if ($arAction["NAME"] == "ACTION_1C_CREATE") {
				$isCreate1C = true;
			}
		}

		//if ($isCreate1C) {
			if ($arActionsItem) {
				if ($isCreate1C) {
					$isAction = false;
					$idItem = 0;
					foreach ($arActionsItem as $arAction) {
						$idItem = $arAction["ITEM_ID"];
						if ($arAction["NAME"] == $action) {
							$isAction = true;
						}
					}

					//Add action
					if (!$isAction && $idItem) {
						$idAction = $this->addAction($idItem, $action);

						if ($idAction) {
							$arResult["status"] = true;
						} else {
							$arResult["errorText"] = 'Ошибка добавления действия ' . $xmlId . ' в отчет.';
						}
					} else {
						if ($isAction) {
							$arResult["errorText"] = 'Повторное добавление действия "'.$action.'" ' . $xmlId . ' в отчет.';
						}

						if (!$idItem) {
							$arResult["errorText"] = 'Товар не найден "'.$action.'" ' . $xmlId . ' в отчет.';
						}

					}
				}

			} else {
				if ($action == "ACTION_1C_CREATE") {
					//$arDataItem = $this->searchDataItem($xmlId);
					$arDataItem["NAME"] = trim($dataCreate["name"]);
					$arDataItem["XML_ID"] = $dataCreate["xml_id"];
					$arDataItem["COLOR"] = $dataCreate["color"] ? $dataCreate["color"] : '';
					$arDataItem["KOD"] = $dataCreate["kod"] ? $dataCreate["kod"] : '';
					$arDataItem["SEZON"] = $dataCreate["sezon"] ? $dataCreate["sezon"] : '';

					if ($arDataItem) {
						//file_put_contents(__DIR__ . '/log.txt', var_export(['date'=> date('d.m.Y H:i:s'), 'trim' => $arDataItem], true)."\r\n", FILE_APPEND);
						$idItem = $this->addItem($arDataItem);

						if ($idItem) {
							$idAction = $this->addAction($idItem, $action);

							if ($idAction) {
								$arResult["status"] = true;
							} else {
								$arResult["errorText"] = 'Ошибка добавления действия "'.$action.'", "' . $xmlId . '" в отчет.';
							}
						} else {
							$arResult["errorText"] = 'Ошибка добавления товара "'.$action.'", "' . $xmlId . '" в отчет.';
						}

					} else {
						$arResult["errorText"] = 'XML_ID ' . $xmlId . ' не найден  в файле.';
					}
				}

			}
		//}
		return $arResult;
	}

	private function stringXmlId ($xmlId, $action, $date = '') {
		$arResult["status"] = false;

		//Search actions for items
		$arActionsItem = $this->searchItem($xmlId);

		$isCreate1C = false;
		foreach ($arActionsItem as $arAction) {

			if ($arAction["NAME"] == "ACTION_1C_CREATE") {
				$isCreate1C = true;
			}
		}

		//if ($isCreate1C) {
			if ($arActionsItem) {
				if ($isCreate1C) {
					$isAction = false;
					$idItem = 0;
					foreach ($arActionsItem as $arAction) {
						$idItem = $arAction["ITEM_ID"];
						if ($arAction["NAME"] == $action) {
							$isAction = true;
						}
					}

					//Add action
					if (!$isAction && $idItem) {
						$idAction = $this->addAction($idItem, $action, $date);

						if ($idAction) {
							$arResult["status"] = true;
						} else {
							$arResult["errorText"] = 'Ошибка добавления действия ' . $xmlId . ' в отчет.';
						}
					} else {
						if ($isAction) {
							$arResult["errorText"] = 'Повторное добавление действия "'.$action.'" ' . $xmlId . ' в отчет.';
						}

						if (!$idItem) {
							$arResult["errorText"] = 'Товар не найден "'.$action.'" ' . $xmlId . ' в отчет.';
						}

					}
				} else {
					$arResult["errorText"] = 'Данные о старом товаре "'.$action.'" ' . $xmlId . '.';
				}

			}/* else {
				$arDataItem = $this->searchDataItem($xmlId);

				if ($arDataItem) {

					$idItem = $this->addItem($arDataItem);

					if ($idItem) {
						$idAction = $this->addAction($idItem, $action, $date);

						if ($idAction) {
							$arResult["status"] = true;
						} else {
							$arResult["errorText"] = 'Ошибка добавления действия "'.$action.'", "' . $xmlId . '" в отчет.';
						}
					} else {
						$arResult["errorText"] = 'Ошибка добавления товара "'.$action.'", "' . $xmlId . '" в отчет.';
					}

				} else {
					$arResult["errorText"] = 'XML_ID ' . $xmlId . ' не найден  в файле.';
				}

			}*/

		/*} else {
			$arResult["errorText"] = 'Данные о старом товаре "'.$action.'" ' . $xmlId . '.';
		}*/

		return $arResult;
	}

	public function request ($arXmlId, $action = '') {
		$action = mb_strtoupper(trim($action));

		$arResult["status"] = false;
		$arResult["multi"] = false;
		$idAction = 0;

		if (is_array($arXmlId)) {
			$arResult["multi"] = true;
			$result = $this->arrayXmlId($arXmlId, $action);
			if ($result["status"]) {
				$arResult["status"] = $result["status"];
			} else {
				$arResult["errorText"] = $result["errorText"];
			}
		} else {
			$result = $this->stringXmlId($arXmlId, $action);
			if ($result["status"]) {
				$arResult["status"] = $result["status"];
			} else {
				$arResult["errorText"] = $result["errorText"];
			}

			//Debug($arItem);
		}

		return $arResult;
	}

	private function arrayKodColor ($arKodColor, $action) {
		$arResult["status"] = false;
		$statusError = [];


		foreach ($arKodColor as $arItem) {
			$kod = (int) $arItem[0];
			$color = trim($arItem[1]);

			$arActionsItem = $this->searchItemPhoto($kod, $color);

			$isCreate1C = false;
			foreach ($arActionsItem as $arAction) {

				if ($arAction["NAME"] == "ACTION_1C_CREATE") {
					$isCreate1C = true;
				}
			}

			if ($isCreate1C) {

				if ($arActionsItem) {
					$isAction = false;
					$idItem = 0;
					foreach ($arActionsItem as $arAction) {
						$idItem = $arAction["ITEM_ID"];
						$xmlId = $arAction["XML_ID"];
						if ($arAction["NAME"] == $action) {
							$isAction = true;
						}
					}

					//Add action
					if (!$isAction && $idItem) {
						$idAction = $this->addAction($idItem, $action);

						if ($idAction) {
							//$arResult["status"][$xmlId] = true;
						} else {
							$statusError[$xmlId] = 'Ошибка добавления действия ' . $xmlId . ' в отчет.';
						}
					} else {
						if ($isAction) {
							$statusError[$xmlId] = 'Повторное добавление действия "'.$action.'" ' . $xmlId . ' в отчет.';
						}

						if (!$idItem) {
							$statusError[$xmlId] = 'Товар не найден "'.$action.'" ' . $xmlId . ' в отчет.';
						}

					}

				}/* else {
					$arDataItem = $this->searchDataItemPhoto($kod, $color);

					if ($arDataItem) {
						$xmlId = $arDataItem["XML_ID"];

						$idItem = $this->addItem($arDataItem);

						if ($idItem) {
							$idAction = $this->addAction($idItem, $action);

							if ($idAction) {
								//$arResult["status"][$xmlId] = true;
							} else {
								$statusError[$xmlId] = 'Ошибка добавления действия "'.$action.'", "' . $xmlId . '" в отчет.';
							}
						} else {
							$statusError[$xmlId] = 'Ошибка добавления товара "'.$action.'", "' . $xmlId . '" в отчет.';
						}

					} else {
						$statusError[implode('_', $arItem)] = 'Код/цвет ' . implode('_', $arItem) . ' не найден  в файле.';
					}
				}*/
			}
		}


		if (empty($statusError)) {
			$arResult["status"] = true;
		} else {
			$arResult["errorText"] = $statusError;
		}
		return $arResult;
	}

	public function requestPhoto ($arKodColor, $action = '') {
		$action = mb_strtoupper(trim($action));

		$arResult["status"] = false;

		$result = $this->arrayKodColor($arKodColor, $action);
		if ($result["status"]) {
			$arResult["status"] = $result["status"];
		} else {
			$arResult["errorText"] = $result["errorText"];
		}

		return $arResult;
	}

	public function requestSiteData ($xml_site = []) {

		$arResult["status"] = true;

		foreach ($xml_site as $arXml) {
			$xmlId = $arXml['UF_XML_ID'];
			$action = $arXml['UF_ACTION'];
			$date = $arXml['UF_DATE_CREATE'];

			$this->stringXmlId($xmlId, $action, $date);
		}


		return $arResult;
	}

	private function searchItem ($xmlId) {
		$strSql = "
		SELECT ri.XML_ID, ra.*
		FROM report_items as ri
		LEFT JOIN report_actions as ra
			ON ri.ID = ra.ITEM_ID
		WHERE
			ri.XML_ID = '".$this->database->filter($xmlId)."'
		";

		$row = $this->database->get_results($strSql);

		return $row;
	}

	private function searchItemPhoto ($kod, $color) {
		$strSql = "
		SELECT ri.XML_ID, ra.*
		FROM report_items as ri
		LEFT JOIN report_actions as ra
			ON ri.ID = ra.ITEM_ID
		WHERE
			ri.KOD = '".$this->database->filter($kod)."' AND
			ri.COLOR = '".$this->database->filter($color)."'
		";

		$row = $this->database->get_results($strSql);

		return $row;
	}

	private function searchDataItemPhoto ($kod, $color) {

		$statusFind = false;
		$arResult = [];

		foreach ($this->lines as $line_num => $line) {
			if (substr_count($line, '0'.$kod) && substr_count($line, $color)) {
				$statusFind = true;

				$arString = explode(';', $line);
				$strArt = ltrim(trim($arString["0"]), "0");

				$arColorSize = explode(',', $arString["3"]);
				$color = isset($arColorSize[0]) ? trim($arColorSize[0]) : '';

				$arResult["NAME"] = trim($arString["1"]);
				$arResult["XML_ID"] = array_shift(explode('s', $arString["10"]));
				$arResult["COLOR"] = $color;
				$arResult["KOD"] = $strArt;

				break;
			}
		}

		return $arResult;
	}

	private function searchDataItem ($xmlId) {

		$statusFind = false;
		$arResult = [];

		foreach ($this->lines as $line_num => $line) {
			if (substr_count($line, $xmlId)) {
				$statusFind = true;

				$arString = explode(';', $line);
				$strArt = ltrim(trim($arString["0"]), "0");

				$arColorSize = explode(',', $arString["3"]);
				$color = isset($arColorSize[0]) ? trim($arColorSize[0]) : '';

				$arResult["NAME"] = trim($arString["1"]);
				$arResult["XML_ID"] = $xmlId;
				$arResult["COLOR"] = $color;
				$arResult["KOD"] = $strArt;

				break;
			}
		}

		return $arResult;
	}

	private function addAction($idItem, $action, $date = '', $auto = null){
		if (is_null($date)) {
			$date = null;
		} else if($date == '') {
			$date = date('Y-m-d H:i:s');
		} else {
			$date = date('Y-m-d H:i:s', strtotime($date));
		}

		$arData["NAME"] = $action;
		$arData["DATE"] = $date;
		$arData["ITEM_ID"] = $idItem;
		$arData["AUTO"] = $auto;

		$idAction = $this->database->insert('report_actions', $arData);

		if (is_null($auto)) {
			$names = ['ACTION_1C_SEND_PHOTO','ACTION_1C_APPLY_PHOTO','ACTION_PHOTO_CREATE','ACTION_PHOTO_PROCESSED','ACTION_PICTURE',];

			$strSql = "select
				max(case when (NAME='ACTION_1C_SEND_PHOTO') then TRUE else FALSE end) as 'ACTION_1C_SEND_PHOTO',
				max(case when (NAME='ACTION_1C_APPLY_PHOTO') then TRUE else FALSE end) as 'ACTION_1C_APPLY_PHOTO',
				max(case when (NAME='ACTION_PHOTO_CREATE') then TRUE else FALSE end) as 'ACTION_PHOTO_CREATE',
				max(case when (NAME='ACTION_PHOTO_PROCESSED') then TRUE else FALSE end) as 'ACTION_PHOTO_PROCESSED',
				max(case when (NAME='ACTION_PICTURE') then TRUE else FALSE end) as 'ACTION_PICTURE',
				max(case when (NAME='ACTION_TEXT') then TRUE else FALSE end) as 'ACTION_TEXT',
				max(case when (NAME='ACTION_VIDEO') then TRUE else FALSE end) as 'ACTION_VIDEO'
				from report_actions
				group by ITEM_ID
				HAVING ITEM_ID = {$idItem}";

			if ($arDBRes = $this->database->get_results($strSql)) {
				foreach ($arDBRes as $row) {
					$fieldNamesForInsert = [];
					$fieldNames = [];
					foreach ($row as $field_name => $value) {
						if ($value == 0) {
							if (in_array($field_name, $names)){
								$fieldNames[] = $field_name;
							}
						} else {
							$fieldNamesForInsert = [...$fieldNamesForInsert, ...$fieldNames];
							$fieldNames = [];
						}
					}

					foreach ($fieldNamesForInsert as $actionName) {
						$this->addAction($idItem, $actionName, null, true);
					}
				}
			}
		}

		return $idAction;
		/*
		report_actions

		NAME
		DATE
		ITEM_ID
		*/
	}

	private function addItem($arItem){

		$arData["NAME"] = $arItem["NAME"];
		$arData["XML_ID"] = $arItem["XML_ID"];
		$arData["COLOR"] = $arItem["COLOR"];
		$arData["KOD"] = $arItem["KOD"];
		$arData["SEZON"] = $arItem["SEZON"];
		//file_put_contents(__DIR__ . '/log.txt', var_export(['date'=> date('d.m.Y H:i:s'), 'addItem' => $arData], true)."\r\n", FILE_APPEND);
		$idItem = $this->database->insert('report_items', $arData);

		return $idItem;
		/*
		report_items
		NAME
		XML_ID
		COLOR
		KOD
		*/
	}
}

Class TableItems {
	private $database;

	function __construct()
    {
		$this->database = new DB();

		$this->actualColsTable();
	}

	function actualColsTable() {
		$actualCols = $this->colReport();

		$arRow = $this->colView();

		$arResult = [];

		if ($arRow) {
			foreach ($arRow as $row) {
				$arResult[] = $row["CODE"];
			}
		}

		foreach ($actualCols as $colName) {
			if (!in_array($colName,$arResult)) {
				$arData["CODE"] = $colName;
				$arData["SORT"] = 100;
				$arData["VIEW_FILTER"] = 1;

				$this->database->insert('report_cols', $arData);
			}
		}
	}

	function colView($viewFilter = false) {
		$strSql = "
			SELECT *
			FROM report_cols
		";

		if ($viewFilter) {
			$strSql .= " WHERE VIEW_FILTER = 1";
		}

		$strSql .= " ORDER BY SORT ASC";


		$arRow = $this->database->get_results($strSql);

		$arResult = [];

		if ($arRow) {
			foreach ($arRow as $row) {
				$arResult[] = $row;
			}
		}

		return $arResult;
	}

	function colSezon() {
		$strSql = "
			SELECT `SEZON`
			FROM report_items GROUP BY `SEZON`
		";

		$arRow = $this->database->get_results($strSql);

		$arResult = [];

		if ($arRow) {
			foreach ($arRow as $row) {
				if (!empty($row['SEZON'])) {
					$arResult[] = $row['SEZON'];
				}
			}
		}

		usort($arResult, function($a, $b){
			$_a = explode(' ', $a);
			$_b = explode(' ', $b);

			return (int)$_b[1] <=> (int)$_a[1];
		});

		return $arResult;
	}

	function colReport($filter = []){
		$strSql = "
			SELECT DISTINCT NAME
			FROM report_actions
			WHERE 1=1
			GROUP BY
				NAME
		";

		$arRow = $this->database->get_results($strSql);

		$arResult = [];

		if ($arRow) {
			foreach ($arRow as $row) {
				$arResult[] = $row["NAME"];
			}
		}

		return $arResult;
	}

	function deleteReport($emptyFilter = false){
		$strSql = "
		DELETE FROM report_actions WHERE NAME = 'ACTION_TEXT_1C'
		";

		$arRow = $this->database->delete("report_actions", array('NAME' => "ACTION_TEXT_1C"));


	}

	function filtration($filter = []){
		$ids = [];
		$conditionForKod = "";
		$conditionForBrand = "";
		$conditionForSezon = "";
		$conditionForBrandAndSezon = "";
		$conditionForDoneFilter = "";
		$joinForDoneFilter = "";

		if (!empty($filter['kod'])) {//если есть код, то берем только по коду
			$kod = htmlspecialchars($filter['kod']);
			$conditionForKod = "AND `KOD` = {$kod}";
			$strSql = "
				SELECT ID as product_id
				FROM report_items
				WHERE
					1 = 1
					AND `KOD` = {$kod}
				";
			$arRow = $this->database->get_results($strSql);
			if ($arRow) {
				$ids = array_map(function($e){
					return $e['product_id'];
				}, $arRow);
			} else {
				return [];
			}
		}

		if (!empty($filter['brand']) && empty($filter['kod']) && empty($filter['sezon'])) {//только по бренду
			$brand = htmlspecialchars($filter['brand']);
			$conditionForBrand = "AND `NAME` LIKE '%{$brand}%'";

			$strSql = "
				SELECT ID as product_id
				FROM report_items
				WHERE
					1 = 1
					{$conditionForBrand}
				";
			$arRow = $this->database->get_results($strSql);
			if ($arRow) {
				$ids = array_map(function($e){
					return $e['product_id'];
				}, $arRow);
			} else {
				return [];
			}
		}

		if (!empty($filter['sezon']) && empty($filter['kod']) && empty($filter['brand'])) {//только по сезону
			$sezon = htmlspecialchars($filter['sezon']);
			$conditionForSezon = "AND `SEZON` LIKE '%{$sezon}%'";
			$strSql = "
				SELECT ID as product_id
				FROM report_items
				WHERE
					1 = 1
					AND `SEZON` LIKE '%{$sezon}%'
				";
			$arRow = $this->database->get_results($strSql);
			if ($arRow) {
				$ids = array_map(function($e){
					return $e['product_id'];
				}, $arRow);
			} else {
				return [];
			}
		}

		if (!empty($filter['sezon']) && empty($filter['kod']) && !empty($filter['brand'])) {//по сезону и бренду
			$sezon = htmlspecialchars($filter['sezon']);
			$brand = htmlspecialchars($filter['brand']);
			$conditionForBrandAndSezon = "AND `SEZON` LIKE '%{$sezon}%' AND `NAME` LIKE '%{$brand}%'";
			$strSql = "
				SELECT ID as product_id
				FROM report_items
				WHERE
					1 = 1
					AND `SEZON` LIKE '%{$sezon}%'
					AND `NAME` LIKE '%{$brand}%'
				";
			$arRow = $this->database->get_results($strSql);
			if ($arRow) {
				$ids = array_map(function($e){
					return $e['product_id'];
				}, $arRow);
			} else {
				return [];
			}
		}

		if (!empty($filter['done_action'])) {
			$joinForDoneFilter = "JOIN report_actions ra ON ra.ITEM_ID = ri.ID";
			$conditionForDoneFilter = "AND ra.`NAME` = '{$filter['done_action']}'";
		}

		if (!empty($filter['action'])) {
			// $dateFrom = htmlspecialchars($filter['dateFrom']);
			// $dateTo = htmlspecialchars($filter['dateTo']);
			// $sqlDateFrom = !empty($dateFrom) ? $dateFrom : '2000-01-01 00:00:00';
			// $sqlDateTo = !empty($dateTo) ? "'" . $dateTo . "'" : 'CURRENT_DATE()'; не включительно!!!
			//--AND (`DATE` BETWEEN '{$sqlDateFrom}' AND {$sqlDateTo})

			$strSql = "
				SELECT ri.ID as ID
				FROM report_items ri
				{$joinForDoneFilter}
				WHERE
					1 = 1
					{$conditionForDoneFilter}
					{$conditionForKod}
					{$conditionForBrand}
					{$conditionForSezon}
					{$conditionForBrandAndSezon}
					AND ri.ID NOT IN(SELECT ITEM_ID
						FROM report_actions
						WHERE
							1 = 1
							AND `NAME` = '{$filter['action']}')
				";

			$arRow = $this->database->get_results($strSql);
			if ($arRow) {

				$ids = array_map(function($e){
					return $e['ID'];
				}, $arRow);
			} else {
				return [];
			}
		}

		if (empty($filter['action']) && !empty($filter['done_action'])) {
			$strSql = "
				SELECT ri.ID as ID
				FROM report_items ri
				{$joinForDoneFilter}
				WHERE
					1 = 1
					{$conditionForDoneFilter}
					{$conditionForKod}
					{$conditionForBrand}
					{$conditionForSezon}
					{$conditionForBrandAndSezon}
				";

			$arRow = $this->database->get_results($strSql);
			if ($arRow) {

				$ids = array_map(function($e){
					return $e['ID'];
				}, $arRow);
			} else {
				return [];
			}
		}

		return $ids;
	}

	 function viewReport($itemsIds = []){
		$strSql = "
		SELECT #COUNT# ri.NAME as ITEM_NAME, ri.COLOR, ri.KOD, ri.XML_ID, ra.*
		FROM report_items as ri
		JOIN report_actions as ra
			ON ri.ID = ra.ITEM_ID
		WHERE
			1 = 1
		";

		$sqlWhereCount = '';
		$emptyFilter = true;

		if (!empty($itemsIds)) {
			$strSql .= " AND ri.ID IN ( ". implode(',', $itemsIds) .')';
			$emptyFilter = false;
		}
		// if (!empty($filter)) {
		// 	$filter = $this->database->filter($filter);
		// 	if (!empty($filter["brand"])) {
		// 		$strSql .= " AND ri.NAME LIKE '%".$filter["brand"]."%'";
		// 		$emptyFilter = false;
		// 	}
		// 	if (!empty($filter["kod"])) {
		// 		$strSql .= " AND ri.KOD = '".$filter["kod"]."'";
		// 		$emptyFilter = false;
		// 	}/*
		// 	if (!empty($filter["dateFrom"])) {
		// 		$strSql .= $sqlWhereCount .= " AND ra.DATE >= '".$filter["dateFrom"]." 00:00:00'";
		// 		$emptyFilter = false;
		// 	}
		// 	if (!empty($filter["dateTo"])) {
		// 		$strSql .= $sqlWhereCount .= " AND ra.DATE <= '".$filter["dateFrom"]." 23:59:59'";
		// 		$emptyFilter = false;
		// 	}*/

		// 	$isStrongLogic = false;
		// 	$sqlOr = [];
		// 	foreach ($filter as $filterName => $filterVal) {
		// 		if (substr_count($filterName, 'ACTION_')) {
		// 			if ($filterVal == "Y") {
		// 				$sqlOr[] = "NAME = '".$filterName."'";
		// 				$emptyFilter = false;
		// 			}

		// 			if ($filterVal == "N") {
		// 				$sqlOr[] = "NAME <> '".$filterName."'";
		// 				$emptyFilter = false;
		// 			}
		// 			$isStrongLogic = true;
		// 		}
		// 	}

		// 	$strSqlDate = '';
		// 	if (!empty($filter["dateFrom"]) || !empty($filter["dateTo"])) {

		// 		$emptyFilter = false;
		// 		$isStrongLogic = true;


		// 		if (!empty($filter["dateFrom"])) {
		// 			$strSqlDate .= " AND DATE >= '".$filter["dateFrom"]." 00:00:00'";

		// 		}
		// 		if (!empty($filter["dateTo"])) {
		// 			$strSqlDate .= " AND DATE <= '".$filter["dateTo"]." 23:59:59'";

		// 		}

		// 	}

		// 	if ($isStrongLogic){
		// 		$strSqlStrongLogic = " AND ra.ITEM_ID IN (SELECT ITEM_ID FROM report_actions WHERE 1=1";

		// 		if ($strSqlDate) {
		// 			$strSqlStrongLogic .= $strSqlDate;
		// 		}
		// 		if ($sqlOr) {
		// 			 $strSqlStrongLogic .= " AND (" . implode(' OR ', $sqlOr) . ")";
		// 			//$strSqlStrongLogic .= " AND " . implode(' AND ', $sqlOr);
		// 		}

		// 		$strSqlStrongLogic .= ")";


		// 		$strSql .= $strSqlStrongLogic;
		// 		$sqlWhereCount .= $strSqlStrongLogic;
		// 	}






		// }

		$sqlWhereCount = str_replace('ra.', '', $sqlWhereCount);
		/*
		$strSql = str_replace("#COUNT#", "(
			SELECT COUNT(ID)
			FROM report_actions
			WHERE
				ITEM_ID = ra.ITEM_ID #WHERE#
			GROUP BY ITEM_ID
		) AS COUNT,", $strSql);

		$strSql = str_replace("#WHERE#", $sqlWhereCount, $strSql);*/
		$strSql = str_replace("#COUNT#", "(
			SELECT COUNT(ID)
			FROM report_actions
			WHERE
				ITEM_ID = ra.ITEM_ID
			GROUP BY ITEM_ID
		) AS COUNT_ACTION,", $strSql);

		//Debug($filter);


		//Debug($strSql);

		if (!$emptyFilter) {
			$arRow = $this->database->get_results($strSql);
		}

		$arResult["ACTIONS"] = [];
		$arResult["ITEMS"] = [];

		if ($arRow) {
			foreach ($arRow as $row) {
				$row["DATE"] = date('d.m.Y H:i:s', strtotime($row["DATE"]));
				$arResult["ACTIONS"][$row["ITEM_ID"]][$row["NAME"]] = $row;
				$arResult["ITEMS"][$row["ITEM_ID"]] = $row;
			}
		}


		return $arResult;
	}

	function filterParam ($arFilter = []){
		$empty = true;
		foreach ($arFilter as &$filter) {
			if (is_array($filter)) {
				$filter = $this->filterParam($filter);
			} else {
				if ($filter) {
					$filter = htmlspecialchars(trim($filter));
					$empty = false;
				}
			}
		}
		$arFilter["EMPTY"] = $empty;

		return $arFilter;
	}
}

$arResult["COL"] = [];
$arResult["ROW"] = [];

$objTableItems = new TableItems;
$filterParams = $objTableItems->colView(true);
$filterSezon = $objTableItems->colSezon();


if (isset($_GET["action"])) {
	if (htmlspecialchars($_GET["action"]) == "filter") {
		$filter = is_array($_GET["filter"]) ? $_GET["filter"] : htmlspecialchars($_GET["filter"]);
		$itemsIds = $objTableItems->filtration($filter);
		$arResult["FILTER"] = $objTableItems->filterParam($filter);
		$arResult["COL"] = $objTableItems->colView();
		$arResult["ROW"] = $objTableItems->viewReport($itemsIds);
	}
}

function download_send_headers($filename) {
    // disable caching
    $now = gmdate("D, d M Y H:i:s");
    header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
    header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
    header("Last-Modified: {$now} GMT");

    // force download
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");

    // disposition / encoding on response body
    header("Content-Disposition: attachment;filename={$filename}");
    header("Content-Transfer-Encoding: binary");
}

function utf8to1251(&$text) {
	//$text = iconv("utf-8", "windows-1252", $text); //without return
}

function array2csv(array &$array, $titles) {
    if (count($array) == 0) {
        return null;
    }
    ob_start();
    $df = fopen("php://output", 'w');
    echo "\xEF\xBB\xBF";
	//array_walk_recursive($titles, "utf8to1251");
    fputcsv($df, $titles, ';');

	//array_walk_recursive($array, "utf8to1251");
    foreach ($array as $row) {
        fputcsv($df, $row, ';');
    }
    fclose($df);
    return ob_get_clean();
}

function getInfoWithTitles($arResult) {
	$th = [];
	$th[] = 'Код';
	$th[] = 'Название';
	$th[] = 'Цвет';
	$th[] = 'ID';

	foreach ($arResult["COL"] as $arCol) {
		$th[] = $arCol['NAME'];
	}

	$data = [];
	foreach ($arResult["ROW"]["ITEMS"] as $arItem) {
		$tr = [];
		$tr[] = $arItem["KOD"];
		$tr[] = $arItem["ITEM_NAME"];
		$tr[] = $arItem["COLOR"];
		$tr[] = $arItem["ITEM_ID"];

		foreach ($arResult["COL"] as $arCol) {
			if (!is_null($arResult["ROW"]["ACTIONS"][$arItem["ITEM_ID"]][$arCol["CODE"]]["DATE"])) {
				$dtstmp = strtotime($arResult["ROW"]["ACTIONS"][$arItem["ITEM_ID"]][$arCol["CODE"]]["DATE"]);
				if ($dtstmp > 0) {
					$tr[] = date('d.m.Y', $dtstmp);
				} else {
					$tr[] = 'auto';
				}
			} else {
				$tr[] = '';
			}
		}

		$data[] = $tr;
	}

	unset($arItem);
	unset($arCol);

	return [$data, $th];
}
