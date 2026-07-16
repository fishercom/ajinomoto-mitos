<?php
/**
 * Funciones y definiciones del tema Ajinomoto Mitos.
 *
 * @package Ajinomoto_Mitos
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Configuración de claves por defecto para Google reCAPTCHA v2 (pueden sobreescribirse en wp-config.php)
 */
if ( ! defined( 'RECAPTCHA_SITE_KEY' ) ) {
    define( 'RECAPTCHA_SITE_KEY', '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI' );
}
if ( ! defined( 'RECAPTCHA_SECRET_KEY' ) ) {
    define( 'RECAPTCHA_SECRET_KEY', '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe' );
}

/**
 * Encolar los estilos y scripts del tema.
 */
function ajinomoto_mitos_scripts() {
    // Fuentes de Google y FontAwesome
    wp_enqueue_style( 'ajinomoto-fonts', 'https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300..900;1,300..900&display=swap', array(), null );
    wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css', array(), null );
    wp_enqueue_style( 'fancybox-css', 'https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css', array(), null );
    
    // Hojas de estilo locales
    wp_enqueue_style( 'swiper-css', get_template_directory_uri() . '/assets/vendor/swiper/swiper-bundle.min.css', array(), '8.0.0' );
    wp_enqueue_style( 'ajinomoto-output', get_template_directory_uri() . '/assets/css/output.css', array(), '1.0.0' );
    wp_enqueue_style( 'ajinomoto-style', get_stylesheet_uri(), array( 'ajinomoto-output' ), '1.0.0' );

    // Scripts en el Header (AlpineJS defered)
    wp_enqueue_script( 'alpine-focus', 'https://cdn.jsdelivr.net/npm/@alpinejs/focus@3.x.x/dist/cdn.min.js', array(), null, false );
    wp_enqueue_script( 'alpine-collapse', 'https://cdn.jsdelivr.net/npm/@alpinejs/collapse/dist/cdn.min.js', array(), null, false );
    wp_enqueue_script( 'alpine-js', 'https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js', array( 'alpine-focus', 'alpine-collapse' ), null, false );

    // GSAP en Footer
    wp_enqueue_script( 'gsap', 'https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js', array(), null, true );
    wp_enqueue_script( 'gsap-scroll-trigger', 'https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js', array( 'gsap' ), null, true );
    
    // Swiper y Custom JS en Footer
    wp_enqueue_script( 'swiper-js', get_template_directory_uri() . '/assets/vendor/swiper/swiper-bundle.min.js', array(), '8.0.0', true );
    wp_enqueue_script( 'ajinomoto-custom', get_template_directory_uri() . '/assets/custom.js', array( 'gsap', 'swiper-js' ), '1.0.0', true );
    wp_enqueue_script( 'ajinomoto-forms', get_template_directory_uri() . '/assets/forms.js', array(), '1.0.0', true );

    // Parámetros globales para el JS de búsqueda y formularios
    wp_localize_script( 'ajinomoto-custom', 'ajinomoto_params', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'site_url' => site_url(),
    ));
    wp_localize_script( 'ajinomoto-forms', 'ajinomoto_params', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'site_url' => site_url(),
    ));
}
add_action( 'wp_enqueue_scripts', 'ajinomoto_mitos_scripts' );

/**
 * Añadir atributo 'defer' a AlpineJS.
 */
function ajinomoto_mitos_defer_scripts( $tag, $handle, $src ) {
    $defer_handles = array( 'alpine-focus', 'alpine-collapse', 'alpine-js' );
    if ( in_array( $handle, $defer_handles ) ) {
        return '<script defer src="' . esc_url( $src ) . '"></script>' . "\n";
    }
    return $tag;
}
add_filter( 'script_loader_tag', 'ajinomoto_mitos_defer_scripts', 10, 3 );

/**
 * Configuración del soporte del tema.
 */
function ajinomoto_mitos_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    
    register_nav_menus( array(
        'primary' => 'Menú Principal (Cabecera)',
        'footer'  => 'Menú del Pie de Página',
    ) );
}
add_action( 'after_setup_theme', 'ajinomoto_mitos_setup' );

/**
 * Registro de Custom Post Types (Mitos y Revisores).
 */
function ajinomoto_mitos_register_post_types() {
    // 1. CPT Revisores (Fichas profesionales)
    $labels_revisores = array(
        'name'               => 'Revisores',
        'singular_name'      => 'Revisor',
        'menu_name'          => 'Revisores',
        'add_new'            => 'Añadir Nuevo',
        'add_new_item'       => 'Añadir Nuevo Revisor',
        'edit_item'          => 'Editar Revisor',
        'new_item'           => 'Nuevo Revisor',
        'view_item'          => 'Ver Revisor',
        'search_items'       => 'Buscar Revisores',
        'not_found'          => 'No se encontraron revisores',
        'not_found_in_trash' => 'No hay revisores en la papelera',
    );
    $args_revisores = array(
        'labels'             => $labels_revisores,
        'public'             => true,
        'has_archive'        => false,
        'supports'           => array( 'title', 'thumbnail' ),
        'show_in_rest'       => true,
        'menu_icon'          => 'dashicons-businessman',
        'rewrite'            => array( 'slug' => 'revisores' ),
    );
    register_post_type( 'revisores', $args_revisores );

    // 2. CPT Mitos (GMS y Cocina)
    $labels_mitos = array(
        'name'               => 'Mitos',
        'singular_name'      => 'Mito',
        'menu_name'          => 'Mitos',
        'add_new'            => 'Añadir Nuevo',
        'add_new_item'       => 'Añadir Nuevo Mito',
        'edit_item'          => 'Editar Mito',
        'new_item'           => 'Nuevo Mito',
        'view_item'          => 'Ver Mito',
        'search_items'       => 'Buscar Mitos',
        'not_found'          => 'No se encontraron mitos',
        'not_found_in_trash' => 'No hay mitos en la papelera',
    );
    $args_mitos = array(
        'labels'             => $labels_mitos,
        'public'             => true,
        'has_archive'        => true,
        'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
        'show_in_rest'       => true,
        'menu_icon'          => 'dashicons-lightbulb',
        'rewrite'            => array( 'slug' => 'mitos' ),
    );
    register_post_type( 'mitos', $args_mitos );
}
add_action( 'init', 'ajinomoto_mitos_register_post_types' );

/**
 * Registro de Taxonomías (Tipo de Mito y Categoría de Mito).
 */
function ajinomoto_mitos_register_taxonomies() {
    // 1. Taxonomía Tipo de Mito (gms vs cocina)
    $labels_tipo = array(
        'name'              => 'Tipos de Mito',
        'singular_name'     => 'Tipo de Mito',
        'search_items'      => 'Buscar Tipos de Mito',
        'all_items'         => 'Todos los Tipos',
        'edit_item'         => 'Editar Tipo de Mito',
        'update_item'       => 'Actualizar Tipo de Mito',
        'add_new_item'      => 'Añadir Nuevo Tipo de Mito',
        'new_item_name'     => 'Nombre del Nuevo Tipo',
        'menu_name'         => 'Tipos de Mito',
    );
    $args_tipo = array(
        'hierarchical'      => true,
        'labels'            => $labels_tipo,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'show_in_rest'      => true,
        'rewrite'           => array( 'slug' => 'tipo-mito' ),
    );
    register_taxonomy( 'tipo_mito', array( 'mitos' ), $args_tipo );

    // 2. Taxonomía Categoría de Mito (Seguridad, Evidencia, etc.)
    $labels_cat = array(
        'name'              => 'Categorías de Mito',
        'singular_name'     => 'Categoría de Mito',
        'search_items'      => 'Buscar Categorías',
        'all_items'         => 'Todas las Categorías',
        'edit_item'         => 'Editar Categoría',
        'update_item'       => 'Actualizar Categoría',
        'add_new_item'      => 'Añadir Nueva Categoría',
        'new_item_name'     => 'Nombre de la Nueva Categoría',
        'menu_name'         => 'Categorías de Mito',
    );
    $args_cat = array(
        'hierarchical'      => true,
        'labels'            => $labels_cat,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'show_in_rest'      => true,
        'rewrite'           => array( 'slug' => 'categoria-mito' ),
    );
    register_taxonomy( 'categoria_mito', array( 'mitos' ), $args_cat );
}
add_action( 'init', 'ajinomoto_mitos_register_taxonomies' );

/**
 * Procesar el envío del formulario 'Cuéntanos tu mito'.
 */
function handle_enviar_mito() {
    // Verificar nonce de seguridad
    if ( ! isset( $_POST['security'] ) || ! wp_verify_nonce( $_POST['security'], 'enviar_mito_nonce' ) ) {
        wp_send_json_error( array( 'message' => 'Error de seguridad. Por favor, recarga la página.' ) );
    }

    // Validar reCAPTCHA
    $recaptcha_response = isset( $_POST['g-recaptcha-response'] ) ? sanitize_text_field( $_POST['g-recaptcha-response'] ) : '';
    if ( empty( $recaptcha_response ) ) {
        wp_send_json_error( array( 'message' => 'Por favor, completa la verificación de reCAPTCHA.' ) );
    }

    $secret_key = RECAPTCHA_SECRET_KEY;
    $verify_url = 'https://www.google.com/recaptcha/api/siteverify';
    
    $response = wp_remote_post( $verify_url, array(
        'body' => array(
            'secret'   => $secret_key,
            'response' => $recaptcha_response,
            'remoteip' => $_SERVER['REMOTE_ADDR'],
        ),
    ) );

    if ( is_wp_error( $response ) ) {
        wp_send_json_error( array( 'message' => 'No se pudo verificar el reCAPTCHA. Inténtalo más tarde.' ) );
    }

    $response_body = json_decode( wp_remote_retrieve_body( $response ), true );
    if ( ! isset( $response_body['success'] ) || ! $response_body['success'] ) {
        wp_send_json_error( array( 'message' => 'Falló la verificación de reCAPTCHA. Inténtalo de nuevo.' ) );
    }

    // Obtener y sanitizar datos del formulario
    $nombre        = isset( $_POST['nombre'] ) ? sanitize_text_field( $_POST['nombre'] ) : '';
    $dni           = isset( $_POST['dni'] ) ? sanitize_text_field( $_POST['dni'] ) : '';
    $email         = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
    $celular       = isset( $_POST['celular'] ) ? sanitize_text_field( $_POST['celular'] ) : '';
    $mensaje_mito  = isset( $_POST['mensaje_mito'] ) ? sanitize_textarea_field( $_POST['mensaje_mito'] ) : '';

    // Validar campos requeridos
    if ( empty( $nombre ) || empty( $dni ) || empty( $email ) || empty( $celular ) || empty( $mensaje_mito ) ) {
        wp_send_json_error( array( 'message' => 'Todos los campos son obligatorios.' ) );
    }

    // Validar que el DNI sea numérico
    if ( ! preg_match( '/^[0-9]+$/', $dni ) ) {
        wp_send_json_error( array( 'message' => 'El Número de DNI debe contener solo números.' ) );
    }

    // Validar que el celular sea numérico (corrigiendo el typo de la solicitud)
    if ( ! preg_match( '/^[0-9]+$/', $celular ) ) {
        wp_send_json_error( array( 'message' => 'El Número de celular debe contener solo números.' ) );
    }

    // Validar formato de correo electrónico
    if ( ! is_email( $email ) ) {
        wp_send_json_error( array( 'message' => 'Por favor, ingresa un correo electrónico válido.' ) );
    }

    // Correo de destino del administrador
    $to = get_option( 'admin_email' );
    $subject = 'Nuevo Mito Recibido de: ' . $nombre;
    
    // Contenido del correo en formato HTML
    $headers = array( 'Content-Type: text/html; charset=UTF-8' );
    $body = "
    <h2>Nuevo Mito Enviado por un Usuario</h2>
    <p><strong>Nombres y Apellidos:</strong> {$nombre}</p>
    <p><strong>DNI:</strong> {$dni}</p>
    <p><strong>Correo Electrónico:</strong> {$email}</p>
    <p><strong>Celular:</strong> {$celular}</p>
    <p><strong>Mito Propuesto:</strong></p>
    <p style='background:#f4f4f4; padding: 15px; border-left: 4px solid #ff0000; font-style: italic;'>
        " . nl2br( esc_html( $mensaje_mito ) ) . "
    </p>
    ";

    // Guardar en la base de datos como post de tipo 'mitos_recibidos'
    $post_id = wp_insert_post( array(
        'post_title'   => 'Mito de ' . $nombre,
        'post_content' => $mensaje_mito,
        'post_status'  => 'publish', // Al ser no público, funciona como registro privado
        'post_type'    => 'mitos_recibidos',
    ) );

    if ( ! is_wp_error( $post_id ) ) {
        update_post_meta( $post_id, 'user_dni', $dni );
        update_post_meta( $post_id, 'user_email', $email );
        update_post_meta( $post_id, 'user_celular', $celular );
    }

    $sent = wp_mail( $to, $subject, $body, $headers );

    if ( ! is_wp_error( $post_id ) ) {
        wp_send_json_success( array( 'message' => '¡Gracias! Tu mito ha sido enviado correctamente.' ) );
    } else {
        wp_send_json_error( array( 'message' => 'Error al guardar tu mito. Por favor, inténtalo de nuevo.' ) );
    }
}
add_action( 'wp_ajax_enviar_mito', 'handle_enviar_mito' );
add_action( 'wp_ajax_nopriv_enviar_mito', 'handle_enviar_mito' );

/**
 * Registro de Custom Post Type 'mitos_recibidos' (Buzón de Mitos).
 */
function ajinomoto_mitos_register_recibidos_cpt() {
    $labels = array(
        'name'               => 'Mitos Recibidos',
        'singular_name'      => 'Mito Recibido',
        'menu_name'          => 'Mitos Recibidos',
        'name_admin_bar'     => 'Mito Recibido',
        'add_new'            => 'Añadir Nuevo',
        'add_new_item'       => 'Añadir Nuevo Mito Recibido',
        'new_item'           => 'Nuevo Mito Recibido',
        'edit_item'          => 'Ver/Editar Mito Recibido',
        'view_item'          => 'Ver Mito Recibido',
        'all_items'          => 'Todos los Mitos',
        'search_items'       => 'Buscar Mitos Recibidos',
        'not_found'          => 'No se encontraron mitos recibidos',
        'not_found_in_trash' => 'No hay mitos recibidos en la papelera',
    );

    $args = array(
        'labels'             => $labels,
        'public'             => false, // Privado, no accesible en el frontend
        'show_ui'            => true,  // Mostrar en la administración de WordPress
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite'            => false,
        'capability_type'    => 'post',
        'has_archive'        => false,
        'hierarchical'       => false,
        'menu_position'      => 26,
        'menu_icon'          => 'dashicons-email-alt2',
        'supports'           => array( 'title', 'editor' ),
    );

    register_post_type( 'mitos_recibidos', $args );
}
add_action( 'init', 'ajinomoto_mitos_register_recibidos_cpt' );

/**
 * Agregar columnas personalizadas al listado de Mitos Recibidos.
 */
function set_custom_edit_mitos_recibidos_columns($columns) {
    $new_columns = array(
        'cb' => $columns['cb'],
        'title' => 'Nombre del Usuario',
        'user_dni' => 'DNI',
        'user_email' => 'Correo Electrónico',
        'user_celular' => 'Celular',
        'date' => $columns['date'],
    );
    return $new_columns;
}
add_filter( 'manage_mitos_recibidos_posts_columns', 'set_custom_edit_mitos_recibidos_columns' );

/**
 * Mostrar valores en las columnas personalizadas.
 */
function custom_mitos_recibidos_column( $column, $post_id ) {
    switch ( $column ) {
        case 'user_dni' :
            echo esc_html( get_post_meta( $post_id, 'user_dni', true ) );
            break;
        case 'user_email' :
            echo esc_html( get_post_meta( $post_id, 'user_email', true ) );
            break;
        case 'user_celular' :
            echo esc_html( get_post_meta( $post_id, 'user_celular', true ) );
            break;
    }
}
add_action( 'manage_mitos_recibidos_posts_custom_column' , 'custom_mitos_recibidos_column', 10, 2 );

/**
 * Metabox para mostrar los datos del usuario en la edición del post.
 */
function add_mitos_recibidos_metabox() {
    add_meta_box(
        'mitos_recibidos_details',
        'Datos del Usuario',
        'display_mitos_recibidos_metabox',
        'mitos_recibidos',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'add_mitos_recibidos_metabox' );

function display_mitos_recibidos_metabox( $post ) {
    $dni = get_post_meta( $post->ID, 'user_dni', true );
    $email = get_post_meta( $post->ID, 'user_email', true );
    $celular = get_post_meta( $post->ID, 'user_celular', true );
    ?>
    <table class="form-table">
        <tr>
            <th style="width:20%;"><label>DNI:</label></th>
            <td><input type="text" style="width:100%;" value="<?php echo esc_attr( $dni ); ?>" readonly /></td>
        </tr>
        <tr>
            <th><label>Correo Electrónico:</label></th>
            <td><input type="text" style="width:100%;" value="<?php echo esc_attr( $email ); ?>" readonly /></td>
        </tr>
        <tr>
            <th><label>Celular:</label></th>
            <td><input type="text" style="width:100%;" value="<?php echo esc_attr( $celular ); ?>" readonly /></td>
        </tr>
    </table>
    <?php
}
