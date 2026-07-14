<?php
/**
 * Plantilla para el pie de página.
 *
 * @package Ajinomoto_Mitos
 */

$is_home_footer = is_front_page();
$footer_class = $is_home_footer ? 'class="home"' : '';
?>

    <footer <?php echo $footer_class; ?>>
        <?php if ( $is_home_footer ) : ?>
            <div class="oso">
                <img src="<?php echo get_template_directory_uri(); ?>/img/ositos-home.svg" alt="ositos">
            </div>
        <?php endif; ?>
        
        <img src="<?php echo get_template_directory_uri(); ?>/img/footer-bg.png" alt="ositos">
        
        <div class="contenido">            
            <div class="izq">
                <?php
                // Obtener el menú del pie de página
                $locations = get_nav_menu_locations();
                $menu_items = array();
                if ( isset( $locations['footer'] ) ) {
                    $menu = wp_get_nav_menu_object( $locations['footer'] );
                    if ( $menu ) {
                        $menu_items = wp_get_nav_menu_items( $menu->term_id );
                    }
                }

                if ( ! empty( $menu_items ) ) {
                    echo '<ul class="lista">';
                    foreach ( $menu_items as $item ) {
                        echo '<li><a href="' . esc_url( $item->url ) . '">' . esc_html( $item->title ) . '</a></li>';
                    }
                    echo '</ul>';
                } else {
                    ?>
                    <ul class="lista">
                        <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Los mitos del GMS</a></li>
                        <li><a href="<?php echo esc_url( home_url( '/informacion/' ) ); ?>">Más información GMS</a></li>
                        <li><a href="<?php echo esc_url( home_url( '/mitos-cocina/' ) ); ?>">Mitos de la cocina</a></li>
                        <li><a href="<?php echo esc_url( home_url( '/cuentanos/' ) ); ?>">Cuéntanos tu mito</a></li>
                    </ul>
                    <?php
                }
                ?>
                
                <ul class="redes">
                    <li><a href="https://tiktok.com" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>/img/ic-tiktok.svg" alt="tiktok"></a></li>
                    <li><a href="https://facebook.com" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>/img/ic-facebook.svg" alt="facebook"></a></li>
                    <li><a href="https://instagram.com" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>/img/ic-instagram.svg" alt="instagram"></a></li>
                    <li><a href="https://youtube.com" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>/img/ic-youtube.svg" alt="youtube"></a></li>
                </ul>                
            </div>
            
            <ul class="opciones">
                <li>Creado para resolver dudas con evidencia</li>
                <li><a href="http://ajinomoto.com.pe/" target="_blank"><img src="<?php echo get_template_directory_uri(); ?>/img/logo2.svg" alt="logoajinomoto"></a></li>
            </ul>                            
        </div>
        
        <div class="derechos">
            <p>© <?php echo date('Y'); ?> Ajinomoto del Perú S.A. Todos los derechos reservados</p>
        </div>
    </footer>

    <?php wp_footer(); ?>
</body>
</html>
