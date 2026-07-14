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
                    <form action="">
                        <div class="bloque">
                            <div class="c1">
                                <h3>Ingresa tus datos</h3>
                                <div class="bForm">
                                    <div class="campo">
                                        <label for="nombre">Nombres y apellidos</label>
                                        <input type="text" id="nombre">
                                    </div>
                                    <div class="campo">
                                        <label for="dni">Número de DNI</label>
                                        <input type="text" id="dni">
                                    </div>
                                    <div class="campo">
                                        <label for="email">Correo electrónico</label>
                                        <input type="email" id="email">
                                    </div>
                                    <div class="campo">
                                        <label for="celular">Número de celular</label>
                                        <input type="text" id="celular">
                                    </div>
                                    <div class="campo wfull check">
                                        <input type="checkbox" id="datos" placeholder="acepto"> 
                                        <label for="datos">Autorizo el tratamiento de mis datos por parte de Ajinomoto del Perú S.A.</label><br>
                                        <input type="checkbox" id="terminos" placeholder="acepto"> 
                                        <label for="terminos"><a href="#">Acepto los Términos y Condiciones</a></label>
                                    </div>
                                </div>
                            </div>
                            <div class="c2">
                                <div class="blk textura">
                                    <h3>¿Cuál es tu mito?</h3>
                                    <div class="campo wfull">
                                        <textarea name="mensaje_mito" id="mensaje_mito" cols="30" rows="4"></textarea>
                                    </div>
                                    <img src="<?php echo get_template_directory_uri(); ?>/img/capcha.png" alt="captcha" style="margin-bottom:10px;">
                                    <a href="#" class="btn">Envía mito</a>
                                </div>
                            </div>
                        </div>
                    </form>
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