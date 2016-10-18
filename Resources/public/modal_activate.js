/*!
* jQuery modal activation
*
* Copyright 2016, Slad
*/

(function ($) {
   
        // gestion du remplissage et de l'ouverture de la fenetre modal 
        $('#dayModal').on('show.bs.modal', function (event) {           
            var button = $(event.relatedTarget); 

            var recipient = button.data('whatever'); 
            result 	= recipient.split("--");
            var modal = $(this);
            url =  Routing.generate('day_index', { _locale: locale }),
            $.ajax({
                type : 'POST',
                data : { date: result[0], 
                         item: result[1],
                         project: result[2]
                         },
                url : url,
               success : function(code_html, statut){ 
                   modal.find('.modal-body').html(code_html);
                   code_html='';
               },
               error : function(resultat, statut, erreur){
                   alert('error');  // messsage a personnaliser
               }
            });
            var mes = Translator.trans('booking_date', {}, 'SladBookingBundle');
            modal.find('#dayModalLabel').text(mes + ' ' + result[0]);
        });

})(jQuery);
