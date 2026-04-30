<?php
session_start();
session_destroy();
header('Location: /explora_mais/autenticacao/login.php');
exit;