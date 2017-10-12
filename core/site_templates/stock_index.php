<h1>Акции</h1>
<div class="stock">
<?
$directory = "images/stock/";    // Папка с изображениями
$allowed_types=array("jpg", "png", "gif");  //разрешеные типы изображений
$file_parts = array();
$ext="";
$files = scandir($directory);
foreach ($files as $fileName){
    if($fileName=="." || $fileName == "..") continue;
    $file_parts = explode(".",$fileName);          //разделить имя файла и поместить его в массив
    $ext = strtolower(array_pop($file_parts));   //последний элеменет - это расширение


    if(in_array($ext,$allowed_types))
    {
        echo '<div class = "stock__item" >
                <img src="'.$directory.$fileName.'"/>
            </div>';
    }
}
?>
</div>
