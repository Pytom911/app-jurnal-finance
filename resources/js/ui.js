const qs = (selector, root = document) => root.querySelector(selector);
const qsa = (selector, root = document) => Array.from(root.querySelectorAll(selector));

/* ---------------------------------- Sidebar --------------------------------- */

function setSidebar(open) {
    const sidebar = qs('#app-sidebar');
    const overlay = qs('#sidebar-overlay');

    if (!sidebar || !overlay) {
        return;
    }

    sidebar.classList.toggle('-translate-x-full', !open);
    sidebar.classList.toggle('translate-x-0', open);
    overlay.classList.toggle('hidden', !open);
    document.body.classList.toggle('overflow-hidden', open);
}

qsa('[data-sidebar-toggle]').forEach((button) => {
    button.addEventListener('click', () => setSidebar(true));
});

qsa('[data-sidebar-close]').forEach((button) => {
    button.addEventListener('click', () => setSidebar(false));
});

/* --------------------------------- Dropdown --------------------------------- */

qsa('[data-dropdown]').forEach((dropdown) => {
    const toggle = qs('[data-dropdown-toggle]', dropdown);
    const menu = qs('[data-dropdown-menu]', dropdown);

    if (!toggle || !menu) {
        return;
    }

    toggle.addEventListener('click', (event) => {
        event.stopPropagation();
        menu.classList.toggle('hidden');
    });
});

document.addEventListener('click', (event) => {
    qsa('[data-dropdown]').forEach((dropdown) => {
        if (!dropdown.contains(event.target)) {
            qs('[data-dropdown-menu]', dropdown)?.classList.add('hidden');
        }
    });
});

/* ---------------------------------- Modals ---------------------------------- */

export function openModal(id) {
    const modal = document.getElementById(id);

    if (!modal) {
        return;
    }

    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

export function closeModal(modal) {
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
}

window.openModal = openModal;
window.closeModal = (id) => closeModal(document.getElementById(id));

qsa('[data-modal-open]').forEach((button) => {
    button.addEventListener('click', () => openModal(button.dataset.modalOpen));
});

qsa('[data-modal-close]').forEach((button) => {
    button.addEventListener('click', () => closeModal(button.closest('[data-modal]')));
});

qsa('[data-modal]').forEach((modal) => {
    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            closeModal(modal);
        }
    });
});

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
        qsa('[data-modal]:not(.hidden)').forEach((modal) => closeModal(modal));
    }
});

/* ------------------------------ Confirm delete ------------------------------ */

qsa('form[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (!window.confirm(form.dataset.confirm)) {
            event.preventDefault();
        }
    });
});

/* --------------------------- Edit category modal ---------------------------- */

qsa('[data-edit-kategori]').forEach((button) => {
    button.addEventListener('click', () => {
        const form = document.getElementById('kategori-edit-form');

        if (!form) {
            return;
        }

        form.action = button.dataset.action;
        form.querySelector('[name="nama"]').value = button.dataset.nama ?? '';
        form.querySelector('[name="jenis"]').value = button.dataset.jenis ?? 'pemasukan';
        form.querySelector('[name="_kategori_id"]').value = button.dataset.id ?? '';

        openModal('kategori-edit');
    });
});

/* --------------------------- Edit challenge modal --------------------------- */

qsa('[data-edit-tantangan]').forEach((button) => {
    button.addEventListener('click', () => {
        const form = document.getElementById('tantangan-edit-form');

        if (!form) {
            return;
        }

        form.action = button.dataset.action;
        form.querySelector('[name="nama"]').value = button.dataset.nama ?? '';
        form.querySelector('[name="target"]').value = button.dataset.target ?? '';
        form.querySelector('[name="_tantangan_id"]').value = button.dataset.id ?? '';

        openModal('tantangan-edit');
    });
});

/* ----------------------------- Transaction form ----------------------------- */

const jenisSelect = qs('#jenis');

if (jenisSelect) {
    const sections = qsa('[data-jenis-section]');
    const sumberSelect = qs('#sumber_pemasukan');
    const tantanganWrap = qs('#tantangan-wrap');

    const applySumber = () => {
        if (!sumberSelect || !tantanganWrap) {
            return;
        }

        const aktif = sumberSelect.value === 'penghasilan_sendiri';

        tantanganWrap.classList.toggle('hidden', !aktif);

        qsa('select, input, textarea', tantanganWrap).forEach((field) => {
            field.disabled = !aktif;
        });
    };

    const applyJenis = () => {
        sections.forEach((section) => {
            const aktif = section.dataset.jenisSection === jenisSelect.value;

            section.classList.toggle('hidden', !aktif);

            qsa('select, input, textarea', section).forEach((field) => {
                field.disabled = !aktif;
            });
        });

        applySumber();
    };

    jenisSelect.addEventListener('change', applyJenis);

    if (sumberSelect) {
        sumberSelect.addEventListener('change', applySumber);
    }

    applyJenis();
}

/* ---------------------------- Password visibility --------------------------- */

qsa('[data-password-toggle]').forEach((button) => {
    const input = document.getElementById(button.dataset.passwordToggle);

    if (!input) {
        return;
    }

    button.addEventListener('click', () => {
        const reveal = input.type === 'password';

        input.type = reveal ? 'text' : 'password';
        button.setAttribute('aria-label', reveal ? 'Sembunyikan password' : 'Lihat password');

        qs('[data-password-label]', button)?.classList.toggle('hidden', reveal);
        qs('[data-password-label-off]', button)?.classList.toggle('hidden', !reveal);
    });
});

/* ------------------------------ Dismiss alerts ------------------------------ */

qsa('[data-dismiss-alert]').forEach((button) => {
    button.addEventListener('click', () => {
        button.closest('[data-alert]')?.remove();
    });
});

qsa('[data-auto-dismiss]').forEach((alert) => {
    window.setTimeout(() => {
        alert.classList.add('opacity-0', 'translate-y-1');
        window.setTimeout(() => alert.remove(), 300);
    }, 4500);
});
