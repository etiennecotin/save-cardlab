$(document).ready(function(){
  // $('.regles').hide();
  $('#dossier').click(function(){
    $('.regles').toggle();
  });
  // $('.main_joueur img').draggable({
  //   containment: 'document',
  //   snap: '.drop',
  //   cursor: 'move'
  //   });
  // $('.carte').click(function(){
  //   $(this).detach().appendTo('.temporaire');
  //   $(this).removeClass('carte');
  //   $(this).addClass('carte_active');
  //   $('.main_joueur').css('z-index','1');
  //   $('.overlay').show();
  //   // $('.drop').css('border','1px solid #fff');
  //   if($('.temporaire img').hasClass('carte_active')) {
  //     $('.carte_active').click(function(){
  //       $('.carte_active').detach().appendTo('.main_joueur');
  //       $('.carte_active').removeClass('carte_active').addClass('carte');
  //       $('.overlay').hide();
  //       $('.main_joueur').css('z-index','10');
  //     })
  //     $('.drop').click(function(){
  //       $('.carte_active').detach().appendTo(this);
  //       $('.carte_active').removeClass('carte_active').addClass('carte_jouee');
  //       $('.overlay').hide();
  //       $('.main_joueur').css('z-index','10');
  //     });
  //   };
  // });
setInterval(function(){
  $('.carte').click(function(){
    if($(this).hasClass('carte_trump')){
      $(this).detach().appendTo('#trump .drop').removeClass('carte').removeClass('carte_trump').addClass('carte_jouee');
    };
    if($(this).hasClass('carte_kim')){
      $(this).detach().appendTo('#kim .drop').removeClass('carte').removeClass('carte_kim').addClass('carte_jouee');
    };
    if($(this).hasClass('carte_hollande')){
      $(this).detach().appendTo('#hollande .drop').removeClass('carte').removeClass('carte_hollande').addClass('carte_jouee');
    };
    if($(this).hasClass('carte_elisabeth')){
      $(this).detach().appendTo('#reine .drop').removeClass('carte').removeClass('carte_elisabeth').addClass('carte_jouee');
    };
    if($(this).hasClass('carte_poutine')){
      $(this).detach().appendTo('#poutine .drop').removeClass('carte').removeClass('carte_poutine').addClass('carte_jouee');
    };
  });

  $('.drop').click(function(){
    if($(this).children().length > 0){
      $(this).addClass('drop_show');
      $(this).children().addClass('show');
      $('.overlay').show();
      $('.overlay').click(function(){
        $(this).hide();
        $('.drop').removeClass('drop_show').children().removeClass('show');
      });
    };
  });

  $('.pioche').click(function(){
    if($('.main_joueur').children().length < 8) {
      $('.main_joueur').append('<img class="carte carte_hollande" src="img/cartes/hollande/Hollande_6.png" alt="">')
    }
  });

},300);


});
