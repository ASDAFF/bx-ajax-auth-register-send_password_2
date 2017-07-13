Форма авторизации и регистрации Битрикс на jQuery.ajax(). Часть 2
http://tuning-soft.ru/articles/bitrix/the-authorization-form-and-registration-bitrix-jquery-ajax-part-2.html

© 2015 Антон Кучковский, Тюнинг-Софт
http://tuning-soft.ru/

Версия 2.0


1. Скопировать все можете в дефолтный шаблон, можете в шаблон сайта, но если подключить джаваскрипты в шаблоне сайта,
то надо в файле /js/auth.js изменить значение переменной auth_url до файла /ajax/auth.php
Сам файл auth.php можете на сайте хранить вообще где угодно, главное прописать в джаваскрипте путь до него.

2. Подключаем в /bitrix/templates/шаблон_сайта/header.php джаваскрипты и стили из папок /css/, /js/, /uikit/, например так:
<?
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH .'/uikit/uikit.gradient.min.css');
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH .'/uikit/form-password.gradient.min.css');
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH .'/css/auth.css');

$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH .'/js/auth.js');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH .'/uikit/uikit.min.js');
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH .'/uikit/form-password.min.js');
?>


3. В файле /bitrix/templates/шаблон_сайта/footer.php достаточно разместить верстку модального окна Uikit c id="auth-modal"
В эту верстку будут подгружаться формы авторизации, регистрации, восстановления пароля кликом по ссылке/кнопке "Личный кабинет"
и кликом по всем ссылкам в формах типа "Вспомнить пароль", "Зарегистрироваться", "Авторизация"

<!--auth-modal-->
<div id="auth-modal" class="uk-modal">
    <div class="uk-modal-dialog uk-modal-dialog-slide">
        <a class="uk-modal-close uk-close"></a>
        <div class="uk-modal-header"><h4>Вход в интернет-магазин</h4></div>
        <div class="uk-modal-content">
            <div class="ajaxloader" style="display: none;"></div>
        </div>
    </div>
</div>
<!--//auth-modal-->


4. Где-нибудь на сайте надо вот такую ссылку/кнопку вставить в шаблоне сайта, кликом по которой будут формы открываться в модальном окне
<a data-uk-modal="" href="#auth-modal" id="auth-link">Личный кабинет</a>


В целом все, если все подключили правильно, путь до файла auth.php прописали в файле auth.js (по умолчанию дефолтный шаблон прописан),
то формы будут работать.