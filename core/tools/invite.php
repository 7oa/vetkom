<?php

	$config = parse_ini_file($_SERVER['DOCUMENT_ROOT']."/config.ini");
	require_once $_SERVER['DOCUMENT_ROOT']."/core/tools/send.php";

	$num = 0;
	foreach($_POST as $key=>$arPost) {
		$keyNum = substr($key, -1);
		$keyNew = substr($key, 0, -1);
		if ($num == 0) $keyNew = "partner";
		$new[$keyNum][$keyNew]=$arPost;
		$num = $num + 1;
	}
	
	if (count($new) > 0) {
		
		// Статические данные.
		$file = $_SERVER['DOCUMENT_ROOT']."/docs/buklet.pdf"; // файл
		$from = $config['cmail'];
		$subject = "Приглашение";

		// Шаблон письма.
		ob_start();
			include $_SERVER['DOCUMENT_ROOT']."/core/tools/message_text.html";
		$message_tpl = ob_get_clean();

		for ($i = 0; $i < count($new); $i++){
			
			$num = $i + 1;
			$arPartners = $new[$num];

			$org = $arPartners['partner'];
			$login = $arPartners['login'];
			$password = $arPartners['password'];
			$mailTo = $arPartners['email'];

			// Генерируем сообщение из шаблона
			$message = str_replace(
				array("%ORGANIZATION%", "%LOGIN%", "%PASSWORD%"),
				array($org, $login, $password), 
				$message_tpl);

			ob_start();
				
			$m = ob_get_clean();
			$message = $message."<br />".$m;

			$r = sendMailAttachment($mailTo, $from, $subject, $message, $file);
		
		}

	}
	

?>