<?php
$categoria = 'panaderia'; // Cambiar según archivo
?>
<link href="https://fonts.googleapis.com/css2?family=Quicksand&family=Josefin+Sans&display=swap" rel="stylesheet">
<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Anton&family=Bebas+Neue&family=Oswald:wght@400;500;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="../css/locales.css">
<link rel="stylesheet" href="../css/alimentosPublic.css">
<div class="form-locales">
    <div class="form-container">
        <h2 id="categoriaTitulo" class="titulo-categoria">PANADERÍA</h2>
        <div id="localesSection">
            <div class="loading" id="loading">
                <div class="spinner"></div>
                Cargando locales...
            </div>
            <div class="local-grid" id="GridLocal"></div>
        </div>
    </div>
</div>
<div id="modalLogin" class="custom-modal">
    <div class="custom-modal-content">
        <span class="custom-close">&times;</span>
        <iframe id="loginFrame" src=""></iframe>
    </div>
</div>

<script src="../js/localPublic.js"></script>
<script>
    // Pasar la categoría desde PHP al JS
    initLocales('<?php echo $categoria; ?>');
</script>