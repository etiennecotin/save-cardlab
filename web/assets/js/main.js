$(document).ready(function(){
  $('#dossier').click(function(){
    $('.regles').show();
    $('.regles #fermeture').click(function(){
      $('.regles').hide();
    });
  });


  $('.carte').click(function(){
    var numero = $(this).data('modal');
    $('.modal[data-modal="'+numero+'"]').show();
    $('.modal[data-modal="'+numero+'"] #fermeture').click(function(){
      $('.modal[data-modal="'+numero+'"]').hide();
    })
  });

setInterval(function(){
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

  $('.bouton_chat h2').click(function(){
    $('.bloc_chat').slideDown();
    $('.bouton_chat #fermeture').show();
  });
  $('.chat #fermeture').click(function(){
    $('.bloc_chat').slideUp();
    $('.bouton_chat #fermeture').hide();
  });
},50);



});
