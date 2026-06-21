/*! ------------------------------------------------
 * Project Name: Rayo - Digital Agency & Personal Portfolio HTML Template
 * Project Description: Elevate your digital presence with Rayo - dynamic and stylish HTML template designed for creative agencies and personal brands. With modern layouts, smooth interactions and a polished aesthetic, Rayo template helps showcase projects, services and expertise with clarity and impact.
 * Tags: mix_design, resume, portfolio, personal page, cv, template, one page, responsive, html5, css3, creative, clean, agency, studio
 * Version: 1.0.0
 * Build Date: July 2025
 * Last Update: August 2025
 * This product is available exclusively on Themeforest
 * Author: mix_design
 * Author URI: https://themeforest.net/user/mix_design
 * File name: app.js
 * ------------------------------------------------

 * ------------------------------------------------
 * Table of Contents
 * ------------------------------------------------
 *
 * 01. Loader & Loading Animation
 * 02. Lenis Scroll Plugin
 * 03. Typed.js Plugin
 * 04. Header Scroll Behavior
 * 05. Hero #02 Scroll Out Animation
 * 06. Hero #07 Scroll Out Animation
 * 07. Hero #08 Scroll Out Animation
 * 08. SVG Fallback
 * 09. Chrome Smooth Scroll
 * 10. Images Moving Ban
 * 11. Detecting Mobile/Desktop
 * 12. Smooth Scrolling
 * 13. Menu & Hamburger
 * 14. Menu Accordion
 * 15. Layout Masonry
 * 16. Accordion
 * 17. Magnific Popup Video
 * 18. Mailchimp Subscribe Form
 * 19. Contact Form
 * 20. Parallax - Ukiyo Images & Video
 * 21. Pinned Images
 * 22. Stacking Cards
 * 23. Animation - Buttons Common
 * 24. Animation - Text Reveal
 * 25. Animation - Scroll Universal
 * 26. Animation - Images Reveal on Hover
 * 27. Swiper Slider - Testimonials #01
 * 28. Swiper Slider - Testimonials #02
 * 29. Swiper Slider - Inner Pages Demo
 * 30. CountUp - All Counters Options
 * 31. Marquee - Two Lines
 * 32. Marquee - One Line To Right
 * 33. Marquee - One Line To Left
 * 34. SVG DOM Injection
 * 35. Color Switch
 * 36. Scroll to Top Button
 * 37. Parallax Universal
 *
 * ------------------------------------------------
 * Table of Contents End
 * ------------------------------------------------ */

gsap.registerPlugin(ScrollTrigger);
gsap.registerPlugin(Flip);

// --------------------------------------------- //
// Loader & Loading Animation Start
// --------------------------------------------- //
const content = document.querySelector('body');
const imgLoad = imagesLoaded(content);
const loadingWrap = document.querySelector('.loading-wrap');
const loadingItems = loadingWrap.querySelectorAll('.loading__item');
const fadeInItems = document.querySelectorAll('.loading__fade');

function startLoader() {
  let counterElement = document.querySelector(".loader__count .count__text");
  let currentValue = 0;
  function updateCounter() {
    if (currentValue < 100) {
      let increment = Math.floor(Math.random() * 10) + 1;
      currentValue = Math.min(currentValue + increment, 100);
      counterElement.textContent = currentValue;
      let delay = Math.floor(Math.random() * 120) + 25;
      setTimeout(updateCounter, delay);
    }
  }
  updateCounter();
}
startLoader();

imgLoad.on('done', instance => {
  hideLoader();
  pageAppearance();
});

function hideLoader() {
  gsap.to(".loader__count", { duration: 0.8, ease: 'power2.in', y: "100%", delay: 1.8 });
  gsap.to(".loader__wrapper", { duration: 0.8, ease: 'power4.in', y: "-100%", delay: 2.2 });
  setTimeout(() => {
    document.getElementById("loader").classList.add("loaded");
  }, 3200);
}

function pageAppearance() {
  gsap.set(loadingItems, { opacity: 0 })
  gsap.to(loadingItems, { 
    duration: 1.1,
    ease: 'power4',
    startAt: {y: 120},
    y: 0,
    opacity: 1,
    delay: 0.8,
    stagger: 0.08
  }, '>-=1.1');
  gsap.set(fadeInItems, { opacity: 0 });
  gsap.to(fadeInItems, { duration: 0.8, ease: 'none', opacity: 1, delay: 3.2 });
}
// --------------------------------------------- //
// Loader & Loading Animation End
// --------------------------------------------- //

// --------------------------------------------- //
// Lenis Scroll Plugin Start
// --------------------------------------------- //
const lenis = new Lenis();
lenis.on('scroll', ScrollTrigger.update);
gsap.ticker.add((time) => {
  lenis.raf(time * 1000)
});
gsap.ticker.lagSmoothing(0);
// --------------------------------------------- //
// Lenis Scroll Plugin End
// --------------------------------------------- //

$(window).on("load", function() {
   
  "use strict";

  // --------------------------------------------- //
  // Typed.js Plugin Settings Start
  // --------------------------------------------- //
  var animatedHeadline = $(".animated-type");
  if(animatedHeadline.length){
    var typed = new Typed('#typed', {
      stringsElement: '#typed-strings',
      showCursor: true,
      cursorChar: '_',
      loop: true,
      typeSpeed: 70,
      backSpeed: 30,
      backDelay: 2500
    });
  }
  // --------------------------------------------- //
  // Typed.js Plugin Settings End
  // --------------------------------------------- //
  
});

$(window).on("scroll", function() {

  // --------------------------------------------- //
  // Header Scroll Behavior Start
  // --------------------------------------------- //
  if($(window).scrollTop() > 10) {
      $(".mxd-header").addClass("is-hidden");
  } else {
    $(".mxd-header").removeClass("is-hidden");
  }
  // --------------------------------------------- //
  // Header Scroll Behavior End
  // --------------------------------------------- //

});

// --------------------------------------------- //
// Hero #02 Scroll Out Animation Start
// --------------------------------------------- //
// Hero #02 scroll animated elements
hero02FadeOutEl = document.querySelectorAll(".hero-02-static-anim-el"); 
hero02FadeOutEl.forEach((element) => {
  let hero02fadeOutTl = gsap.timeline({
    scrollTrigger: {
      trigger: ".hero-02-static__tl-trigger",
      start: "top 14%",
      end: "top 0.2%",
      scrub: {
        scrub: true, 
        ease: "sine",
      },
      // markers: true
    },
  });
  hero02fadeOutTl.fromTo(element, {
    transform: "translate3d(0, 0, 0)",
    scaleY: 1,
    opacity: 1
  }, 
  {
    transform: "translate3d(0, -5rem, 0)",
    scaleY: 1.3,
    opacity: 0
  });
});
// Hero #02 pinned screen
fadeOutEl = document.querySelectorAll(".hero-02-fade-out-scroll"); 
fadeOutEl.forEach((element) => {
  let fadeOutTl = gsap.timeline({
    scrollTrigger: {
      trigger: ".mxd-pinned-fullscreen__tl-trigger",
      start: "top 80%",
      end: "top 10%",
      scrub: {
        scrub: true, 
        ease: "sine",
      },
      // markers: true
    },
  });
  fadeOutTl.fromTo(element, { opacity: 1 }, { opacity: 0 });
});
// --------------------------------------------- //
// Hero #02 Scroll Out Animation End
// --------------------------------------------- //

// --------------------------------------------- //
// Hero #07 Scroll Out Animation Start
// --------------------------------------------- //
// Hero #07 scroll animated elements
hero07FadeOutEl = document.querySelectorAll(".hero-07-slide-out-scroll"); 
hero07FadeOutEl.forEach((element) => {
  let hero07fadeOutTl = gsap.timeline({
    scrollTrigger: {
      trigger: ".mxd-hero-07__tl-trigger",
      start: "top 86%",
      end: "top 10%",
      scrub: {
        scrub: true, 
        ease: "power4.out",
      },
      // markers: true
    },
  });
  hero07fadeOutTl.fromTo(element, {
    transform: "translate3d(0, 0, 0)",
    scaleY: 1,
    // opacity: 1
  }, 
  {
    transform: "translate3d(0, -26rem, 0)",
    scaleY: 0.8,
    // opacity: 0
  });
});
// Hero #07 small scroll-out elements
fadeOutEl = document.querySelectorAll(".hero-07-fade-out-scroll"); 
fadeOutEl.forEach((element) => {
  let fadeOutTl = gsap.timeline({
    scrollTrigger: {
      trigger: ".mxd-hero-07__tl-trigger",
      start: "top 70%",
      end: "top 40%",
      scrub: {
        scrub: true, 
        ease: "elastic.out(1,0.3)",
      },
      // markers: true
    },
  });
  fadeOutTl.fromTo(element, { opacity: 1, transform: "translate3d(0, 0, 0)" }, { opacity: 0, transform: "translate3d(0, -10rem, 0)"});
});
// --------------------------------------------- //
// Hero #07 Scroll Out Animation End
// --------------------------------------------- //

// --------------------------------------------- //
// Hero #08 Scroll Out Animation Start
// --------------------------------------------- //
hero07FadeOutEl = document.querySelectorAll(".hero-08-slide-out-scroll");
hero07ScaleOutEl = document.querySelectorAll(".hero-08-scale-out-scroll"); 
hero07FadeOutEl.forEach((element) => {
  let hero07fadeOutTl = gsap.timeline({
    scrollTrigger: {
      trigger: ".mxd-hero-08__tl-trigger",
      start: "top 80%",
      end: "top 40%",
      scrub: {
        scrub: true, 
        ease: "power4.inOut",
      },
      // markers: true
    },
  });
  hero07fadeOutTl.fromTo(element, {
    transform: "translate3d(0, 0, 0)",
    // scaleY: 1,
    opacity: 1
  }, 
  {
    transform: "translate3d(0, -5rem, 0)",
    // scaleY: 0.8,
    opacity: 0
  });
});
hero07ScaleOutEl.forEach((element) => {
  let hero07scaleOutTl = gsap.timeline({
    scrollTrigger: {
      trigger: ".mxd-hero-08__tl-trigger",
      start: "top 40%",
      end: "top 10%",
      scrub: {
        scrub: true, 
        ease: "power4.inOut",
      },
      // markers: true
    },
  });
  hero07scaleOutTl.fromTo(element, {
    transform: "translate3d(0, 0, 0)",
    scaleY: 1,
    opacity: 1
  }, 
  {
    transform: "translate3d(0, -5rem, 0)",
    scaleY: 1.2,
    opacity: 0
  });
});
// --------------------------------------------- //
// Hero #08 Scroll Out Animation End
// --------------------------------------------- //


$(function() {

  // --------------------------------------------- //
  // SVG Fallback Start
  // --------------------------------------------- //
  if(!Modernizr.svg) {
    $("img[src*='svg']").attr("src", function() {
      return $(this).attr("src").replace(".svg", ".png");
    });
  };
  // --------------------------------------------- //
  // SVG Fallback End
  // --------------------------------------------- //

  // --------------------------------------------- //
  // Chrome Smooth Scroll Start
  // --------------------------------------------- //
  try {
    $.browserSelector();
    if($("html").hasClass("chrome")) {
      $.smoothScroll();
    }
  } catch(err) {
  };
  // --------------------------------------------- //
  // Chrome Smooth Scroll End
  // --------------------------------------------- //

  // --------------------------------------------- //
  // Images Moving Ban Start
  // --------------------------------------------- //
  $("img, a").on("dragstart", function(event) { event.preventDefault(); });
  // --------------------------------------------- //
  // Images Moving Ban End
  // --------------------------------------------- //

  // --------------------------------------------- //
  // Detecting Mobile/Desktop Start
  // --------------------------------------------- //
  var isMobile = false;
  if( /Android|webOS|iPhone|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
    $('html').addClass('touch');
    isMobile = true;
  }
  else {
    $('html').addClass('no-touch');
    isMobile = false;
  }
  //IE, Edge
  var isIE = /MSIE 9/i.test(navigator.userAgent) || /rv:11.0/i.test(navigator.userAgent) || /MSIE 10/i.test(navigator.userAgent) || /Edge\/\d+/.test(navigator.userAgent);
  // --------------------------------------------- //
  // Detecting Mobile/Desktop End
  // --------------------------------------------- //

  // --------------------------------------------- //
  // Smooth Scrolling Start
  // --------------------------------------------- //
  $('a[href*="#"]').not('[href="#"]').not('[href="#0"]').click(function(event) {
    if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
      var target = $(this.hash);
      target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
      if (target.length) {
        event.preventDefault();
        $('html, body').animate({
          scrollTop: target.offset().top
        }, 1000, function() {
          var $target = $(target);
          $target.focus();
          if ($target.is(":focus")) {
            return false;
          } else {
            $target.attr('tabindex','-1');
            $target.focus();
          };
        });
      }
    }
  });
  // --------------------------------------------- //
  // Smooth Scrolling End
  // --------------------------------------------- //

  // --------------------------------------------- //
  // Menu & Hamburger Start
  // --------------------------------------------- //
  $(".mxd-nav__wrap").each(function() {
    let hamburgerEl   = $(this).find(".mxd-nav__hamburger");
    let navLineEl     = $(this).find(".hamburger__line");
    let menuContainEl = $(this).find(".mxd-menu__contain");
    let flipItemEl    = $(this).find(".hamburger__base");
    let menuWrapEl    = $(this).find(".mxd-menu__wrapper");
    let menuBaseEl    = $(this).find(".mxd-menu__base");
    let menuLinkEl    = $(this).find(".mxd-menu__link");
    let menuItem      = $(this).find(".main-menu__item");
    let videoEl       = $(this).find(".menu-promo__video");
    let fadeInEl      = $(this).find(".menu-fade-in");
    let flipDuration = 0.6;

    function flip(forwards) {
      let state = Flip.getState(flipItemEl);
      if (forwards) {
        flipItemEl.appendTo(menuContainEl);
      } else {
        flipItemEl.appendTo(hamburgerEl);
      }
      Flip.from(state, { ease: "power4.inOut", duration: 0.8 });
    }

    let tl = gsap.timeline({ paused: true });
    tl.set(menuWrapEl, { display: "flex" });
    tl.from(menuBaseEl, { 
      opacity: 0,
      duration: flipDuration,
      ease: "none",
      onStart: () => {
        flip(true);
      }
    });
    tl.to(navLineEl.eq(0), { y: 5, duration: 0.16 }, "<")
    tl.to(navLineEl.eq(1), { y: -5, duration: 0.16 }, "<")
    tl.to(navLineEl.eq(0), { rotate: 45, duration: 0.16 }, 0.2)
    tl.to(navLineEl.eq(1), { rotate: -45, duration: 0.16 }, 0.2)
    tl.add("fade-in-up")
    .from(menuItem, {
      opacity: 0,
      yPercent: 50,
      duration: 0.2,
      stagger: {amount: 0.2},
      onReverseComplete: () => {
        flip(false);
      }
    }, "fade-in-up")
    .from(videoEl, {
      opacity: 0,
      yPercent: 20,
      duration: 0.2,
    }, "fade-in-up");
    tl.from(fadeInEl, { opacity: 0, duration: 0.3, });

    function openMenu(open) {
      if (!tl.isActive()) {
        if (open) {
          tl.play();
          hamburgerEl.addClass("nav-open");
        } else {
          tl.reverse();
          hamburgerEl.removeClass("nav-open");
        }
      }
    }

    hamburgerEl.on("click", function() {
      event.preventDefault();
      if ($(this).hasClass("nav-open")) {
        openMenu(false);
      } else {
        openMenu(true);
      }
    });
    menuBaseEl.on("click", function () {
      openMenu(false);
    });
    $(document).on("keydown", function (e) {
      if (e.key === "Escape") {
        openMenu(false);
      }
    });

    window.addEventListener("beforeunload", (event) => {
      openMenu(false);
    });

  });
  // --------------------------------------------- //
  // Menu & Hamburger End
  // --------------------------------------------- //

  // --------------------------------------------- //
  // Header/Menu Z-index Change Start
  // --------------------------------------------- //
  $(".mxd-nav__hamburger").on("click", function() {
    if ($(".mxd-nav__hamburger").hasClass("nav-open")) {
      $(".mxd-header").addClass("menu-is-visible");
    } else {
      setTimeout(function() {
        $(".mxd-header").removeClass("menu-is-visible");
      }, 1100);
    }
  });
  // --------------------------------------------- //
  // Header/Menu Z-index Change End
  // --------------------------------------------- //

  // --------------------------------------------- //
  // Menu Accordion Start
  // --------------------------------------------- //
  var Accordion = function(el, multiple) {
    this.el = el || {};
    this.multiple = multiple || false;
    var links = this.el.find('.main-menu__toggle');
    links.on('click', {el: this.el, multiple: this.multiple}, this.dropdown)
  }
  Accordion.prototype.dropdown = function(e) {
    var $el = e.data.el;
        $this = $(this),
        $next = $this.next();
    $next.slideToggle();
    $this.parent().toggleClass('open');
    if (!e.data.multiple) {
      $el.find('.submenu').not($next).slideUp().parent().removeClass('open');
    };
  }
  var accordion = new Accordion($('#main-menu'), false);
  // --------------------------------------------- //
  // Menu Accordion End
  // --------------------------------------------- //

  // --------------------------------------------- //
  // Layout Masonry After Each Image Loads Start
  // --------------------------------------------- //
  $('.mxd-projects-masonry__gallery').imagesLoaded().progress( function() {
    $('.mxd-projects-masonry__gallery').masonry('layout');
    ScrollTrigger.refresh();
  });
  // --------------------------------------------- //
  // Layout Masonry After Each Image Loads End
  // --------------------------------------------- //

  // --------------------------------------------- //
  // Accordion Start
  // --------------------------------------------- //
  $(".mxd-accordion__title").on("click", function(e) {
    e.preventDefault();
    var $this = $(this);
    if (!$this.hasClass("accordion-active")) {
      $(".mxd-accordion__content").slideUp(400);
      $(".mxd-accordion__title").removeClass("accordion-active");
      $('.mxd-accordion__arrow').removeClass('accordion-rotate');
    }
    $this.toggleClass("accordion-active");
    $this.next().slideToggle();
    $('.mxd-accordion__arrow',this).toggleClass('accordion-rotate');
    });
  // --------------------------------------------- //
  // Accordion End
  // --------------------------------------------- //

  // --------------------------------------------- //
  // Magnific Popup Video Start
  // --------------------------------------------- //
  $('#showreel-trigger').magnificPopup({
    type: 'iframe',
    mainClass: 'mfp-fade',
    removalDelay: 160,
    preloader: false,
    fixedContentPos: false,
    callbacks: {
      beforeOpen: function() { $('body').addClass('overflow-hidden'); lenis.stop(); },
      close: function() { $('body').removeClass('overflow-hidden'); lenis.start(); }
    }
  });
  // --------------------------------------------- //
  // Magnific Popup Video End
  // --------------------------------------------- //

  // --------------------------------------------- //
  // Mailchimp Subscribe Form Start
  // --------------------------------------------- //
  $('.notify-form').ajaxChimp({
    callback: mailchimpCallback,
    url: 'https://club.us10.list-manage.com/subscribe/post?u=e8d650c0df90e716c22ae4778&amp;id=54a7906900&amp;f_id=00b64ae4f0'
  });

  function mailchimpCallback(resp) {
    if(resp.result === 'success') {
      $('.notify').find('.form').addClass('is-hidden');
      $('.notify').find('.subscription-ok').addClass('is-visible');
      setTimeout(function() {
        // Done Functions
        $('.notify').find('.subscription-ok').removeClass('is-visible');
        $('.notify').find('.form').delay(300).removeClass('is-hidden');
        $('.notify-form').trigger("reset");
      }, 5000);
    } else if(resp.result === 'error') {
      $('.notify').find('.form').addClass('is-hidden');
      $('.notify').find('.subscription-error').addClass('is-visible');
      setTimeout(function() {
        // Done Functions
        $('.notify').find('.subscription-error').removeClass('is-visible');
        $('.notify').find('.form').delay(300).removeClass('is-hidden');
        $('.notify-form').trigger("reset");
      }, 5000);
    }
  };
  // --------------------------------------------- //
  // Mailchimp Subscribe Form End
  // --------------------------------------------- //

  // --------------------------------------------- //
  // Contact Form Start
  // --------------------------------------------- //
  $("#contact-form").submit(function() { //Change
    var th = $(this);
    $.ajax({
      type: "POST",
      url: "mail.php", //Change
      data: th.serialize()
    }).done(function() {
      $('.contact').find('.form').addClass('is-hidden');
      $('.contact').find('.form__reply').addClass('is-visible');
      setTimeout(function() {
        // Done Functions
        $('.contact').find('.form__reply').removeClass('is-visible');
        $('.contact').find('.form').delay(300).removeClass('is-hidden');
        th.trigger("reset");
      }, 5000);
    });
    return false;
  });
  // --------------------------------------------- //
  // Contact Form End
  // --------------------------------------------- //
  
});

// --------------------------------------------- //
// Parallax - Ukiyo Images & Video Start
// --------------------------------------------- //
const images = document.querySelectorAll(".parallax-img");
const imagesSmall = document.querySelectorAll(".parallax-img-small");
const video = document.querySelectorAll(".parallax-video");
new Ukiyo(images,{
  scale: 1.5,
  speed: 1.5,
  externalRAF: false,
});
new Ukiyo(imagesSmall,{
  scale: 1.2,
  speed: 1.5,
  externalRAF: false
});
new Ukiyo(video,{
  scale: 1.5,
  speed: 1.5,
  externalRAF: false
});
// --------------------------------------------- //
// Parallax - Ukiyo Images & Video End
// --------------------------------------------- //

// --------------------------------------------- //
// Pinned Images (for Services Block) Start
// --------------------------------------------- //
$(".mxd-pinned").each(function (index) {
  let childTriggers = $(this).find(".mxd-pinned__text-item");
  let childTargets = $(this).find(".mxd-pinned__img-item");
  function makeItemActive(index) {
    childTriggers.removeClass('is-active');
    childTargets.removeClass('is-active');
    childTriggers.eq(index).addClass('is-active');
    childTargets.eq(index).addClass('is-active');
  }
  makeItemActive(0);
  childTriggers.each(function (index) {
    ScrollTrigger.create({
      trigger: $(this),
      start: "top center",
      end: "bottom center",
      onToggle: (isActive) => {
        if (isActive) {
          makeItemActive(index);
        }
      }
    })
  });
});
// --------------------------------------------- //
// Pinned Images (for Services Block) End
// --------------------------------------------- //

// --------------------------------------------- //
// Stacking Cards Start
// --------------------------------------------- //
const cards  = document.querySelectorAll('.stack-item');
const stickySpace  = document.querySelector('.stack-offset');
const animation = gsap.timeline();
let cardHeight;

if(document.querySelector(".stack-item")) {
  function initCards(){
    animation.clear();
    cardHeight = cards[0].offsetHeight;
    //console.log("initCards()", cardHeight);
    cards.forEach((card, index) => {
      if(index > 0){
        gsap.set(card, {y:index * cardHeight,});
        animation.to(card, {y:0, duration: index*0.5, ease: "none"},0);
      }
    });
  };
  initCards();
  ScrollTrigger.create({
    trigger: ".stack-wrapper",
    start: "top top",
    pin: true,
    end: ()=>`+=${(cards.length * cardHeight) + stickySpace.offsetHeight}`,
    scrub: true,
    animation: animation,
    // markers: true,
    invalidateOnRefresh: true
  });
  ScrollTrigger.addEventListener("refreshInit", initCards);
};
// --------------------------------------------- //
// Stacking Cards End
// --------------------------------------------- //

// --------------------------------------------- //
// Animation - Buttons Common Start
// --------------------------------------------- //
let elements = document.querySelectorAll(".btn-anim .btn-caption");

elements.forEach((element) => {
  let innerText = element.innerText;
  element.innerHTML = "";
  let textContainer = document.createElement("div");
  textContainer.classList.add("btn-anim__block");
  for (let letter of innerText) {
    let span = document.createElement("span");
    span.innerText = letter.trim() === "" ? "\xa0" : letter;
    span.classList.add("btn-anim__letter");
    textContainer.appendChild(span);
  }
    element.appendChild(textContainer);
    element.appendChild(textContainer.cloneNode(true));
});

elements.forEach((element) => {
  element.addEventListener("mouseover", () => {
    element.classList.remove("play");
  })
});
// --------------------------------------------- //
// Animation - Buttons Common End
// --------------------------------------------- //

// --------------------------------------------- //
// Animation - Text Reveal Start
// --------------------------------------------- //
const splitTypes = document.querySelectorAll(".reveal-type");
splitTypes.forEach((char,i) => {
  const text = new SplitType(char, { types: 'words, chars' });
  gsap.from(text.chars, {
    scrollTrigger: {
      trigger: char,
      start: 'top 80%',
      end: 'top 20%',
      scrub: true,
      markers: false
    },
    opacity: 0.2,
    stagger: 0.1
  });
});

const animInUp = document.querySelectorAll(".reveal-in-up");
animInUp.forEach((char,i) => {
  const text = new SplitType(char);
  gsap.from(text.chars, {
    scrollTrigger: {
      trigger: char,
      start: 'top 90%',
      end: 'top 20%',
      scrub: true,
      // markers: true
    },
    transformOrigin: "top left",
    //rotationY: 90,
    y: 10,
    stagger: 0.2,
    delay: 0.2,
    duration: 2,
    //opacity: 0.2
  });
});
// --------------------------------------------- //
// Animation - Text Reveal End
// --------------------------------------------- //

// --------------------------------------------- //
// Animation - Scroll Universal Animations Start
// --------------------------------------------- //
// Scroll Rotating Animation
const animateRotation = document.querySelectorAll(".animate-rotation");
animateRotation.forEach((section) => {
  var value = $(section).data("value");
  gsap.fromTo(section, {
    ease: 'sine',
    rotate: 0,
  }, {
    rotate: value,
    scrollTrigger: {
      trigger: section,
      scrub: true,
      toggleActions: 'play none none reverse',
    }
  });
});

//Scroll Animation In Up
const animateInUp = document.querySelectorAll(".anim-uni-in-up");
animateInUp.forEach((element) => {
  gsap.fromTo(element, {
    opacity: 0,
    y: 50,
    ease: 'sine',
  }, {
    y: 0,
    opacity: 1,
    scrollTrigger: {
      trigger: element,
      toggleActions: 'play none none reverse',
    }
  });
});

// Scroll Animation Scale In
const animateInUpFront = document.querySelectorAll(".anim-uni-scale-in");
animateInUpFront.forEach((element) => {
  gsap.fromTo(element, {
    opacity: 1,
    y: 50,
    //x: 70,
    scale: 1.2,
    ease: 'sine',
  }, {
    y: 0,
    x: 0,
    opacity: 1,
    scale: 1,
    scrollTrigger: {
      trigger: element,
      toggleActions: 'play none none reverse',
    }
  });
});

// Scroll Animation Scale In Right
const animateInUpRight = document.querySelectorAll(".anim-uni-scale-in-right");
animateInUpRight.forEach((element) => {
  gsap.fromTo(element, {
    opacity: 1,
    y: 50,
    x: -70,
    scale: 1.2,
    ease: 'sine',
    duration: 5
  }, {
    y: 0,
    x: 0,
    opacity: 1,
    scale: 1,
    scrollTrigger: {
      trigger: element,
      toggleActions: 'play none none reverse',
    }
  });
});

// Scroll Animation Scale In Left
const animateInUpLeft = document.querySelectorAll(".anim-uni-scale-in-left");
animateInUpLeft.forEach((element) => {
  gsap.fromTo(element, {
    opacity: 1,
    y: 50,
    x: 70,
    scale: 1.2,
    ease: 'sine',
  }, {
    y: 0,
    x: 0,
    opacity: 1,
    scale: 1,
    scrollTrigger: {
      trigger: element,
      toggleActions: 'play none none reverse',
    }
  });
});

// Grid Animation 2 cards
if(document.querySelector(".animate-card-2")) {
  gsap.set(".animate-card-2", {y: 50, opacity: 0});
  ScrollTrigger.batch(".animate-card-2", {
    interval: 0.1,
    batchMax: 2,
    duration: 3,
    onEnter: batch => gsap.to(batch, {
      opacity: 1, 
      y: 0,
      ease: 'sine',
      stagger: {each: 0.15, grid: [1, 2]}, 
      overwrite: true
    }),
    onLeave: batch => gsap.set(batch, {opacity: 1, y: 0, overwrite: true}),
    onEnterBack: batch => gsap.to(batch, {opacity: 1, y: 0, stagger: 0.15, overwrite: true}),
    onLeaveBack: batch => gsap.set(batch, {opacity: 0, y: 50, overwrite: true})
  });
  ScrollTrigger.addEventListener("refreshInit", () => gsap.set(".animate-card-2", {y: 0, opacity: 1}));
};

// Grid Animation 3 cards
if(document.querySelector(".animate-card-3")) {
  gsap.set(".animate-card-3", {y: 50, opacity: 0});
  ScrollTrigger.batch(".animate-card-3", {
    interval: 0.1,
    batchMax: 3,
    duration: 3,
    onEnter: batch => gsap.to(batch, {
      opacity: 1, 
      y: 0,
      ease: 'sine',
      stagger: {each: 0.15, grid: [1, 3]}, 
      overwrite: true
    }),
    onLeave: batch => gsap.set(batch, {opacity: 1, y: 0, overwrite: true}),
    onEnterBack: batch => gsap.to(batch, {opacity: 1, y: 0, stagger: 0.15, overwrite: true}),
    onLeaveBack: batch => gsap.set(batch, {opacity: 0, y: 50, overwrite: true})
  });
  ScrollTrigger.addEventListener("refreshInit", () => gsap.set(".animate-card-3", {y: 0, opacity: 1}));
};

// Grid Animation 4 cards
if(document.querySelector(".animate-card-4")) {
  gsap.set(".animate-card-4", {y: 50, opacity: 0});
  ScrollTrigger.batch(".animate-card-4", {
    interval: 0.1,
    batchMax: 4,
    delay: 1000,
    onEnter: batch => gsap.to(batch, {
      opacity: 1, 
      y: 0,
      ease: 'sine',
      stagger: {each: 0.15, grid: [1, 4]}, 
      overwrite: true
    }),
    onLeave: batch => gsap.set(batch, {opacity: 1, y: 0, overwrite: true}),
    onEnterBack: batch => gsap.to(batch, {opacity: 1, y: 0, stagger: 0.15, overwrite: true}),
    onLeaveBack: batch => gsap.set(batch, {opacity: 0, y: 50, overwrite: true})
  });
  ScrollTrigger.addEventListener("refreshInit", () => gsap.set(".animate-card-4", {y: 0, opacity: 1}));
};

// Grid Animation 5 cards
if(document.querySelector(".animate-card-5")) {
  gsap.set(".animate-card-5", {y: 50, opacity: 0});
  ScrollTrigger.batch(".animate-card-5", {
    interval: 0.1,
    batchMax: 5,
    delay: 1000,
    onEnter: batch => gsap.to(batch, {
      opacity: 1, 
      y: 0,
      ease: 'sine',
      stagger: {each: 0.15, grid: [1, 5]}, 
      overwrite: true
    }),
    onLeave: batch => gsap.set(batch, {opacity: 1, y: 0, overwrite: true}),
    onEnterBack: batch => gsap.to(batch, {opacity: 1, y: 0, stagger: 0.15, overwrite: true}),
    onLeaveBack: batch => gsap.set(batch, {opacity: 0, y: 50, overwrite: true})
  });
  ScrollTrigger.addEventListener("refreshInit", () => gsap.set(".animate-card-5", {y: 0, opacity: 1}));
};

// Top to Bottom Animation
toBottomEl = document.querySelectorAll(".anim-top-to-bottom"); 
toBottomEl.forEach((element) => {
  let toBottomTl = gsap.timeline({
    scrollTrigger: {
      trigger: ".fullwidth-text__tl-trigger",
      start: "top 99%",
      end: "top 24%",
      scrub: {
        scrub: true, 
        ease: "none" 
      },
      // markers: true
    },
  });
  toBottomTl.fromTo(element, {
    transform: "translate3d(0, -100%, 0)"
  }, 
  {
    transform: "translate3d(0, 0, 0)"
  });
});

// Zoom In / Zoom Out Container Animations
const docStyle = getComputedStyle(document.documentElement);
const zoomInContainer = document.querySelectorAll(".anim-zoom-in-container");
const zoomOutContainer = document.querySelectorAll(".anim-zoom-out-container");

// Zoom In 
zoomInContainer.forEach((element) => {
  let zoomInBlockTl = gsap.timeline({
    scrollTrigger: {
      trigger: element,
      start: "top 82%",
      end: "top 14%",
      scrub: {
        scrub: true, 
        ease: "power4.inOut" 
      },
      // markers: true
    },
  });
  zoomInBlockTl.fromTo(element, {
    borderRadius: '200px',
    transform: "scale3d(0.94, 1, 1)"
  }, 
  {
    borderRadius: docStyle.getPropertyValue("--_radius-l"),
    transform: "scale3d(1, 1, 1)"
  });
});

// Zoom Out
zoomOutContainer.forEach((element) => {
  let zoomOutBlockTl = gsap.timeline({
    scrollTrigger: {
      trigger: element,
      start: "top 82%",
      end: "top 14%",
      scrub: {
        scrub: true, 
        ease: "power4.inOut" 
      },
      // markers: true
    },
  });
  zoomOutBlockTl.fromTo(element, {
    borderRadius: '200px',
    transform: "scale3d(1.14, 1, 1)"
  }, 
  {
    borderRadius: docStyle.getPropertyValue("--_radius-l"),
    transform: "scale3d(1, 1, 1)",
  });
});
// --------------------------------------------- //
// Animation - Scroll Universal Animations End
// --------------------------------------------- //

// --------------------------------------------- //
// Animation - Images Reveal on Hover Start
// --------------------------------------------- //
const link = document.querySelectorAll('.hover-reveal__item');
const linkHoverReveal = document.querySelectorAll('.hover-reveal__content');
const linkImages = document.querySelectorAll('.hover-reveal__image');

for(let i = 0; i < link.length; i++) {
  link[i].addEventListener('mousemove', (e) => {
    linkHoverReveal[i].style.opacity = 1;
    linkHoverReveal[i].style.transform = `translate(-80%, -50% )`;
    linkImages[i].style.transform = 'scale(1, 1)';
    linkHoverReveal[i].style.left = e.clientX + "px";
  })
  
  link[i].addEventListener('mouseleave', (e) => {
    linkHoverReveal[i].style.opacity = 0;
    linkHoverReveal[i].style.transform = `translate(-80%, -50%)`;
    linkImages[i].style.transform = 'scale(1, 1.4)';
  })
}
// --------------------------------------------- //
// Animation - Images Reveal on Hover End
// --------------------------------------------- //

// --------------------------------------------- //
// Swiper Slider - Testimonials #01 Start
// --------------------------------------------- //
const testimonialsSlider = document.querySelector("testimonials-slider");

if (!testimonialsSlider) {
  const swiper = new Swiper('.swiper-testimonials', {
    slidesPerView: 'auto',
    grabCursor: true,
    spaceBetween: 30,
    autoplay: true,
    delay: 3000,
    speed: 1000,
    loop: true,
    parallax: true,
    loopFillGroupWithBlank: true,
    pagination: {
      el: ".swiper-pagination",
      type: "fraction",
    },
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
  });
};
// --------------------------------------------- //
// Swiper Slider - Testimonials #01 End
// --------------------------------------------- //

// --------------------------------------------- //
// Swiper Slider - Testimonials #02 Start
// --------------------------------------------- //
const testimonialsSlider2 = document.querySelector("testimonials-slider-2");

if (!testimonialsSlider2) {
  const swiper = new Swiper('.swiper-testimonials-2', {
    slidesPerView: 1,
    grabCursor: true,
    effect: 'fade',
    spaceBetween: 30,
    autoplay: true,
    delay: 3000,
    speed: 1000,
    loop: true,
    parallax: true,
    loopFillGroupWithBlank: true,
    pagination: {
      el: ".swiper-pagination",
      type: "fraction",
    },
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
  });
};
// --------------------------------------------- //
// Swiper Slider - Testimonials #02 End
// --------------------------------------------- //

// --------------------------------------------- //
// Swiper Slider - Inner Pages Demo Start
// --------------------------------------------- //
const innerDemoSlider = document.querySelector("mxd-demo-swiper");

if (!innerDemoSlider) {
  const swiper = new Swiper('.mxd-demo-swiper', {
    breakpoints: {
      640: {
        slidesPerView: 1,
        spaceBetween: 30,
      },
      768: {
        slidesPerView: 3,
        spaceBetween: 30,
      },
      1600: {
        slidesPerView: 3,
        spaceBetween: 30,
      },
    },
    loop: true,
    parallax: true,
    autoplay: { disableOnInteraction: false, enabled: true },
    grabCursor: true,
    speed: 600,
    centeredSlides: true,
    keyboard: { enabled: true },
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
  });
};
// --------------------------------------------- //
// Swiper Slider - Inner Pages Demo End
// --------------------------------------------- //

// --------------------------------------------- //
// CountUp - All Counters Options Start
// --------------------------------------------- //
const optionsNormal = {
  enableScrollSpy: true
};
const optionsDecimal = {
  decimalPlaces: 1,
  enableScrollSpy: true
};
const optionsDecimalTwo = {
  decimalPlaces: 2,
  enableScrollSpy: true
};
const optionsPercent = {
  suffix: '%',
  enableScrollSpy: true
};
const optionsK = {
  suffix: 'K',
  enableScrollSpy: true
};
const optionsPlus = {
  suffix: '+',
  enableScrollSpy: true
};
// --------------------------------------------- //
// CountUp - All Counters Options End
// --------------------------------------------- //

// --------------------------------------------- //
// Marquee - Two Lines Start
// --------------------------------------------- //
const initMarquees = () => {
  const items = [...document.querySelectorAll(".marquee--gsap")];
  if (items) {
    const marqueeObject = {
      top: {
        el: null,
        width: 0
      },
      bottom: {
        el: null,
        width: 0
      }
    };
    items.forEach((itemBlock) => {
      marqueeObject.top.el = itemBlock.querySelector(".marquee__top");
      marqueeObject.bottom.el = itemBlock.querySelector(".marquee__bottom");
      marqueeObject.top.width = marqueeObject.top.el.offsetWidth;
      marqueeObject.bottom.width = marqueeObject.bottom.el.offsetWidth;
      marqueeObject.top.el.innerHTML += marqueeObject.top.el.innerHTML;
      marqueeObject.bottom.el.innerHTML += marqueeObject.bottom.el.innerHTML;
      let dirFromLeft = "-=50%";
      let dirFromRight = "+=50%";
      let master = gsap
        .timeline()
        .add(marquee(marqueeObject.top.el, 30, dirFromLeft), 0)
        .add(marquee(marqueeObject.bottom.el, 30, dirFromRight), 0);
      let tween = gsap.to(master, { 
        duration: 1.5, 
        timeScale: 1, 
        paused: true 
      });
      let timeScaleClamp = gsap.utils.clamp(1, 6);
      ScrollTrigger.create({
        start: 0,
        end: "max",
        onUpdate: (self) => {
          master.timeScale(timeScaleClamp(Math.abs(self.getVelocity() / 200)));
          tween.invalidate().restart();
        }
      });
    });
  }
};
const marquee = (item, time, direction) => {
  let mod = gsap.utils.wrap(0, 50);
  return gsap.to(item, {
    duration: time,
    ease: "none",
    x: direction,
    modifiers: {
      x: (x) => (direction = mod(parseFloat(x)) + "%")
    },
    repeat: -1
  });
};
initMarquees();
// --------------------------------------------- //
// Marquee - Two Lines End
// --------------------------------------------- //

// --------------------------------------------- //
// Marquee - One Line To Right Start
// --------------------------------------------- //
const initMarquee = () => {
  const items = [...document.querySelectorAll(".marquee-right--gsap")];
  if (items) {
    const marqueeObject = {
      el: null,
      width: 0
    };
    items.forEach((itemBlock) => {
      marqueeObject.el = itemBlock.querySelector(".marquee__toright");
      marqueeObject.width = marqueeObject.el.offsetWidth;
      marqueeObject.el.innerHTML += marqueeObject.el.innerHTML;
      //let dirFromLeft = "-=50%";
      let dirFromRight = "+=50%";
      let master = gsap
        .timeline()
        //.add(marquee(marqueeObject.el, 20, dirFromLeft), 0);
        .add(marqueeRight(marqueeObject.el, 30, dirFromRight), 0);
      let tween = gsap.to(master, { 
        duration: 1.5, 
        timeScale: 1, 
        paused: true 
      });
      let timeScaleClamp = gsap.utils.clamp(1, 6);
      ScrollTrigger.create({
        start: 0,
        end: "max",
        onUpdate: (self) => {
          master.timeScale(timeScaleClamp(Math.abs(self.getVelocity() / 200)));
          tween.invalidate().restart();
        }
      });
    });
  }
};
const marqueeRight = (item, time, direction) => {
  let mod = gsap.utils.wrap(0, 50);
  return gsap.to(item, {
    duration: time,
    ease: "none",
    x: direction,
    modifiers: {
      x: (x) => (direction = mod(parseFloat(x)) + "%")
    },
    repeat: -1
  });
};
initMarquee();
// --------------------------------------------- //
// Marquee - One Line To Right End
// --------------------------------------------- //

// --------------------------------------------- //
// Marquee - One Line To Left Start
// --------------------------------------------- //
const initMarqueeLeft = () => {
  const items = [...document.querySelectorAll(".marquee-left--gsap")];
  if (items) {
    const marqueeObject = {
      el: null,
      width: 0
    };
    items.forEach((itemBlock) => {
      marqueeObject.el = itemBlock.querySelector(".marquee__toleft");
      marqueeObject.width = marqueeObject.el.offsetWidth;
      marqueeObject.el.innerHTML += marqueeObject.el.innerHTML;
      let dirFromLeft = "-=50%";
      // let dirFromRight = "+=50%";
      let master = gsap
        .timeline()
        .add(marquee(marqueeObject.el, 30, dirFromLeft), 0);
        // .add(marqueeRight(marqueeObject.el, 30, dirFromRight), 0);
      let tween = gsap.to(master, { 
        duration: 1.5, 
        timeScale: 1, 
        paused: true 
      });
      let timeScaleClamp = gsap.utils.clamp(1, 6);
      ScrollTrigger.create({
        start: 0,
        end: "max",
        onUpdate: (self) => {
          master.timeScale(timeScaleClamp(Math.abs(self.getVelocity() / 200)));
          tween.invalidate().restart();
        }
      });
    });
  }
};
const marqueeLeft = (item, time, direction) => {
  let mod = gsap.utils.wrap(0, 50);
  return gsap.to(item, {
    duration: time,
    ease: "none",
    x: direction,
    modifiers: {
      x: (x) => (direction = mod(parseFloat(x)) + "%")
    },
    repeat: -1
  });
};
initMarqueeLeft();
// --------------------------------------------- //
// Marquee - One Line To Left End
// --------------------------------------------- //

// --------------------------------------------- //
// SVG DOM Injection Start
// --------------------------------------------- //
var mySVGsToInject = document.querySelectorAll('img.inject-me');
var injectorOptions = {
  evalScripts: 'once',
  pngFallback: 'assets/png',
  each: function (svg) {
  }
};
SVGInjector(mySVGsToInject, injectorOptions, function (totalSVGsInjected) {
  console.log('We injected ' + totalSVGsInjected + ' SVG(s)!');
});
// --------------------------------------------- //
// SVG DOM Injection End
// --------------------------------------------- //

// --------------------------------------------- //
// Color Switch Start
// --------------------------------------------- //
const themeBtn = document.querySelector('#color-switcher');

if (themeBtn) { // Verificação de segurança adicionada
  function getCurrentTheme(){
    let theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    localStorage.getItem('template.theme') ? theme = localStorage.getItem('template.theme') : null;
    return theme;
  }
  function loadTheme(theme){
    const root = document.querySelector(':root');
    if(theme === "light"){
      themeBtn.innerHTML = `<i class="ph-bold ph-moon-stars"></i>`;
    } else {
      themeBtn.innerHTML = `<i class="ph-bold ph-sun-horizon"></i>`;
    }
    root.setAttribute('color-scheme', `${theme}`);
  };
  themeBtn.addEventListener('click', () => {
    let theme = getCurrentTheme();
    if(theme === 'dark'){
      theme = 'light';
    } else {
      theme = 'dark';
    }
    localStorage.setItem('template.theme', `${theme}`);
    loadTheme(theme);
  });
  window.addEventListener('DOMContentLoaded', () => {
    loadTheme(getCurrentTheme());
  });
}
// --------------------------------------------- //
// Color Switch End
// --------------------------------------------- //

// --------------------------------------------- //
// Scroll to Top Button Start
// --------------------------------------------- //
const toTop = document.querySelector(".btn-to-top");

if (toTop) { // Verificação de segurança adicionada
  $(".btn-to-top").each(function() {
    toTop.addEventListener("click", function(event){
      event.preventDefault()
    });

    toTop.addEventListener("click", () => gsap.to(window, { 
      scrollTo: 0, 
      ease: 'power4.inOut',
      duration: 1.3,
    }));

    gsap.set(toTop, { opacity: 0 });

    gsap.to(toTop, {
      opacity: 1,
      autoAlpha: 1,
      scrollTrigger: {
        trigger: "body",
        start: "top -20%",
        end: "top -20%",
        toggleActions: "play none reverse none"
      }
    });
  });
}
// --------------------------------------------- //
// Scroll to Top Button End
// --------------------------------------------- //

// ------------------------------------------------------------------------------ //
// Parallax Universal (apply parallax effect to any element with a data-speed attribute) Start
// ------------------------------------------------------------------------------ //
if (document.querySelector("[data-speed]")) { // <--- Adicionei esta verificação
  gsap.to("[data-speed]", {
    y: (i, el) => (1 - parseFloat(el.getAttribute("data-speed"))) * ScrollTrigger.maxScroll(window) ,
    ease: "none",
    scrollTrigger: {
      start: 0,
      end: "max",
      invalidateOnRefresh: true,
      scrub: 0
    }
  });
}
// --------------------------------------------- //
// Parallax Universal End
// --------------------------------------------- //

// --- Custom Cyberpunk Modal Integration for Lead Capture & Analytics ---
document.addEventListener('DOMContentLoaded', () => {
  // 1. Load the CSS dynamically
  const link = document.createElement('link');
  link.rel = 'stylesheet';
  link.href = 'css/cyber-modal.css';
  document.head.appendChild(link);

  // 2. Track access locally (calls base/api/track.php)
  try {
    fetch('base/api/track.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        referrer: document.referrer || '',
        screen_resolution: `${window.screen.width}x${window.screen.height}`,
        language: navigator.language || ''
      })
    }).catch(err => console.error('Tracking non-blocking error:', err));
  } catch (e) {}

  // 3. Inject Modal HTML into body
  const modalHTML = `
    <!-- SVG Turbulence Filter for Electric Border -->
    <svg class="svg-container" style="position: absolute; width: 0; height: 0; pointer-events: none;">
      <defs>
        <filter id="turbulent-displace" color-interpolation-filters="sRGB" x="-20%" y="-20%" width="140%"
          height="140%">
          <feTurbulence type="turbulence" baseFrequency="0.02" numOctaves="2" result="noise1" seed="1" />
          <feOffset in="noise1" dx="0" dy="0" result="offsetNoise1">
            <animate attributeName="dy" values="700; 0" dur="6s" repeatCount="indefinite" calcMode="linear" />
          </feOffset>

          <feTurbulence type="turbulence" baseFrequency="0.02" numOctaves="2" result="noise2" seed="1" />
          <feOffset in="noise2" dx="0" dy="0" result="offsetNoise2">
            <animate attributeName="dy" values="0; -700" dur="6s" repeatCount="indefinite" calcMode="linear" />
          </feOffset>

          <feTurbulence type="turbulence" baseFrequency="0.02" numOctaves="2" result="noise1" seed="2" />
          <feOffset in="noise1" dx="0" dy="0" result="offsetNoise3">
            <animate attributeName="dx" values="490; 0" dur="6s" repeatCount="indefinite" calcMode="linear" />
          </feOffset>

          <feTurbulence type="turbulence" baseFrequency="0.02" numOctaves="2" result="noise2" seed="2" />
          <feOffset in="noise2" dx="0" dy="0" result="offsetNoise4">
            <animate attributeName="dx" values="0; -490" dur="6s" repeatCount="indefinite" calcMode="linear" />
          </feOffset>

          <feComposite in="offsetNoise1" in2="offsetNoise2" result="part1" />
          <feComposite in="offsetNoise3" in2="offsetNoise4" result="part2" />
          <feBlend in="part1" in2="part2" mode="color-dodge" result="combinedNoise" />

          <feDisplacementMap in="SourceGraphic" in2="combinedNoise" scale="30" xChannelSelector="R"
            yChannelSelector="B" />
        </filter>

        <filter id="turbulent-displace-soft" color-interpolation-filters="sRGB" x="-20%" y="-20%" width="140%"
          height="140%">
          <feTurbulence type="turbulence" baseFrequency="0.03" numOctaves="2" result="noise1" seed="3" />
          <feOffset in="noise1" dx="0" dy="0" result="offsetNoise1">
            <animate attributeName="dy" values="500; 0" dur="8s" repeatCount="indefinite" calcMode="linear" />
          </feOffset>

          <feTurbulence type="turbulence" baseFrequency="0.03" numOctaves="2" result="noise2" seed="3" />
          <feOffset in="noise2" dx="0" dy="0" result="offsetNoise2">
            <animate attributeName="dy" values="0; -500" dur="8s" repeatCount="indefinite" calcMode="linear" />
          </feOffset>

          <feTurbulence type="turbulence" baseFrequency="0.03" numOctaves="2" result="noise1" seed="4" />
          <feOffset in="noise1" dx="0" dy="0" result="offsetNoise3">
            <animate attributeName="dx" values="350; 0" dur="8s" repeatCount="indefinite" calcMode="linear" />
          </feOffset>

          <feTurbulence type="turbulence" baseFrequency="0.03" numOctaves="2" result="noise2" seed="4" />
          <feOffset in="noise2" dx="0" dy="0" result="offsetNoise4">
            <animate attributeName="dx" values="0; -350" dur="8s" repeatCount="indefinite" calcMode="linear" />
          </feOffset>

          <feComposite in="offsetNoise1" in2="offsetNoise2" result="part1" />
          <feComposite in="offsetNoise3" in2="offsetNoise4" result="part2" />
          <feBlend in="part1" in2="part2" mode="color-dodge" result="combinedNoise" />

          <feDisplacementMap in="SourceGraphic" in2="combinedNoise" scale="10" xChannelSelector="R"
            yChannelSelector="B" />
        </filter>
      </defs>
    </svg>
    <div class="modal" popover="auto" id="cyber-form-popover">
      <button type="button" class="modal-close" popovertarget="cyber-form-popover" popovertargetaction="close"
        aria-label="Fechar" style="outline: none;">
        <svg viewBox="0 0 24 24" width="14" height="14" stroke="currentColor" stroke-width="3" fill="none"
          stroke-linecap="round" stroke-linejoin="round" style="display: block;">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
      </button>
      <section class="modal__body">
        <div class="body__backdrop">
          <div class="corner"></div>
        </div>
        <div class="body__content">
          <div class="form-container" id="cyber-form-container" style="padding-top: 0;">
            <form class="cyber-form" id="cyber-lead-form" style="margin-top: 0;">
              <!-- STEP 1: NOME -->
              <div class="cyber-step active" data-step="1">
                <div class="cyber-input-group">
                  <div class="glitch-input-wrapper" style="padding: 0; margin-bottom: 8px;">
                    <div class="input-container" style="width: 100%;">
                      <input type="text" id="lead-name" class="holo-input" placeholder=" " required />
                      <label for="lead-name" class="input-label" data-text="01. QUAL É O SEU NOME?">01. Qual é o seu nome?</label>
                      <div class="input-border"></div>
                      <div class="input-scanline"></div>
                      <div class="input-glow"></div>
                      <div class="input-data-stream">
                        <div class="stream-bar" style="--i: 0;"></div>
                        <div class="stream-bar" style="--i: 1;"></div>
                        <div class="stream-bar" style="--i: 2;"></div>
                        <div class="stream-bar" style="--i: 3;"></div>
                        <div class="stream-bar" style="--i: 4;"></div>
                        <div class="stream-bar" style="--i: 5;"></div>
                        <div class="stream-bar" style="--i: 6;"></div>
                        <div class="stream-bar" style="--i: 7;"></div>
                        <div class="stream-bar" style="--i: 8;"></div>
                        <div class="stream-bar" style="--i: 9;"></div>
                      </div>
                      <div class="input-corners">
                        <div class="corner corner-tl"></div>
                        <div class="corner corner-tr"></div>
                        <div class="corner corner-bl"></div>
                        <div class="corner corner-br"></div>
                      </div>
                    </div>
                  </div>
                  <div class="cyber-error-msg" id="error-name"
                    style="color: #ff4444; font-size: 0.72rem; font-family: 'Cyber', sans-serif; display: none; margin-top: 4px; letter-spacing: 0.05em; font-weight: 800;">
                    [!] NOME É REQUERIDO</div>
                </div>
                <div class="modal__actions" style="margin-top: 14px;">
                  <button type="button" class="cyber-btn btn-cyber-purple" popovertarget="cyber-form-popover"
                    popovertargetaction="close" aria-label="Cancelar">
                    <span class="backdrop"><span class="corner"></span></span>
                    <span>Cancelar</span>
                  </button>
                  <button type="button" class="cyber-btn btn-cyber-cyan btn-next-step" data-step="1"
                    aria-label="Próximo">
                    <span class="backdrop"><span class="corner"></span></span>
                    <span>Próximo</span>
                  </button>
                </div>
              </div>

              <!-- STEP 2: WHATSAPP -->
              <div class="cyber-step" data-step="2" style="display: none;">
                <div class="cyber-input-group">
                  <div class="glitch-input-wrapper" style="padding: 0; margin-bottom: 8px;">
                    <div class="input-container" style="width: 100%;">
                      <input type="tel" id="lead-phone" class="holo-input" placeholder=" " required />
                      <label for="lead-phone" class="input-label" data-text="02. QUAL É O SEU WHATSAPP?">02. Qual é o seu WhatsApp?</label>
                      <div class="input-border"></div>
                      <div class="input-scanline"></div>
                      <div class="input-glow"></div>
                      <div class="input-data-stream">
                        <div class="stream-bar" style="--i: 0;"></div>
                        <div class="stream-bar" style="--i: 1;"></div>
                        <div class="stream-bar" style="--i: 2;"></div>
                        <div class="stream-bar" style="--i: 3;"></div>
                        <div class="stream-bar" style="--i: 4;"></div>
                        <div class="stream-bar" style="--i: 5;"></div>
                        <div class="stream-bar" style="--i: 6;"></div>
                        <div class="stream-bar" style="--i: 7;"></div>
                        <div class="stream-bar" style="--i: 8;"></div>
                        <div class="stream-bar" style="--i: 9;"></div>
                      </div>
                      <div class="input-corners">
                        <div class="corner corner-tl"></div>
                        <div class="corner corner-tr"></div>
                        <div class="corner corner-bl"></div>
                        <div class="corner corner-br"></div>
                      </div>
                    </div>
                  </div>
                  <div class="cyber-error-msg" id="error-phone"
                    style="color: #ff4444; font-size: 0.72rem; font-family: 'Cyber', sans-serif; display: none; margin-top: 4px; letter-spacing: 0.05em; font-weight: 800;">
                    [!] WHATSAPP REQUERIDO</div>
                </div>
                <div class="modal__actions" style="margin-top: 14px;">
                  <button type="button" class="cyber-btn btn-cyber-purple btn-prev-step" data-step="2"
                    aria-label="Voltar">
                    <span class="backdrop"><span class="corner"></span></span>
                    <span>Voltar</span>
                  </button>
                  <button type="button" class="cyber-btn btn-cyber-cyan btn-next-step" data-step="2"
                    aria-label="Próximo">
                    <span class="backdrop"><span class="corner"></span></span>
                    <span>Próximo</span>
                  </button>
                </div>
              </div>

              <!-- STEP 3: EMAIL -->
              <div class="cyber-step" data-step="3" style="display: none;">
                <div class="cyber-input-group">
                  <div class="glitch-input-wrapper" style="padding: 0; margin-bottom: 8px;">
                    <div class="input-container" style="width: 100%;">
                      <input type="email" id="lead-email" class="holo-input" placeholder=" " required />
                      <label for="lead-email" class="input-label" data-text="03. QUAL É O SEU MELHOR E-MAIL?">03. Qual é o seu melhor e-mail?</label>
                      <div class="input-border"></div>
                      <div class="input-scanline"></div>
                      <div class="input-glow"></div>
                      <div class="input-data-stream">
                        <div class="stream-bar" style="--i: 0;"></div>
                        <div class="stream-bar" style="--i: 1;"></div>
                        <div class="stream-bar" style="--i: 2;"></div>
                        <div class="stream-bar" style="--i: 3;"></div>
                        <div class="stream-bar" style="--i: 4;"></div>
                        <div class="stream-bar" style="--i: 5;"></div>
                        <div class="stream-bar" style="--i: 6;"></div>
                        <div class="stream-bar" style="--i: 7;"></div>
                        <div class="stream-bar" style="--i: 8;"></div>
                        <div class="stream-bar" style="--i: 9;"></div>
                      </div>
                      <div class="input-corners">
                        <div class="corner corner-tl"></div>
                        <div class="corner corner-tr"></div>
                        <div class="corner corner-bl"></div>
                        <div class="corner corner-br"></div>
                      </div>
                    </div>
                  </div>
                  <div class="cyber-error-msg" id="error-email"
                    style="color: #ff4444; font-size: 0.72rem; font-family: 'Cyber', sans-serif; display: none; margin-top: 4px; letter-spacing: 0.05em; font-weight: 800;">
                    [!] ENDEREÇO DE E-MAIL VÁLIDO REQUERIDO</div>
                </div>
                <div class="modal__actions" style="margin-top: 14px;">
                  <button type="button" class="cyber-btn btn-cyber-purple btn-prev-step" data-step="3"
                    aria-label="Voltar">
                    <span class="backdrop"><span class="corner"></span></span>
                    <span>Voltar</span>
                  </button>
                  <button type="button" class="cyber-btn btn-cyber-cyan btn-next-step" data-step="3"
                    aria-label="Próximo">
                    <span class="backdrop"><span class="corner"></span></span>
                    <span>Próximo</span>
                  </button>
                </div>
              </div>

              <!-- STEP 4: INFRA / DUVIDAS -->
              <div class="cyber-step" data-step="4" style="display: none;">
                <div class="cyber-input-group">
                  <label for="lead-infra">04. O que você tem de infra ou suas dúvidas?</label>
                  <div class="input-wrapper">
                    <textarea id="lead-infra" name="infra" rows="3" required
                      placeholder="CONTE DETALHES DO SEU PROJETO OU DÚVIDAS..."></textarea>
                  </div>
                  <div class="cyber-error-msg" id="error-infra"
                    style="color: #ff4444; font-size: 0.72rem; font-family: 'Cyber', sans-serif; display: none; margin-top: 4px; letter-spacing: 0.05em; font-weight: 800;">
                    [!] RESPOSTA REQUERIDA</div>
                </div>
                <div class="modal__actions" style="margin-top: 14px;">
                  <button type="button" class="cyber-btn btn-cyber-purple btn-prev-step" data-step="4"
                    aria-label="Voltar">
                    <span class="backdrop"><span class="corner"></span></span>
                    <span>Voltar</span>
                  </button>
                  <button type="submit" class="cyber-btn btn-cyber-cyan" id="popover-submit-btn"
                    aria-label="Prosseguir">
                    <span class="backdrop"><span class="corner"></span></span>
                    <span>Prosseguir</span>
                  </button>
                </div>
              </div>
            </form>

            <div class="progress-bar-container" style="margin-top: 26px; margin-bottom: 0;">
              <div class="progress-header"
                style="display: flex; justify-content: space-between; font-size: 0.8rem; font-family: 'Cyber', sans-serif; color: var(--accent); margin-bottom: 6px; letter-spacing: 0.05em; font-weight: 800;">
                <span id="progress-text">[ PROTOCOLO DE CONEXÃO: ETAPA 1 / 4 ]</span>
                <span id="progress-percent">25%</span>
              </div>
              <div class="progress-track"
                style="height: 5px; background: rgba(255, 255, 255, 0.04); width: 100%; border: 1px solid rgba(0, 243, 255, 0.1); border-radius: 2px; overflow: hidden; position: relative;">
                <div class="progress-fill" id="cyber-progress-fill"
                  style="height: 100%; width: 25%; background: var(--accent); box-shadow: 0 0 8px var(--accent); transition: width 0.35s cubic-bezier(0.25, 0.8, 0.25, 1);">
                </div>
              </div>
            </div>
          </div>

          <div class="success-screen" id="cyber-success-screen">
            <div class="success-icon">⚡</div>
            <h3>CONEXÃO ESTABELECIDA</h3>
            <p>Redirecionando você para o terminal seguro do WhatsApp...</p>
          </div>

          <div class="modal__glitch" aria-hidden="true">
            <div class="cyber-form" style="opacity: 0.15; margin-top: 0;">
              <p>Carregando protocolo de comunicação...</p>
            </div>
          </div>
        </div>
      </section>
    </div>
  `;
  document.body.insertAdjacentHTML('beforeend', modalHTML);

  // 4. Audio Effects Setup
  const audio = {
    slide: new Audio('https://cdn.freesound.org/previews/367/367997_6512973-lq.mp3'),
    accept: new Audio('https://cdn.freesound.org/previews/220/220166_4100837-lq.mp3'),
    reject: new Audio('https://cdn.freesound.org/previews/657/657950_6142149-lq.mp3')
  };

  const playAudio = (sound) => {
    try {
      if (audio[sound]) {
        audio[sound].currentTime = 0;
        audio[sound].play().catch(() => {});
      }
    } catch (e) {}
  };

  // Get DOM Elements
  const popover = document.getElementById('cyber-form-popover');
  const leadForm = document.getElementById('cyber-lead-form');
  const successScreen = document.getElementById('cyber-success-screen');
  const formContainer = document.getElementById('cyber-form-container');
  const phoneInput = document.getElementById('lead-phone');
  const steps = document.querySelectorAll('.cyber-step');
  const nextButtons = document.querySelectorAll('.btn-next-step');
  const prevButtons = document.querySelectorAll('.btn-prev-step');
  const progressFill = document.getElementById('cyber-progress-fill');
  const progressText = document.getElementById('progress-text');
  const progressPercent = document.getElementById('progress-percent');

  let currentStep = 1;
  let currentSource = 'atendimento';

  // Input phone mask formatting
  if (phoneInput) {
    phoneInput.addEventListener('input', (e) => {
      let x = e.target.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,5})(\d{0,4})/);
      if (!x) return;
      e.target.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
    });
  }

  function showStep(stepNumber) {
    currentStep = stepNumber;
    const percent = stepNumber * 25;
    if (progressFill) progressFill.style.width = percent + '%';
    if (progressPercent) progressPercent.textContent = percent + '%';
    if (progressText) progressText.textContent = `[ PROTOCOLO DE CONEXÃO: ETAPA ${stepNumber} / 4 ]`;

    steps.forEach(step => {
      const stepNum = parseInt(step.getAttribute('data-step'));
      if (stepNum === stepNumber) {
        step.classList.add('active');
        step.style.display = 'block';
        const input = step.querySelector('input, textarea');
        if (input) {
          setTimeout(() => input.focus(), 150);
        }
      } else {
        step.classList.remove('active');
        step.style.display = 'none';
      }
    });
  }

  function validateStep(stepNumber) {
    if (stepNumber === 1) {
      const val = document.getElementById('lead-name').value.trim();
      const err = document.getElementById('error-name');
      if (!val) {
        if (err) err.style.display = 'block';
        return false;
      }
      if (err) err.style.display = 'none';
      return true;
    }
    if (stepNumber === 2) {
      const val = document.getElementById('lead-phone').value.replace(/\D/g, '');
      const err = document.getElementById('error-phone');
      if (val.length < 10 || val.length > 11) {
        if (err) err.style.display = 'block';
        return false;
      }
      if (err) err.style.display = 'none';
      return true;
    }
    if (stepNumber === 3) {
      const val = document.getElementById('lead-email').value.trim();
      const err = document.getElementById('error-email');
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(val)) {
        if (err) err.style.display = 'block';
        return false;
      }
      if (err) err.style.display = 'none';
      return true;
    }
    if (stepNumber === 4) {
      const val = document.getElementById('lead-infra').value.trim();
      const err = document.getElementById('error-infra');
      if (!val) {
        if (err) err.style.display = 'block';
        return false;
      }
      if (err) err.style.display = 'none';
      return true;
    }
    return true;
  }

  function triggerStepGlitch() {
    const mainBody = document.querySelector('.modal__body');
    if (mainBody) {
      mainBody.style.animation = 'glitch 0.25s 1';
      setTimeout(() => { mainBody.style.animation = ''; }, 250);
    }
  }

  nextButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      const step = parseInt(btn.getAttribute('data-step'));
      if (validateStep(step)) {
        triggerStepGlitch();
        playAudio('slide');
        showStep(step + 1);
      } else {
        playAudio('reject');
      }
    });
  });

  prevButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      const step = parseInt(btn.getAttribute('data-step'));
      triggerStepGlitch();
      playAudio('slide');
      showStep(step - 1);
    });
  });

  // Glitch loop background animation
  let glitched = false;
  let glitchClock;
  const modalGlitch = document.querySelector('.modal__glitch');

  const kickOff = () => {
    glitchClock = setTimeout(() => {
      if (modalGlitch) {
        modalGlitch.style.setProperty('animation-name', 'glitch');
        requestAnimationFrame(async () => {
          await Promise.allSettled(
            modalGlitch.getAnimations().map((a) => a.finished)
          );
          glitched = true;
          modalGlitch.style.removeProperty('animation-name');
          kickOff();
        });
      }
    }, !glitched ? 1500 : Math.random() * 10000 + 2000);
  };

  if (popover) {
    popover.addEventListener('toggle', (event) => {
      if (event.newState === 'open') {
        playAudio('slide');
        kickOff();
        if (leadForm) leadForm.reset();
        showStep(1);
        document.querySelectorAll('.cyber-error-msg').forEach(el => el.style.display = 'none');
        if (formContainer) formContainer.style.display = 'block';
        if (successScreen) successScreen.style.display = 'none';
      } else {
        if (glitchClock !== undefined) clearTimeout(glitchClock);
        glitched = false;
      }
    });
  }

  // Intercept all atendimento links to open the modal popover
  document.addEventListener('click', (e) => {
    const target = e.target.closest('a');
    if (target && target.href && (target.href.includes('atendimento.agenciacyborg.com') || target.href.includes('atendimento.agenciacyborg.com/'))) {
      e.preventDefault();
      
      // Get source context from url path/title
      const pathname = window.location.pathname.split('/').pop() || 'index.html';
      const cleanPath = pathname.replace('.html', '').toUpperCase();
      const parentSection = target.closest('section');
      const sectionName = parentSection ? parentSection.id : 'contato';
      currentSource = `${cleanPath} - Seção ${sectionName.toUpperCase()}`;

      if (popover) {
        try {
          popover.showPopover();
        } catch (err) {
          // Fallback if browser doesn't support popovers fully
          popover.style.display = 'block';
          popover.style.opacity = '1';
        }
      }
    }
  });

  // Handle Form Submission
  if (leadForm) {
    leadForm.addEventListener('submit', (e) => {
      e.preventDefault();
      if (!validateStep(4)) {
        playAudio('reject');
        return;
      }
      
      const name = document.getElementById('lead-name').value;
      const phone = document.getElementById('lead-phone').value;
      const email = document.getElementById('lead-email').value;
      const infra = document.getElementById('lead-infra').value;

      // Dispatch to SMTP lead.php
      fetch("base/api/lead.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          name: name,
          phone: phone,
          email: email,
          infra: infra,
          source: currentSource
        })
      }).catch(err => console.error("Error dispatching lead to SMTP backend:", err));

      playAudio('accept');
      
      if (formContainer) formContainer.style.display = 'none';
      if (successScreen) successScreen.style.display = 'flex';

      const text = `Olá Cyborg! Acabo de preencher o formulário no site:
*Nome:* ${name}
*WhatsApp:* ${phone}
*E-mail:* ${email}
*Infra / Dúvidas:* ${infra}
*Origem:* Site Principal - ${currentSource}`;

      const phoneCyborg = '5535997286067';
      const waUrl = `https://wa.me/${phoneCyborg}?text=${encodeURIComponent(text)}`;

      setTimeout(() => {
        if (popover) {
          try {
            popover.hidePopover();
          } catch (err) {
            popover.style.display = 'none';
            popover.style.opacity = '0';
          }
        }
        window.open(waUrl, '_blank');
      }, 1200);
    });
  }

  // Keyboard shortcut listener for Enter on steps
  window.addEventListener('keydown', (e) => {
    if (popover && popover.matches && popover.matches(':popover-open')) {
      if (e.key === 'Enter') {
        const activeEl = document.activeElement;
        if (activeEl && (activeEl.tagName === 'INPUT' || activeEl.tagName === 'TEXTAREA')) {
          if (currentStep < 4) {
            e.preventDefault();
            const nextBtn = document.querySelector(`.cyber-step[data-step="${currentStep}"] .btn-next-step`);
            if (nextBtn) nextBtn.click();
          }
        }
      }
    }
  });

  // --- Flying Lottie Robot (Robozin) Implementation ---
  setTimeout(() => {
    // 1. Create and inject the robot HTML elements
    const robotContainer = document.createElement('div');
    robotContainer.id = 'lottie-robot-container';
    robotContainer.innerHTML = `
      <div id="lottie-robot-speech">
        <strong>Podemos te<br>ajudar?</strong>
      </div>
      <div id="lottie-robot-body"></div>
    `;
    document.body.appendChild(robotContainer);

    // 2. Load the Lottie script dynamically if not present, then initialize
    const initLottieRobot = () => {
      if (typeof lottie !== 'undefined') {
        lottie.loadAnimation({
          container: document.getElementById('lottie-robot-body'),
          renderer: 'svg',
          loop: true,
          autoplay: true,
          path: 'robozin.json'
        });
      }
    };

    if (typeof lottie === 'undefined') {
      const script = document.createElement('script');
      script.src = 'https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js';
      script.onload = initLottieRobot;
      document.head.appendChild(script);
    } else {
      initLottieRobot();
    }

    // 3. Position and flying/walking movement logic
    // Start off-screen right
    let currentX = window.innerWidth + 200;
    let currentY = window.innerHeight - 290;

    // Apply off-screen initial position
    robotContainer.style.left = currentX + 'px';
    robotContainer.style.top = currentY + 'px';

    // Fade in and slide in from right to left
    setTimeout(() => {
      robotContainer.style.opacity = '1';
      
      // Target starting position on-screen (e.g. bottom-right area)
      const targetX = window.innerWidth - 240;
      const targetY = window.innerHeight - 290;

      // Face left since it is moving from right to left
      const robotBody = document.getElementById('lottie-robot-body');
      if (robotBody) {
        robotBody.style.transform = 'scaleX(-1)';
      }

      robotContainer.style.left = targetX + 'px';
      robotContainer.style.top = targetY + 'px';

      currentX = targetX;
      currentY = targetY;
    }, 100);

    const moveRobot = () => {
      // Pause movement if the lead capture popover is open
      const popover = document.getElementById('cyber-form-popover');
      if (popover && popover.matches && popover.matches(':popover-open')) {
        return;
      }

      // Safe boundaries padding
      const padding = 120;
      const targetX = Math.random() * (window.innerWidth - 260) + padding;
      const targetY = Math.random() * (window.innerHeight - 260) + padding;

      const robotBody = document.getElementById('lottie-robot-body');
      if (robotBody) {
        if (targetX < currentX) {
          robotBody.style.transform = 'scaleX(-1)'; // Flip left
        } else {
          robotBody.style.transform = 'scaleX(1)';  // Face right
        }
      }

      robotContainer.style.left = targetX + 'px';
      robotContainer.style.top = targetY + 'px';

      currentX = targetX;
      currentY = targetY;
    };

    // Make the robot move to a new random location every 7 seconds
    const robotInterval = setInterval(moveRobot, 7000);

    // 4. Click interaction: open the cyberpunk lead capture modal
    robotContainer.addEventListener('click', (e) => {
      const popover = document.getElementById('cyber-form-popover');
      if (popover) {
        const pathname = window.location.pathname.split('/').pop() || 'index.html';
        const cleanPath = pathname.replace('.html', '').toUpperCase();
        currentSource = `${cleanPath} - ROBOZIN`;

        try {
          popover.showPopover();
        } catch (err) {
          popover.style.display = 'block';
          popover.style.opacity = '1';
        }
      }
    });

    // 5. Speech bubble text rotation loop (every 5 seconds)
    const phrases = [
      "Bora automatizar<br>seu negócio?",
      "Sua operação ainda<br>é 100% manual?",
      "Atendentes com IA<br>que vendem 24h!",
      "Perdendo leads por<br>falta de automação?",
      "Seu site não vende?<br>Nós resolvemos!",
      "Domine o Google ADS<br>e fature muito mais!",
      "Coloque inteligência<br>artificial no Whats!",
      "Sites ultra rápidos<br>que geram leads!",
      "Automações que compram<br>tempo para você!",
      "Pronto para escalar<br>com sistemas de IA?"
    ];

    let phraseIndex = 0;
    const speechTextStrong = robotContainer.querySelector('#lottie-robot-speech strong');

    const rotatePhrases = () => {
      // Pause phrase switching if lead capture popover is open
      const popover = document.getElementById('cyber-form-popover');
      if (popover && popover.matches && popover.matches(':popover-open')) {
        return;
      }

      const speechBubble = document.getElementById('lottie-robot-speech');
      if (speechBubble && speechTextStrong) {
        speechBubble.style.transition = 'opacity 0.3s ease';
        speechBubble.style.opacity = '0';

        setTimeout(() => {
          phraseIndex = (phraseIndex + 1) % phrases.length;
          speechTextStrong.innerHTML = phrases[phraseIndex]; // Render `<br>` as HTML
          speechBubble.style.opacity = '1';
        }, 300);
      }
    };

    const phraseInterval = setInterval(rotatePhrases, 5000);

  }, 3000); // Trigger after 3 seconds of page load
});