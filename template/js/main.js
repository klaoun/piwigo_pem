
function onSubmitDisplaySpinner(formId)
{
  const inputs = jQuery('#'+formId+' [required]')
  if(jQuery(inputs).val() !== ''){
    jQuery('#'+formId+' .btn').addClass('d-none');
    jQuery('#'+formId+' .spinner-border').removeClass('d-none');
  }
}
