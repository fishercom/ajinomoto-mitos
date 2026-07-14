document.addEventListener('DOMContentLoaded', () => {

    // Variable para controlar el tiempo de la animación de salida (encogimiento)
    let timeoutSalidaFondo = null;

    // 1. CONTROL DE ANIMACIONES (CASCADA Y REINICIO DE FONDO) - CORREGIDO SELECTOR LOOP
    const handleCascadaLoop = {
        init: (swiper) => {
            setTimeout(() => {
                // Captura con total certeza el slide real o clonado visible en este instante
                const activeSlide = swiper.slides[swiper.activeIndex];
                if (activeSlide) {
                    activeSlide.classList.add('animar-elementos');
                }
                
                // Entrada explosiva desde 0 al cargar
                entradaExplosivaFondo();
            }, 300);
        },
    
        slideChangeTransitionStart: (swiper) => {
            // Removemos de forma masiva la clase en todos los slides para resetear la bola y SVGs
            swiper.slides.forEach(slide => slide.classList.remove('animar-elementos'));
        
            setTimeout(() => {
                // Identificamos el slide activo exacto en la transición
                const activeSlide = swiper.slides[swiper.activeIndex];
                if (activeSlide) {
                    activeSlide.classList.add('animar-elementos');
                }
                
                // Cada vez que inicia una pestaña, entra desde cero
                entradaExplosivaFondo();
            }, 400);
        }
    };

    // 2. CONFIGURACIÓN COMPARTIDA
    const commonConfig = {
        slidesPerView: 1,
        spaceBetween: 80, 
        loop: true,         
        speed: 1500,        
        autoplay: {
            delay: 7000,    // 7 segundos por sección
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

    function constructorSlider(tipo, selector) {
        if (sliders[tipo]) {
            sliders[tipo].destroy(true, true);
        }

        sliders[tipo] = new Swiper(selector, {
            ...commonConfig,
            navigation: { 
                nextEl: `.swiper-button-next.${tipo.substring(0,3)}`, 
                prevEl: `.swiper-button-prev.${tipo.substring(0,3)}` 
            },
        });

        adjuntarListenerCambio(tipo, sliders[tipo]);
    }

    // Inicialización
    constructorSlider('seguridad', '.seguridad');
    constructorSlider('evidencia', '.evidencia');
    constructorSlider('origen', '.origen');
    constructorSlider('factores', '.factores');

    function cambiarTab(tabDestino, tipoDestino, selectorDestino) {
        const homeEl = document.querySelector('.home');
        if (homeEl && homeEl._x_dataStack) {
            homeEl._x_dataStack[0].tab = tabDestino;
        }

        moverIndicadorPestana(tabDestino);

        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                constructorSlider(tipoDestino, selectorDestino);
            });
        });
    }

    function adjuntarListenerCambio(tipo, swiperInstancia) {
        let completado = false;
        const limiteSlide = (tipo === 'seguridad') ? 1 : 2;

        swiperInstancia.on('slideChange', function () {
            if (this.realIndex === limiteSlide) {
                completado = true;
                return;
            }
            
            if (completado && this.realIndex === 0) {
                completado = false;

                if (this.autoplay) this.autoplay.stop();

                if (tipo === 'seguridad') cambiarTab(2, 'evidencia', '.evidencia');
                if (tipo === 'evidencia') cambiarTab(3, 'origen', '.origen');
                if (tipo === 'origen')    cambiarTab(4, 'factores', '.factores');
                if (tipo === 'factores')  cambiarTab(1, 'seguridad', '.seguridad'); 
            }
        });
    }

    // Control de pestañas manual (Hacer click)
    const tabButtons = document.querySelectorAll('.tabs > div');
    tabButtons.forEach((button, index) => {
        button.addEventListener('click', () => {
            const tabId = index + 1;
            moverIndicadorPestana(tabId);

            const mapeo = [
                { llave: 'seguridad', selector: '.seguridad' },
                { llave: 'evidencia', selector: '.evidencia' },
                { llave: 'origen',    selector: '.origen' },
                { llave: 'factores',  selector: '.factores' }
            ];

            mapeo.forEach((item) => {
                const swiperInstancia = sliders[item.llave];
                if (swiperInstancia) {
                    swiperInstancia.slides.forEach(slide => slide.classList.remove('animar-elementos'));
                    swiperInstancia.slideToLoop(0, 0);
                }
            });

            // Si el usuario hace click manual, forzamos al fondo a esconderse de inmediato
            salidaEscondidoFondo();

            setTimeout(() => {
                mapeo.forEach((item) => {
                    const wrapperTab = sliders[item.llave]?.el.closest('[x-show]');
                    if (wrapperTab && wrapperTab.style.display !== 'none') {
                        constructorSlider(item.llave, item.selector);
                    } else {
                        if (sliders[item.llave] && sliders[item.llave].autoplay) {
                            sliders[item.llave].autoplay.stop();
                        }
                    }
                });
            }, 450); 
        });
    });

    function moverIndicadorPestana(tabId) {
        const contenedor = document.querySelector('.tabs');
        const pestañaActiva = document.querySelector(`.tabs > div:nth-child(${tabId})`);
        const indicador = document.querySelector('.tab-indicator');
        
        if (!contenedor || !pestañaActiva || !indicador) return;
    
        const contenedorRect = contenedor.getBoundingClientRect();
        const pestañaRect = pestañaActiva.getBoundingClientRect();
        const desplazamientoX = pestañaRect.left - contenedorRect.left + contenedor.scrollLeft;
        const desplazamientoY = pestañaRect.top - contenedorRect.top;
    
        indicador.style.width = `${pestañaRect.width}px`;
        indicador.style.height = `${pestañaRect.height}px`;
        indicador.style.transform = `translate(${desplazamientoX}px, ${desplazamientoY}px)`;
        indicador.style.opacity = "1";
    
        if (!indicador.classList.contains('efecto-activo')) {
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    indicador.classList.add('efecto-activo');
                });
            });
        }
    
        pestañaActiva.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
    }
    
    setTimeout(() => moverIndicadorPestana(1), 350);

    // Fondo Swiper
    const bgSwiper = new Swiper('.bgSwiper', {
        effect: 'fade',
        fadeEffect: { crossFade: true },
        loop: true,
        speed: 1500,         
        autoplay: { 
            delay: 2000,     
            disableOnInteraction: false 
        },
        allowTouchMove: false,
    });

    // --- FUNCIONES DE ENTRADA (POP) Y SALIDA (ESCONDER) ---

    function entradaExplosivaFondo() {
        if (!bgSwiper) return;

        // Limpiamos cualquier temporizador de salida previo
        clearTimeout(timeoutSalidaFondo);

        bgSwiper.slideToLoop(0, 0);
        bgSwiper.autoplay.stop();
        bgSwiper.autoplay.start();

        // ANIMACIÓN DE ENTRADA
        gsap.fromTo('.bgSwiper', 
            { scale: 0, opacity: 0 }, 
            { 
                scale: 1, 
                opacity: 1, 
                duration: 2.2, 
                ease: "elastic.out(1, 0.85)", 
                overwrite: "auto" 
            }
        );

        // PROGRAMAMOS LA SALIDA: A los 6 segundos (6000ms)
        timeoutSalidaFondo = setTimeout(() => {
            salidaEscondidoFondo();
        }, 6000);
    }

    function salidaEscondidoFondo() {
        // ANIMACIÓN DE SALIDA
        gsap.to('.bgSwiper', {
            scale: 0,
            opacity: 0,
            duration: 0.8,
            ease: "back.in(1.5)", 
            overwrite: "auto"
        });
    }

    // Fondo Mitos
    const mitoSwiper = new Swiper('.mitoSwiper', {
        fadeEffect: { crossFade: true },
        loop: true,
        speed: 1200, 
        autoplay: false,
        allowTouchMove: false,
        navigation: { 
            nextEl: '.slider-botones .btn.circular.next', 
            prevEl: '.slider-botones .btn.circular.prev' 
        },
    });
});

// --- RESTO DE EFECTOS GSAP ---
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

let dentroHero = false;
const hero = document.querySelector('.hero');

if (hero) {
    hero.addEventListener('mouseenter', () => { dentroHero = true; });
    hero.addEventListener('mouseleave', () => {
        dentroHero = false;
        const activeText = document.querySelector('.swiper-slide-active .cBlanco');
        if (!activeText) return;
        gsap.to(activeText, { x: 0, y: 0, duration: 0.5, ease: "power3.out" });
    });
}

window.addEventListener('mousemove', (e) => {
    if (!dentroHero) return;
    const activeText = document.querySelector('.swiper-slide-active .cBlanco');
    if (!activeText) return;
    const x = (e.clientX / window.innerWidth - 0.5) * 40;
    const y = (e.clientY / window.innerHeight - 0.5) * 40;
    gsap.to(activeText, { x: x, y: y, duration: 0.2, overwrite: "auto" });
});

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