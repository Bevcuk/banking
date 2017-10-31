var EditForm = function (selector, url) {
    this.selector = selector;
    this.url = url;
}

EditForm.prototype.success = function(){
}

EditForm.prototype.warning = function (errors) {
    $('.error', this.selector).hide();
    
    $.each(errors, function(index, value ) {
        var li = $('<li>', {text: value});
        $('.validation ul', this.selector).append(li);
    });

    $('.validation', this.selector).show();
}

EditForm.prototype.error = function(){
    $('.validation', this.selector).hide();
    $('.error', this.selector).show();
}

EditForm.prototype.bindSubmit = function() {
    var main = this;
    
    $(main.selector).submit(function(e) {
         var formData = new FormData($(this)[0]);
         
         $.ajax({
             url: main.url,
             type: "POST",
             data: formData,
             async: false,
             success: function (response) {
                 try {                        
                     $('.validation', main.selector).hide();
                     $('.error', main.selector).hide();
                     $('.validation ul', main.selector).html('');
                     response = JSON.parse(response);

                     if (response.success === true) {                                    
                         main.success();
                     } else {
                         main.warning(response.errors);
                     }
                 } catch(ex) {
                     main.error();
                 }
             },
             error: function() {
                main.error();
             },
             cache: false,
             contentType: false,
             processData: false
         });

         e.preventDefault();
     }); 
 }



