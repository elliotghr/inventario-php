<?php
$bienvenido = "<h2 class='subtitle'>¡Bienvenido " . $_SESSION['nombre'] . "!</h2>"
?>

<div class="container is-fluid">
	<h1 class="title">Home</h1>
	<?php echo $bienvenido ?>
</div>