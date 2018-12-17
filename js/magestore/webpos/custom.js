$(document).on('change','.extra_div input',function() {
  //  alert(jQuery('input[name=guest_checkout]:checked').val());
  var sygf = $('input[name=guest_checkout]:checked').attr('value');
  alert(sygf);
});
