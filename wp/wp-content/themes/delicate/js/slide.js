(function($) {
  var slides = [
    {
      "artist": "Живопись", 
      "image": "/wp-content/uploads/2009/10/paint.jpg", 
      "publisher": "", 
      "title": "Конкурс живописи"
    }, 
    {
      "artist": "Графика и Дизайн", 
      "image": "/wp-content/uploads/2009/10/graph.jpg", 
      "publisher": "", 
      "title": "Конкурс графики"
    }, 
    {
      "artist": "Фотография", 
      "image": "/wp-content/uploads/2009/10/photo.jpg", 
      "publisher": "", 
      "title": "Конкурс фотографии"
    }, 
    {
      "artist": "Скульптура", 
      "image": "/wp-content/uploads/2009/10/sculpt.jpg", 
      "publisher": "", 
      "title": "Конкурс скульптуры"
    }, 
    {
      "artist": "Декоративно-прикладное искусство", 
      "image": "/wp-content/uploads/2009/10/dpi.jpg", 
      "publisher": "", 
      "title": "Современное искусство"
    }
  ];//end

  function shuffle(array) {
    var tmp, current, top = array.length;

    if(top) while(--top) {
        current = Math.floor(Math.random() * (top + 1));
        tmp = array[current];
        array[current] = array[top];
        array[top] = tmp;
    }

    return array;
  }
  slides = shuffle(slides);
  
  function nextSlide($container) {
    var $slides = $container.children();
    var $active = $slides.filter('.active');
    var $next =  $active.next().length ? $active.next() : $slides.eq(0);
    
    $active.addClass('last-active');
    $next.css({opacity: 0.0})
      .addClass('active')
      .animate({opacity: 1.0}, 1000, function() {
          $active.removeClass('active last-active').css('display', '');
      });
    
    //preload the slide after the one that fades in
    var position = $slides.index($next);
    if(position < slides.length-1 && $next.next().length == 0) {
      $container.append(createSlide(position+1));
    }
  }
  
  function createSlide(index) {
    var slide = slides[index];
    return $("<div class='galleryItem'></div>")
      .append(
        $("<div class='galleryImage'></div>")
          .css('background-image', 'url(' + slide['image'] + ')'),
        "<div class='galleryCaptionFold'></div>",
        $("<div class='galleryCaption'></div>").append(
          $("<div class='artist'></div>").text(slide['artist']),
          $("<div class='title'></div>").text(slide['title']),
          $("<div class='publisher'></div>").text(slide['publisher'])
        )
      ).show().css('opacity', 0);
  }
  
  $(function() {
    //initialize the slideshow
    var container = $('#featuredArt').empty().append(createSlide(0).css('opacity', 1), createSlide(1));
    
    $('#featuredArt').children()
      .filter(':first').addClass('active') //show the first slide
      .next().show();                      //and preload the next
    
    setInterval(function() { nextSlide(container) }, 5000);
  });
})(jQuery);