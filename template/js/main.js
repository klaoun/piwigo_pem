
function onSubmitDisplaySpinner(formId)
{
  jQuery('#'+formId+' .btn').addClass('d-none');
  jQuery('#'+formId+' .spinner-border').removeClass('d-none');
}
