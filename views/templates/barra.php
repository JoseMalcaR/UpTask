<div class="barra">
    <p><span class="bienvenida">Bienvenido: </span><span class="usuario"><?php echo s($_SESSION['nombre'] ?? ''); ?></span>!</p>

    <a href="/logout" class="cerrar-sesion">Cerrar Sesión</a>
</div>