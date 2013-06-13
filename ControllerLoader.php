<?php
function ControllerLoader($controllerName) {
	require_once("controller/".$controllerName.".php");
}

spl_autoload_register("ControllerLoader");
