document.addEventListener('DOMContentLoaded', () => {
    const templateButtons = document.getElementById('template-buttons');
    if (templateButtons) {
        fetch('/api/templates')
            .then(r => r.json())
            .then(list => {
                list.forEach(t => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.textContent = `★ ${t.name}`;
                    btn.addEventListener('click', () => applyTemplate(t.id));
                    templateButtons.appendChild(btn);
                });
            });
    }

    const checkAll = document.getElementById('check-all');
    if (checkAll) {
        checkAll.addEventListener('change', () => {
            document.querySelectorAll('input[name="receipt_ids[]"]').forEach(cb => {
                cb.checked = checkAll.checked;
            });
        });
    }
});

function applyTemplate(id) {
    fetch(`/api/template?id=${id}`)
        .then(r => r.json())
        .then(data => {
            const form = document.getElementById('receipt-form');
            if (!form) return;
            Object.entries(data).forEach(([key, value]) => {
                const field = form.querySelector(`[name="${key}"]`);
                if (field) {
                    if (field.type === 'checkbox') {
                        field.checked = !!value;
                    } else {
                        field.value = value;
                    }
                }
            });
            if (Array.isArray(data.accessories)) {
                form.querySelectorAll('input[name="accessories[]"]').forEach(cb => {
                    cb.checked = data.accessories.includes(parseInt(cb.value));
                });
            }
        });
}
