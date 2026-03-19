  (function ($) {
  $.fn.countTo = function (options) {
    options = options || {};    
    return $(this).each(function () {    
      var settings = $.extend({}, $.fn.countTo.defaults, {
        from:            $(this).data('from'),
        to:              $(this).data('to'),
        speed:           $(this).data('speed'),
        refreshInterval: $(this).data('refresh-interval'),
        decimals:        $(this).data('decimals')
      }, options);      
      var loops = Math.ceil(settings.speed / settings.refreshInterval),
        increment = (settings.to - settings.from) / loops;     
      var self = this,
        $self = $(this),
        loopCount = 0,
        value = settings.from,
        data = $self.data('countTo') || {};      
      $self.data('countTo', data);
     
      if (data.interval) {
        clearInterval(data.interval);
      }
      data.interval = setInterval(updateTimer, settings.refreshInterval);
      render(value);      
      function updateTimer() {
        value += increment;
        loopCount++;        
        render(value);        
        if (typeof(settings.onUpdate) == 'function') {
          settings.onUpdate.call(self, value);
        }        
        if (loopCount >= loops) {        
          $self.removeData('countTo');
          clearInterval(data.interval);
          value = settings.to;          
          if (typeof(settings.onComplete) == 'function') {
            settings.onComplete.call(self, value);
          }
        }
      }
      
      function render(value) {
        var formattedValue = settings.formatter.call(self, value, settings);
        $self.html(formattedValue);
      }
    });
  };
  
  $.fn.countTo.defaults = {
    from: 0,               // the number the element should start at
    to: 0,                 // the number the element should end at
    speed: 1000,           // how long it should take to count between the target numbers
    refreshInterval: 100,  // how often the element should be updated
    decimals: 0,           // the number of decimal places to show
    formatter: formatter,  // handler for formatting the value before rendering
    onUpdate: null,        // callback method for every time the element is updated
    onComplete: null       // callback method for when the element finishes updating
  };
  
  function formatter(value, settings) {
    return value.toFixed(settings.decimals);
  }
}(jQuery));

jQuery(function ($) {
  // custom formatting example
  $('.count-number').data('countToOptions', {
  formatter: function (value, options) {
    return value.toFixed(options.decimals).replace(/\B(?=(?:\d{3})+(?!\d))/g, ',');
  }
  });
  
  
  $('.timer').each(count);  
  
  function count(options) {
  var $this = $(this);
  options = $.extend({}, options || {}, $this.data('countToOptions') || {});
  $this.countTo(options);
  }
});

// start all the timers

 $(document).ready(function(){
     $("#list-slider").owlCarousel({
     items:3,
     loop:true,
     margin:10,
     autoplay:true,
     autoplayTimeout:3000,
     responsiveClass:true,
     responsive:{
        0:{
            items:1,
            nav:true
        },
        600:{
            items:3,
            nav:true
        },
        1000:{
            items:3,
            nav:true,
           
        }
    }
     });
  });
 // start all the testimonial

 $(document).ready(function(){
     $("#testimonial").owlCarousel({
     items:3,
     loop:true,
     center:true,
     nav:true,
     margin:10,
     autoplay:true,
     transitionStyle : true,
     autoplayTimeout:5000,
     responsiveClass:true,
     responsive:{
        0:{
            items:1,
            nav:true
        },
        600:{
            items:3,
            nav:true
        },
        1000:{
            items:3,
            nav:true,
           
        }
    }
     });
  });
// start all the back to top
$(function(){
    //$(".chevron-down").
    $("div[data-toggle=collapse]").click(function(){
        $(this).children('span').toggleClass("fa-angle-down fa-angle-up");
    });
})

//back to top button
let mybutton = document.getElementById("btn-back-to-top");

window.onscroll = function () {
  scrollFunction();
};

function scrollFunction() {
  if (
    document.body.scrollTop > 20 ||
    document.documentElement.scrollTop > 20
  ) {
    mybutton.style.display = "block";
  } else {
    mybutton.style.display = "none";
  }
}
mybutton.addEventListener("click", backToTop);

function backToTop() {
  document.body.scrollTop = 0;
  document.documentElement.scrollTop = 0;
}