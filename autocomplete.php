<?php
include 'header.php';
include 'configParameter.php';

$requestCharacter = '%'.$_POST['queryString'].'%';

$sql = "SELECT id, nme FROM article WHERE articleStatusID = '".$articleStatus."' AND nme LIKE ? LIMIT 0, 10";

$stmt = $connect -> prepare($sql);
$stmt -> bind_param('s', $requestCharacter);
$stmt -> execute();
$stmt -> bind_result($articleID, $articleName);

while($stmt -> fetch())
{
    echo '<li onClick="selectValue(\''.utf8_encode($articleName).'\');">
		  <a href="productDetail.php?aid='.$articleID.'" title="'.utf8_encode($articleName).'">
		  '.utf8_encode($articleName).'</li>';
}

$stmt -> close();
?>
