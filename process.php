<?php
// Подключение библиотеки Bitrix24
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Получите введенный текст из формы
    $textInput = $_POST["textInput"];

    // Создайте нов XML-документ
    $xmlDoc = new DOMDocument('1.0', 'utf-8');

    // Создайте корневой элемент
    $root = $xmlDoc->createElement('data');
    $xmlDoc->appendChild($root);

    // Получение информации о текущем пользователе
    global $USER;
    $dbUser = \Bitrix\Main\UserTable::getList(array(
        'select' => [
            'ID',
            'NAME',
            'LAST_NAME', // Фамилия
            'SECOND_NAME' // Отчество
        ],
        'filter' => [
            'ID' => $USER->GetID()
        ]
    ));
    if ($arUser = $dbUser->fetch()) {
        // Добавьте информацию о пользователе в XML-документ
        $fullName = trim($arUser['LAST_NAME'] . ' ' . $arUser['NAME'] . ' ' . $arUser['SECOND_NAME']);

        $userElement = $xmlDoc->createElement('user');
        $root->appendChild($userElement);

        $idElement = $xmlDoc->createElement('ID', $arUser['ID']);
        $userElement->appendChild($idElement);

        $fullNameElement = $xmlDoc->createElement('FULL_NAME', $fullName);
        $userElement->appendChild($fullNameElement);
    }

    // Создание элемента для введенного текста
    $textElement = $xmlDoc->createElement('text', $textInput);
    $root->appendChild($textElement);

    // Создание элемента с выбранным статусом
    $selected = $_POST['selectText'];
    $statusElement = $xmlDoc->createElement('status', $selected);
    $root->appendChild($statusElement);

    $id = uniqid();

    $xmlDoc->save("./doc89c/{$id}.xml");

    // После успешной обработки перенаправьте пользователя на предыдущую страницу
    header('Location: https://bitrix24.martinural.ru/documents1c/');
    exit;
} else {
    // Если запрос не POST, выполните соответствующие действия
    echo "Invalid request method.";
}
?>