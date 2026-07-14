<?php
/**
 * Template Name: Artículo de Información GMS (Nieta)
 *
 * @package Ajinomoto_Mitos
 */

get_header();

if ( have_posts() ) :
    while ( have_posts() ) : the_post();
        $post_id = get_the_ID();
        
        // Obtener ancestros (Padre y Abuelo)
        $parent_id = wp_get_post_parent_id( $post_id );
        $grandparent_id = $parent_id ? wp_get_post_parent_id( $parent_id ) : 0;
        
        $top_title = $grandparent_id ? get_the_title( $grandparent_id ) : ( $parent_id ? get_the_title( $parent_id ) : get_the_title() );
        $top_desc = $grandparent_id ? get_the_excerpt( $grandparent_id ) : ( $parent_id ? get_the_excerpt( $parent_id ) : '' );
        ?>

        <main class="interna">
            <div class="contenido">
                <div class="intro">
                    <h1><?php echo esc_html( $top_title ); ?></h1>
                    <?php if ( ! empty( $top_desc ) ) : ?>
                        <p><?php echo esc_html( $top_desc ); ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="bloqueBlanco info">
                    <h3><?php the_title(); ?></h3>
                    
                    <div class="articulo-contenido">
                        <?php the_content(); ?>
                    </div>
                    
                    <a href="javascript:history.back()" class="btn circular" style="margin-left: 0 !important;"></a>
                </div>
                
                <div class="imgOsito">
                    <img src="<?php echo get_template_directory_uri(); ?>/img/ositos-bt-info.svg" alt="ositos">
                </div>
                <a href="javascript:history.back()" class="btn circular"></a>
            </div>
        </main>

        <?php
    endwhile;
endif;

get_footer();
