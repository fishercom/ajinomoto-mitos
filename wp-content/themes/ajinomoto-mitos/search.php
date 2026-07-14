<?php
/**
 * Plantilla de resultados de búsqueda.
 *
 * @package Ajinomoto_Mitos
 */

get_header();
?>

<main class="interna">
    <div class="contenido">
        <div class="intro">
            <h1>Resultados de búsqueda</h1>
            <p>Mostrando resultados para: <strong>"<?php echo esc_html( get_search_query() ); ?>"</strong></p>
        </div>

        <div class="bloqueBlanco">
            <?php if ( have_posts() ) : ?>
                <div class="mitos" style="margin-top: 20px;">
                    <div class="informacion" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
                        <?php while ( have_posts() ) : the_post(); 
                            $post_type_label = '';
                            $p_type = get_post_type();
                            if ( $p_type === 'mitos' ) {
                                $tipo_terms = get_the_terms( get_the_ID(), 'tipo_mito' );
                                $tipo_slug = ( ! empty( $tipo_terms ) && ! is_wp_error( $tipo_terms ) ) ? $tipo_terms[0]->slug : 'cocina';
                                $post_type_label = ( $tipo_slug === 'gms' ) ? 'Mito del GMS' : 'Mito de la cocina';
                            } elseif ( $p_type === 'revisores' ) {
                                $post_type_label = 'Revisor Profesional';
                            } else {
                                $post_type_label = 'Información';
                            }
                        ?>
                            <div class="item" style="display: flex; flex-direction: column; width: 100%;">
                                <div class="caja" style="width: 100%; display: flex; flex-direction: column; justify-content: space-between; min-height: 220px;">
                                    <div class="top">
                                        <h4><?php echo esc_html( $post_type_label ); ?></h4>
                                    </div>
                                    <div class="descripcion" style="flex-grow: 1; padding: 15px 0;">
                                        <h5 style="margin: 0; color: #ff0000; font-size: 1.1em;"><?php the_title(); ?></h5>
                                        <p style="margin: 10px 0 0; font-size: 0.9em; line-height: 1.4; color: #666;">
                                            <?php echo wp_trim_words( get_the_excerpt(), 20, '...' ); ?>
                                        </p>
                                    </div>
                                    <a href="<?php the_permalink(); ?>" class="btn circular"></a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
                
                <?php
                // Paginación
                the_posts_pagination( array(
                    'prev_text' => '<i class="fas fa-chevron-left"></i> Anterior',
                    'next_text' => 'Siguiente <i class="fas fa-chevron-right"></i>',
                ) );
                ?>
            <?php else : ?>
                <div class="no-results" style="padding: 40px 20px; text-align: center;">
                    <h3 style="color: #ff0000; margin-bottom: 15px;">No se encontraron resultados</h3>
                    <p style="color: #666; font-size: 1.1em;">Lo sentimos, no encontramos información o mitos relacionados con <strong>"<?php echo esc_html( get_search_query() ); ?>"</strong>. Por favor, intenta de nuevo con otros términos de búsqueda.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="imgOsito">
            <img src="<?php echo get_template_directory_uri(); ?>/img/ositos-bt-info.svg" alt="ositos">
        </div>
        <a href="javascript:history.back()" class="btn circular"></a>
    </div>
</main>

<?php
get_footer();
