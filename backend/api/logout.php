<?php
session_start();
session_destroy();
header('Location: ../../frontend/admin_login.html');
?>
