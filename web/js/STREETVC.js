


// Gallery
$(function(){

      "use strict";
	  
      var $container = $('#gallery-items');

      $container.isotope({
        itemSelector : '.element'
      });
      
      
      var $optionSets = $('#options .option-set'),
          $optionLinks = $optionSets.find('a');

      $optionLinks.click(function(){
        var $this = $(this);
        // don't proceed if already selected
        if ( $this.hasClass('selected') ) {
          return false;
        }
        var $optionSet = $this.parents('.option-set');
        $optionSet.find('.selected').removeClass('selected');
        $this.addClass('selected');
  
        // make option object dynamically, i.e. { filter: '.my-filter-class' }
        var options = {},
            key = $optionSet.attr('data-option-key'),
            value = $this.attr('data-option-value');
        // parse 'false' as false boolean
        value = value === 'false' ? false : value;
        options[ key ] = value;
        if ( key === 'layoutMode' && typeof changeLayoutMode === 'function' ) {
          // changes in layout modes need extra logic
          changeLayoutMode( $this, options )
        } else {
          // otherwise, apply new options
          $container.isotope( options );
        }
        
        return false;
      });

      
    });



//EasyChart

    var initCharts = function() {
	
	"use strict";
	
      var charts = $('.percentage');
      charts.easyPieChart({
        animate: 1400,
        onStep: function(value) {
          this.$el.find('span').text(~~value);
        }
      });
      $('.updatePieCharts').on('click', function(e) {
        e.preventDefault();
        charts.each(function() {
          $(this).data('easyPieChart').update(Math.floor(100*Math.random()));
        });
      });
    }

	
// DL Menu

	$(function() {
	
	"use strict";
	
		$( '#dl-menu' ).dlmenu();
	});

	

// Query Loader 2

$(document).ready(function () {
	
	"use strict";

    $("body").queryLoader2({
        barColor: "#233548",
        backgroundColor: "#fff",
        percentage: true,
        barHeight: 1,
        completeAnimation: "grow",
        minimumTime: 0
    });
});

// Scroll

jQuery(document).ready(function ($) {


    $(window).stellar();

	"use strict";
	
    var links = $('.navigation').find('li');
    slide = $('.slide');
    button = $('.button');
    mywindow = $(window);
    htmlbody = $('html,body');


    slide.waypoint(function (event, direction) {

        dataslide = $(this).attr('data-slide');

        if (direction === 'down') {
            $('.navigation a[data-slide="' + dataslide + '"]').addClass('active').prev().removeClass('active');
        }
        else {
            $('.navigation li[data-slide="' + dataslide + '"]').addClass('active').next().removeClass('active');
        }

    });
 
    mywindow.scroll(function () {
	
		"use strict";
	
        if (mywindow.scrollTop() == 0) {
            $('.navigation li[data-slide="1"]').addClass('active');
            $('.navigation li[data-slide="2"]').removeClass('active');
        }
    });

    function goToByScroll(dataslide) {
        htmlbody.animate({
            scrollTop: $('.slide[data-slide="' + dataslide + '"]').offset().top
        }, 1000, 'easeInOutQuint');
    }



    links.click(function (e) {
        e.preventDefault();
        dataslide = $(this).attr('data-slide');
        goToByScroll(dataslide);
    });

    button.click(function (e) {
        e.preventDefault();
        dataslide = $(this).attr('data-slide');
        goToByScroll(dataslide);

    });


});




