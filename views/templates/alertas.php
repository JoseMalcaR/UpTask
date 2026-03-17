<?php if(!empty($alertas)) : ?>
    <?php foreach($alertas as $key => $alerta) : ?>
        <?php foreach($alerta as $mensaje) : ?>
            <div class="alerta <?php echo $key; ?>"><?php echo $mensaje; ?></div>
        <?php endforeach; ?>
    <?php endforeach; ?>
<?php endif; ?>