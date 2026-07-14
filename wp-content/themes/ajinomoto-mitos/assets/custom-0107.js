document.addEventListener('DOMContentLoaded', () => {

    const handleCascadaLoop = {
        init: (swiper) => {
            setTimeout(() => {
                const index = swiper.realIndex;
                const activeSlides = swiper.el.querySelectorAll(
                    `[data-swiper-slide-index="${index}"]`
                );
                activeSlides.forEach(s => s.classList.add('animar-elementos'));
            }, 300);
        },
    
        slideChangeTransitionStart: (swiper) => {
            swiper.slides.forEach(slide => slide.classList.remove('animar-elementos'));
        
            setTimeout(() => {
                const currentIndex = swiper.realIndex;
                const slidesToAnimate = swiper.el.querySelectorAll(
                    `[data-swiper-slide-index="${currentIndex}"]`
                );
                slidesToAnimate.forEach(slide => slide.classList.add('animar-elementos'));
            }, 300);
        }
    };

    // 2. CONFIGURACIÓN COMPARTIDA
    const commonConfig = {
        slidesPerView: 1,
        spaceBetween: 80, 
        loop: true,         
        speed: 1200,      
        autoplay: {
            delay: 4500,  
            disableOnInteraction: false, 
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        on: handleCascadaLoop 
    };

    // Objeto global para controlar las referencias de los sliders
    const sliders = {
        seguridad: null,
        evidencia: null,
        origen: null,
        factores: null
    };

    // Función constructora limpia
    function constructorSlider(tipo, selector) {
        // Si ya existe una instancia previa del slider, la destruimos por completo
        if (sliders[tipo]) {
            sliders[tipo].destroy(true, true);
        }

        // Creamos la nueva instancia desde cero absoluto
        sliders[tipo] = new Swiper(selector, {
            ...commonConfig,
            navigation: { 
                nextEl: `.swiper-button-next.${tipo.substring(0,3)}`, 
                prevEl: `.swiper-button-prev.${tipo.substring(0,3)}` 
            },
        });

        // Volvemos a asignar el listener al nuevo objeto creado
        adjuntarListenerCambio(tipo, sliders[tipo]);
    }

    // Inicialización inicial ordenada
    constructorSlider('seguridad', '.seguridad');
    constructorSlider('evidencia', '.evidencia');
    constructorSlider('origen', '.origen');
    constructorSlider('factores', '.factores');

    // --- FUNCIÓN CAMBIAR TAB OPTIMIZADA ---
    function cambiarTab(tabDestino, tipoDestino, selectorDestino) {
        // 1. Cambiamos el estado en Alpine.js
        const homeEl = document.querySelector('.home');
        if (homeEl && homeEl._x_dataStack) {
            homeEl._x_dataStack[0].tab = tabDestino;
        }

        // ¡AQUÍ!: Mueve el indicador visual suavemente hacia la pestaña destino
        moverIndicadorPestana(tabDestino);

        // 2. Sincronizamos con el repintado del DOM (doble frame)
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                // Ahora que la pestaña destino está 100% visible en pantalla,
                // destruimos el slider residual y lo montamos de cero para evitar bloqueos
                constructorSlider(tipoDestino, selectorDestino);
            });
        });
    }

    // --- ASIGNACIÓN DINÁMICA DE EVENTOS ---
    function adjuntarListenerCambio(tipo, swiperInstancia) {
        let completado = false;
        
        // Identificar dinámicamente cuál es el último slide real según la categoría
        // (Ajustar el número si el slider de seguridad tiene un número diferente de slides)
        const limiteSlide = (tipo === 'seguridad') ? 1 : 2;

        swiperInstancia.on('slideChange', function () {
            if (this.realIndex === limiteSlide) {
                completado = true;
                return;
            }
            
            if (completado && this.realIndex === 0) {
                completado = false;

                // Apagamos el autoplay del que está muriendo para mitigar fugas de memoria
                if (this.autoplay) this.autoplay.stop();

                // Cambios secuenciales pausados
                if (tipo === 'seguridad') cambiarTab(2, 'evidencia', '.evidencia');
                if (tipo === 'evidencia') cambiarTab(3, 'origen', '.origen');
                if (tipo === 'origen')    cambiarTab(4, 'factores', '.factores');
                if (tipo === 'factores')  cambiarTab(1, 'seguridad', '.seguridad'); 
            }
        });
    }

    // 5. Control de pestañas manual (Hacer click en los tabs)
    const tabButtons = document.querySelectorAll('.tabs > div');
    tabButtons.forEach((button, index) => {
        button.addEventListener('click', () => {
            // El index empieza en 0, sumamos 1 para obtener la pestaña correspondiente (1, 2, 3, 4)
            const tabId = index + 1;

            // ¡AQUÍ!: Desplazamos de inmediato la barra/pastilla indicadora flotante
            moverIndicadorPestana(tabId);

            const mapeo = [
                { llave: 'seguridad', selector: '.seguridad' },
                { llave: 'evidencia', selector: '.evidencia' },
                { llave: 'origen',    selector: '.origen' },
                { llave: 'factores',  selector: '.factores' }
            ];

            // PASO 1: Antes del timeout, preparamos los sliders visualmente de forma instantánea
            mapeo.forEach((item) => {
                const swiperInstancia = sliders[item.llave];
                if (swiperInstancia) {
                    // Quitamos las clases de animación inmediatamente para que no parpadeen al salir
                    swiperInstancia.slides.forEach(slide => slide.classList.remove('animar-elementos'));
                    // Lo movemos al inicio con 0ms de velocidad (invisible para el usuario)
                    swiperInstancia.slideToLoop(0, 0);
                }
            });

            // PASO 2: Esperamos a que Alpine juegue con la opacidad/despliegue del contenedor
            setTimeout(() => {
                mapeo.forEach((item) => {
                    const wrapperTab = sliders[item.llave]?.el.closest('[x-show]');
                    
                    if (wrapperTab && wrapperTab.style.display !== 'none') {
                        // Recreamos el slider activo de forma limpia ahora que es 100% visible
                        constructorSlider(item.llave, item.selector);
                    } else {
                        // Si no está activo, aseguramos que su autoplay esté muerto en segundo plano
                        if (sliders[item.llave] && sliders[item.llave].autoplay) {
                            sliders[item.llave].autoplay.stop();
                        }
                    }
                });
            }, 350); // Mantenemos el delay sincronizado con los tiempos de Alpine
        });
    });

    function moverIndicadorPestana(tabId) {
        const contenedor = document.querySelector('.tabs');
        const pestañaActiva = document.querySelector(`.tabs > div:nth-child(${tabId})`);
        const indicador = document.querySelector('.tab-indicator');
        
        if (!contenedor || !pestañaActiva || !indicador) return;
    
        // 1. Calculamos las posiciones relativas a la pantalla
        const contenedorRect = contenedor.getBoundingClientRect();
        const pestañaRect = pestañaActiva.getBoundingClientRect();
        
        /* ¡LA CLAVE PARA MÓVILES!: 
           Sumamos 'contenedor.scrollLeft' para compensar el arrastre del dedo.
        */
        const desplazamientoX = pestañaRect.left - contenedorRect.left + contenedor.scrollLeft;
        
        // Si tus pestañas están perfectamente alineadas arriba, el desfase Y suele ser constante
        const desplazamientoY = pestañaRect.top - contenedorRect.top;
    
        // 2. Seteamos las dimensiones exactas del botón activo
        indicador.style.width = `${pestañaRect.width}px`;
        indicador.style.height = `${pestañaRect.height}px`;
        
        // 3. Movemos el indicador a su coordenada real con scroll incluido
        indicador.style.transform = `translate(${desplazamientoX}px, ${desplazamientoY}px)`;
        indicador.style.opacity = "1";
    
        // 4. Activamos la animación si no estaba puesta
        if (!indicador.classList.contains('efecto-activo')) {
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    indicador.classList.add('efecto-activo');
                });
            });
        }
    
        // 5. Desplazamos el scroll del contenedor automáticamente para no perder de vista la pestaña
        pestañaActiva.scrollIntoView({
            behavior: 'smooth',
            block: 'nearest',
            inline: 'center' // Centra la pestaña activa en la pantalla del móvil
        });
    }
    
    // Inicializar la posición en la primera carga
    setTimeout(() => moverIndicadorPestana(1), 350);

    // Fondo Swiper invariable
    const bgSwiper = new Swiper('.bgSwiper', {
        effect: 'fade',
        fadeEffect: { crossFade: true },
        loop: true,
        speed: 6000, 
        autoplay: { delay: 3000, disableOnInteraction: false },
        allowTouchMove: false,
    });

    // Fondo Swiper invariable
    const mitoSwiper = new Swiper('.mitoSwiper', {
        fadeEffect: { crossFade: true },
        loop: true,
        speed: 1000, 
        autoplay: false,
        allowTouchMove: false,
        navigation: { 
            nextEl: '.slider-botones .btn.circular.next', 
            prevEl: '.slider-botones .btn.circular.prev' 
        },
    });
});

// --- EL RESTO DE TUS EFECTOS GSAP Y MOUSEMOVE SE MANTIENEN IGUAL ---
gsap.registerPlugin(ScrollTrigger);
gsap.utils.toArray("section").forEach((section) => {
    const contenido = section.querySelectorAll(".contenido");
    if (!contenido.length) return;
    gsap.from(contenido, { 
        opacity: 0, y: 50, duration: 0.8, stagger: 0.2,
        scrollTrigger: {
            trigger: section, start: "top 80%", toggleActions: "play none none none",
        },
    });  
});

// --- CORRECCIÓN DEL EFECTO PARALLAX (MOUSEMOVE) ---

let dentroHero = false;
const hero = document.querySelector('.hero');

if (hero) {
    hero.addEventListener('mouseenter', () => { 
        dentroHero = true; 
    });

    hero.addEventListener('mouseleave', () => {
        dentroHero = false;
        // Buscamos dinámicamente el texto activo actual
        const activeText = document.querySelector('.swiper-slide-active .cBlanco');
        if (!activeText) return;
        
        // Lo devolvemos a su posición original
        gsap.to(activeText, { 
            x: 0, 
            y: 0, 
            duration: 0.5, 
            ease: "power3.out" 
        });
    });
}

window.addEventListener('mousemove', (e) => {
    if (!dentroHero) return;

    // CORRECCIÓN CLAVE: Buscamos el elemento .cBlanco dentro del slide visible real de Swiper (.swiper-slide-active)
    // Ya no dependemos de 'animar-elementos' que puede tardar 300ms en ponerse
    const activeText = document.querySelector('.swiper-slide-active .cBlanco');
    if (!activeText) return;

    const x = (e.clientX / window.innerWidth - 0.5) * 40;
    const y = (e.clientY / window.innerHeight - 0.5) * 40;

    gsap.to(activeText, {
        x: x,
        y: y,
        duration: 0.2,
        overwrite: "auto" // Evita conflictos con la animación de regreso
    });
});

// El efecto de las imágenes de fondo se queda igual pero con una pequeña validación
const bgImages = document.querySelectorAll('.bgSwiper img');
if (hero && bgImages.length) {
    hero.addEventListener('mousemove', (e) => {
        const rect = hero.getBoundingClientRect();
        const x = ((e.clientX - rect.left) / rect.width - 0.5);
        const y = ((e.clientY - rect.top) / rect.height - 0.5);
        bgImages.forEach(img => {
            gsap.to(img, { x: x * 15, y: y * 15, duration: 1, ease: "power2.out" });
        });
    });

    hero.addEventListener('mouseleave', () => {
        bgImages.forEach(img => {
            gsap.to(img, { x: 0, y: 0, duration: 1.2, ease: "power3.out" });
        });
    });
}