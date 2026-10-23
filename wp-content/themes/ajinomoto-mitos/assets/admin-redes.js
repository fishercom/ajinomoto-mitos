// Ajustes Mitos: lista editable de redes sociales (agregar, eliminar, ordenar y elegir
// el icono desde la biblioteca de medios).
jQuery(function ($) {
    var $lista = $('#mitos-redes-lista');
    var plantilla = $('#mitos-redes-plantilla').html();
    var siguiente = Date.now();

    $lista.sortable({ handle: '.mitos-red__handle', placeholder: 'mitos-red-placeholder', forcePlaceholderSize: true });

    $('#mitos-redes-agregar').on('click', function () {
        $lista.append(plantilla.replace(/__INDEX__/g, siguiente++));
    });

    $lista.on('click', '.mitos-red__eliminar', function () {
        $(this).closest('.mitos-red').remove();
    });

    function setIcono($fila, id, url) {
        $fila.find('.mitos-red__icono-id').val(id);
        $fila.find('.mitos-red__preview img').attr('src', url);
        $fila.find('.mitos-red__preview, .mitos-red__quitar-icono').prop('hidden', !id);
    }

    $lista.on('click', '.mitos-red__quitar-icono', function () {
        setIcono($(this).closest('.mitos-red'), '', '');
    });

    $lista.on('click', '.mitos-red__elegir', function () {
        var $fila = $(this).closest('.mitos-red');
        var frame = wp.media({
            title: 'Elegir icono',
            button: { text: 'Usar este icono' },
            library: { type: 'image' },
            multiple: false
        });
        frame.on('select', function () {
            var adjunto = frame.state().get('selection').first().toJSON();
            setIcono($fila, adjunto.id, adjunto.url);
        });
        frame.open();
    });
});
