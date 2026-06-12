const rupiah = new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
});

function parseMoney(value) {
    const number = Number.parseFloat(value);

    return Number.isNaN(number) ? 0 : number;
}

function refreshSaleTotals(form) {
    const rows = form.querySelectorAll('[data-sale-row]');
    let subtotal = 0;

    rows.forEach((row) => {
        const select = row.querySelector('[data-product-select]');
        const quantityInput = row.querySelector('[data-quantity-input]');
        const selectedOption = select?.selectedOptions?.[0];
        const price = parseMoney(selectedOption?.dataset.price ?? 0);
        const quantity = Math.max(parseInt(quantityInput?.value || 0, 10), 0);
        const lineTotal = price * quantity;

        subtotal += lineTotal;

        const lineTarget = row.querySelector('[data-line-total]');
        if (lineTarget) {
            lineTarget.textContent = rupiah.format(lineTotal);
        }
    });

    const discount = parseMoney(form.querySelector('[data-discount-input]')?.value ?? 0);
    const tax = parseMoney(form.querySelector('[data-tax-input]')?.value ?? 0);
    const paid = parseMoney(form.querySelector('[data-paid-input]')?.value ?? 0);
    const grandTotal = Math.max(subtotal - discount + tax, 0);
    const change = Math.max(paid - grandTotal, 0);

    form.querySelector('[data-subtotal]').textContent = rupiah.format(subtotal);
    form.querySelector('[data-grand-total]').textContent = rupiah.format(grandTotal);
    form.querySelector('[data-change-total]').textContent = rupiah.format(change);
}

function bootSaleForm() {
    const form = document.querySelector('[data-sale-form]');

    if (!form) {
        return;
    }

    const itemsTarget = form.querySelector('[data-sale-items]');
    const template = document.querySelector('#sale-row-template');

    form.addEventListener('input', () => refreshSaleTotals(form));
    form.addEventListener('change', () => refreshSaleTotals(form));

    form.querySelector('[data-add-sale-row]')?.addEventListener('click', () => {
        const fragment = template.content.cloneNode(true);
        itemsTarget.appendChild(fragment);
        refreshSaleTotals(form);
    });

    form.addEventListener('click', (event) => {
        const removeButton = event.target.closest('[data-remove-sale-row]');

        if (!removeButton) {
            return;
        }

        const rows = form.querySelectorAll('[data-sale-row]');

        if (rows.length === 1) {
            rows[0].querySelector('[data-product-select]').value = '';
            rows[0].querySelector('[data-quantity-input]').value = 1;
        } else {
            removeButton.closest('[data-sale-row]').remove();
        }

        refreshSaleTotals(form);
    });

    refreshSaleTotals(form);
}

function bootSidebar() {
    const button = document.querySelector('[data-sidebar-toggle]');
    const sidebar = document.querySelector('#sidebar');
    const closeTargets = document.querySelectorAll('[data-sidebar-close]');

    button?.addEventListener('click', () => {
        const isOpen = !sidebar?.classList.contains('open');

        sidebar?.classList.toggle('open', isOpen);
        document.body.classList.toggle('sidebar-open', isOpen);
    });

    closeTargets.forEach((target) => {
        target.addEventListener('click', () => {
            sidebar?.classList.remove('open');
            document.body.classList.remove('sidebar-open');
        });
    });

    sidebar?.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => {
            if (window.innerWidth <= 1120) {
                sidebar.classList.remove('open');
                document.body.classList.remove('sidebar-open');
            }
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            sidebar?.classList.remove('open');
            document.body.classList.remove('sidebar-open');
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    bootSidebar();
    bootSaleForm();
});
