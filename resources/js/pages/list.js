// ── Referencias DOM ──────────────────────────────────────────────
const qInput     = document.getElementById('q');
const selectImp  = document.getElementById('importance');
const dateMode   = document.getElementById('due_date_mode');
const dateInput  = document.getElementById('due_date');
const orderSel   = document.getElementById('order_by');
const notesList  = document.getElementById('notesList');
const notesCount = document.getElementById('notesCount');
const clearBtn   = document.getElementById('clear-filters-btn');

function escapeHtml(str) {
    return String(str ?? '').replace(/[&<>"']/g, m => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;',
    }[m]));
}

// ── Render de lista ──────────────────────────────────────────────
function renderNoteList(notes) {
    if (notesCount) notesCount.textContent = notes.length;
    if (!notesList) return;

    if (!notes.length) {
        notesList.innerHTML = `
            <div class="flex flex-col items-center gap-2 py-10 px-4 text-center">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                    style="background:var(--color-primary-soft);">
                    <svg class="w-5 h-5" style="color:var(--color-primary);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                    </svg>
                </div>
                <p class="text-xs font-medium" style="color:var(--color-text);">No hay notas</p>
                <p class="text-xs" style="color:var(--color-text-subtle);">Crea una o ajusta los filtros</p>
            </div>`;
        return;
    }

    const threeDotsIcon = `<svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
        <path d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z"/>
    </svg>`;

    const trashIcon = `<svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
    </svg>`;

    const items = notes.map(n => {
        const border = { alta: '#EF4444', media: '#F59E0B', baja: '#3B82F6' }[n.importance] ?? 'transparent';
        const emoji  = { alta: '🔴', media: '🟡', baja: '🔵' }[n.importance] ?? '';
        const safeTitle = escapeHtml(n.title ?? 'Nota sin título');

        return `
        <li class="note-item">
            <a href="/notes/${n.id}/edit"
                data-note-id="${n.id}"
                class="note-link block px-3 py-2.5 rounded-xl border-l-2 transition-fast pr-9"
                style="border-left-color:${escapeHtml(border)}; background:transparent;"
                onmouseover="this.style.background='var(--color-bg)';"
                onmouseout="this.style.background='transparent';">
                <div class="flex items-start gap-1.5 min-w-0">
                    ${emoji ? `<span class="shrink-0 text-xs leading-5">${escapeHtml(emoji)}</span>` : ''}
                    <span class="text-sm font-medium truncate leading-5" style="color:var(--color-text);">
                        ${safeTitle}
                    </span>
                </div>
                ${n.excerpt ? `<p class="text-xs line-clamp-1 mt-0.5" style="color:var(--color-text-subtle);">${escapeHtml(n.excerpt)}</p>` : ''}
                <div class="flex items-center gap-1.5 mt-1">
                    ${n.due_date_label ? `<span class="text-xs" style="color:var(--color-text-subtle);">📅 ${escapeHtml(n.due_date_label)}</span>` : ''}
                    <span class="text-xs ml-auto" style="color:var(--color-text-subtle);">${escapeHtml(n.last_edited_label ?? '')}</span>
                </div>
            </a>
            <button type="button"
                class="note-menu-btn"
                data-menu-id="${n.id}"
                aria-label="Opciones de nota"
                title="Opciones">
                ${threeDotsIcon}
            </button>
            <div class="note-menu-dropdown hidden" id="note-menu-${n.id}">
                <button type="button"
                    class="note-menu-item danger"
                    data-delete-id="${n.id}"
                    data-delete-title="${safeTitle}">
                    ${trashIcon}
                    Eliminar nota
                </button>
            </div>
        </li>`;
    }).join('');

    notesList.innerHTML = `<ul role="list" class="space-y-0.5 pt-1">${items}</ul>`;
}

// ── Búsqueda y filtros ───────────────────────────────────────────
let searchTimer = null;

async function fetchNotes() {
    const q    = qInput?.value.trim()    ?? '';
    const imp  = selectImp?.value.trim() ?? '';
    const mode = dateMode?.value.trim()  ?? '';
    const dd   = dateInput?.value.trim() ?? '';
    const ord  = orderSel?.value.trim()  ?? '';

    // Actualizar URL sin recargar
    const url = new URL(window.location.href);
    if (q)    url.searchParams.set('q', q);    else url.searchParams.delete('q');
    if (imp)  url.searchParams.set('importance', imp);  else url.searchParams.delete('importance');
    if (ord)  url.searchParams.set('order_by', ord);    else url.searchParams.delete('order_by');
    if (mode) url.searchParams.set('due_date_mode', mode); else url.searchParams.delete('due_date_mode');
    if (mode === 'exact' && dd) url.searchParams.set('due_date', dd); else url.searchParams.delete('due_date');
    history.replaceState({}, '', url);

    const api = new URL('/notes/search', location.origin);
    if (q)    api.searchParams.set('q', q);
    if (imp)  api.searchParams.set('importance', imp);
    if (ord)  api.searchParams.set('order_by', ord);
    if (mode) api.searchParams.set('due_date_mode', mode);
    if (mode === 'exact' && dd) api.searchParams.set('due_date', dd);

    try {
        const res = await fetch(api, { headers: { Accept: 'application/json' } });
        if (!res.ok) return;
        renderNoteList(await res.json());
    } catch { /* red error silencioso */ }
}

function debounceSearch() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(fetchNotes, 280);
}

function syncDateInput() {
    if (!dateMode || !dateInput) return;
    const on = dateMode.value === 'exact';
    dateInput.disabled = !on;
    dateInput.style.opacity = on ? '1' : '0.4';
    if (!on) dateInput.value = '';
}

if (qInput)    qInput.addEventListener('input', debounceSearch);
if (selectImp) selectImp.addEventListener('change', () => { clearTimeout(searchTimer); fetchNotes(); });
if (dateMode)  dateMode.addEventListener('change', () => { syncDateInput(); clearTimeout(searchTimer); fetchNotes(); });
if (dateInput) dateInput.addEventListener('input', debounceSearch);
if (orderSel)  orderSel.addEventListener('change', () => { clearTimeout(searchTimer); fetchNotes(); });

clearBtn?.addEventListener('click', () => {
    if (qInput)    qInput.value    = '';
    if (selectImp) selectImp.value = '';
    if (dateMode)  dateMode.value  = '';
    if (dateInput) dateInput.value = '';
    if (orderSel)  orderSel.value  = '';
    syncDateInput();
    fetchNotes();
});

syncDateInput();

// ── Eliminación AJAX desde el menú tres puntos (lista dinámica) ──
document.addEventListener('click', e => {
    const deleteBtn = e.target.closest('[data-delete-id]');
    if (!deleteBtn) return;

    e.stopPropagation();

    const id    = deleteBtn.dataset.deleteId;
    const title = deleteBtn.dataset.deleteTitle ?? 'esta nota';

    if (!confirm(`¿Eliminar "${title}"?\nEsta acción no se puede deshacer.`)) return;

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
    fetch(`/notes/${id}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrf,
        },
        body: new URLSearchParams({ _method: 'DELETE' }),
    }).then(r => {
        if (r.ok) {
            // Quitar de la lista
            notesList?.querySelector(`[data-note-id="${id}"]`)?.closest('li')?.remove();
            const cnt = parseInt(notesCount?.textContent ?? '0');
            if (notesCount) notesCount.textContent = Math.max(0, cnt - 1);
            if (window.showToast) window.showToast('info', 'Nota eliminada');
        } else {
            if (window.showToast) window.showToast('error', 'No se pudo eliminar la nota');
        }
    }).catch(() => {
        if (window.showToast) window.showToast('error', 'Error de conexión');
    });
});
