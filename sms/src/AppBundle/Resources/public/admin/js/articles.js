function showArticleCategoryModal(element){
  $.ajax({
      url: $(element).attr('href'),
      success: function(data){
        $('#showModalBody').html(data.html);
        $('#showModalTitle').html(data.title);
        $('#showModal').modal('show');
      }, 
      complete: function()
      {
      }
  });

  return false;  
}

function saveArticleCategory(form){
    $.ajax({
      url: $(form).attr('action'),
      data: $(form).serialize(),
      type: 'post',
      dataType: 'json',
      success: function(json){
          if(json.result)
          {
            toastr.info(json.message);
            if(!json.update){
                $('#article_categories_table_body').append(json.html);
            }else{
                $('#article_category_row_'+json.id).replaceWith(json.html);
            }
            $('#showModal').modal('hide');
          }
          else
          {
              toastr.error(json.message);
          }
          
          
      }
      , 
      complete: function()
      {
      }
  });
  return false; 
}

function removeArticleCategory(element, confirmation){
  if(confirm(confirmation))
  {
    $.ajax({
      url: $(element).attr('href'),
      type: 'post',
      dataType: 'json',
      success: function(json){
        if(json.result){
            toastr.info(json.message);
            $('#article_category_row_'+json.id).remove();
        }else{
            toastr.error(json.message);
        }
      }, 
      complete: function()
      {
      }
    });
  }
  

  return false;  
}