//FILTER

var currentFilter;

function filter(category) {
    var clicked, filters, books, i, len;
    clicked = document.getElementById(category);

    if (currentFilter == category) {
        currentFilter = null;
        clicked.classList.remove('active');
        removeAllFilter();
        return false;
    }

    currentFilter = category;

    filters = document.querySelectorAll('.filter');
    for (i = 0, len = filters.length; i < len; i++) {
        if (filters[i].classList.contains('active')) {
            filters[i].classList.remove('active')
        }
    }

    clicked.classList.add('active');

    books = document.querySelectorAll('.book');
    for (i = 0, len = books.length; i < len; i++) {
        if (books[i].classList.contains('hide'))
            books[i].classList.remove('hide');
        if (!books[i].classList.contains(category) && !books[i].classList.contains('hide'))
            books[i].classList.add('hide');
    }
}

function removeAllFilter() {
    var books;

    books = document.querySelectorAll('.book');
    for (var i = 0, len = books.length; i < len; i++) {
        if (books[i].classList.contains('hide'))
            books[i].classList.remove('hide');
    }
}

//Form Validation

function validateForm() {
    var inputs = document.querySelectorAll('input,textarea');

    for (var i = 0; i<inputs.length; i++) {
        if (!inputs[i].value && inputs[i].getAttribute('data-required') == "true"){
            alert("Bitte füllen Sie alle mit * gekennzeichneten Felder aus!");
            return false;
        }
    }
}

//Cart
function checkAndSubmit(el, form) {
    var is, max;
    is = el.value;
    max = el.getAttribute("data-maxamount");

    if (is == "") {
        return false;
    } else if (is < 1) {
        el.value = 1;
        return false;
    } else if (parseInt(is) > parseInt(max)) {
        alert("Leider ist das Produkt nicht mehr in der gewünschten Menge vorhanden!");
        el.value = max;
    }
    document.getElementById(form).submit();
}

//ProductPage

function checkMax(selectfield) {
    var is, max;
    is = selectfield.options[selectfield.selectedIndex].value;
    max = selectfield.getAttribute("data-maxamount");

    if (parseInt(is) > parseInt(max)) {
        alert("Leider ist das Produkt nicht mehr in der gewünschten Menge verfügbar!");
        selectfield.selectedIndex = max - 1;
    }
}