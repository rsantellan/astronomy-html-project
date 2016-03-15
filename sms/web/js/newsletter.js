function sendNewsletterFormData(form)
{
  $.ajax({
    url: $(form).attr('action'),
    data: $(form).serialize(),
    type: 'post',
    dataType: 'json',
    success: function(json){
      if(json.result == true)
      {
        $('#newsletterform').hide();
        $('#newsletterformMessage').show();
        
      }
      else
      {
        $('#newsletterformContainer').replaceWith(json.html);
      }
              
    }
    , 
    complete: function()
    {

    }
  });
  return false;
}