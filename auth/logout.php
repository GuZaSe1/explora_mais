<?php
session_start();
session_destroy();
header('Location: /explora_mais/auth/login.php');
exit;