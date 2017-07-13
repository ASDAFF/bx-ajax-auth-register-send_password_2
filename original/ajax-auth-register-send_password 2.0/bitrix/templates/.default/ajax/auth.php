<?
if (!defined('PUBLIC_AJAX_MODE'))
	define('PUBLIC_AJAX_MODE', true);

//Подключаем ядро Битрикс
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
global $APPLICATION, $USER;

//Скрывает в форме регистрации служебные поля Логин, Пароль, Подтверждение пароля при = true
$bHideLogin = true;

//Функция генерации логина для подстраховки, когда в форме нет e-mail, чтобы не выводились ошибки, типа нет логина
function API_GenerateLogin($length = 10, $prefix = '')
{
	if ($length > 32)
		$length = 32;

	mt_srand((double)microtime() * 10000);
	$chars = ToUpper(md5(uniqid(rand(), true)));

	$uuid = substr($chars, 0, $length);

	if (strlen($prefix))
		$uuid = $prefix . $uuid;

	return $uuid;
}

//Тут изменяем капчу аяксом
if ($_REQUEST['reload_captcha'] == 'yes')
{
	$arResult["capCode"] = htmlspecialcharsbx($APPLICATION->CaptchaGetCode());

	$html = '<input type="hidden" name="captcha_sid" value="' . $arResult["capCode"] . '">
			 <img src="/bitrix/tools/captcha.php?captcha_sid=' . $arResult["capCode"] . '"
			      width="180" height="40" alt="loading captcha...">';

	$APPLICATION->RestartBuffer();
	echo $html;

	die();
}


//Просим формы аяксом по ссылкам в формах типа - Вспомнить пароль, Авторизация, Регистрация и т.д.
if (!$_REQUEST['TYPE'])
{
	if ($_REQUEST['forgot_password'] == 'yes')
		$_REQUEST['TYPE'] = 'SEND_PWD';

	if ($_REQUEST['register'] == 'yes')
		$_REQUEST['TYPE'] = 'REGISTRATION';

	//Форма авторизации срабатывает в дефолтном кейсе, оставим для примера
	//if($_REQUEST['login']=='yes')
		//$_REQUEST['TYPE'] = 'SEND_PWD';
}


switch ($_REQUEST['TYPE'])
{
	case "SEND_PWD": //Вспомнить пароль
	{
		//Компонент авторизации с шаблоном errors выводит только ошибки
		$APPLICATION->IncludeComponent(
			"bitrix:system.auth.form",
			"errors",
			Array(
				"REGISTER_URL"        => "",
				"FORGOT_PASSWORD_URL" => "",
				"PROFILE_URL"         => "",
				"SHOW_ERRORS"         => "Y",
			)
		);


		//Компонент восстановления пароля с шаблоном modal
		$APPLICATION->IncludeComponent(
			"bitrix:system.auth.forgotpasswd",
			"modal",
			Array()
		);
	}
	break;

	case "REGISTRATION": //Регистрация
	{

		//Тут в случае скрытых полей логина и пароля генерируем их
		if ($bHideLogin && isset($_REQUEST['REGISTER']))
		{

			foreach ($_REQUEST["REGISTER"] as $key => $val)
				$_REQUEST["REGISTER"][$key] = strip_tags(trim($val));

			$def_group = COption::GetOptionString("main", "new_user_registration_def_group", "");
			if ($def_group != "")
			{
				$GROUP_ID = explode(",", $def_group);
				$arPolicy = $USER->GetGroupPolicy($GROUP_ID);
			} else
			{
				$arPolicy = $USER->GetGroupPolicy(array());
			}

			$password_min_length = (intval($arPolicy["PASSWORD_LENGTH"]) ? intval($arPolicy["PASSWORD_LENGTH"]) : 6);
			$password_chars      = array(
				//"abcdefghijklnmopqrstuvwxyz",
				"ABCDEFGHIJKLNMOPQRSTUVWXYZ",
				"0123456789",
			);
			if ($arPolicy["PASSWORD_PUNCTUATION"] === "Y")
				$password_chars[] = ",.<>/?;:'\"[]{}\|`~!@#\$%^&*()-_+=";

			$_REQUEST["REGISTER"]["PASSWORD"] = $_REQUEST["REGISTER"]["CONFIRM_PASSWORD"] = randString($password_min_length, $password_chars);
			$_REQUEST["REGISTER"]['LOGIN']    = (strlen(trim($_REQUEST["REGISTER"]['EMAIL'])) ? trim($_REQUEST["REGISTER"]['EMAIL']) : API_GenerateLogin());
		}


		//Компонент авторизации с шаблоном errors выводит только ошибки
		$APPLICATION->IncludeComponent(
			"bitrix:system.auth.form",
			"errors",
			Array(
				"REGISTER_URL"        => "",
				"FORGOT_PASSWORD_URL" => "",
				"PROFILE_URL"         => "",
				"SHOW_ERRORS"         => "Y",
			)
		);

		//Компонент настраиваемой регистрации  с шаблоном modal
		$APPLICATION->IncludeComponent(
			"bitrix:main.register",
			"modal",
			Array(
				"SHOW_FIELDS"        => array("NAME", "LAST_NAME", "EMAIL"),
				"REQUIRED_FIELDS"    => array("NAME", "LAST_NAME", "EMAIL"),
				"AUTH"               => "Y",
				"USE_BACKURL"        => "N",
				"SUCCESS_PAGE"       => "",
				"SET_TITLE"          => "N",
				"USER_PROPERTY"      => array(),
				"USER_PROPERTY_NAME" => "",
				"HIDE_LOGIN"         => $bHideLogin,
			)
		);


		//Если в настройках главного модуля отключено "Запрашивать подтверждение регистрации по E-mail"
		//и в настройках включена автоматическая авторизация после регистрации "AUTH" => "Y",
		//то пользователю будет показано это сообщение и страница перезагрузится,
		if ($USER->IsAuthorized())
		{
			$APPLICATION->RestartBuffer();
			$backurl = $_REQUEST["backurl"] ? $_REQUEST["backurl"] : '/';

			//тут выводим любую информацию посетителю
			?>
			<p>
				Здравствуйте, <?= $USER->GetFullName(); ?>!<br>
				Вы зарегистрированы и успешно вошли на сайт!
			</p>
			<p>Сейчас страница автоматически перезагрузится и вы сможете продолжить работу под своим именем.</p>
			<script>
				function TSRedirectUser() {
					window.location.href = '<?=$backurl;?>';
				}

				// - через 3 секунды перезагружаем страницу, чтобы вся страница знала, что посетитель авторизовался.
				// 1000 - это 1 секунда
				window.setTimeout('TSRedirectUser()', 3000);
			</script>
			<?
		}
	}
	break;


	default: //Авторизация
	{
		//Компонент авторизации с шаблоном modal
		$APPLICATION->IncludeComponent(
			"bitrix:system.auth.form",
			"modal",
			Array(
				"REGISTER_URL"        => "",
				"FORGOT_PASSWORD_URL" => "",
				"PROFILE_URL"         => "",
				"SHOW_ERRORS"         => "Y",
			)
		);


		//1. Если нужно показать какую-нибудь информацию об успешном входе на сайт и перезагрузить страницу через X сек.
		if ($USER->IsAuthorized())
		{
			//Если посетитель авторизовался/вошел на сайт под своим логином и паролем, необходимо очистить буфер
			$APPLICATION->RestartBuffer();
			$backurl = $_REQUEST["backurl"] ? $_REQUEST["backurl"] : '/';
			?>
			<div>
				Здравствуйте, <?= $USER->GetFullName(); ?>!<br>
				Спасибо за визит на наш сайт!
			</div>
			<script>
				function TSRedirectUser() {
					window.location.href = '<?=$backurl;?>';
				}

				//Через 2 секунды перезагружаем страницу, чтобы вся страница знала, что посетитель авторизовался.
				//1000 - это 1 секунда
				window.setTimeout('TSRedirectUser()', 2000);
			</script>
			<?
		}

		//2. Если не нужно выводить никакую информацию после авторизации и немедленно перезагрузить страницу,
		//тогда аналогичный код выше в п.1 закомментируйте, а этот раскомментируйте.
		/*if($USER->IsAuthorized())
		{
			$APPLICATION->RestartBuffer();
			$backurl = $_REQUEST["backurl"] ? $_REQUEST["backurl"] : '/';
			?>
			<script>
				window.location.href = '<?=$backurl;?>';
			</script>
		<?
		}*/
	}
}
die();