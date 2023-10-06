<?php
$arUrlRewrite=array (
  0 => 
  array (
    'CONDITION' => '#^\\/?\\/mobileapp/jn\\/(.*)\\/.*#',
    'RULE' => 'componentName=$1',
    'ID' => NULL,
    'PATH' => '/bitrix/services/mobileapp/jn.php',
    'SORT' => 100,
  ),
  2 => 
  array (
    'CONDITION' => '#^/novyy-razdel/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/novyy-razdel/index.php',
    'SORT' => 100,
  ),
  3 => 
  array (
    'CONDITION' => '#^/products/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/products/index.php',
    'SORT' => 100,
  ),
  6 => 
  array (
    'CONDITION' => '#^/testovyy/#',
    'RULE' => '',
    'ID' => 'custom:vacacies',
    'PATH' => '/testovyy/index.php',
    'SORT' => 100,
  ),
  7 => 
  array (
    'CONDITION' => '#^/vakansii/#',
    'RULE' => '',
    'ID' => 'bitrix:form.result.new',
    'PATH' => '/vakansii/index.php',
    'SORT' => 100,
  ),
  1 => 
  array (
    'CONDITION' => '#^/rest/#',
    'RULE' => '',
    'ID' => NULL,
    'PATH' => '/bitrix/services/rest/index.php',
    'SORT' => 100,
  ),
);
