<?php
/**
 * Plantilla principal de fallback.
 *
 * @package Ajinomoto_Mitos
 */

get_header();
?>

<main class="interna">
    <div class="contenido">
        <div class="bloqueBlanco" style="margin-top: 40px; padding: 40px;">
            <?php if ( have_posts() ) : ?>
                <?php while ( have_posts() ) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?> style="margin-bottom: 30px;">
                        <h2 style="color: #ff0000;"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                        <div class="entry-content" style="margin-top: 15px; line-height: 1.6; color: #666;">
                            <?php the_excerpt(); ?>
                        </div>
                    </article>
                <?php endwhile; ?>
                
                <?php the_posts_navigation(); ?>
            <?php else : ?>
                <p>No se encontraron publicaciones.</p>
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
