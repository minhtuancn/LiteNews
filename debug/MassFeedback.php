<?php
require_once("Debug.php");
$debug = new Debug("Feedback");

for($i = 1; $i < 10; ++$i) {
	$debug->db->AddFeedback(rand(1, 3), "Feedback number ".$i);
}

echo "Feedbacks added succesfully";
