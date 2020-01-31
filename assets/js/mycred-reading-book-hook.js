jQuery(document).ready(function ($) {

    setTimeout(() => {
        $('div[class*=real3dflipbook]').find('a').on('click', function () {
            let bookId = $(this).parent().attr('class');
            window.bookId = extractBookId(bookId);
            console.log($(this).parent().attr('class'));

        });

        function extractBookId(str) {
            return str.substring(
                str.lastIndexOf("-") + 1,
                str.lastIndexOf("_")
            );
        }

        function extractTotalPages(val) {
            let totalPageNumbers = '';

            if ((val.length === 6) || (val.length === 7)) {
                totalPageNumbers = val.substring(4, 10);
            } else if (val.length === 8) {
                totalPageNumbers = val.substring(5, 10);
            } else if (val.length === 10) {
                totalPageNumbers = val.substring(7, 10);
            }
            return totalPageNumbers;
        }

        function extractCurrentPage(val) {
            let currentPageNumber = '';

            if ((val.length === 6) || (val.length === 8)) {
                currentPageNumber = val.substring(0, 1);
            } else if ((val.length === 10) || (val.length === 7)) {
                currentPageNumber = val.substring(0, 2);
            }
            return currentPageNumber;
        }

        let object_interval = 0;
        let page_interval = 0;

        function timer(start = false) {
            let start_date = new Date;

            if (start === false) {
                clearInterval(object_interval);
                page_interval = 0;
                return 0;
            }

            object_interval = setInterval(function () {
                page_interval = Math.floor((new Date - start_date) / 1000);
            }, 1000);
        }

        let bookPages = '';
        let totalPages = '';
        let readPages = '';

        let bookBlock = document.querySelector('.flipbook-overlay');

        if (bookBlock == null) return;

        let previousValue = bookBlock.style.display;

        let observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (mutation) {
                if (mutation.attributeName !== 'style') return;

                let currentValue = mutation.target.style.display;

                if (currentValue !== previousValue) {
                    if (previousValue === "none" && currentValue !== "none") {
                        timer(true);
                    } else {
                        if (!window.hasOwnProperty('bookTitle')) window.bookTitle = "The book title is not assigned";
                        if (!window.hasOwnProperty('bookId')) window.bookId = "The book id is not assigned";


                        bookPages = document.querySelector('.flipbook-currentPageNumber');
                        totalPages = extractTotalPages($(bookPages).text());
                        readPages = extractCurrentPage($(bookPages).text());


                        let book_data = {
                            'total_pages': totalPages,
                            'read_pages': readPages,
                            'page_interval': page_interval,
                            'book_title': window.bookTitle,
                            'book_id': window.bookId,
                        };

                        let data = {
                            'action': 'reading_book',
                            'token': myCREDbook.token,
                            'ctype': 'mycred_default',
                            book_data: book_data
                        };

                        if ((totalPages !== '') && (readPages !== '')) {

                            $.ajax({
                                type: "post",
                                dataType: "json",
                                url: myCREDbook.ajaxurl,
                                data: data,
                                success: function (response) {
                                    console.log(
                                        "أسم الكتاب :" + response.book_title + "\n" +
                                        "الرقم التعريفى :" + response.book_id + "\n" +
                                        "الصفحات الكلية :" + response.total_pages + "\n" +
                                        "الصفحات المقروءه :" + response.read_pages + "\n" +
                                        "زمن المستغرق فى القراءة :" + response.book_interval + "\n" +
                                        "النقاط المستحقة :" + response.eared_points + "\n"
                                    );
                                },
                                error: function (response) {
                                    console.log(response);
                                },
                                complete: function () {
                                    window.bookId = '';
                                    window.bookTitle = '';
                                    totalPages = 0;
                                    readPages = 0;
                                    page_interval = 0;
                                    timer(false);
                                    location.reload();
                                }
                            });
                        }
                    }

                    previousValue = mutation.target.style.display;
                }
            });
        });
        observer.observe(bookBlock, {attributes: true});
    }, 100);

});

