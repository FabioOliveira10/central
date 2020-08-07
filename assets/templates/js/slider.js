/*
 * jQuery Sliders plugin
 * Copyright (c) 2010 xThemes
 * Version: 1.0 (19-NOV-2010)
 */

(function($) {

		$.fn.sliders = function(options) {
	
		var defaults = {
		animationSpeed : 500,
		cycleInterval : 5000,
		slideWidth : 1000,
		cycle : true,
		bulletNavigationWrapper : 'div.sliderdots div.dots',
		arrowNavigationWrapper : 'div.sliderdots div.dots',
		sliderdots : false,
		projectSlider : false
		}, 
		settings = $.extend({}, defaults, options);
		
		// return each slider instance
		return this.each(function() {
		
		// save slider in var
		var $this = $(this); 
		
		var i = 0;
		var intervalID = 1;
		var intervalAnimate = settings.slideWidth;
		var click = true;
		
			// Loop through slides 	
			$this.find('li').each(function() {
				
				// Add ID to each slide
				$(this).attr('id', 'slide-' + i + '');
				
				// If there are slider dots
				if(settings.sliderdots) {
					
					// Append dots
					$(settings.bulletNavigationWrapper).append('<a href="#" id="' + i + '" title=""></a>');
				
				}
				
				i++;
				
			});
			
			// Give the slider ul the total width of all it's children
			$this.find('ul').css('width', i * settings.slideWidth);
			
			// Give the slider ul an id of zero
			$this.find('ul').attr('id', 0);
			
			// If the slider uses navigation dots
			if(settings.sliderdots) {
				
				// If there is a bullet navigation, add 'current' class to first child
				$(settings.bulletNavigationWrapper).children('a:first-child').addClass('current');
				
				// When a bullet navigation link is clicked
				$this.parent().find(settings.bulletNavigationWrapper).children('a').click(function(e) {
					
					// If there is still some animation going on, stop it
					if(!click) {
					
						return false;
					
					}
					
					// Set 'stop multiple animation' var
					click = false;
					
					// stop normal link behaviour					
					e.preventDefault();
					
					// If cycling is activated, stop the cycle
					if(settings.cycle) {
					
						clearInterval($this.interval);
					
					}
					
					// If the link in the bullet navigation is not an arrow link
					if($(this).attr('class') !== 'leftarrow' && $(this).attr('class') !== 'rightarrow') {
						
						// get id
						id = $(this).attr('id');
						
						
						animate = 0;
						
						// animate width = id multiplied by the slide width
						animate = $(this).attr('id') * settings.slideWidth;
						
						// call slide animation function
						slideIt(id, animate);
			
					}
		
				});
			
			}
			
			// append arrows
			$(settings.arrowNavigationWrapper).append('<a href="#" title="" class="rightarrow"></a>');
			$(settings.arrowNavigationWrapper).prepend('<a href="#" title="" class="leftarrow"></a>');
			
			// If clicked on one of the arrows
			$this.parent().find('a.rightarrow, a.leftarrow').click(function(e) {
				
					// If there is still some animation going on, stop it
					if(!click) {
					
						return false;
					
					}
					
					// Set 'stop multiple animation' var
					click = false;
				
				// stop normal link behaviour
				e.preventDefault(); 
				
				// reset animate var
				animate = 0;
				
				// If cycling is activated, stop the cycle
				if(settings.cycle) {
				
					clearInterval($this.interval);
				
				}
			
				// If clicked on rightarrow
				if($(this).attr('class') == 'rightarrow') {
					
					// Get the current activated ID, if it is the last id
					if(parseInt($this.find('ul').attr('id')) == i - 1) {
						
						// start at zero again
						animate = 0;
						id = 0;
												
					} else {
						
						// If we are currently not on the last slide, get 'position: left' + add the width of a slide
						// And integer from positive/negative and pixels 
						animate = Math.abs(parseInt($this.find('ul').css('left'))) + settings.slideWidth;
						
						// Id = current activated ID + 1
						id = parseInt($this.find('ul').attr('id')) + 1;
							
					}
					
					// If we are on a instance of the projectslider
					if(settings.projectSlider) {
						
						// Because we show 3 slides per animation, we need to calculate amount of slides - 2		
						if(parseInt($this.find('ul').attr('id')) == i - 3) {
							
							// If so, start at zero again
							animate = 0;
							id = 0;
												
						} 
					
					}
				
				} else {
					// If clicked on left arrow
					// If current ID = 0
					if(parseInt($this.find('ul').attr('id')) == 0) {
						
						// get width value of the last slide
						animate = i * settings.slideWidth - settings.slideWidth;
						// reset id to the id of the last slide
						id = i - 1;
						
					} else {
						
						// If current ID is not 0, get 'position: left' value, and subtract slide width
						animate = Math.abs(parseInt($this.find('ul').css('left'))) - settings.slideWidth;
						
						// New ID = current ID - 1
						id = parseInt($this.find('ul').attr('id')) - 1;
							
					}
						
						// If we are on an instance of the project slider
						if(settings.projectSlider) {
							
							// If current ID = 0
							if(parseInt($this.find('ul').attr('id')) == 0) {
								
								// Go to the last slide, but also calculate that the 3 last slides must be showed
								animate = i * settings.slideWidth - settings.slideWidth * 3;
								
								// New ID � current ID - 2
								id = i - 3;
							
							}
						
						}
	
				}
				
				// call slide animation function			
				slideIt(id, animate);
		
			}); // end arrow click function
			
			function slideIt(id, animate) {
				
				// If there is a slider dotted navigation
				if(settings.sliderdots) {
				
					// Delete current class
					$(settings.bulletNavigationWrapper).children('a').removeClass('current');
					
					// Add current class					
					$(settings.bulletNavigationWrapper).find('a#' + id + '').addClass('current');
									
				}
				
				// Reset old ID with new ID
				$this.find('ul').attr('id', id);

				// Make animation
				$this.find('ul').animate({
						
						// Left is new position
						left: '-' + animate + 'px'
						
						// use speeds settings, and use 'easeInCubic' easing
					}, settings.animationSpeed, 'easeInCubic', function() {
						
						// Set 'stop multiple animation' var to true, everything is oke now :)
						click = true;
					
					});
			
			} // End slideIt function
			
			
			function intervalSlider() {
				
				// If we are not on the project slider
				if(!settings.projectSlider) {
					
					// If we are on the last slide
					if(intervalID == i) {
						
						// Reset animate and ID values
						intervalID = 0;
						intervalAnimate = 0;
							
					}
					
				}
				
				// If we are on the project slider				
				if(settings.projectSlider) {
					
					// If we are on the last slide (note that this slider shows the last 3 items at once)
					if(intervalID == i - 2) {
						
						// Reset animate and ID values
						intervalID = 0;
						intervalAnimate = 0;
								
					}
					
				}
				
				// call slide animation function
				slideIt(intervalID, intervalAnimate);
				
				// intervalID + 1
				intervalID++;
				
				// intervalAnimate + Slider width
				intervalAnimate += settings.slideWidth;
	
			} // End intervalSlider function
			
			// If we have activated the cycle option	
			if(settings.cycle) {
				
				// Call the interval function for each instance of this plugin (second param = the times between intervals, which we can setup in the plugin options)
				$this.interval = setInterval(intervalSlider, settings.cycleInterval);
			
			}

		}); // end this each

	}

})(jQuery)