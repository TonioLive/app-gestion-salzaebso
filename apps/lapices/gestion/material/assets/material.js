(function () {
    'use strict';

    document.addEventListener('click', function (event) {
        var button = event.target.closest('[data-quantity-step]');
        if (!button) {
            return;
        }
        var target = document.getElementById(button.getAttribute('data-target'));
        if (!target) {
            return;
        }
        var step = parseFloat(button.getAttribute('data-quantity-step')) || 0;
        var value = parseFloat(target.value) || 0;
        target.value = Math.max(0, value + step);
        target.dispatchEvent(new Event('change', {bubbles: true}));
    });

    var search = document.querySelector('[data-material-search]');
    if (search) {
        search.addEventListener('input', function () {
            var term = search.value.toLowerCase().trim();
            document.querySelectorAll('[data-material-card]').forEach(function (card) {
                card.hidden = term !== ''
                    && card.textContent.toLowerCase().indexOf(term) === -1;
            });
        });
    }

    var deliverySearch = document.querySelector('[data-delivery-search]');
    if (deliverySearch) {
        deliverySearch.addEventListener('input', function () {
            var term = deliverySearch.value.toLowerCase().trim();
            document.querySelectorAll('[data-delivery-row]').forEach(function (row) {
                row.hidden = term !== ''
                    && row.textContent.toLowerCase().indexOf(term) === -1;
            });
        });
    }
}());
