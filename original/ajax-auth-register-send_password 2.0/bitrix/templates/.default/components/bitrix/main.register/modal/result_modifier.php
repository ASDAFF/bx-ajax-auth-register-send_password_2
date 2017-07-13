<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if($arParams["SHOW_FIELDS"] && $arResult["SHOW_FIELDS"])
{
	$arResult["SHOW_FIELDS"] = array_diff($arResult["SHOW_FIELDS"],$arParams["SHOW_FIELDS"]);

	//Тут скрываем системные поля Логин, Пароль, Подтверждение пароля
	//покажем только поля из настроек компонента в файле /ajax/auth.php
	//в том порядке, в котором они заданы

	if($arParams['HIDE_LOGIN'])
		$arResult["SHOW_FIELDS"] = array();

	$arResult["SHOW_FIELDS"] = array_merge($arResult["SHOW_FIELDS"],$arParams["SHOW_FIELDS"]);
}