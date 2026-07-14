document.addEventListener('DOMContentLoaded', () => {

  const handleCascadaLoop = {
    init: (swiper) => {
        // Un retraso muy corto (300ms) para permitir que Swiper se monte, 
        // e inmediatamente disparamos la cascada larga desde la derecha
        setTimeout(() => {
            const index = swiper.realIndex;
            const activeSlides = swiper.el.querySelectorAll(`[data-swiper-slide-index="${index}"]`);
            activeSlides.forEach(s => s.classList.add('animar-elementos'));
        }, 300); // <-- Bajado de 600ms a 300ms para atrapar el movimiento a tiempo
    },
    slideChangeTransitionStart: (swiper) => {
        swiper.slides.forEach(slide => slide.classList.remove('animar-elementos'));
        
        const currentIndex = swiper.realIndex;
        const slidesToAnimate = swiper.el.querySelectorAll(`[data-swiper-slide-index="${currentIndex}"]`);
        slidesToAnimate.forEach(slide => {
            slide.classList.add('animar-elementos');
        });
    }
};
  // 2. CONFIGURACIÓN COMPARTIDA CON AUTOPLAY Y LOOP
  const commonConfig = {
    slidesPerView: 1,
    spaceBetween: 80, // Aumentamos el espacio para que la tarjeta vieja tenga más pista donde deslizarse al salir
    loop: true,         
    speed: 1600,      /* <-- Subimos a 1600ms para que la salida y la entrada sean más pausadas */
    autoplay: {
        delay: 4500,  // Añadimos un poco más de tiempo de lectura (4.5 segundos)
        disableOnInteraction: false, 
    },
    pagination: {
        el: '.swiper-pagination',
        clickable: true,
    },
    on: handleCascadaLoop 
};

  // 3. Inicializar los 4 sliders de los mitos
  const sliderSeguridad = new Swiper('.seguridad', {
      ...commonConfig,
      navigation: { nextEl: '.swiper-button-next.seg', prevEl: '.swiper-button-prev.seg' },
  });

  const sliderEvidencia = new Swiper('.evidencia', {
      ...commonConfig,
      navigation: { nextEl: '.swiper-button-next.evi', prevEl: '.swiper-button-prev.evi' },
  });

  const sliderOrigen = new Swiper('.origen', {
      ...commonConfig,
      navigation: { nextEl: '.swiper-button-next.ori', prevEl: '.swiper-button-prev.ori' },
  });

  const sliderFactores = new Swiper('.factores', {
      ...commonConfig,
      navigation: { nextEl: '.swiper-button-next.fac', prevEl: '.swiper-button-prev.fac' },
  });

  const bgSwiper = new Swiper('.bgSwiper', {
    effect: 'fade',
    fadeEffect: {
        crossFade: true,
    },
    loop: true,
    speed: 8000, // transiciÃ³n de 8 segundos
    autoplay: {
        delay: 3000,
        disableOnInteraction: false,
    },
    allowTouchMove: false,
  });

  // 5. Control de pestañas Alpine.js
  const tabButtons = document.querySelectorAll('.tabs > div');
  tabButtons.forEach(button => {
      button.addEventListener('click', () => {
          // Subimos a 300ms para esperar que Alpine ejecute el x-show y pinte el bloque oculto
          setTimeout(() => {
              const sliders = [sliderSeguridad, sliderEvidencia, sliderOrigen, sliderFactores];
              
              sliders.forEach(s => {
                  s.update();
                  const wrapperTab = s.el.closest('[x-show]');
                  if (wrapperTab && wrapperTab.style.display !== 'none') {
                      s.autoplay.start();
                      s.slides.forEach(slide => slide.classList.remove('animar-elementos'));
                      
                      // Activamos la cascada una vez que la pestaña ya es 100% visible
                      const index = s.realIndex;
                      const targetSlides = s.el.querySelectorAll(`[data-swiper-slide-index="${index}"]`);
                      targetSlides.forEach(slide => slide.classList.add('animar-elementos'));
                  } else {
                      s.autoplay.stop();
                  }
              });
          }, 300); // <-- Antes 100ms
      });
  });
});
gsap.registerPlugin(ScrollTrigger);

// ANIMACIÓN GENERAL DE SECCIONES
gsap.utils.toArray("section").forEach((section) => {
  const contenido = section.querySelectorAll(".contenido");

  if (!contenido.length) return;
  gsap.from(contenido, { opacity: 0, y: 50, duration: 0.8, stagger: 0.2,
    scrollTrigger: {
      trigger: section, start: "top 80%", toggleActions: "play none none none",
    },
  });
  
});

let dentroHero = false;

const hero = document.querySelector('.hero');

hero.addEventListener('mouseenter', () => {
    dentroHero = true;
});

hero.addEventListener('mouseleave', () => {
    dentroHero = false;

    const activeText = document.querySelector(
        '.hero .swiper-slide.animar-elementos .texto'
    );

    if (!activeText) return;

    gsap.to(activeText, {
        x: 0,
        y: 0,
        duration: 0.5,
        ease: "power3.out"
    });
});

window.addEventListener('mousemove', (e) => {

    if (!dentroHero) return;

    const activeText = document.querySelector(
        '.hero .swiper-slide.animar-elementos .texto'
    );

    if (!activeText) return;

    const x = (e.clientX / window.innerWidth - 0.5) * 40;
    const y = (e.clientY / window.innerHeight - 0.5) * 40;

    gsap.to(activeText, {
        x,
        y,
        duration: 0.2,
        overwrite: true
    });
});

const bgImages = document.querySelectorAll('.bgSwiper img');

hero.addEventListener('mousemove', (e) => {

    const rect = hero.getBoundingClientRect();

    const x = ((e.clientX - rect.left) / rect.width - 0.5);
    const y = ((e.clientY - rect.top) / rect.height - 0.5);

    bgImages.forEach(img => {

        gsap.to(img, {
            x: x * 15,
            y: y * 15,
            duration: 1,
            ease: "power2.out"
        });

    });

});

hero.addEventListener('mouseleave', () => {

    bgImages.forEach(img => {

        gsap.to(img, {
            x: 0,
            y: 0,
            duration: 1.2,
            ease: "power3.out"
        });

    });

});