<?php
unset($_SESSION["user_login"]);
unset($_COOKIE["user_login"]);
setcookie('user_login', null, -1, '/');
do_redirect($_SERVER["HTTP_REFERER"]);
?>