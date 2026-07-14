<?php
/**
 * Template Name: Detalle de Información GMS (Hija)
 *
 * @package Ajinomoto_Mitos
 */

get_header();

if ( have_posts() ) :
    while ( have_posts() ) : the_post();
        $post_id = get_the_ID();
        $parent_id = wp_get_post_parent_id( $post_id );
        
        $featured_url = get_the_post_thumbnail_url( $post_id, 'large' );
        if ( empty( $featured_url ) ) {
            $featured_url = get_template_directory_uri() . '/img/informacion/detalle-que-es.jpg';
        }
        
        $child_slug = get_post_field( 'post_name', $post_id );
        $slug_icons = array(
            'que-es'       => array( 'ic01.svg', 'ic02.svg', 'ic03.svg' ),
            'historia'     => array( 'ic04.svg', 'ic05.svg', 'ic06.svg' ),
            'como-se-hace' => array( 'ic07.svg', 'ic08.svg', 'ic09.svg' ),
            'seguridad'    => array( 'ic04.svg', 'ic11.svg', 'ic12.svg' ),
            'como-se-usa'  => array( 'ic04.svg', 'ic01.svg', 'ic12.svg' ),
        );
        ?>

        <main class="interna">
            <div class="contenido">
                <div class="intro">
                    <h1><?php echo esc_html( get_the_title( $parent_id ) ); ?></h1>
                    <p><?php echo esc_html( get_the_excerpt( $parent_id ) ); ?></p>
                </div>
                
                <div class="informacion-detalle">
                    <div class="blk textura">                    
                        <figure>
                            <img src="<?php echo esc_url( $featured_url ); ?>" alt="<?php the_title_attribute(); ?>" class="foto">
                        </figure>
                        <div class="bloque">
                            <div class="c1">
                                <div class="icono">
                                    <?php
                                    if ( isset( $slug_icons[$child_slug] ) ) {
                                        foreach ( $slug_icons[$child_slug] as $icon ) {
                                            echo '<img src="' . esc_url( get_template_directory_uri() . '/img/iconos/' . $icon ) . '" alt="icon">';
                                        }
                                    }
                                    ?>
                                </div>                            
                            </div>
                            <div class="c2">
                                <div class="descripcion">
                                    <h2><?php the_title(); ?></h2>
                                    <?php the_content(); ?>
                                </div>
                            </div>
                        </div>
                        <a href="javascript:history.back()" class="btn circular pIzq"></a>
                    </div>

                    <!-- Listado de páginas nietas (Sub-artículos) -->
                    <div class="informacion">
                        <?php
                        $grandchild_args = array(
                            'post_type'      => 'page',
                            'posts_per_page' => -1,
                            'post_parent'    => $post_id,
                            'orderby'        => 'menu_order',
                            'order'          => 'ASC',
                        );
                        $grandchild_query = new WP_Query( $grandchild_args );
                        
                        if ( $grandchild_query->have_posts() ) :
                            while ( $grandchild_query->have_posts() ) : $grandchild_query->the_post();
                                $grandchild_photo = get_the_post_thumbnail_url( get_the_ID(), 'medium_large' );
                                if ( empty( $grandchild_photo ) ) {
                                    $grandchild_photo = get_template_directory_uri() . '/img/informacion/umami01.jpg';
                                }
                                ?>
                                <div class="item">                    
                                    <div class="caja">                    
                                        <div class="top">
                                            <h4><?php the_title(); ?></h4>
                                        </div>
                                        <div class="foto">
                                            <img src="<?php echo esc_url( $grandchild_photo ); ?>" alt="<?php the_title_attribute(); ?>">
                                        </div>
                                        <div class="descripcion">
                                            <?php if ( has_excerpt() ) : ?>
                                                <p><?php echo esc_html( get_the_excerpt() ); ?></p>
                                            <?php else : ?>
                                                <p><?php echo wp_trim_words( get_the_content(), 40, '...' ); ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <a href="<?php the_permalink(); ?>" class="btn circular"></a>
                                    </div>
                                </div>
                                <?php
                            endwhile;
                            wp_reset_postdata();
                        endif;
                        ?>
                    </div>
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
