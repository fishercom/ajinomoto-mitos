document.addEventListener("DOMContentLoaded", function () {
  
    const mainItems = document.querySelectorAll(".main-menu a");
    let activeItem = document.querySelector(".main-menu a.active");

    function setActive(item) {
        mainItems.forEach(li => li.classList.remove("active"));
        item.classList.add("active");
        activeItem = item;
    }


  const listSeguridad = document.querySelector(".seguridad");
  if (listSeguridad) {
    const swiperSeguridad = new Swiper(listSeguridad, {
      loop: false,    
      effect: 'fade',
      fadeEffect: {
        crossFade: true
      },
      slidesPerView: 1,
      autoplay: {
        delay: 4000,
        disableOnInteraction: false,
      },
      navigation: { nextEl: ".swiper-button-next.seg", prevEl: ".swiper-button-prev.seg"},
      scrollbar: {
        el: ".swiper-scrollbar",
        draggable: true,
      },
      pagination: {
        el: listSeguridad.querySelector('.swiper-pagination'),
        clickable: true,
      },  
    });
  }

  const listEvidencia = document.querySelector(".evidencia");
  if (listEvidencia) {
    const swiperEvidencia = new Swiper(listEvidencia, {
      loop: true,    
      slidesPerView: 1,
      autoplay: {
        delay: 4000,
        disableOnInteraction: false,
      },
      navigation: { nextEl: ".swiper-button-next.evi", prevEl: ".swiper-button-prev.evi"},
      scrollbar: {
        el: ".swiper-scrollbar",
        draggable: true,
      },
      pagination: {
        el: listEvidencia.querySelector('.swiper-pagination'),
        clickable: true,
      },  
    });
  }  

  const listOrigen = document.querySelector(".origen");
  if (listOrigen) {
    const swiperOrigen = new Swiper(listOrigen, {
      loop: true,    
      slidesPerView: 1,
      autoplay: {
        delay: 4000,
        disableOnInteraction: false,
      },
      navigation: { nextEl: ".swiper-button-next.ori", prevEl: ".swiper-button-prev.ori"},
      scrollbar: {
        el: ".swiper-scrollbar",
        draggable: true,
      },
      pagination: {
        el: listOrigen.querySelector('.swiper-pagination'),
        clickable: true,
      },  
    });
  }  

  const listFactores = document.querySelector(".factores");
  if (listFactores) {
    const swiperFactores = new Swiper(listFactores, {
      loop: true,    
      slidesPerView: 1,
      autoplay: {
        delay: 4000,
        disableOnInteraction: false,
      },
      navigation: { nextEl: ".swiper-button-next.fac", prevEl: ".swiper-button-prev.fac"},
      scrollbar: {
        el: ".swiper-scrollbar",
        draggable: true,
      },
      pagination: {
        el: listFactores.querySelector('.swiper-pagination'),
        clickable: true,
      },  
    });
  } 


  const bgSwiper = new Swiper('.bgSwiper', {
    effect: 'fade',
    fadeEffect: {
        crossFade: true,
    },
    loop: true,
    speed: 8000, // transición de 8 segundos
    autoplay: {
        delay: 3000,
        disableOnInteraction: false,
    },
    allowTouchMove: false,
  });

  
});

(function($) {
  "use strict";   

  $('#bmenu').on('click', function() {
    $(this).toggleClass('open');
    $('.bTop').toggleClass('open');
    $('body').toggleClass('no-scroll');
  });


})(jQuery);

gsap.registerPlugin(ScrollTrigger);

// ANIMACIÓN GENERAL DE SECCIONES

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
gsap.to(".btDown a", {
  y: 15,
  duration: 1.2,
  ease: "power1.inOut",
  repeat: -1,
  yoyo: true
});

const t2 = gsap.timeline({ delay: 0.3 }); // pequeño delay elegante

t2.from(".banner .mascarainf", {
  y: 120,
  opacity: 0,
  duration: 1.8,
  ease: "expo.out"
})

// .from(".banner.interna .texto", {
//   y: 40,
//   opacity: 0,
//   duration: .8,
//   ease: "power3.out"
// }, "+=0.2");
