<?php
include ('cfg.php');
include ('./libs/Mobile-Detect/Mobile_Detect.php');

$detect = new Mobile_Detect();

header('Content-Type: text/html; charset= utf-8');

mysql_connect($hostname,$username,$password) OR DIE("Не могу создать соединение "); 
$mysqli = mysqli_connect($hostname,$username,$password,$dbName) OR DIE("Не могу создать соединение "); 
mysqli_query($mysqli, "SET NAMES utf8");
mysql_query("SET NAMES utf8");
mysql_select_db($dbName) or die(mysql_error());

$amount = $_POST['amount'];
$desc = $_POST['desc'];
$account = $_POST['account'];
$dates = $_POST['dates'];
$dimension = $_POST['dimension'];


$act = $_GET['act'];
$edit = $_GET['edit'];
$delete_id = $_GET['delete_id'];
$update=$_GET['update'];



$dates_formated = date('Y-m-d',strtotime($dates));

if (!isset($_POST['viewGrid']) or $_POST['viewGrid'] == ''){
	$viewGrid = date("Y-m-").'01';
}else{
	$viewGrid = $_POST['viewGrid'];
}

if (!isset($_POST['viewDim'])){
	 $viewDim = 0;
}else{
	$viewDim = $_POST['viewDim'];
}


if ($_POST['action']=='add_new'){

$query = "CALL transactions_add('$desc', $amount,$account,'$dates_formated',$dimension)"; 

mysqli_query($mysqli, $query) or die(mysql_error()); 
echo "Информация о вас занесена в базу данных."; 	
	
}



function getList($tname,$id_selected=0){
$query = "SELECT * FROM $tname"; 

$res = mysql_query($query) or die(mysql_error()); 

  while ($row=mysql_fetch_array($res)) { 
    echo '<option value="'.$row['id'].'"';
		if ($id_selected == $row['id']) 
			echo' selected';
	echo '>'.$row['desc'].'</option>';	
  } 

}

function getMonthList($viewGrid){
//setlocale(LC_ALL, 'ru_RU','rus_RUS','Russian');
$cd = strtotime("2013-01-01");
do{

	if ($viewGrid == date('Y-m-d', $cd)){
		echo '<option selected value="'.date('Y-m-d', $cd).'">'.strftime("%B %Y",$cd).'</option>';
	}else{
		echo '<option value="'.date('Y-m-d', $cd).'">'.strftime("%B %Y",$cd).'</option>';
	}

$cd = mktime(0,0,0,date('m',$cd)+1,date('d',$cd),date('Y',$cd));
	
}
while ($cd < time());


/*
$query = "SELECT * FROM $tname"; 

$res = mysql_query($query) or die(mysql_error()); 

  while ($row=mysql_fetch_array($res)) { 
    echo '<option value="'.$row['id'].'"';
		if ($id_selected == $row['id']) 
			echo' selected';
	echo '>'.$row['desc'].'</option>';	
  } 
*/
/*

	if ($viewGrid == '2013-01-01'){
		echo '<option selected value="2013-01-01">Январь 2013</option>';
	}else{
		echo '<option value="2013-01-01">Январь 2013</option>';
	}
	
	if ($viewGrid == '2013-02-01'){
		echo '<option selected value="2013-02-01">Февраль 2013</option>';
	}else{
		echo '<option value="2013-02-01">Февраль 2013</option>';
	}
	
	if ($viewGrid == '2013-03-01'){
		echo '<option selected value="2013-03-01">Март 2013</option>';
	}else{
		echo '<option value="2013-03-01">Март 2013</option>';
	}

	if ($viewGrid == '2013-04-01'){
		echo '<option selected value="2013-04-01">Апрель 2013</option>';
	}else{
		echo '<option value="2013-04-01">Апрель 2013</option>';
	}	
	
	if ($viewGrid == '2013-05-01'){
		echo '<option selected value="2013-05-01">Май 2013</option>';
	}else{
		echo '<option value="2013-05-01">Май 2013</option>';
	}	

	if ($viewGrid == '2013-06-01'){
		echo '<option selected value="2013-06-01">Июнь 2013</option>';
	}else{
		echo '<option value="2013-06-01">Июнь 2013</option>';
	}	

	if ($viewGrid == '2013-07-01'){
		echo '<option selected value="2013-07-01">Июль 2013</option>';
	}else{
		echo '<option value="2013-07-01">Июль 2013</option>';
	}		
	if ($viewGrid == '2013-08-01'){
		echo '<option selected value="2013-08-01">Август 2013</option>';
	}else{
		echo '<option value="2013-08-01">Август 2013</option>';
	}		
	if ($viewGrid == '2013-09-01'){
		echo '<option selected value="2013-09-01">Сентябрь 2013</option>';
	}else{
		echo '<option value="2013-09-01">Сентябрь 2013</option>';
	}		
	if ($viewGrid == '2013-10-01'){
		echo '<option selected value="2013-10-01">Октябрь 2013</option>';
	}else{
		echo '<option value="2013-10-01">Октябрь 2013</option>';
	}	
	if ($viewGrid == '2013-11-01'){
		echo '<option selected value="2013-11-01">Ноябрь 2013</option>';
	}else{
		echo '<option value="2013-11-01">Ноябрь 2013</option>';
	}		
	if ($viewGrid == '2013-12-01'){
		echo '<option selected value="2013-12-01">Декабрь 2013</option>';
	}else{
		echo '<option value="2013-12-01">Декабрь 2013</option>';
	}	
	if ($viewGrid == '2014-01-01'){
		echo '<option selected value="2014-01-01">Январь 2014</option>';
	}else{
		echo '<option value="2014-01-01">Январь 2014</option>';
	}		
	if ($viewGrid == '2014-02-01'){
		echo '<option selected value="2014-02-01">Февраля 2014</option>';
	}else{
		echo '<option value="2014-02-01">Февраля 2014</option>';
	}	
	if ($viewGrid == '2014-03-01'){
		echo '<option selected value="2014-03-01">Март 2014</option>';
	}else{
		echo '<option value="2014-03-01">Март 2014</option>';
	}		
	if ($viewGrid == '2014-04-01'){
		echo '<option selected value="2014-04-01">Апрель 2014</option>';
	}else{
		echo '<option value="2014-04-01">Апрель 2014</option>';
	}		
	if ($viewGrid == '2014-05-01'){
		echo '<option selected value="2014-05-01">Май 2014</option>';
	}else{
		echo '<option value="2014-05-01">Май 2014</option>';
	}	
	if ($viewGrid == '2014-06-01'){
		echo '<option selected value="2014-06-01">Июнь 2014</option>';
	}else{
		echo '<option value="2014-06-01">Июнь 2014</option>';
	}	
	if ($viewGrid == '2014-07-01'){
		echo '<option selected value="2014-07-01">Июль 2014</option>';
	}else{
		echo '<option value="2014-07-01">Июль 2014</option>';
	}	
	if ($viewGrid == '2014-08-01'){
		echo '<option selected value="2014-08-01">Август 2014</option>';
	}else{
		echo '<option value="2014-08-01">Август 2014</option>';
	}		
	if ($viewGrid == '2014-09-01'){
		echo '<option selected value="2014-09-01">Сентябрь 2014</option>';
	}else{
		echo '<option value="2014-09-01">Сентябрь 2014</option>';
	}	
	if ($viewGrid == '2014-10-01'){
		echo '<option selected value="2014-10-01">Октябрь 2014</option>';
	}else{
		echo '<option value="2014-10-01">Октябрь 2014</option>';
	}	
	if ($viewGrid == '2014-11-01'){
		echo '<option selected value="2014-11-01">Ноябрь 2014</option>';
	}else{
		echo '<option value="2014-11-01">Ноябрь 2014</option>';
	}	
	if ($viewGrid == '2014-12-01'){
		echo '<option selected value="2014-12-01">Декабрь 2014</option>';
	}else{
		echo '<option value="2014-12-01">Декабрь 2014</option>';
	}	
	if ($viewGrid == '2015-01-01'){
		echo '<option selected value="2015-01-01">Январь 2015</option>';
	}else{
		echo '<option value="2015-01-01">Январь 2015</option>';
	}	
	if ($viewGrid == '2015-02-01'){
		echo '<option selected value="2015-02-01">Февраль 2015</option>';
	}else{
		echo '<option value="2015-02-01">Февраль 2015</option>';
	}
	if ($viewGrid == '2015-03-01'){
		echo '<option selected value="2015-03-01">Март 2015</option>';
	}else{
		echo '<option value="2015-03-01">Март 2015</option>';
	}
	if ($viewGrid == '2015-04-01'){
		echo '<option selected value="2015-04-01">Апрель 2015</option>';
	}else{
		echo '<option value="2015-04-01">Апрель 2015</option>';
	}	
	*/
}

function getDimentionsList($viewDim, $mysqli){
$query = "CALL transactions_perAccView('2013-01-01')";

$q = mysqli_multi_query($mysqli, $query)  or die( mysqli_error( $mysqli ));
echo '<option value="0">Все статьи</option>';
if ($q)
{
do{
if ($result = mysqli_store_result($mysqli)){

  while ($row=mysqli_fetch_row($result)) { 
		if ($row[4]==$viewDim){
			echo '<option value="'.$row[4].'" selected>'.$row[0].'</option>';
		}else{
			echo '<option value="'.$row[4].'">'.$row[0].'</option>';
		}
  } 
}
}while(mysqli_next_result($mysqli));
}
}

function TransactionGridView($mysqli,$viewDim, $viewGrid){
echo '<table width="100%" border="1" cellspacing="0" cellpadding="0">';
$query = "CALL transactions_gridView($viewDim , NULL , '$viewGrid', -1)";	

$q = mysqli_multi_query($mysqli, $query)  or die( mysqli_error( $mysqli ));// or die(mysql_error()); 

if ($q)
{
do{
if ($result = mysqli_store_result($mysqli)){

  while ($row=mysqli_fetch_row($result)) { 
    echo '<tr>';
	echo '<td><img src="./images/';
	echo ($row[4]=="1")?'m':'p';
	echo '.png"></td>';
	echo'<td>'.$row[1].'</td><td>'.$row[3].'</td><td><a href="?act=edit&edit='.$row[0].'">'.$row[6].'</a></td>';
	echo '<td>'.$row[2].'</td>';
	echo '<td>'.$row[5].'</td>';
	
	echo '<tr>';	
  } 
}
}while(mysqli_next_result($mysqli));
}
echo '</table>';
}
function print_header(){
 echo '<head>';
  echo '<link rel="stylesheet" href="./jquery/css/redmond/jquery-ui-1.10.1.custom.min.css" />';
  echo '<script src="./jquery/js/jquery-1.9.1.js"></script>';
  echo '<script src="./jquery/js/jquery-ui-1.10.1.custom.min.js"></script>';
  echo '<!--link rel="stylesheet" href="./jquery/css/redmond/demos.css" /-->';
  echo '<script>';
  echo '$(function() {';
    echo '$( "#dates" ).datepicker({';
    echo '            buttonImageOnly: true,';
    echo "            showAnim: '',";
    echo "            dateFormat: 'dd.mm.yy'";
	echo '			});';
	echo "			$.datepicker.setDefaults($.datepicker.regional['ru']);";
  echo '});';
  echo '</script>';
echo '</head>';
}
function getDimentionsItogs($mysqli, $viewGrid){
echo '<table width="100%" border="1" cellspacing="0" cellpadding="0">';
$query = "CALL transactions_perAccView('$viewGrid')";

$q = mysqli_multi_query($mysqli, $query)  or die( mysqli_error( $mysqli ));

if ($q)
{
do{
if ($result = mysqli_store_result($mysqli)){

  while ($row=mysqli_fetch_row($result)) { 
		if ($row[4]==$viewDim){
			echo '<tr bgcolor="grey"><td><b><font color="';
			echo ($row[1]=="1")?'red':'';
			echo ($row[1]=="2")?'green':'';
			echo ($row[1]=="3")?'blue':'';
			if ($row[2]=0)
				echo $row[3];
			else{
				echo ($row[3]=="")?'0':$row[3];
				echo ' из '.$row[2];				
			}
			echo '</td></tr>';		
		}else{
			echo '<tr><td><font color="';
			echo ($row[1]=="1")?'red':'';
			echo ($row[1]=="2")?'green':'';
			echo ($row[1]=="3")?'blue':'';
			echo'">'.$row[0].'</font></td><td>';
			if ($row[2]==0)
				echo $row[3];
			else{
				echo ($row[3]=="")?'0':$row[3];
				echo ' из '.$row[2];				
			}
			echo '</td></tr>';			
		}
  } 
}
}while(mysqli_next_result($mysqli));
}
echo '</table>';

$query = "CALL transactions_itog('$viewGrid')";

$q = mysqli_multi_query($mysqli, $query)  or die( mysqli_error( $mysqli ));

if ($q)
{
do{
if ($result = mysqli_store_result($mysqli)){

  while ($row=mysqli_fetch_row($result)) { 
		$i_doh = $row[0];
		$i_rash= $row[1];
		$i_rashb= $row[2];
		$i_ipot= $row[3];
  } 
}
}while(mysqli_next_result($mysqli));
echo '<b><font color="green">Итого доход:</font></b> - '.$i_doh.'<br>';
echo '<b><font color="red">Итого расход:</font></b> - '.$i_rash.' из '.$i_rashb.'<br>';
if($i_rash>$i_rashb){
	echo '<b><font color="red">Перерасход:</font></b> - '.($i_rash-$i_rashb).'<br>';
}else{
	echo '<b><font color="green">Остаток бюджета:</font></b> - '.($i_rashb-$i_rash).'<br>';
}
echo '<b>Осаток:</b> - ';
echo $i_doh-$i_rash-$i_ipot;
}
}

function ParameterList($mysqli, $viewGrid, $viewDim){
?>
<form method="post">
<label for="viewGrid">Периуд:</label>
<select id="viewGrid" name="viewGrid" onchange="this.form.submit()">
 <?php
 getMonthList($viewGrid);
 ?>
</select>
<label for="viewDim">Статья:</label>
<select id="viewDim" name="viewDim" onchange="this.form.submit()">
 <?php
 getDimentionsList($viewDim, $mysqli);
 ?>
</select>
</form>
<?php
}



if (!$detect->isMobile()) {
?>

<html>
<?php
print_header()
?>
<body>
<form method="post">
<input type="hidden" name="action" value="add_new"/>
<input type="hidden" name="viewGrid" value="<?=$viewGrid?>"/>
<input type="hidden" name="viewDim" value="<?=$viewDim?>"/>

<label for="amount">Сумма:</label>
<input type="text" id="amount" name="amount" />

<label for="desc">Описание:</label>
<input type="text" id="desc" name="desc" />

<label for="dimension">Статья</label>
<select id="dimension" name="dimension">
<?php
 getList('dimensions');
 ?>
</select>

<label for="account">Счет:</label>
<select id="account" name="account">
 <?php
 getList('accounts');
 ?>
</select>
<label for="dates">Дата:</label>
<input type="text" id="dates" name="dates" value="<?=date("d.m.Y")?>" />
<input type="submit" name="Добавить">
</form>
<br>

<?php
ParameterList($mysqli, $viewGrid, $viewDim);

?>
<table width="100%" border="1" cellspacing="0" cellpadding="0">
<tr>
<td width="285px" valign="top">

<?php

getDimentionsItogs($mysqli, $viewGrid);

?>
</td>
<td valign="top">

<?php
TransactionGridView($mysqli,$viewDim, $viewGrid);
?>

</td>
</tr>
</table>
</body>
</html>
<?php
}else{

?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html>
<body>
<?php

print_header();

if($act=="s"){

echo '<h3>Статьи</h3><a href="./"><<< назад</a>';
getDimentionsItogs($mysqli, $viewGrid);

}else if($act=="v"){
	if ($delete_id > 0){
		$query = "CALL transactions_delete($delete_id)";
		 mysqli_query($mysqli, $query) or die(mysql_error()); 
		echo "<b><font color='red'>Записть удалена!</font></b>";
	}
echo '<h3>Операции</h3><a href="./"><<< назад</a>';
ParameterList($mysqli, $viewGrid, $viewDim);
TransactionGridView($mysqli,$viewDim, $viewGrid);

}else if($act=="edit"){
	if($update==1){
		$dates_formated = date('Y-m-d',strtotime($dates));
		$query = "CALL transactions_update($edit , $amount , '$desc', $dimension, '$dates_formated')";
		//echo $query;
		mysqli_query($mysqli, $query) or die(mysql_error()); 
		echo "Информация изменена!"; 
	}

$query = "CALL transactions_gridView(NULL , NULL , NULL, $edit)";	

$q = mysqli_multi_query($mysqli, $query)  or die( mysqli_error( $mysqli ));// or die(mysql_error()); 

if ($q)
{
do{
	if ($result = mysqli_store_result($mysqli)){

	  while ($row=mysqli_fetch_row($result)) { 
		echo '<h3>Редактирование операции</h3>';
		echo '<form action="./?act=edit&edit='.$row[0].'&update=1" method="post">';
		echo '<table><tr><td>';
		echo '<a href="./?act=v"><<< назад</a></td><td align="right"><a href="?act=v&delete_id='.$row[0].'" onclick="return ';
		echo "confirm('Удалить запись?')";
		echo '? true : false;">Удалить</a></td></tr>';
		echo '<tr><td colspan="2"><input type="hidden" name="edit" value="'.$row[0].'"/><input type="hidden" name="update" value="1"/>';
		echo '<label for="amount">Сумма:</label><input type="text" id="amount" name="amount" value="'.$row[1].'" /><br>';
		echo '<label for="desc">Описание:</label><input type="text" id="desc" name="desc" value="'.$row[6].'" /><br>';
		echo '<label for="dimension">Статья</label><select id="dimension" name="dimension">';
		 getList('dimensions', $row[7]);
		echo '</select><br>';
		echo '<input type="hidden" name="account" value="1"/>';
		echo '<label for="dates">Дата:</label><input type="text" id="dates" name="dates" value="'.$row[5].'"/><br>';
		echo '<input type="submit" value="Изменить">';
		echo '</tr></td></table>';
		echo '</form>';
	  } 
	}
}while(mysqli_next_result($mysqli));
}
}else{
?>
<form method="post">
<h3>Добавление новых затрат</h3>
<a href="?act=s">Статьи</a> | <a href="?act=v">Операции</a><br>
<input type="hidden" name="action" value="add_new"/>
<input type="hidden" name="viewGrid" value="<?=$viewGrid?>"/>
<input type="hidden" name="viewDim" value="<?=$viewDim?>"/>

<label for="amount">Сумма:</label>
<input type="text" id="amount" name="amount" /><br>

<label for="desc">Описание:</label>
<input type="text" id="desc" name="desc" /><br>

<label for="dimension">Статья</label>
<select id="dimension" name="dimension">
<?php
 getList('dimensions');
 ?>
</select><br>
<input type="hidden" name="account" value="1"/>
<input type="hidden" name="dates" value="<?=date("d.m.Y")?>"/>

<input type="submit" name="Добавить">

</form>

<?php
}
?>
</body>
</html>
<?php	
 }
?>