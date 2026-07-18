document.addEventListener('DOMContentLoaded', () => {

    let timeoutSalidaFondo = null;
    let autoplayTimeout = null; 

    // --- SISTEMA GLOBAL DE ALEATORIEDAD SIN REPETICIÓN CONSECUTIVA ---
    let fondosDisponibles = [];
    let indiceFondoActual = 0;
    let ultimoIndiceAsignado = -1;

    const bgSwiperInstancia = new Swiper('.bgSwiper', {
        effect: 'fade',
        fadeEffect: { crossFade: true },
        slidesPerView: 1,
        allowTouchMove: false,
        speed: 800 
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

        if (!fondosDisponibles || fondosDisponibles.length === 0 || indiceFondoActual >= fondosDisponibles.length) {
            let nuevoOrden;
            let intentos = 0;
            do {
                nuevoOrden = generarOrdenAleatorioFondos(totalFondos);
                intentos++;
            } while (nuevoOrden[0] === ultimoIndiceAsignado && totalFondos > 1 && intentos < 10);

            fondosDisponibles = nuevoOrden;
            indiceFondoActual = 0;
        }

        const siguienteFondo = fondosDisponibles[indiceFondoActual];
        ultimoIndiceAsignado = siguienteFondo;
        indiceFondoActual++;
        bgSwiperInstancia.slideTo(siguienteFondo, 0);
    }

    // ============================================================
    // SLIDERS DE TEXTO POR PESTAÑA
    // ============================================================

    const tabsOrden = [
        { id: 1, tipo: 'seguridad', selector: '.seguridad' },
        { id: 2, tipo: 'evidencia', selector: '.evidencia' },
        { id: 3, tipo: 'origen', selector: '.origen' },
        { id: 4, tipo: 'factores', selector: '.factores' },
        { id: 5, tipo: 'nutricion', selector: '.nutricion' }
    ].filter(tab => document.querySelector(tab.selector) !== null);

    let sliderActivo = null;
    let tabActivoIndex = 0;

    function construirSliderActivo(tabInfo) {
        if (sliderActivo) {
            sliderActivo.destroy(true, true);
            sliderActivo = null;
        }

        sliderActivo = new Swiper(tabInfo.selector, {
            slidesPerView: 1,
            spaceBetween: 80,
            loop: false, 
            speed: 600, 
            autoplay: false, 
            navigation: false, 
            pagination: {
                el: '.swiper-pagination',
                clickable: true
            },
            on: {
                init: (swiper) => {
                    gsap.killTweensOf('.cBlanco, .bgSwiper');
                    gsap.set('.cBlanco, .bgSwiper', { scale: 0, opacity: 0, transformOrigin: "center center" });

                    setTimeout(() => {
                        const activeSlide = swiper.slides[swiper.activeIndex];
                        if (activeSlide) activeSlide.classList.add('animar-elementos');
                        cambiarSlideFondoAleatorio();
                        entradaExplosivaFondo(swiper);
                        programarSiguienteAutoplay(swiper); 
                    }, 150); 
                }
            }
        });

        adjuntarControlesManuales(tabInfo, sliderActivo);
    }

    function programarSiguienteAutoplay(swiper) {
        clearTimeout(autoplayTimeout);
        autoplayTimeout = setTimeout(() => {
            // Check if modal is open on Home page
            const homeEl = document.querySelector('.home');
            if (homeEl && homeEl._x_dataStack && homeEl._x_dataStack[0].modal) {
                // Modal is open, do not transition, just reschedule check
                programarSiguienteAutoplay(swiper);
                return;
            }

            clearTimeout(timeoutSalidaFondo);
            salidaEscondidoFondo();

            // Aumentado a 600ms para dar espacio a que el fondo y la bola terminen su coreografía suave
            setTimeout(() => {
                if (swiper.isEnd) {
                    if (tabsOrden.length > 0) {
                        const siguienteIndex = (tabActivoIndex + 1) % tabsOrden.length;
                        cambiarTab(siguienteIndex);
                    }
                } else {
                    swiper.slides.forEach(slide => slide.classList.remove('animar-elementos'));
                    swiper.slideNext(600); 
                    
                    setTimeout(() => {
                        const activeSlide = swiper.slides[swiper.activeIndex];
                        if (activeSlide) activeSlide.classList.add('animar-elementos');
                        cambiarSlideFondoAleatorio();
                        entradaExplosivaFondo(swiper);
                        programarSiguienteAutoplay(swiper);
                    }, 620); 
                }
            }, 600);
        }, 4500);
    }

    function adjuntarControlesManuales(tabInfo, swiper) {
        const btnNext = document.querySelector(`.swiper-button-next.${tabInfo.tipo.substring(0, 3)}`);
        const btnPrev = document.querySelector(`.swiper-button-prev.${tabInfo.tipo.substring(0, 3)}`);

        if (btnNext) {
            const nuevoBtnNext = btnNext.cloneNode(true);
            btnNext.parentNode.replaceChild(nuevoBtnNext, btnNext);
            
            nuevoBtnNext.addEventListener('click', () => {
                clearTimeout(autoplayTimeout);
                clearTimeout(timeoutSalidaFondo);
                salidaEscondidoFondo();

                setTimeout(() => {
                    if (swiper.isEnd) {
                        const BrandonSiguienteIndex = (tabActivoIndex + 1) % tabsOrden.length;
                        cambiarTab(BrandonSiguienteIndex);
                    } else {
                        swiper.slides.forEach(slide => slide.classList.remove('animar-elementos'));
                        swiper.slideNext(600);
                        setTimeout(() => {
                            const activeSlide = swiper.slides[swiper.activeIndex];
                            if (activeSlide) activeSlide.classList.add('animar-elementos');
                            cambiarSlideFondoAleatorio();
                            entradaExplosivaFondo(swiper);
                            programarSiguienteAutoplay(swiper);
                        }, 620);
                    }
                }, 600);
            });
        }

        if (btnPrev) {
            const nuevoBtnPrev = btnPrev.cloneNode(true);
            btnPrev.parentNode.replaceChild(nuevoBtnPrev, btnPrev);

            nuevoBtnPrev.addEventListener('click', () => {
                clearTimeout(autoplayTimeout);
                clearTimeout(timeoutSalidaFondo);
                salidaEscondidoFondo();

                setTimeout(() => {
                    if (swiper.isBeginning) {
                        const anteriorIndex = (tabActivoIndex - 1 + tabsOrden.length) % tabsOrden.length;
                        cambiarTab(anteriorIndex);
                    } else {
                        swiper.slides.forEach(slide => slide.classList.remove('animar-elementos'));
                        swiper.slidePrev(600);
                        setTimeout(() => {
                            const activeSlide = swiper.slides[swiper.activeIndex];
                            if (activeSlide) activeSlide.classList.add('animar-elementos');
                            cambiarSlideFondoAleatorio();
                            entradaExplosivaFondo(swiper);
                            programarSiguienteAutoplay(swiper);
                        }, 620);
                    }
                }, 600);
            });
        }
    }

    function cambiarTab(index) {
        clearTimeout(autoplayTimeout);
        clearTimeout(timeoutSalidaFondo);
        
        tabActivoIndex = index;
        const tabInfo = tabsOrden[index];

        const homeEl = document.querySelector('.home');
        if (homeEl && homeEl._x_dataStack) {
            homeEl._x_dataStack[0].tab = tabInfo.id;
        }

        moverIndicadorPestana(tabInfo.id);
        salidaEscondidoFondo();

        setTimeout(() => {
            construirSliderActivo(tabInfo);
        }, 650); // Ajustado al nuevo tiempo de salida total
    }

    // Control de pestañas manual (Tabs)
    const tabButtons = document.querySelectorAll('.tabs > div');
    const esHome = document.querySelector('.home') !== null;

    tabButtons.forEach((button, index) => {
        button.addEventListener('click', () => {
            if (esHome) {
                if (index === tabActivoIndex) return; 
                cambiarTab(index);
            } else {
                moverIndicadorPestana(index + 1);
            }
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

        //pestañaActiva.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
    }

    // Arranque inicial
    setTimeout(() => {
        if (esHome) {
            if (tabsOrden.length > 0) {
                moverIndicadorPestana(tabsOrden[0].id);
                construirSliderActivo(tabsOrden[0]);
            }
        } else {
            // En interna, al inicio, posicionar el indicador en el primer tab activo
            const activeTab = document.querySelector('.tabs > div.active');
            if (activeTab) {
                const index = Array.from(tabButtons).indexOf(activeTab);
                if (index !== -1) {
                    moverIndicadorPestana(index + 1);
                }
            } else {
                moverIndicadorPestana(1);
            }
        }
    }, 250);


    // --- LÓGICA DE ANIMACIÓN ELÁSTICA ---

    function entradaExplosivaFondo(swiperInstancia) {
        gsap.killTweensOf('.bgSwiper');
        gsap.fromTo('.bgSwiper',
            { scale: 0, opacity: 0, transformOrigin: "center center" },
            { scale: 1, opacity: 1, duration: 1.0, ease: "elastic.out(1, 0.6)", overwrite: "auto" }
        );

        let targetBlanco = '.swiper-slide-active .cBlanco';
        if (swiperInstancia && swiperInstancia.slides) {
            const slideActual = swiperInstancia.slides[swiperInstancia.activeIndex];
            if (slideActual) {
                const cBlancoInterno = slideActual.querySelector('.cBlanco');
                if (cBlancoInterno) targetBlanco = cBlancoInterno;
            }
        }

        gsap.killTweensOf(targetBlanco);
        gsap.fromTo(targetBlanco,
            { scale: 0, opacity: 0, transformOrigin: "center center" },
            {
                scale: 1,
                opacity: 1,
                duration: 1.0,
                delay: 0.15, 
                ease: "elastic.out(1, 0.6)",
                overwrite: "auto"
            }
        );
    }

    // AJUSTADO: Salida más lenta del fondo y delay optimizado para la bola blanca
    function salidaEscondidoFondo() {
        gsap.killTweensOf('.bgSwiper, .cBlanco');
        
        // El fondo ahora se desvanece de manera más elegante (0.5 segundos) y suave (power2.in)
        gsap.to('.bgSwiper', {
            scale: 0,
            opacity: 0,
            duration: 0.5, // Suavizado y ralentizado
            ease: "power2.in",
            transformOrigin: "center center",
            overwrite: "auto"
        });

        // La bola espera a que el fondo avance en su animación (0.35s) y luego se encoje con fuerza
        gsap.to('.cBlanco', {
            scale: 0,
            opacity: 0,
            duration: 0.2, 
            delay: 0.35,   // Sincronizado para que la bola se vaya justo después
            ease: "back.in(1.2)",
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
    const button = buscador.querySelector('button');

    // Manejar el click en el disparador (botón/icono)
    const trigger = button || icono;
    if (trigger) {
        trigger.addEventListener('click', (e) => {
            if (!buscador.classList.contains('activo')) {
                // Si no está activo, prevenimos el envío de formulario y lo activamos
                e.preventDefault();
                buscador.classList.add('activo');
                setTimeout(() => input.focus(), 100);
            } else {
                // Si está activo
                const texto = input.value.trim();
                if (texto === '') {
                    // Si está vacío, contrae el buscador y previene el envío
                    e.preventDefault();
                    buscador.classList.remove('activo');
                } else {
                    // Si tiene texto, se realiza el submit normal del formulario
                    // Si no hay un elemento form (maqueta estática), hacemos la redirección manual
                    const form = buscador.closest('form');
                    if (!form) {
                        e.preventDefault();
                        window.location.href = "resultados.html?buscar=" + encodeURIComponent(texto);
                    }
                }
            }
        });
    }

    // Colapsar si se pierde el foco y está vacío
    input.addEventListener('blur', () => {
        setTimeout(() => {
            if (input.value.trim() === '') {
                buscador.classList.remove('activo');
            }
        }, 200);
    });

    // Soporte para tecla Enter
    input.addEventListener("keydown", function(event) {
        if (event.key === "Enter") {
            const texto = input.value.trim();
            if (texto === '') {
                event.preventDefault();
                buscador.classList.remove('activo');
            } else {
                const form = buscador.closest('form');
                if (!form) {
                    event.preventDefault();
                    window.location.href = "resultados.html?buscar=" + encodeURIComponent(texto);
                }
            }
        }
    });
});