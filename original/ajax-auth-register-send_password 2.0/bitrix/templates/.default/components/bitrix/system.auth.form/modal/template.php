<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
/**
 * Bitrix vars
 *
 * @var CBitrixComponent         $component
 * @var CBitrixComponentTemplate $this
 * @var array                    $arParams
 * @var array                    $arResult
 * @var array                    $arLangMessages
 * @var array                    $templateData
 *
 * @var string                   $templateFile
 * @var string                   $templateFolder
 * @var string                   $parentTemplateFolder
 * @var string                   $templateName
 * @var string                   $componentPath
 *
 * @var CDatabase                $DB
 * @var CUser                    $USER
 * @var CMain                    $APPLICATION
 */

?>
<div class="bx-system-auth-form" id="modal-login">
	<?
	if ($arResult['SHOW_ERRORS'] == 'Y' && $arResult['ERROR'])
		ShowMessage($arResult['ERROR_MESSAGE']);
	?>
	<form name="system_auth_form<?= $arResult["RND"] ?>"
	      method="post"
	      action="<?= $arResult["AUTH_URL"] ?>"
	      class="uk-form  uk-form-stacked">
		<? foreach ($arResult["POST"] as $key => $value): ?>
			<input type="hidden" name="<?= $key ?>" value="<?= $value ?>"/>
		<? endforeach ?>
		<? if ($arResult["STORE_PASSWORD"] == "Y"): ?>
			<input type="hidden" name="USER_REMEMBER" value="Y">
		<? endif ?>
		<input type="hidden" name="AUTH_FORM" value="Y"/>
		<input type="hidden" name="TYPE" value="AUTH"/>
		<input type="hidden" name="backurl" value="" class="backurl">
		<div class="uk-form-row">
			<label class="uk-form-label"><?=GetMessage("AUTH_LOGIN") ?></label>

			<div class="uk-form-controls">
				<input type="text"
				       name="USER_LOGIN"
				       maxlength="50"
				       value="<?= $arResult["USER_LOGIN"] ?>"
				       class="uk-form-large uk-width-1-1">
			</div>
		</div>
		<div class="uk-form-row">
			<label class="uk-form-label"><?=GetMessage("AUTH_PASSWORD") ?></label>

			<div class="uk-form-controls">
				<div class="uk-form-password uk-width-1-1">
					<input type="password"
					       name="USER_PASSWORD"
					       maxlength="50"
					       class="uk-form-large uk-width-1-1">
						<a data-uk-form-password='{lblShow: "<i class=\"uk-icon-eye-slash\"></i>", lblHide: "<i class=\"uk-icon-eye\"></i>"}' class="uk-form-password-toggle" href=""><i class='uk-icon-eye-slash'></i></a>
				</div>
			</div>
		</div>
		<? if ($arResult["CAPTCHA_CODE"]): ?>
			<div class="uk-form-row">
				<label class="uk-form-label"></label>
				<div class="uk-form-controls">
					<div class="bx-captcha">
						<input type="hidden" name="captcha_sid" value="<? echo $arResult["CAPTCHA_CODE"] ?>"/>
						<img src="/bitrix/tools/captcha.php?captcha_sid=<? echo $arResult["CAPTCHA_CODE"] ?>"
						     width="180" height="40" alt="<?=GetMessage("AUTH_CAPTCHA_LOADING") ?>">
					</div>
					<a href="<?= $APPLICATION->GetCurPage() ?>?reload_captcha=yes"
					   class="reload-captcha"
					   title="<?=GetMessage('AUTH_RELOAD_CAPTCHA_TITLE')?>"><i class="uk-icon-refresh uk-link-muted"></i></a>
					<input type="text"
					       name="captcha_word"
					       maxlength="50"
					       value=""
					       class="uk-width-1-1"
					       placeholder="<?=GetMessage("AUTH_CAPTCHA_PROMT") ?>">
				</div>
			</div>
		<? endif ?>
		<div class="uk-form-row">
			<button type="submit"
			        name="Login"
			        value="<?= GetMessage("AUTH_LOGIN_BUTTON") ?>"
			        class="uk-button uk-button-large uk-button-primary  uk-width-1-1"><?= GetMessage("AUTH_LOGIN_BUTTON") ?></button>
		</div>
		<div class="uk-form-row">
			<noindex>
				<a href="<?= $arResult["AUTH_FORGOT_PASSWORD_URL"] ?>"
				   rel="nofollow"
				   class="ajax-link"><?= GetMessage("AUTH_FORGOT_PASSWORD") ?></a>
			</noindex>
			<? if ($arResult["NEW_USER_REGISTRATION"] == "Y"): ?>
				<noindex>
					<a href="<?= $arResult["AUTH_REGISTER_URL"] ?>"
					   rel="nofollow"
					   class="ajax-link"><?= GetMessage("AUTH_REGISTER") ?></a>
				</noindex>
			<? endif ?>
		</div>
		<? if ($arResult["AUTH_SERVICES"] && COption::GetOptionString('main', 'allow_socserv_authorization', 'N') == 'Y'): ?>
			<div class="uk-form-row auth-services">
				<span class="bx-authform-title"><?= GetMessage("AUTH_SERVICES_LOGIN") ?></span>
				<?
				$APPLICATION->IncludeComponent("bitrix:socserv.auth.form", "flat",
					array(
						"AUTH_SERVICES" => $arResult["AUTH_SERVICES"],
						"AUTH_URL"      => $arResult["AUTH_URL"],
						"POST"          => $arResult["POST"],
						"POPUP"         => "Y",
						"SUFFIX"        => "form",
					),
					false,
					array("HIDE_ICONS" => "Y")
				);
				?>
			</div>
		<? endif ?>
	</form>
</div>