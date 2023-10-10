<?php

class Reports
{
    private $lines = [];
    private $database;

    public function __construct()
    {
        $this->lines = file('http://pioneergroupmobile.ru/upload/1c_catalog/importFull.csv');
        $this->database = new DB();
    }

    private function arrayXmlId($arXmlId, $action)
    {
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
                            $statusError[$xmlId] = 'Повторное добавление действия "' . $action . '" ' . $xmlId . ' в отчет.';
                        }

                        if (!$idItem) {
                            $statusError[$xmlId] = 'Товар не найден "' . $action . '" ' . $xmlId . ' в отчет.';
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
                                $statusError[$xmlId] = 'Ошибка добавления действия "' . $action . '", "' . $xmlId . '" в отчет.';
                            }
                        } else {
                            //$arResult["errorText"][$xmlId] = 'Ошибка добавления товара "'.$action.'", "' . $xmlId . '" в отчет.';
                            $statusError[$xmlId] = 'Ошибка добавления товара "' . $action . '", "' . $xmlId . '" в отчет.';
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

    public function create($dataCreate = [])
    {

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

    private function createXmlId($dataCreate)
    {
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
                        $arResult["errorText"] = 'Повторное добавление действия "' . $action . '" ' . $xmlId . ' в отчет.';
                    }

                    if (!$idItem) {
                        $arResult["errorText"] = 'Товар не найден "' . $action . '" ' . $xmlId . ' в отчет.';
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
                            $arResult["errorText"] = 'Ошибка добавления действия "' . $action . '", "' . $xmlId . '" в отчет.';
                        }
                    } else {
                        $arResult["errorText"] = 'Ошибка добавления товара "' . $action . '", "' . $xmlId . '" в отчет.';
                    }
                } else {
                    $arResult["errorText"] = 'XML_ID ' . $xmlId . ' не найден  в файле.';
                }
            }
        }
        //}
        return $arResult;
    }

    private function stringXmlId($xmlId, $action, $date = '')
    {
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
                        $arResult["errorText"] = 'Повторное добавление действия "' . $action . '" ' . $xmlId . ' в отчет.';
                    }

                    if (!$idItem) {
                        $arResult["errorText"] = 'Товар не найден "' . $action . '" ' . $xmlId . ' в отчет.';
                    }
                }
            } else {
                $arResult["errorText"] = 'Данные о старом товаре "' . $action . '" ' . $xmlId . '.';
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

    public function request($arXmlId, $action = '')
    {
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

    private function arrayKodColor($arKodColor, $action)
    {
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
                            $statusError[$xmlId] = 'Повторное добавление действия "' . $action . '" ' . $xmlId . ' в отчет.';
                        }

                        if (!$idItem) {
                            $statusError[$xmlId] = 'Товар не найден "' . $action . '" ' . $xmlId . ' в отчет.';
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

    public function requestPhoto($arKodColor, $action = '')
    {
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

    public function requestSiteData($xml_site = [])
    {

        $arResult["status"] = true;

        foreach ($xml_site as $arXml) {
            $xmlId = $arXml['UF_XML_ID'];
            $action = $arXml['UF_ACTION'];
            $date = $arXml['UF_DATE_CREATE'];

            $this->stringXmlId($xmlId, $action, $date);
        }


        return $arResult;
    }

    private function searchItem($xmlId)
    {
        $strSql = "
		SELECT ri.XML_ID, ra.*
		FROM report_items as ri
		LEFT JOIN report_actions as ra
			ON ri.ID = ra.ITEM_ID
		WHERE
			ri.XML_ID = '" . $this->database->filter($xmlId) . "'
		";

        $row = $this->database->get_results($strSql);

        return $row;
    }

    private function searchItemPhoto($kod, $color)
    {
        $strSql = "
		SELECT ri.XML_ID, ra.*
		FROM report_items as ri
		LEFT JOIN report_actions as ra
			ON ri.ID = ra.ITEM_ID
		WHERE
			ri.KOD = '" . $this->database->filter($kod) . "' AND
			ri.COLOR = '" . $this->database->filter($color) . "'
		";

        $row = $this->database->get_results($strSql);

        return $row;
    }

    private function searchDataItemPhoto($kod, $color)
    {

        $statusFind = false;
        $arResult = [];

        foreach ($this->lines as $line_num => $line) {
            if (substr_count($line, '0' . $kod) && substr_count($line, $color)) {
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

    private function searchDataItem($xmlId)
    {

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

    private function addAction($idItem, $action, $date = '', $auto = null)
    {
        if (is_null($date)) {
            $date = null;
        } else if ($date == '') {
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
            $names = ['ACTION_1C_SEND_PHOTO', 'ACTION_1C_APPLY_PHOTO', 'ACTION_PHOTO_CREATE', 'ACTION_PHOTO_PROCESSED', 'ACTION_PICTURE',];

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
                            if (in_array($field_name, $names)) {
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

    private function addItem($arItem)
    {

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
