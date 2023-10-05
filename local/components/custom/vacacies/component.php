<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}
/**
 *  @var CBitrixComponent $this
 */

// дефолтные адрес для ЧПУ режима
$arDefaultUrlTemplates404 = [
    'vacancies'    => '',
    'rezume'    => '#ELEMENT_ID#/form/',
    'vacancy' => '#ELEMENT_ID#',
];
// дефолтные переменные для чпу режима
$arDefaultVariableAliases404 = [];

// дефолтные переменные адресов для неЧПУ
$arDefaultVariableAliases    = [];
// дефолтные переменные для неЧПУ
$arComponentVariables = [];

$SEF_FOLDER  = '';
$arUrlTemplates = [];

if ($arParams['SEF_MODE'] == 'Y') {

    $arVariables = [];
    $arUrlTemplates = CComponentEngine::MakeComponentUrlTemplates(
        $arDefaultUrlTemplates404,
        $arParams['SEF_URL_TEMPLATES']
    );
    $arVariableAliases = CComponentEngine::MakeComponentVariableAliases(
        $arDefaultVariableAliases404,
        $arParams['VARIABLE_ALIASES']
    );
    $componentPage = CComponentEngine::ParseComponentPath(
        $arParams['SEF_FOLDER'],
        $arUrlTemplates,
        $arVariables
    );
    if (!$componentPage) {
        $componentPage = 'vacancies';
    }
    CComponentEngine::InitComponentVariables(
        $componentPage,
        $arComponentVariables,
        $arVariableAliases,
        $arVariables
    );
    $SEF_FOLDER = $arParams['SEF_FOLDER'];
    $detailUrl = $SEF_FOLDER . ($componentPage === 'vacancies' ? $arUrlTemplates['vacancy'] : $arUrlTemplates[$componentPage]);
} else {
    $arVariables = [];
    $arVariableAliases = CComponentEngine::MakeComponentVariableAliases(
        $arDefaultVariableAliases,
        $arParams['VARIABLE_ALIASES']
    );

    CComponentEngine::InitComponentVariables(
        false,
        $arComponentVariables,
        $arVariableAliases,
        $arVariables
    );

    $componentPage = '';
    if (isset($arVariables['FORM_ID']) && isset($arVariables['ELEMENT_ID'])) {
        $componentPage = 'rezume';
    } else if (intval($arVariables['ELEMENT_ID']) > 0) {
        $componentPage = 'vacancy';
    } else {
        $componentPage = 'vacancies';
    }
}
$arResult = [
    'FOLDER'        => $SEF_FOLDER,
    'URL_TEMPLATES' => $arUrlTemplates,
    "VARIABLES"     => $arVariables,
    'ALIASES'       => $arVariableAliases,
    "DETAIL_URL" => $detailUrl ?? ''
];

dump($arResult);

$this->IncludeComponentTemplate($componentPage);
