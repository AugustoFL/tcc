<?php
session_start();
session_destroy(); // Destroi todas as sessões
header("Location: index2.php"); // Redireciona para a página inicial
exit;
?>
