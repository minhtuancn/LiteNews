<?php
chdir("..");
require_once("install/controller.php");

$controller = new InstallController;
echo $controller->GetPage(isset($_GET['step']) ? $_GET['step'] : NULL);