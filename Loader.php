<?php
function Loader($className) {
	if(strpos($className, "Controller") != false && file_exists("controller/".$className.".php"))
		require_once("controller/".$className.".php");
	elseif(strpos($className, "Parser") != false && file_exists("parser/".$className.".php"))
		require_once("parser/".$className.".php");
}

spl_autoload_register("Loader");