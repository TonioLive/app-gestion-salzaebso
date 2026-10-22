(function () {
    'use strict';

    function openDialog(dialog) {
        if (!dialog) return;
        if (typeof dialog.showModal === 'function') {
            dialog.showModal();
        } else {
            dialog.setAttribute('open', 'open');
        }
    }

    function closeDialog(dialog) {
        if (!dialog) return;
        if (typeof dialog.close === 'function') dialog.close();
        else dialog.removeAttribute('open');
    }

    document.addEventListener('click', function (event) {
        var opener = event.target.closest('[data-dialog-open]');
        if (opener) {
            openDialog(document.getElementById(opener.getAttribute('data-dialog-open')));
            return;
        }

        var closer = event.target.closest('[data-dialog-close]');
        if (closer) {
            closeDialog(closer.closest('dialog'));
            return;
        }

        var movement = event.target.closest('[data-movement-open]');
        if (movement) {
            var movementDialog = document.getElementById('dialog-movimiento');
            movementDialog.querySelector('[data-movement-id]').value = movement.dataset.id;
            movementDialog.querySelector('[data-movement-title]').textContent = movement.dataset.name;
            movementDialog.querySelector('[data-movement-stock]').textContent = 'Stock actual: ' + movement.dataset.stock.replace('.', ',');
            var selectedType = movementDialog.querySelector('input[name="tipo"][value="' + movement.dataset.type + '"]');
            if (selectedType) selectedType.checked = true;
            openDialog(movementDialog);
            return;
        }

        var edit = event.target.closest('[data-edit-open]');
        if (edit) {
            var editDialog = document.getElementById('dialog-editar');
            var fields = ['id', 'name', 'reference', 'unit', 'family', 'location', 'minimum', 'description'];
            fields.forEach(function (field) {
                var control = editDialog.querySelector('[data-edit="' + field + '"]');
                if (control) control.value = edit.dataset[field] || '';
            });
            openDialog(editDialog);
        }
    });

    document.querySelectorAll('.warehouse-dialog').forEach(function (dialog) {
        dialog.addEventListener('click', function (event) {
            if (event.target === dialog) closeDialog(dialog);
        });
    });
}());
