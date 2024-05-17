
function onSubmitDisplaySpinner(formId)
{
  const inputs = jQuery('#'+formId+' [required]')

  console.log(inputs)

  var allFieldsFilled = true
  jQuery(inputs).each(function(i, input){
    if(jQuery(input).val() == ''){
      allFieldsFilled = false;
    }
  });
  
  if(false != allFieldsFilled){
    jQuery('#'+formId+' .btn').addClass('d-none');
    jQuery('#'+formId+' .spinner-border').removeClass('d-none');
  }
}
