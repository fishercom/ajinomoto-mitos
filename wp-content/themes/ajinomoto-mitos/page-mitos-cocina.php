<?php
/**
 * Template Name: Mitos de Cocina
 *
 * @package Ajinomoto_Mitos
 */

get_header();

// Consultar todos los mitos de cocina
$cocina_args = array(
    'post_type'      => 'mitos',
    'posts_per_page' => -1,
    'orderby'        => 'menu_order',
    'order'          => 'ASC',
    'tax_query'      => array(
        array(
            'taxonomy' => 'tipo_mito',
            'field'    => 'slug',
            'terms'    => 'cocina',
        ),
    ),
);
$cocina_query = new WP_Query( $cocina_args );
$mitos_list = array();
if ( $cocina_query->have_posts() ) {
    while ( $cocina_query->have_posts() ) {
        $cocina_query->the_post();
        $terms = get_the_terms( get_the_ID(), 'categoria_mito' );
        $cat_slug = 'tecnicas';
        $cat_name = 'Técnicas de cocina';
        if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
            $cat_slug = $terms[0]->slug;
            $cat_name = $terms[0]->name;
        }
        
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
            $revisor_img = get_template_directory_uri() . '/img/personal/chica.png';
        }

        $subtitulo = get_post_meta( get_the_ID(), 'subtitulo_mito', true );
        if ( empty( $subtitulo ) ) {
            $subtitulo = get_the_title();
        }

        $mitos_list[] = array(
            'id'             => get_the_ID(),
            'title'          => $subtitulo,
            'cat_slug'       => $cat_slug,
            'cat_name'       => $cat_name,
            'realidad_corta' => $realidad_corta,
            'revisor_name'   => $revisor_name,
            'revisor_cargo'  => $revisor_cargo,
            'revisor_img'    => $revisor_img,
            'permalink'      => get_permalink(),
        );
    }
    wp_reset_postdata();
}
?>
    <main class="interna" x-data="{ tab: 'todos', modal: false }">
        <div class="contenido">
            <div class="intro">
                <h1>Mitos de la cocina</h1>
                <p>Creencias populares que se repiten en la cocina de generación en generación. ¿Cuántas son ciertas?
                    Descúbrelo con ciencia.</p>
            </div>
            <div class="tabs-container" style="position: relative;">
                <div class="tab-indicator"></div>
                <div class="tabs">
                    <div @click="tab='todos'" :class="{ 'active': tab === 'todos' }">Todos</div>
                    <div @click="tab='tecnicas'" :class="{ 'active': tab === 'tecnicas' }">Técnicas de cocina</div>
                    <div @click="tab='salud'" :class="{ 'active': tab === 'salud' }">Salud en la Mesa</div>
                    <div @click="tab='productos'" :class="{ 'active': tab === 'productos' }">Productos y Conservación
                    </div>
                    <div @click="tab='nutricion'" :class="{ 'active': tab === 'nutricion' }">Nutrición, Cuerpo y Dietas
                    </div>
                </div>
            </div>

            <div class="mitos">
                <div class="informacion">
                    <?php if ( ! empty( $mitos_list ) ) : ?>
                        <?php foreach ( $mitos_list as $index => $mito ) : ?>
                        <div class="item" x-show="tab === 'todos' || tab === '<?php echo esc_attr( $mito['cat_slug'] ); ?>'">
                            <div class="caja">
                                <div class="top">
                                    <h4><?php echo esc_html( $mito['cat_name'] ); ?></h4>
                                </div>
                                <div class="descripcion">
                                    <h5>MITO:</h5>
                                    <p><?php echo esc_html( $mito['title'] ); ?></p>
                                </div>
                                <a href="javascript:;" 
                                   @click="modal = true; $nextTick(() => { const sw = document.querySelector('.mitoSwiper'); if (sw && sw.swiper) { sw.swiper.slideToLoop(<?php echo $index; ?>, 0); } });" 
                                   class="btn circular"></a>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <p>No hay mitos de cocina registrados.</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="imgOsito">
                <img src="<?php echo get_template_directory_uri(); ?>/img/osito-bt-cocina.svg" alt="ositos">
            </div>
            <a href="javascript:history.back()" class="btn circular"></a>
        <div x-cloak x-show="modal" x-transition.opacity.duration.500ms @keydown.esc.window="modal = false"
            @click.self="modal = true;" class="modal" role="dialog" aria-modal="true"
            aria-labelledby="defaultModalTitle">
            <!-- Modal Dialog -->
            <div x-show="modal"
                x-transition:enter="transition ease-out duration-200 delay-100 motion-reduce:transition-opacity"
                x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-100"
                class="content-modal">
                <div class="imgCategoriaMito">
                    <img src="<?php echo get_template_directory_uri(); ?>/img/informacion/mito-cocina.jpg" alt="">
                </div>
                <div class="top-modal">
                    <h5>Mitos de la cocina</h5>
                </div>
                <div class="descripcion">
                    <div class="swiper mitoSwiper">
                        <div class="swiper-wrapper">
                            <?php if ( ! empty( $mitos_list ) ) : ?>
                                <?php foreach ( $mitos_list as $mito ) : ?>
                                <div class="swiper-slide">
                                    <h4>MITO:</h4>
                                    <p><?php echo esc_html( $mito['title'] ); ?></p>
                                    <h4>REALIDAD:</h4>
                                    <p><?php echo esc_html( $mito['realidad_corta'] ); ?></p>
                                    <a href="<?php echo esc_url( $mito['permalink'] ); ?>" class="btn">Lee el artículo completo</a>
                                    <?php if ( ! empty( $mito['revisor_name'] ) ) : ?>
                                    <div class="personal">
                                        <img src="<?php echo esc_url( $mito['revisor_img'] ); ?>" alt="<?php echo esc_attr( $mito['revisor_name'] ); ?>">
                                        <div class="datos">
                                            <h5>Revisado por: </h5>
                                            <p><strong><?php echo esc_html( $mito['revisor_name'] ); ?></strong></p>
                                            <p><?php echo esc_html( $mito['revisor_cargo'] ); ?></p>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="bot-modal">
                    <div class="slider-botones">
                        <div class="btn circular prev"></div>
                        <div class="btn circular next"></div>
                        <a href="javascript:;" class="btn circular close" @click="modal = false;"></a>
                    </div>
                </div>
            </div>
        </div>
</div>
        </div>

    </main>
<?php
get_footer();
?>