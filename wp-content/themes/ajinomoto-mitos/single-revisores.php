<?php
/**
 * Plantilla para el detalle de un Revisor (Perfil Profesional).
 *
 * @package Ajinomoto_Mitos
 */

get_header();

if ( have_posts() ) :
    while ( have_posts() ) : the_post();
        $post_id = get_the_ID();

        // Obtener campos ACF
        $cargo = get_post_meta( $post_id, 'cargo_revisor', true );
        $resumen = get_post_meta( $post_id, 'resumen_revisor', true );
        $formacion = get_post_meta( $post_id, 'formacion_academica', true );
        $experiencia = get_post_meta( $post_id, 'experiencia_profesional', true );
        $premios = get_post_meta( $post_id, 'premios_distinciones', true );
        $publicaciones = get_post_meta( $post_id, 'publicaciones', true );

        // Foto de perfil
        $photo_url = get_the_post_thumbnail_url( $post_id, 'medium_large' );
        if ( empty( $photo_url ) ) {
            $photo_url = get_template_directory_uri() . '/img/personal/silueta.png'; // fallback
        }
        ?>

        <main class="interna">
            <div class="contenido">            
                <div class="bloqueBlanco profesional">
                    <a href="javascript:history.back()" class="btn circular"></a>
                    
                    <div class="personal gr">
                        <img src="<?php echo esc_url( $photo_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>">
                        <div class="datos">
                            <h3><?php the_title(); ?></h3>
                            <?php if ( ! empty( $cargo ) ) : ?>
                                <p><strong><?php echo esc_html( $cargo ); ?></strong></p>
                            <?php endif; ?>
                            
                            <?php if ( ! empty( $resumen ) ) : ?>
                                <div class="blk textura">
                                    <p><?php echo esc_html( $resumen ); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ( ! empty( $formacion ) ) : ?>
                        <div class="detalle-personal">
                            <h4>
                                <img src="<?php echo get_template_directory_uri(); ?>/img/iconos/ic-formacion.svg" alt="Formación"> 
                                Formación Académica
                            </h4>
                            <div class="contenido-detalle">
                                <?php echo wp_kses_post( wpautop( $formacion ) ); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ( ! empty( $experiencia ) ) : ?>
                        <div class="detalle-personal">
                            <h4>
                                <img src="<?php echo get_template_directory_uri(); ?>/img/iconos/ic-experiencia.svg" alt="Experiencia"> 
                                Experiencia Profesional
                            </h4>
                            <div class="contenido-detalle">
                                <?php echo wp_kses_post( wpautop( $experiencia ) ); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ( ! empty( $premios ) ) : ?>
                        <div class="detalle-personal">
                            <h4>
                                <img src="<?php echo get_template_directory_uri(); ?>/img/iconos/ic-premios.svg" alt="Premios"> 
                                Premios y Distinciones
                            </h4>
                            <div class="contenido-detalle">
                                <?php echo wp_kses_post( wpautop( $premios ) ); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ( ! empty( $publicaciones ) ) : ?>
                        <div class="detalle-personal">
                            <h4>
                                <img src="<?php echo get_template_directory_uri(); ?>/img/iconos/ic-publicaciones.svg" alt="Publicaciones"> 
                                Publicaciones
                            </h4>
                            <div class="contenido-detalle">
                                <?php echo wp_kses_post( wpautop( $publicaciones ) ); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>            
                
                <div class="imgOsito">
                    <img src="<?php echo get_template_directory_uri(); ?>/img/osito-bt-cocina.svg" alt="ositos">
                </div>    
                
                <a href="javascript:history.back()" class="btn circular"></a>        
            </div>
        </main>

        <?php
    endwhile;
endif;

get_footer();
