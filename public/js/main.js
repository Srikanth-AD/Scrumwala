jQuery(document).ready(function($) {
    setTimeout(function(){
        $('.alert-dismissible').fadeOut(1000);
    }, 4000);
    // Set focus on the first input field; exclude the search form in top navbar
    $("form").not(".navbar-form").find("input").filter(":visible").first().focus();
});