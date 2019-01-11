
$(function() {

    $(window).load(function() {
        $(".loader-container").hide().fadeOut(1500);
    });
	/*----------  Vegas Slideshow  ----------*/
	
	$(".coffee_slider").vegas({
	    slides: [
	        { src: "img/slider/1.jpg" },
	        { src: "img/slider/2.jpg" },
	        { src: "img/slider/3.jpg" },
	        { src: "img/slider/4.jpg" },
	        { src: "img/slider/5.jpg" },
	        { src: "img/slider/6.jpg" }
	       
	    ],
	    shuffle: true,
	    timer: false,
	    transition: 'random'
	});


	$(".card-list").scrollbar();
	$(".register-area").scrollbar();

	$('.slim').slim();

});
