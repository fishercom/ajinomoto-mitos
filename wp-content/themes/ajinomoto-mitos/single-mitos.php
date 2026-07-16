<?php
/**
 * Plantilla para el detalle de un Mito.
 *
 * @package Ajinomoto_Mitos
 */

get_header();

if ( have_posts() ) :
    while ( have_posts() ) : the_post();
        $post_id = get_the_ID();

        // Obtener taxonomías
        $tipo_terms = get_the_terms( $post_id, 'tipo_mito' );
        $tipo_slug = ( ! empty( $tipo_terms ) && ! is_wp_error( $tipo_terms ) ) ? $tipo_terms[0]->slug : 'cocina';
        $tipo_label = ( $tipo_slug === 'gms' ) ? 'Mitos del GMS' : 'Mitos de la cocina';
        
        $cat_terms = get_the_terms( $post_id, 'categoria_mito' );
        $cat_name = ( ! empty( $cat_terms ) && ! is_wp_error( $cat_terms ) ) ? $cat_terms[0]->name : '';

        // Campos personalizados de ACF / Post Meta
        $subtitulo = get_post_meta( $post_id, 'subtitulo_mito', true );
        if ( empty( $subtitulo ) ) {
            $subtitulo = get_the_title();
        }
        
        $realidad_corta = get_post_meta( $post_id, 'realidad_corta', true );
        
        $sustento = get_post_meta( $post_id, 'sustento_cientifico', true );
        if ( empty( $sustento ) ) {
            $sustento = get_the_content();
        }
        
        $nutricional = get_post_meta( $post_id, 'perspectiva_nutricional', true );
        $palatabilidad = get_post_meta( $post_id, 'palatabilidad_digestion', true );
        $referencias = get_post_meta( $post_id, 'referencias', true );

        // Obtener imagen destacada del mito
        $featured_img_id = get_post_thumbnail_id();
        $featured_img_url = '';
        if ( $featured_img_id ) {
            $featured_img_url = wp_get_attachment_image_url( $featured_img_id, 'large' );
        }
        if ( empty( $featured_img_url ) ) {
            $featured_img_url = get_template_directory_uri() . '/img/informacion/mito-cocina.jpg';
        }

        // Obtener Revisor
        $revisor_id = get_post_meta( $post_id, 'revisor', true );
        $revisor_name = ''; $revisor_cargo = ''; $revisor_img = ''; $revisor_url = '';
        if ( $revisor_id ) {
            $revisor_name = get_the_title( $revisor_id );
            $revisor_cargo = get_post_meta( $revisor_id, 'cargo_revisor', true );
            $revisor_url = get_permalink( $revisor_id );
            $revisor_img_id = get_post_thumbnail_id( $revisor_id );
            if ( $revisor_img_id ) {
                $revisor_img = wp_get_attachment_image_url( $revisor_img_id, 'thumbnail' );
            }
        }
        if ( empty( $revisor_img ) ) {
            $revisor_img = get_template_directory_uri() . '/img/personal/chica.png';
        }

        // Osito decorativo según tipo de mito
        $osito_file = ( $tipo_slug === 'gms' ) ? 'ositos-bt-gms.svg' : 'osito-bt-cocina.svg';
        ?>

        <main class="interna">
            <div class="contenido">
                <div class="intro">
                    <h1><?php echo esc_html( $tipo_label ); ?></h1>
                </div>
                
                <div class="bloqueBlanco">
                    <div class="intro">
                        <h3>MITO: <?php echo esc_html( $subtitulo ); ?></h3>
                    </div>
                    
                    <div class="blk textura foto">                    
                        <div class="imgCategoriaMito">
                            <img src="<?php echo esc_url( $featured_img_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>">
                        </div>
                        <p><span class="textoRojo"><strong>Realidad:</strong></span> <?php echo esc_html( $realidad_corta ); ?></p>                        
                    </div>
                    
                    <h4>Sustento Científico y Técnico:</h4>
                    <div class="sustento-contenido">
                        <?php echo wp_kses_post( wpautop( $sustento ) ); ?>
                    </div>
                    
                    <?php if ( ! empty( $nutricional ) ) : ?>
                        <h4>Perspectiva Nutricional:</h4>
                        <div class="nutricional-contenido">
                            <?php echo wp_kses_post( wpautop( $nutricional ) ); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ( ! empty( $palatabilidad ) ) : ?>
                        <h4>Palatabilidad y Digestión:</h4>
                        <div class="palatabilidad-contenido">
                            <?php echo wp_kses_post( wpautop( $palatabilidad ) ); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="blCajas">
                        <?php if ( ! empty( $referencias ) ) : ?>
                            <div class="blk gris">
                                <h5>Referencia: </h5>
                                <p><?php echo esc_html( $referencias ); ?></p>
                            </div>    
                        <?php endif; ?>

                        <?php if ( ! empty( $revisor_name ) ) : ?>
                            <div class="blk textura">
                                <div class="personal">
                                    <img src="<?php echo esc_url( $revisor_img ); ?>" alt="<?php echo esc_attr( $revisor_name ); ?>">
                                    <div class="datos">
                                        <h5>Revisado por: </h5>
                                        <p><strong><?php echo esc_html( $revisor_name ); ?></strong></p>
                                        <p><?php echo esc_html( $revisor_cargo ); ?></p>
                                    </div>
                                </div>
                                <?php if ( ! empty( $revisor_url ) ) : ?>
                                    <a href="<?php echo esc_url( $revisor_url ); ?>" class="btn circular pDer"></a>
                                <?php endif; ?>
                            </div>    
                        <?php endif; ?>
                    </div>
                </div>

                <div class="intro">
                    <h2>Cuéntanos tu mito</h2>
                    <p>No solo queremos contarte todos los mitos que conocemos, también queremos que nos ayudes a descubrir más.<br>
                        <strong>Comparte con nosotros los mitos que más recuerdes, y podríamos incluirlos en nuestra lista.</strong>
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
                    // Fallback estático maquetado
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
                                        <div class="g-recaptcha" data-sitekey="<?php echo esc_attr( defined('RECAPTCHA_SITE_KEY') ? RECAPTCHA_SITE_KEY : '6LeGxAcTAAAAAJcZ57UY7RvwA2W6vM9VEwUylNsQ' ); ?>" style="margin-bottom: 15px;"></div>
                                        
                                        <button type="submit" class="btn" style="border: none; cursor: pointer; display: inline-block;">Envía mito</button>
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
                    <img src="<?php echo get_template_directory_uri(); ?>/img/<?php echo esc_attr( $osito_file ); ?>" alt="ositos">
                </div>
                
                <a href="javascript:history.back()" class="btn circular"></a>
            </div>
        </main>

        <?php
    endwhile;
endif;

get_footer();
