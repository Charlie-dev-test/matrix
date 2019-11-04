<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<?php
//подключаемся к базе
$db = @mysqli_connect('localhost', 'root','', 'matrix') or die('Ошибка соединения с базой данных');
//if (!$db) die(mysqli_connect_error());
mysqli_set_charset($db, "utf8");

//исходная строка
$string = '{Пожалуйста,|Просто|Если сможете,} сделайте так, чтобы это {удивительное|крутое|простое|важное|бесполезное} тестовое предложение {изменялось {быстро|мгновенно|оперативно|правильно} случайным образом|менялось каждый раз}';

//запускаем разбор строки
$volume = preg_replace_callback('~{(?>[^{}]+|(?0))*}~', 'search', $string);


/**
 * функция для построения случайной строки
 * @param $matches
 * @return string
 */
function search($matches) {
    //раскрываем первый уровень
    $str = substr($matches[0], 1, strlen($matches[0]) - 2);
    //ищем вложенные
    $str = preg_replace_callback('~{(?>[^{}]+|(?0))*}~', 'search', $str);
    //получаем варианты из строки
    $variants = explode('|', $str);
    //возвращаем случайно собранную строку
    return $variants[mt_rand(0, count($variants) - 1)];
}

//обращаемся к базе, ищем есть ли уже такой вариант
$res = mysqli_query($db, "SELECT `quotes_text` FROM `quotes` WHERE `quotes_text` = '$volume'");
$data = mysqli_fetch_all($res, MYSQLI_ASSOC);

//если в базе уже есть такой вариант строки, то сообщаем об этом.
if(!empty($data)){
    echo '<h5>' . $volume . '</h5>';
    echo '<h2>Такой вариант уже есть <br> Чтобы получить другую строку перезагрузите страницу</h2>';
}else {
    $insert = "INSERT INTO `quotes` (`quotes_text`) VALUES ('$volume')";
    $res_insert = mysqli_query($db, $insert);
    if ($res_insert) {
        echo '<h5>' . $volume . '</h5>';
        echo '<h2>Полученное значение занесено в базу данных</h2>';
    }else echo '<h2>Error</h2>';
}
?>
</body>
</html>

