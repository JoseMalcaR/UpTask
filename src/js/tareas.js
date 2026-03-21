
(function() {
    //Boton para mostrar el modal de agregar tarea
    const nuevaTareaBtn = document.querySelector('#agregar-tarea');
    nuevaTareaBtn.addEventListener('click', mostrarFormulario); 


    function mostrarFormulario() {
        const modal = document.createElement('DIV');
        modal.classList.add('modal');
        modal.innerHTML = `
            <form class="formulario nueva-tarea">
                <legend>Agregar Nueva Tarea</legend>
                <div class="campo">
                    <label for="tarea">Tarea:</label>
                    <input 
                    type="text" 
                    name="tarea" 
                    id="tarea" 
                    placeholder="Nombre de la tarea"
                    >
                </div>
                    <div class="opciones">
                        <input type="submit" class="submit-nueva-tarea" value="Agregar Tarea"/>
                        <button type="button" class="cerrar-modal">Cancelar</button>
                    </div>
            </form>
        `;

        setTimeout(() => {
            const formulario = document.querySelector('.formulario');
            formulario.classList.add('animar');
        }, 0);

        modal.addEventListener('click', function(e) {
            e.preventDefault();

            if(e.target.classList.contains('cerrar-modal')) {
                const formulario = document.querySelector('.formulario');
                formulario.classList.add('cerrar');
                setTimeout(() => {
                modal.remove();
                }, 500);
            }

            if(e.target.classList.contains('submit-nueva-tarea')) {
                submitFormularioNuevaTarea();
            
            }

        });

        document.querySelector('.dashboard').appendChild(modal);
    }

    function submitFormularioNuevaTarea() {
        const tarea = document.querySelector('#tarea').value.trim();

            if(tarea === '') {
                //Mostrar mensaje de error
                mostrarAlerta('El nombre de la tarea es obligatorio', 'error', document.querySelector('.formulario legend'));
                return;
            } 

            agregarTarea(tarea);
    
    }

    //Muestra una alerta en pantalla
    function mostrarAlerta(mensaje, tipo, referencia) {
        //Previene que se creen varias alertas
        const alertaPrevia = document.querySelector('.alerta');
        if(alertaPrevia) {
            alertaPrevia.remove();
        }

        const alerta = document.createElement('DIV');
        alerta.classList.add('alerta', tipo);
        alerta.textContent = mensaje;

        //Inserta la alerta antes del legend
        referencia.parentElement.insertBefore(alerta, referencia.nextElementSibling);

        //Elimina la alerta después de 3 segundos
        setTimeout(() => {
            alerta.remove();
        }, 5000);
    }

        //Agrega la tarea al proyecto actual
        async function agregarTarea(tarea) {
            //Construir la petición
            const datos = new FormData();
            datos.append('nombre', tarea);
            datos.append('proyectoId', obtenerProyectoId());


            try {
                const url = 'http://localhost:8000/api/tarea';
                const respuesta = await fetch(url, {
                    method: 'POST',
                    body: datos
                });

                const resultado = await respuesta.json();
                console.log(resultado);

                mostrarAlerta(resultado.mensaje, resultado.tipo, document.querySelector('.formulario legend'));

                if(resultado.tipo === 'exito') {
                    const modal = document.querySelector('.modal');
                    setTimeout(() => {
                        modal.remove();
                    }
                    , 2000);
                }

            } catch (error) {
                console.error(error);
                
            }
 
        }

        function obtenerProyectoId() {
            //Obtener la URL actual
            const proyectoParams = new URLSearchParams(window.location.search);
            const proyecto = Object.fromEntries(proyectoParams.entries());
            return proyecto.id;

        }

}());

