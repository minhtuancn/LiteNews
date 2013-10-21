<?php
function Loader($className) {
	if(strpos($className, "Controller") != false && file_exists("app/controller/".$className.".php"))
		require_once("app/controller/".$className.".php");
	elseif(strpos($className, "SQL") != false && file_exists("app/sql/".$className.".php"))
		require_once("app/sql/".$className.".php");
	elseif(strpos($className, "Parser") != false && file_exists("app/parser/".$className.".php"))
		require_once("app/parser/".$className.".php");
}

spl_autoload_register("Loader");