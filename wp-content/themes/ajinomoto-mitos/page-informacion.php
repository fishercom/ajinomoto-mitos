<?php
/**
 * Template Name: Más Información GMS Index
 *
 * @package Ajinomoto_Mitos
 */

get_header();

if ( have_posts() ) :
    while ( have_posts() ) : the_post();
        $post_id = get_the_ID();
        ?>

        <main class="interna">
            <div class="contenido">
                <div class="intro">
                    <h1><?php the_title(); ?></h1>
                    <?php if ( has_excerpt() ) : ?>
                        <p><?php echo esc_html( get_the_excerpt() ); ?></p>
                    <?php else : ?>
                        <p><?php the_content(); ?></p>
                    <?php endif; ?>
                </div>

                <div class="informacion">
                    <?php
                    // Consultar páginas hijas de la página actual
                    $child_args = array(
                        'post_type'      => 'page',
                        'posts_per_page' => -1,
                        'post_parent'    => $post_id,
                        'orderby'        => 'menu_order',
                        'order'          => 'ASC',
                    );
                    $child_query = new WP_Query( $child_args );
                    
                    // Iconos decorativos según el slug de la página hija
                    $slug_icons = array(
                        'que-es'       => array( 'ic01.svg', 'ic02.svg', 'ic03.svg' ),
                        'historia'     => array( 'ic04.svg', 'ic05.svg', 'ic06.svg' ),
                        'como-se-hace' => array( 'ic07.svg', 'ic08.svg', 'ic09.svg' ),
                        'seguridad'    => array( 'ic04.svg', 'ic11.svg', 'ic12.svg' ),
                        'como-se-usa'  => array( 'ic04.svg', 'ic01.svg', 'ic12.svg' ),
                    );

                    if ( $child_query->have_posts() ) :
                        $index = 0;
                        while ( $child_query->have_posts() ) : $child_query->the_post();
                            $child_slug = get_post_field( 'post_name', get_the_ID() );
                            $item_class = ( $index % 2 === 0 ) ? 'ps' : 'pe';
                            
                            $photo_url = get_the_post_thumbnail_url( get_the_ID(), 'medium_large' );
                            if ( empty( $photo_url ) ) {
                                $photo_url = get_template_directory_uri() . '/img/informacion/que-es.jpg';
                            }
                            ?>
                            <div class="item <?php echo esc_attr( $item_class ); ?>">                    
                                <div class="caja">                    
                                    <div class="top">
                                        <h4><?php the_title(); ?></h4>
                                        <div class="iconos">
                                            <?php
                                            // Renderizar iconos según slug
                                            if ( isset( $slug_icons[$child_slug] ) ) {
                                                foreach ( $slug_icons[$child_slug] as $icon ) {
                                                    echo '<img src="' . esc_url( get_template_directory_uri() . '/img/iconos/' . $icon ) . '" alt="icon">';
                                                }
                                            } else {
                                                // Iconos por defecto
                                                echo '<img src="' . esc_url( get_template_directory_uri() . '/img/iconos/ic01.svg' ) . '" alt="icon">';
                                                echo '<img src="' . esc_url( get_template_directory_uri() . '/img/iconos/ic02.svg' ) . '" alt="icon">';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="foto">
                                        <img src="<?php echo esc_url( $photo_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>">
                                    </div>
                                    <div class="descripcion">
                                        <?php if ( has_excerpt() ) : ?>
                                            <p><?php echo esc_html( get_the_excerpt() ); ?></p>
                                        <?php else : ?>
                                            <p><?php echo wp_trim_words( get_the_content(), 40, '...' ); ?></p>
                                        <?php endif; ?>
                                        <a href="<?php the_permalink(); ?>" class="btn circular"></a>
                                    </div>
                                </div>
                            </div>
                            <?php
                            $index++;
                        endwhile;
                        wp_reset_postdata();
                    else :
                        ?>
                        <p>No se encontraron sub-páginas registradas.</p>
                    <?php
                    endif;
                    ?>
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
