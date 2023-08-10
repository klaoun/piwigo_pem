jQuery("#edit_mode").change(function() {
  jQuery('.edit_mode').toggle();
});

// Selectize modal inputs
jQuery('.extension_author_select').selectize({
  plugins: ["remove_button"],
})

jQuery('.extension_tag_select').selectize({
  plugins: ["remove_button"],
})

jQuery('.extension_lang_desc_select').selectize({
  plugins: ["remove_button"],
})

jQuery('.extension_category_select').selectize();
