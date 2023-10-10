<?php
require_once(__DIR__ . '/function.php' );
$accessToken = "1b234120bvv602";
$requestAccess = htmlspecialchars($_REQUEST["access"]);

$arResult = ['status' => false];
//$arResult['result'] = $_REQUEST;

if ($requestAccess == $accessToken) {

	if (isset($_REQUEST["xml_id"])) {

		$requestXmlId = is_array($_REQUEST["xml_id"]) ? $_REQUEST["xml_id"] : htmlspecialchars(trim($_REQUEST["xml_id"]));
		$requestAction = htmlspecialchars(trim($_REQUEST["action"]));

		if (!$requestXmlId) {
			$arResult['errorText'] = 'Нет парметра "xml_id"';
		}
		if (!$requestAction) {
			$arResult['errorText'] = 'Нет парметра "status"';
		}

		if (!isset($arResult['errorText'])) {
			$objReports = new Reports;


			if ($requestAction == 'ACTION_1C_CREATE') {
				$dataCreate = [
					'action' => $requestAction,
					'xml_id' => $requestXmlId,
				];
				if (htmlspecialchars($_REQUEST["name"])) {
					$dataCreate['name'] = htmlspecialchars($_REQUEST["name"]);
				}
				if (htmlspecialchars($_REQUEST["color"])) {
					$dataCreate['color'] = htmlspecialchars($_REQUEST["color"]);
				}
				if (htmlspecialchars($_REQUEST["kod"])) {
					$dataCreate['kod'] = htmlspecialchars($_REQUEST["kod"]);
				}
				if (htmlspecialchars($_REQUEST["seazon"])) {
					$szn = htmlspecialchars($_REQUEST["seazon"]);
					$szn = preg_replace('/[^a-zA-Zа-яА-Я0-9]/ui', '', $szn);
					$szn = str_replace(['a2', 'ь2', 'о2'], ['a 2', 'ь 2', 'о 2'], $szn);
					$dataCreate['seazon'] = $szn;
				}
				//file_put_contents(__DIR__ . '/log.txt', var_export(['date'=> date('d.m.Y H:i:s'), 'get' => $_REQUEST], true)."\r\n", FILE_APPEND);
				file_put_contents(__DIR__ . '/log.txt', var_export(['date'=> date('d.m.Y H:i:s'), 'request' => $dataCreate], true)."\r\n", FILE_APPEND);
				$arResult = $objReports->create($dataCreate);
			} else {
				$arResult = $objReports->request($requestXmlId, $requestAction);
			}

			//$arResult['get'] = $_GET;
			/*$arResult['multi'] = $resultRequest['multi'];
			if ($resultRequest['status']) {
				$arResult['status'] = true;
			} else {
				$arResult['errorText'] = $resultRequest['errorText'];
			}*/

			//$arResult['result'] = Reports::request($requestXmlId, $requestAction);
		}

		if (strripos($requestAction, 'ACTION_1C') !== false) {
			if (isset($_REQUEST["XML_LIST"])) {
				$xmls = explode(',', htmlspecialchars($_REQUEST["XML_LIST"]));
				$kod = ltrim(explode('c', $requestXmlId)[0], '0');

				if (!empty($xmls) && !empty($kod)) {
					$database = new DB();
					$strSql = "DELETE FROM report_items WHERE `KOD` = $kod AND `XML_ID` NOT IN ('" . implode("','", $xmls) . "')";
					$database->get_results($strSql);
				}
			}
		}
	}

	if (isset($_REQUEST["xml_site"])) {
		$objReports = new Reports;
		$xml_site = $_REQUEST["xml_site"];
		$arResult = $objReports->requestSiteData($xml_site);
	}

	if (isset($_REQUEST["folder"])) {
		$arResult['status'] = true;
		$arFolder = json_decode($_REQUEST["folder"], true);

		//file_put_contents(__DIR__ . '/log.txt', var_export($arFolder, true));
		//file_put_contents(__DIR__ . '/log.txt', var_export(['date'=> date('d.m.Y H:i:s'), 'obmen' => $arFolder], true)."\r\n", FILE_APPEND);

		$objReports = new Reports;

		$arKodColor = [];
		foreach ($arFolder as $folderPath) {
			$arPath = explode('\\', $folderPath);
			$lastFolder = array_pop($arPath);

			//выбирем только папки с кодом и цветом
			if (substr_count($lastFolder, '_')) {
				$arItemPath = explode('_', $lastFolder);
				$arKodColor[] = $arItemPath;
			}
		}


		$arResult = $objReports->requestPhoto($arKodColor, 'ACTION_PHOTO_CREATE');

	}

	if (isset($_REQUEST["folder_processed"])) {
		//$arResult['status'] = true;
		$arFolder = json_decode($_REQUEST["folder_processed"], true);

		//file_put_contents(__DIR__ . '/log.txt', var_export($arFolder, true));
		//file_put_contents(__DIR__ . '/log.txt', var_export(['date'=> date('d.m.Y H:i:s'), 'obmen' => $arFolder], true)."\r\n", FILE_APPEND);

		$objReports = new Reports;

		$arKodColor = [];
		foreach ($arFolder as $folderPath) {
			$arPath = explode('\\', $folderPath);
			$lastFolder = array_pop($arPath);

			//выбирем только папки с кодом и цветом
			if (substr_count($lastFolder, '_')) {
				$arItemPath = explode('_', $lastFolder);
				$arKodColor[] = $arItemPath;
			}
		}


		$arResult = $objReports->requestPhoto($arKodColor, 'ACTION_PHOTO_PROCESSED');

	}


} else {
	$arResult['errorText'] = 'Нет доступа';
}

header("Content-Type: application/json");
echo json_encode($arResult);
