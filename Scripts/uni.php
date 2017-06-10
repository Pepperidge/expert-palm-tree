<?php
if(!isset($_SERVER["HTTP_HOST"])) {// Allows for post commands to be used in console
  parse_str($argv[1], $_GET);
  parse_str($argv[1], $_POST);
}
foreach($_POST as $key => $val){ //Each request cleaned
    $_POST[$key] = saftey($_POST[$key]);
}
foreach($_GET as $key => $val){ //Each request cleaned
    $_GET[$key] = saftey($_GET[$key]);
}
function saftey($x){ //attempt to stop code injection along with other input falts
    $x = trim($x);
    $x = stripslashes($x);
    $x = htmlspecialchars($x);
    return $x;
}
function getJson($file){//returns an array from a file
    return json_decode(file_get_contents($file), true);
}
function saveJson($file, $array){//saves an array as a jason file
    file_put_contents($file, json_encode($array));
}
?>