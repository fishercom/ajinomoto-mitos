<?php
/**
 * Plantilla de Portada (Home)
 *
 * @package Ajinomoto_Mitos
 */

get_header();

// Configuración de categorías centralizada
$categories_slugs = array(
    1 => 'seguridad',
    2 => 'evidencia',
    3 => 'origen',
    4 => 'factores'
);
$categories_names = array(
    1 => 'Seguridad e impacto en la salud',
    2 => 'Evidencia científica',
    3 => 'Origen',
    4 => 'Factores nutricionales'
);
$categories_classes = array(
    1 => 'seguridad',
    2 => 'evidencia',
    3 => 'origen',
    4 => 'factores'
);
$categories_button_classes = array(
    1 => 'seg',
    2 => 'evi',
    3 => 'ori',
    4 => 'fac'
);
?>
    <main x-data="{ tab: 1, modal: false, modalTitle: '', modalMito: '', modalRealidad: '', modalLink: '', modalRevisorName: '', modalRevisorCargo: '', modalRevisorImg: '', modalImg: '' }" class="home">
         <div class="swiper bgSwiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide">                    
<?php get_template_part( 'template-parts/svg-hero-gms' ); ?>
                </div>
                <div class="swiper-slide">                    
                    <?php get_template_part( 'template-parts/svg-hero-cocina' ); ?>                       
                </div>
                <div class="swiper-slide">                    
                    <?php get_template_part( 'template-parts/svg-hero-origen' ); ?>                  
                </div>
            </div>
        </div> 
        <h1>Los mitos del GMS</h1>
        <div id="hero" class="hero">
            <div class="content-tabs">
                <?php
                foreach ( $categories_slugs as $tab_num => $slug ) :
                    $args = array(
                        'post_type'      => 'mitos',
                        'posts_per_page' => -1,
                        'orderby'        => 'menu_order',
                        'order'          => 'ASC',
                        'tax_query'      => array(
                            'relation' => 'AND',
                            array(
                                'taxonomy' => 'tipo_mito',
                                'field'    => 'slug',
                                'terms'    => 'gms',
                            ),
                            array(
                                'taxonomy' => 'categoria_mito',
                                'field'    => 'slug',
                                'terms'    => $slug,
                            ),
                        ),
                    );
                    $query = new WP_Query( $args );
                ?>
                <div x-show="tab === <?php echo $tab_num; ?>">
                    <div class="<?php echo $categories_classes[$tab_num]; ?> swiper">
                        <div class="swiper-wrapper">
                            <?php if ( $query->have_posts() ) : ?>
                                <?php while ( $query->have_posts() ) : $query->the_post(); 
                                    $realidad_corta = get_post_meta( get_the_ID(), 'realidad_corta', true );
                                    $revisor_id = get_post_meta( get_the_ID(), 'revisor', true );
                                    $revisor_name = ''; $revisor_cargo = ''; $revisor_img = '';
                                    if ( $revisor_id ) {
                                        $revisor_name = get_the_title( $revisor_id );
                                        $revisor_cargo = get_post_meta( $revisor_id, 'cargo_revisor', true );
                                        $revisor_img_id = get_post_thumbnail_id( $revisor_id );
                                        if ( $revisor_img_id ) {
                                            $revisor_img = wp_get_attachment_image_url( $revisor_img_id, 'thumbnail' );
                                        }
                                    }
                                    if ( empty( $revisor_img ) ) {
                                        $revisor_img = get_template_directory_uri() . '/img/personal/silueta.png';
                                    }
                                    $featured_img_id = get_post_thumbnail_id();
                                    $featured_img_url = '';
                                    if ( $featured_img_id ) {
                                        $featured_img_url = wp_get_attachment_image_url( $featured_img_id, 'large' );
                                    }
                                    if ( empty( $featured_img_url ) ) {
                                        $featured_img_url = get_template_directory_uri() . '/img/informacion/mito-cocina.jpg';
                                    }
                                ?>
                                    <div class="swiper-slide">
                                        <div class="item">
                                            <a href="javascript:;" 
                                               @click="modal = true; 
                                                       modalTitle = '<?php echo esc_js( $categories_names[$tab_num] ); ?>'; 
                                                       modalMito = '<?php echo esc_js( get_the_title() ); ?>'; 
                                                       modalRealidad = '<?php echo esc_js( $realidad_corta ); ?>'; 
                                                       modalLink = '<?php echo esc_url( get_permalink() ); ?>';
                                                       modalRevisorName = '<?php echo esc_js( $revisor_name ); ?>';
                                                       modalRevisorCargo = '<?php echo esc_js( $revisor_cargo ); ?>';
                                                       modalRevisorImg = '<?php echo esc_url( $revisor_img ); ?>';
                                                       modalImg = '<?php echo esc_url( $featured_img_url ); ?>';" 
                                               class="full"></a>
                                            <div class="cBlanco">
                                                <div class="texto">
                                                    <h2><?php the_title(); ?></h2>
                                                    <a href="javascript:;" 
                                                       @click="modal = true; 
                                                               modalTitle = '<?php echo esc_js( $categories_names[$tab_num] ); ?>'; 
                                                               modalMito = '<?php echo esc_js( get_the_title() ); ?>'; 
                                                               modalRealidad = '<?php echo esc_js( $realidad_corta ); ?>'; 
                                                               modalLink = '<?php echo esc_url( get_permalink() ); ?>';
                                                               modalRevisorName = '<?php echo esc_js( $revisor_name ); ?>';
                                                               modalRevisorCargo = '<?php echo esc_js( $revisor_cargo ); ?>';
                                                               modalRevisorImg = '<?php echo esc_url( $revisor_img ); ?>';
                                                               modalImg = '<?php echo esc_url( $featured_img_url ); ?>';" 
                                                       class="boton">Descúbrelo aquí<img src="<?php echo get_template_directory_uri(); ?>/img/ic-ir.svg" alt="ir"></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else : ?>
                                <div class="swiper-slide">
                                    <div class="item">
                                        <div class="cBlanco">
                                            <div class="texto">
                                                <h2>Próximamente más mitos</h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; wp_reset_postdata(); ?>
                        </div>
                        <div class="swiper-button-prev <?php echo $categories_button_classes[$tab_num]; ?>"></div>
                        <div class="swiper-button-next <?php echo $categories_button_classes[$tab_num]; ?>"></div>
                        <div class="pagination-container">
                            <div class="swiper-pagination-indicator"></div>
                            <div class="swiper-pagination"></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="tabs-container" style="position: relative;">
            <div class="tab-indicator"></div>
            <div class="tabs">
                <?php foreach ( $categories_names as $index => $name ) : ?>
                    <div @click="tab=<?php echo $index; ?>" :class="{ 'active': tab === <?php echo $index; ?> }" data-tab="<?php echo $index; ?>"><?php echo esc_html( $name ); ?></div>
                <?php endforeach; ?>
            </div>
        </div>
        <br>
        <div class="contenido">
            <div class="mitos">
                <div class="informacion">
                    <?php
                    // Consultar todos los mitos de GMS
                    $grid_args = array(
                        'post_type'      => 'mitos',
                        'posts_per_page' => -1,
                        'tax_query'      => array(
                            array(
                                'taxonomy' => 'tipo_mito',
                                'field'    => 'slug',
                                'terms'    => 'gms',
                            ),
                        ),
                    );
                    $grid_query = new WP_Query( $grid_args );
                    if ( $grid_query->have_posts() ) :
                        while ( $grid_query->have_posts() ) : $grid_query->the_post();
                            // Obtener categoría para mapear a la pestaña Alpine (1 a 4)
                            $terms = get_the_terms( get_the_ID(), 'categoria_mito' );
                            $tab_index = 1;
                            $cat_name = 'Seguridad e impacto en la salud';
                            if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                                $term_slug = $terms[0]->slug;
                                $cat_name = $terms[0]->name;
                                $key = array_search( $term_slug, $categories_slugs );
                                if ( $key !== false ) {
                                    $tab_index = $key;
                                }
                            }
                            
                            $realidad_corta = get_post_meta( get_the_ID(), 'realidad_corta', true );
                            
                            // Datos del revisor
                            $revisor_id = get_post_meta( get_the_ID(), 'revisor', true );
                            $revisor_name = ''; $revisor_cargo = ''; $revisor_img = '';
                            if ( $revisor_id ) {
                                $revisor_name = get_the_title( $revisor_id );
                                $revisor_cargo = get_post_meta( $revisor_id, 'cargo_revisor', true );
                                $revisor_img_id = get_post_thumbnail_id( $revisor_id );
                                if ( $revisor_img_id ) {
                                    $revisor_img = wp_get_attachment_image_url( $revisor_img_id, 'thumbnail' );
                                }
                            }
                            if ( empty( $revisor_img ) ) {
                                $revisor_img = get_template_directory_uri() . '/img/personal/silueta.png';
                            }
                            
                            $featured_img_id = get_post_thumbnail_id();
                            $featured_img_url = '';
                            if ( $featured_img_id ) {
                                $featured_img_url = wp_get_attachment_image_url( $featured_img_id, 'large' );
                            }
                            if ( empty( $featured_img_url ) ) {
                                $featured_img_url = get_template_directory_uri() . '/img/informacion/mito-cocina.jpg';
                            }
                            ?>
                            <div class="item" x-show="tab === <?php echo $tab_index; ?>">                    
                                <div class="caja">                    
                                    <div class="top">
                                        <h4><?php echo esc_html( $cat_name ); ?></h4>
                                    </div>                            
                                    <div class="descripcion">
                                        <h5>MITO:</h5>
                                        <p><?php the_title(); ?></p>
                                    </div>
                                    <a href="javascript:;" 
                                       @click="modal = true; 
                                               modalTitle = '<?php echo esc_js( $cat_name ); ?>'; 
                                               modalMito = '<?php echo esc_js( get_the_title() ); ?>'; 
                                               modalRealidad = '<?php echo esc_js( $realidad_corta ); ?>'; 
                                               modalLink = '<?php echo esc_url( get_permalink() ); ?>';
                                               modalRevisorName = '<?php echo esc_js( $revisor_name ); ?>';
                                               modalRevisorCargo = '<?php echo esc_js( $revisor_cargo ); ?>';
                                               modalRevisorImg = '<?php echo esc_url( $revisor_img ); ?>';
                                               modalImg = '<?php echo esc_url( $featured_img_url ); ?>';" 
                                       class="btn circular"></a>
                                </div>
                            </div>
                            <?php
                        endwhile;
                        wp_reset_postdata();
                    else :
                        ?>
                        <p>No hay mitos cargados de momento.</p>
                    <?php
                    endif;
                    ?>
                </div>
            </div>
        </div>

        <div x-cloak x-show="modal" x-transition.opacity.duration.500ms @keydown.esc.window="modal = false"
            @click.self="modal = true;" class="modal" role="dialog" aria-modal="true"
            aria-labelledby="defaultModalTitle">
            <!-- Modal Dialog -->
            <div x-show="modal"
                x-transition:enter="transition ease-out duration-200 delay-100 motion-reduce:transition-opacity"
                x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-100"
                class="content-modal">
                <div class="imgCategoriaMito">
                    <img :src="modalImg" alt="">
                </div>
                <div class="top-modal home">
                    <h5 x-text="modalTitle"></h5>
                </div>
                <div class="descripcion">
                    <h4>MITO:</h4>
                    <p x-text="modalMito"></p>
                    <h4>REALIDAD:</h4>
                    <p x-text="modalRealidad"></p>
                    <a :href="modalLink" class="btn">Lee el artículo completo</a>
                    <div class="personal" x-show="modalRevisorName">
                        <img :src="modalRevisorImg" alt="">
                        <div class="datos">
                            <h5>Revisado por: </h5>
                            <p><strong x-text="modalRevisorName"></strong></p>
                            <p x-text="modalRevisorCargo"></p>
                        </div>
                    </div>
                </div>
                <div class="bot-modal">
                    <div class="slider-botones">
                        <a href="javascript:;" class="btn circular close" @click="modal = false;"></a>
                    </div>
                </div>
            </div>
        </div>        
    </main>
<?php
get_footer();
?>