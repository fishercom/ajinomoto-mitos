<?php
/**
 * Plantilla de resultados de búsqueda (search.php).
 *
 * @package Ajinomoto_Mitos
 */

get_header();

global $wp_query;
$total_results = $wp_query->found_posts;
$posts_per_page = get_option( 'posts_per_page' );
$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
$start_result = ( ( $paged - 1 ) * $posts_per_page ) + 1;
$end_result = min( $paged * $posts_per_page, $total_results );
?>

<main class="interna">
    <div class="contenido">
        <div class="intro">
            <h1>Resultado de búsqueda</h1>            
        </div>
        
        <?php if ( have_posts() ) : ?>
            <p>Resultado <strong><?php echo $start_result; ?></strong> de <strong><?php echo $total_results; ?></strong> de "<strong><?php echo esc_html( get_search_query() ); ?></strong>"</p>
            
            <div class="resultados">
                <?php while ( have_posts() ) : the_post(); ?>
                    <div class="item">
                        <h4><?php the_title(); ?></h4>
                        <p><?php echo wp_trim_words( get_the_excerpt(), 50, '...' ); ?></p>
                        <a href="<?php the_permalink(); ?>"><?php the_permalink(); ?></a>
                    </div>
                <?php endwhile; ?>
            </div>

            <?php
            // Paginación
            $pages = paginate_links( array(
                'type'      => 'array',
                'prev_next' => false,
            ) );

            if ( is_array( $pages ) ) {
                echo '<div class="paginado">';
                foreach ( $pages as $page ) {
                    if ( strpos( $page, 'current' ) !== false ) {
                        $page = str_replace( array( '<span', '</span>', 'class="page-numbers current"' ), array( '<a', '</a>', 'class="active"' ), $page );
                    }
                    echo $page;
                }
                echo '</div>';
            }
            ?>
        <?php else : ?>
            <p>No se encontraron resultados de "<strong><?php echo esc_html( get_search_query() ); ?></strong>"</p>
            <div class="resultados">
                <div class="item" style="border-bottom: none;">
                    <p>Lo sentimos, no encontramos información o mitos relacionados con tus términos de búsqueda. Por favor, intenta de nuevo con otros términos.</p>
                </div>
            </div>
        <?php endif; ?>

        <div class="imgOsito">
            <img src="<?php echo get_template_directory_uri(); ?>/img/osito-bt-cuentanos.svg" alt="ositos">
        </div>            
    </div>
</main>

<?php
get_footer();
