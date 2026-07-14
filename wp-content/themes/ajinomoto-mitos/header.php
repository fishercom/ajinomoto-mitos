<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/img/favicon.ico">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?> x-data="{ open: false, modal: false }">

    <header>
        <div class="contenido">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                <img src="<?php echo get_template_directory_uri(); ?>/img/logo.svg" class="logo" alt="<?php bloginfo( 'name' ); ?>">
            </a>

            <nav class="main-menu" :class="{ 'is-active': open }" aria-label="Menú principal">
                <?php
                // Obtener el menú dinámico "primary"
                $locations = get_nav_menu_locations();
                $menu_items = array();
                if ( isset( $locations['primary'] ) ) {
                    $menu = wp_get_nav_menu_object( $locations['primary'] );
                    if ( $menu ) {
                        $menu_items = wp_get_nav_menu_items( $menu->term_id );
                    }
                }

                if ( ! empty( $menu_items ) ) {
                    foreach ( $menu_items as $item ) {
                        // Comprobar si el item es el actual
                        $is_current = ( $item->url == get_permalink() || ( is_front_page() && $item->url == home_url( '/' ) ) );
                        $active_class = $is_current ? 'class="active"' : '';
                        echo '<a href="' . esc_url( $item->url ) . '" ' . $active_class . '><div>' . esc_html( $item->title ) . '</div></a>';
                    }
                } else {
                    // Fallback estático
                    $current_url = home_url( add_query_arg( array(), $wp->request ) ) . '/';
                    $home_url = home_url( '/' );
                    
                    $is_home = is_front_page() || ( $current_url == $home_url ) || ( $current_url == $home_url . 'index.html' );
                    $is_informacion = is_page( 'informacion' ) || strpos( $_SERVER['REQUEST_URI'], 'informacion' ) !== false;
                    $is_cocina = is_page( 'mitos-cocina' ) || strpos( $_SERVER['REQUEST_URI'], 'mitos-cocina' ) !== false;
                    $is_cuentanos = is_page( 'cuentanos' ) || strpos( $_SERVER['REQUEST_URI'], 'cuentanos' ) !== false;
                    ?>
                    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" <?php echo $is_home ? 'class="active"' : ''; ?>><div>Mitos del GMS</div></a>
                    <a href="<?php echo esc_url( home_url( '/informacion/' ) ); ?>" <?php echo $is_informacion ? 'class="active"' : ''; ?>><div>Más información sobre GMS</div></a>
                    <a href="<?php echo esc_url( home_url( '/mitos-cocina/' ) ); ?>" <?php echo $is_cocina ? 'class="active"' : ''; ?>><div>Mitos de la cocina</div></a>
                    <a href="<?php echo esc_url( home_url( '/cuentanos/' ) ); ?>" <?php echo $is_cuentanos ? 'class="active"' : ''; ?>><div>Cuéntanos tu mito</div></a>
                    <?php
                }
                ?>
                <form role="search" method="get" class="buscador" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                    <input id="buscar" type="search" name="s" placeholder="¿Qué estás buscando?" value="<?php echo get_search_query(); ?>">
                    <button type="submit" style="background:none; border:none; padding:0; color:inherit; cursor:pointer;">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </nav>

            <button id="bmenu" @click="open = !open" :class="{ 'open': open }" aria-label="Abrir menú de navegación">
                <span></span><span></span><span></span>
            </button>
        </div>
    </header>
