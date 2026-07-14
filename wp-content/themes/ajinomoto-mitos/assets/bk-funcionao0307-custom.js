document.addEventListener('DOMContentLoaded', () => {

    // Variable para controlar el tiempo de la animación de salida (encogimiento)
    let timeoutSalidaFondo = null;

    // --- SISTEMA GLOBAL DE ALEATORIEDAD SIN REPETICIÓN CONSECUTIVA ---
    let fondosDisponibles = [];
    let indiceFondoActual = 0;
    let ultimoIndiceAsignado = -1;

    // Inicializamos el Swiper de fondo en modo Fade para que no se mueva de lado
    const bgSwiperInstancia = new Swiper('.bgSwiper', {
        effect: 'fade',
        fadeEffect: { crossFade: true },
        slidesPerView: 1,
        allowTouchMove: false,
        speed: 1200 // Sincronizado con la velocidad de tus textos
    });

    function generarOrdenAleatorioFondos(totalSlides) {
        let array = Array.from(Array(totalSlides).keys());
        for (let i = array.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [array[i], array[j]] = [array[j], array[i]];
        }
        return array;
    }

    function cambiarSlideFondoAleatorio() {
        if (!bgSwiperInstancia) return;
        
        const totalFondos = bgSwiperInstancia.slides.length;
        if (totalFondos <= 1) return;
    
        // 1. Si la cola no existe, está vacía, o ya procesamos todos sus elementos
        if (!fondosDisponibles || fondosDisponibles.length === 0 || indiceFondoActual >= fondosDisponibles.length) {
            let nuevoOrden;
            let intentos = 0; // Guardavías para evitar bucles infinitos
    
            do {
                nuevoOrden = generarOrdenAleatorioFondos(totalFondos);
                intentos++;
            } while (nuevoOrden[0] === ultimoIndiceAsignado && totalFondos > 1 && intentos < 10);
    
            fondosDisponibles = nuevoOrden;
            indiceFondoActual = 0; // Reiniciamos el puntero al inicio de la nueva cola limpia
        }
    
        // 2. Extraemos el siguiente fondo de la cola asegurada
        const siguienteFondo = fondosDisponibles[indiceFondoActual];
        
        // 3. Actualizamos los rastreadores ANTES de mover el slider
        ultimoIndiceAsignado = siguienteFondo;
        indiceFondoActual++;
    
        // 4. Cambiamos el Swiper de fondo
        bgSwiperInstancia.slideTo(siguienteFondo, 0);
    }

    // 1. CONTROL DE ANIMACIONES ORIGINALES (TOTALMENTE INTACTO)
    const handleCascadaLoop = {
        init: (swiper) => {
            gsap.set('.cBlanco', { scale: 0, opacity: 0, transformOrigin: "center center" });
            gsap.set('.bgSwiper', { scale: 0, opacity: 0 });
            
            setTimeout(() => {
                const activeSlide = swiper.slides[swiper.activeIndex];
                if (activeSlide) {
                    activeSlide.classList.add('animar-elementos');
                }
                // Cambiamos al primer fondo aleatorio antes de que explote el contenedor
                cambiarSlideFondoAleatorio();
                entradaExplosivaFondo(swiper);
            }, 300);
        },
    
        slideChangeTransitionStart: (swiper) => {
            swiper.slides.forEach(slide => slide.classList.remove('animar-elementos'));
            
            clearTimeout(timeoutSalidaFondo);
            salidaEscondidoFondo();
        },

        slideChangeTransitionEnd: (swiper) => {
            const activeSlide = swiper.slides[swiper.activeIndex];
            if (activeSlide) {
                activeSlide.classList.add('animar-elementos');
            }
            // Cambiamos el fondo internamente justo antes de que vuelva a explotar la escala
            cambiarSlideFondoAleatorio();
            entradaExplosivaFondo(swiper);
        }
    };

    // 2. CONFIGURACIÓN COMPARTIDA DE LOS CARRUSELES DE TEXTO
    const commonConfig = {
        slidesPerView: 1,
        spaceBetween: 80, 
        loop: true,          
        speed: 800, 
        autoplay: {
            delay: 4500,    
            disableOnInteraction: true, // <- CAMBIAR A TRUE
            pauseOnMouseEnter: false // <- ASEGURAR EN FALSE (ya que lo controlamos nosotros por código)
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        on: handleCascadaLoop 
    };

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
        salidaEscondidoFondo();

        setTimeout(() => {
            constructorSlider(tipoDestino, selectorDestino);
        }, 500); 
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

    // Control de pestañas manual (Tabs)
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
            }, 500); 
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


    // --- TU LOGICA DE ANIMACIÓN ELÁSTICA ORIGINAL EXACTA AL 100% ---

// --- TU LOGICA DE ANIMACIÓN ELÁSTICA OPTIMIZADA ---

function entradaExplosivaFondo(swiperInstancia) {
    // Tu animación original se mantiene intacta en estilo, pero reducida a 1.2s para que sea más ágil
    gsap.fromTo('.bgSwiper', 
        { scale: 0, opacity: 0, transformOrigin: "center center" },
        { scale: 1, opacity: 1, duration: 1.2, ease: "elastic.out(1, 0.5)", overwrite: "auto" }
    );

    let targetBlanco = '.swiper-slide-active .cBlanco';
    if (swiperInstancia && swiperInstancia.slides) {
        const slideActual = swiperInstancia.slides[swiperInstancia.activeIndex];
        if (slideActual) {
            const cBlancoInterno = slideActual.querySelector('.cBlanco');
            if (cBlancoInterno) targetBlanco = cBlancoInterno;
        }
    }

    gsap.fromTo(targetBlanco,
        { scale: 0, opacity: 0, transformOrigin: "center center" },
        {
            scale: 1,
            opacity: 1,
            duration: 1.2,
            delay: 0.1,
            ease: "elastic.out(1, 0.5)",
            overwrite: "auto"
        }
    );

    // Ajustamos este timeout: si el autoplay dura 4500ms y la animación de salida toma 500ms,
    // debemos empezar a encoger exactamente a los 4000ms. Así evitamos el "tiempo muerto".
    timeoutSalidaFondo = setTimeout(() => {
        salidaEscondidoFondo();
    }, 4000); 
}

function salidaEscondidoFondo() {
    // Animación de salida un poco más rápida (0.4s) para mejorar el ritmo de transición
    gsap.to('.bgSwiper', {
        scale: 0,
        opacity: 0,
        duration: 0.4,
        ease: "back.in(1.5)",
        transformOrigin: "center center",
        overwrite: "auto"
    });

    gsap.to('.cBlanco', {
        scale: 0,
        opacity: 0,
        duration: 0.4,
        delay: 0.05,
        ease: "back.in(1.5)",
        transformOrigin: "center center",
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

// --- RESTO DE EFECTOS INTERACTIVOS GSAP INTACTOS ---
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

const bgImages = document.querySelectorAll('.bgSwiper img, .bgSwiper svg');
if (hero && bgImages.length) {
    hero.addEventListener('mousemove', (e) => {
        const rect = hero.getBoundingClientRect();
        const x = ((e.clientX - rect.left) / rect.width - 0.5);
        const y = ((e.clientY - rect.top) / rect.height - 0.5);
        bgImages.forEach(img => {
            gsap.to(img, { x: x * 20, y: y * 20, duration: 0.3, overwrite: "auto" });
        });
    });

    hero.addEventListener('mouseleave', () => {
        bgImages.forEach(img => {
            gsap.to(img, { x: 0, y: 0, duration: 0.5, ease: "power3.out", overwrite: "auto" });
        });
    });
}

document.querySelectorAll('.buscador').forEach(buscador => {

    const icono = buscador.querySelector('i');
    const input = buscador.querySelector('input');

    icono.addEventListener('click', () => {
        buscador.classList.toggle('activo');

        if (buscador.classList.contains('activo')) {
            setTimeout(() => input.focus(), 300);
        }
    });

    input.addEventListener('blur', () => {
        if (input.value.trim() === '') {
            buscador.classList.remove('activo');
        }
    });

});


const input = document.getElementById("buscar");
input.addEventListener("keydown", function(event) {
    if (event.key === "Enter") {
        const texto = input.value;
        window.location.href = "resultados.html?buscar=" + encodeURIComponent(texto);
    }
});