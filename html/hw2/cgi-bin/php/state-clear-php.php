<?php
setcookie("hw2_state", "", time() - 3600, "/");
header("Location: /cgi-bin/php/state-view-php.php");
