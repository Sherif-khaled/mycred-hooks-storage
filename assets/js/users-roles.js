jQuery(document).ready(function ($) {
    books = $('div[class*=real3dflipbook]');
    books.forEach(function (book) {
        book.css('display','none');
    });
}