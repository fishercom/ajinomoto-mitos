<?php
/**
 * Template Name: Cuéntanos tu mito
 *
 * @package Ajinomoto_Mitos
 */

get_header();
?>
    <main class="interna">
        <div class="contenido">            
            <div class="intro">
                <h1>Cuéntanos tu mito</h1>
                <p>No solo queremos contarte todos los mitos que conocemos, también queremos que nos ayudes a descubrir
                    más.<br>
                    <strong>Comparte con nosotros los mitos que más recuerdes, y podríamos incluirlos en nuestra
                        lista.</strong>
                </p>
            </div>
            <?php
            // Intentar cargar dinámicamente el primer formulario de WPForms
            $wpforms_shortcode = '';
            if ( function_exists( 'wpforms' ) ) {
                $forms = get_posts( array(
                    'post_type'      => 'wpforms',
                    'posts_per_page' => 1,
                ) );
                if ( ! empty( $forms ) ) {
                    $wpforms_shortcode = '[wpforms id="' . $forms[0]->ID . '" title="false" description="false"]';
                }
            }

            if ( ! empty( $wpforms_shortcode ) ) {
                echo '<div class="formulario wpforms-container">';
                echo do_shortcode( $wpforms_shortcode );
                echo '</div>';
            } else {
                ?>
                <div class="formulario">
                    <!-- Script oficial de reCAPTCHA v2 -->
                    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                    <form id="form-cuentanos-mito" method="POST">
                        <!-- Campo oculto para indicar acción AJAX de WordPress y nonce -->
                        <input type="hidden" name="action" value="enviar_mito">
                        <?php wp_nonce_field( 'enviar_mito_nonce', 'security' ); ?>
                        
                        <div class="bloque">
                            <div class="c1">
                                <h3>Ingresa tus datos</h3>
                                <div class="bForm">
                                    <div class="campo">
                                        <label for="nombre">Nombres y apellidos</label>
                                        <input type="text" id="nombre" name="nombre" required>
                                    </div>
                                    <div class="campo">
                                        <label for="dni">Número de DNI</label>
                                        <input type="text" id="dni" name="dni" required>
                                    </div>
                                    <div class="campo">
                                        <label for="email">Correo electrónico</label>
                                        <input type="email" id="email" name="email" required>
                                    </div>
                                    <div class="campo">
                                        <label for="celular">Número de celular</label>
                                        <input type="text" id="celular" name="celular" required>
                                    </div>
                                    <div class="campo wfull check">
                                        <input type="checkbox" id="datos" name="datos" value="1" required> 
                                        <label for="datos">Autorizo el tratamiento de mis datos por parte de Ajinomoto del Perú S.A.</label><br>
                                        <input type="checkbox" id="terminos" name="terminos" value="1" required> 
                                        <label for="terminos"><a href="#">Acepto los Términos y Condiciones</a></label>
                                    </div>
                                </div>
                            </div>
                            <div class="c2">
                                <div class="blk textura">
                                    <h3>¿Cuál es tu mito?</h3>
                                    <div class="campo wfull">
                                        <textarea name="mensaje_mito" id="mensaje_mito" cols="30" rows="4" required></textarea>
                                    </div>
                                    
                                    <!-- Contenedor del reCAPTCHA de Google -->
                                    <div class="g-recaptcha" data-sitekey="<?php echo esc_attr( defined('RECAPTCHA_SITE_KEY') ? RECAPTCHA_SITE_KEY : '6LeIxAcTAAAAAJcZVRqyHh71UMIEGNQ_MXjiZKhI' ); ?>" style="margin-bottom: 15px;"></div>
                                    
                                    <a href="#" class="btn btn-submit-form" id="btn-submit-mito">Envía mito</a>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div id="form-message" style="margin-top: 15px; padding: 10px; display: none; border-radius: 4px; font-weight: bold; text-align: center;"></div>
                </div>
                <?php
            }
            ?>
            <div class="imgOsito">
                <img src="<?php echo get_template_directory_uri(); ?>/img/osito-bt-cuentanos.svg" alt="ositos">
            </div>
            <a href="javascript:history.back()" class="btn circular"></a>
        </div>

    </main>
<?php
get_footer();
?>