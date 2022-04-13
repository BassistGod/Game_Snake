<form action='index.php' method='get'> 
    <p>Высота поля <input type='text' name='height'></p>
    <p>Ширина поля <input type='text' name='width'></p>
    <input type='submit' value='Start game'>
</form>

<?php
echo "<hr>";

// функция проверки введенных значений, если что то кроме цифр, то Problem
function CheckVar($a){
    $a = trim($a);
    if (preg_match("/[\D]/",$a) != true)
        if ($a > 50)
            exit ('The value is too large');
        else    
            return $a;
    else 
        exit ('Problem');
}

// Открыли сессию, создается новая, либо откроется ранее созданная
session_start(); 

// определяем размер поля, создаем массив, заполняем его элементами и определяем начало
if (CheckVar ($_GET['height']) != 0 & CheckVar ($_GET['width'] != 0)){
    session_destroy();
    session_start();
    $_SESSION['field_height'] = CheckVar ($_GET['height']);
    $_SESSION['field_width'] = CheckVar ($_GET['width']);
    $_SESSION['value'] = round ($_SESSION['field_height'] * ($_SESSION['field_width'] / 2));
    $_SESSION['point'] = rand(1 , $_SESSION['field_height'] * $_SESSION['field_width']);
}

// задаем необходимые переменные
$field_height = $_SESSION['field_height'];
$field_width = $_SESSION['field_width'];
$size = $field_height * $field_width;
$value = '*';
$snake_skin = 'O';
$start = 1;
$arr = array_fill($start, $size, $value);
$arr_en = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];

// функция Game over
function Game_over(){
    exit ('Game over');
}

// меняем значение элемента массива, передвигая змею по полю
if ($_POST['symbol'] == 'Up'){
    if ($_SESSION['value'] > $field_width){
        $_SESSION['value'] -= $field_width;
    }
    else {
        $_SESSION['value'] += ($field_width * ($field_height - 1));
    }  
}
elseif ($_POST['symbol'] == 'Down'){
    if ($_SESSION['value'] <= ($field_height * ($field_width - 1))){
        $_SESSION['value'] += $field_width;
    }
    else {
        $_SESSION['value'] -= ($field_width * ($field_height - 1));
    }
} 
elseif ($_POST['symbol'] == 'Right'){
    if ($_SESSION['value'] % $field_width != 0){
        $_SESSION['value'] += 1;
    }
    else {
        $_SESSION['value'] -= ($field_width - 1);
    }
} 
elseif ($_POST['symbol'] == 'Left'){
    if ($_SESSION['value'] % $field_width != 1){
        $_SESSION['value'] -= 1;
    }
    else {
        $_SESSION['value'] += ($field_width - 1);
    }
} 

// съедение значка змеей 
if ($_SESSION['value'] == $_SESSION['point']){
    $_SESSION['snake_size']++;
}

// добавляем хвост змейке, в количестве съеденных значков, и создаем массив тела змеи
if (isset($_SESSION['snake_size'])){
    $n = $_SESSION['snake_size'];
    while ($n > 0){
        $n--;
        if ($n == 0){
            $_SESSION[$arr_en[$n]] = $_SESSION['value_old'];
        }    
        else{
            $_SESSION[$arr_en[$n]] = $_SESSION[$arr_en[$n-1]];
        } 
        $arr[$_SESSION[$arr_en[$n]]] = $snake_skin;
        $snake_body[] = $_SESSION[$arr_en[$n]];
    }
}

// Если змея врезается в свое тело, то конец игры
if (in_array($_SESSION['value'], $snake_body)){
    Game_over();
}

// Назначаем новую позицию для цели, отличную от тела змеи 
if ($_SESSION['value'] == $_SESSION['point']){
    $snake_body[] = $_SESSION['value'];
    while(in_array(($rand = rand(1 , $field_height * $field_width)), $snake_body));
    $_SESSION['point'] = $rand;
}  

// вид для змеи и цели
$arr[$_SESSION['value']] = $snake_skin;
$arr[$_SESSION['point']] = '$';

// запоминаем предыдущее положение для расчета хвоста 
$_SESSION['value_old'] = $_SESSION['value']; 

// выводим массив на экран в таблице и создаем кнопки управления
?>
<table border="0">
    <?php for ($j = 1; $j < $field_height+1; $j++){ ?>
    <tr>
    <?php
        for ($i = 1; $i < $field_width+1; $i++){
            ?><th width="15" height="15"> <?php 
            $k ++;
            echo $arr[$k]; ?></th>  
    <?php } ?>
    </tr>
    <?php } ?>
</table>
   
<hr>
</hr>

<form action="index.php" method="post" name="symbol">
 <p><select name="symbol" multiple>
  <option>Up</option>
  <option>Down</option>
  <option>Left</option>
  <option>Right</option>
 </select></p>
 <p><input type="submit" value="Отправить"></p>
</form>
