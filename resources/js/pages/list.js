// ── Referencias DOM ──────────────────────────────────────────────
const qInput     = document.getElementById('q');
const selectImp  = document.getElementById('importance');
const dateMode   = document.getElementById('due_date_mode');
const dateInput  = document.getElementById('due_date');
const orderSel   = document.getElementById('order_by');
const notesList  = document.getElementById('notesList');
const notesCount = document.getElementById('notesCount');
const notePanel  = document.getElementById('note-panel');
const headerTitle = document.getElementById('header-note-title');
const clearBtn   = document.getElementById('clear-filters-btn');

// ── Estado ───────────────────────────────────────────────────────
let activeNoteId = null;

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

    const items = notes.map(n => {
        const border = { alta: '#EF4444', media: '#F59E0B', baja: '#3B82F6' }[n.importance] ?? 'transparent';
        const emoji  = { alta: '🔴', media: '🟡', baja: '🔵' }[n.importance] ?? '';
        const isActive = String(n.id) === String(activeNoteId);

        return `
        <li>
            <a href="/notes/${n.id}"
                data-note-id="${n.id}"
                class="note-link block px-3 py-2.5 rounded-xl border-l-2 transition-fast"
                style="border-left-color:${escapeHtml(border)}; background:${isActive ? 'var(--color-primary-soft)' : 'transparent'};"
                ${!isActive ? `onmouseover="this.style.background='var(--color-bg)';" onmouseout="this.style.background='transparent';"` : ''}>
                <div class="flex items-start gap-1.5 min-w-0">
                    ${emoji ? `<span class="shrink-0 text-xs leading-5">${escapeHtml(emoji)}</span>` : ''}
                    <span class="text-sm font-medium truncate leading-5" style="color:${isActive ? 'var(--color-primary)' : 'var(--color-text)'};">
                        ${escapeHtml(n.title ?? 'Nota sin título')}
                    </span>
                </div>
                ${n.excerpt ? `<p class="text-xs line-clamp-1 mt-0.5" style="color:var(--color-text-subtle);">${escapeHtml(n.excerpt)}</p>` : ''}
                <div class="flex items-center gap-1.5 mt-1">
                    ${n.due_date_label ? `<span class="text-xs" style="color:var(--color-text-subtle);">📅 ${escapeHtml(n.due_date_label)}</span>` : ''}
                    <span class="text-xs ml-auto" style="color:var(--color-text-subtle);">${escapeHtml(n.last_edited_label ?? '')}</span>
                </div>
            </a>
        </li>`;
    }).join('');

    notesList.innerHTML = `<ul role="list" class="space-y-0.5 pt-1">${items}</ul>`;
}

// ── Búsqueda en tiempo real ───────────────────────────────────────
let searchTimer = null;

async function fetchNotes() {
    const q    = qInput?.value.trim()    ?? '';
    const imp  = selectImp?.value.trim() ?? '';
    const mode = dateMode?.value.trim()  ?? '';
    const dd   = dateInput?.value.trim() ?? '';
    const ord  = orderSel?.value.trim()  ?? '';

    // Actualizar URL de la barra de búsqueda sin recargar
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

    const res = await fetch(api, { headers: { Accept: 'application/json' } });
    if (!res.ok) return;
    renderNoteList(await res.json());
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

// ── Render de nota (AJAX) ────────────────────────────────────────
function renderNote(data) {
    if (!notePanel) return;

    const badgeStyle = { alta: 'background:#FEF2F2;color:#991B1B;', media: 'background:#FFFBEB;color:#92400E;', baja: 'background:#EFF6FF;color:#1E40AF;' }[data.importance] ?? '';
    const badgeLabel = { alta: '🔴 Alta', media: '🟡 Media', baja: '🔵 Baja' }[data.importance] ?? null;
    const dueDateStyle = data.is_overdue ? 'background:#FEF2F2;color:#991B1B;' : 'background:var(--color-bg);color:var(--color-text-muted);';

    notePanel.innerHTML = `
        <div class="max-w-3xl mx-auto px-4 py-6 sm:px-6 lg:px-8">

            <div class="mb-5">
                <div class="flex items-start justify-between gap-4">
                    <h1 class="text-2xl sm:text-3xl font-bold leading-tight break-words flex-1"
                        style="color:var(--color-text);">${escapeHtml(data.title ?? 'Nota sin título')}</h1>

                    <div class="flex items-center gap-1.5 shrink-0">
                        <a href="${escapeHtml(data.edit_url)}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-sm font-medium transition-fast"
                            style="color:var(--color-text-muted);border:1px solid var(--color-border);"
                            onmouseover="this.style.background='var(--color-bg)';"
                            onmouseout="this.style.background='transparent';">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/>
                            </svg>
                            <span class="hidden sm:inline">Editar</span>
                        </a>
                        <button type="button" id="aj-delete-btn"
                            data-url="${escapeHtml(data.delete_url)}"
                            data-csrf="${escapeHtml(data.csrf_token)}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-sm font-medium transition-fast"
                            style="color:#DC2626;border:1px solid transparent;"
                            onmouseover="this.style.background='#FEF2F2';this.style.borderColor='#FECACA';"
                            onmouseout="this.style.background='transparent';this.style.borderColor='transparent';">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                            </svg>
                            <span class="hidden sm:inline">Eliminar</span>
                        </button>
                    </div>
                </div>

                <div class="mt-3 flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 text-xs" style="color:var(--color-text-subtle);">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        </svg>
                        Editado ${escapeHtml(data.updated_at_label)}
                    </span>
                    ${badgeLabel ? `<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium" style="${escapeHtml(badgeStyle)}">${escapeHtml(badgeLabel)}</span>` : ''}
                    ${data.due_date_label ? `
                        <span class="inline-flex items-center gap-1.5 text-xs rounded-full px-2.5 py-0.5 font-medium" style="${escapeHtml(dueDateStyle)}">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v7.5"/>
                            </svg>
                            Entrega: ${escapeHtml(data.due_date_label)}${data.is_overdue ? ' · Vencida' : ''}
                        </span>` : ''}
                </div>
            </div>

            <hr style="border:none;border-top:1px solid var(--color-border);margin-bottom:1.5rem;">

            <article id="note-markdown-content"
                data-toggle-task-url-template="${escapeHtml(data.toggle_task_url_template)}"
                data-csrf-token="${escapeHtml(data.csrf_token)}"
                class="prose-note">
                ${data.content_html}
            </article>
        </div>`;

    // Inicializar toggles de tarea
    initTaskToggles();

    // Botón eliminar
    document.getElementById('aj-delete-btn')?.addEventListener('click', (e) => {
        const btn = e.currentTarget;
        if (!confirm('¿Eliminar esta nota? Esta acción no se puede deshacer.')) return;
        fetch(btn.dataset.url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'Accept': 'application/json', 'X-CSRF-TOKEN': btn.dataset.csrf },
            body: new URLSearchParams({ _method: 'DELETE' }),
        }).then(r => {
            if (r.ok || r.status === 302) {
                // Quitar nota de la lista
                notesList.querySelector(`[data-note-id="${activeNoteId}"]`)?.closest('li')?.remove();
                const cnt = parseInt(notesCount?.textContent ?? '0');
                if (notesCount) notesCount.textContent = Math.max(0, cnt - 1);

                activeNoteId = null;
                history.pushState({ noteId: null }, '', '/notes');
                showEmptyState();
                updateHeaderTitle(null);
                if (window.showToast) window.showToast('info', 'Nota eliminada');
            }
        }).catch(() => { if (window.showToast) window.showToast('error', 'Error al eliminar'); });
    });
}

function showEmptyState() {
    if (!notePanel) return;
    notePanel.innerHTML = `
        <div class="flex flex-col items-center justify-center gap-4 h-full text-center px-6">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center" style="background:var(--color-primary-soft);">
                <svg class="w-8 h-8" style="color:var(--color-primary);opacity:0.5;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                </svg>
            </div>
            <div>
                <p class="text-base font-semibold" style="color:var(--color-text);">Selecciona una nota</p>
                <p class="text-sm mt-0.5" style="color:var(--color-text-muted);">Elige una nota de la lista para leerla aquí</p>
            </div>
        </div>`;
}

function showLoadingState() {
    if (!notePanel) return;
    notePanel.innerHTML = `
        <div class="flex items-center justify-center h-full gap-3" style="color:var(--color-text-muted);">
            <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 00-8 8h4z"/>
            </svg>
            <span class="text-sm">Cargando...</span>
        </div>`;
}

function updateActiveInList(id) {
    // Quitar active de todos
    notesList?.querySelectorAll('.note-link').forEach(a => {
        a.style.background = 'transparent';
        a.removeAttribute('data-active');
        a.onmouseover = function () { this.style.background = 'var(--color-bg)'; };
        a.onmouseout  = function () { this.style.background = 'transparent'; };
        a.querySelector('span[style*="color"]')?.removeAttribute('style');
    });

    // Marcar el activo
    const activeEl = notesList?.querySelector(`[data-note-id="${id}"]`);
    if (activeEl) {
        activeEl.style.background = 'var(--color-primary-soft)';
        activeEl.setAttribute('data-active', 'true');
        activeEl.onmouseover = null;
        activeEl.onmouseout  = null;
        const titleSpan = activeEl.querySelector('.text-sm.font-medium');
        if (titleSpan) titleSpan.style.color = 'var(--color-primary)';
    }
}

function updateHeaderTitle(title) {
    if (headerTitle) {
        headerTitle.textContent = title ?? 'Mis notas';
    }
}

// ── Cargar nota via AJAX ─────────────────────────────────────────
async function loadNote(id, pushState = true) {
    activeNoteId = String(id);
    updateActiveInList(id);
    showLoadingState();

    // Cerrar sidebar en móvil
    window.dispatchEvent(new CustomEvent('close-sidebar'));

    try {
        const res = await fetch(`/notes/${id}`, { headers: { Accept: 'application/json' } });

        if (!res.ok) {
            if (!notePanel) return;
            notePanel.innerHTML = `
                <div class="flex flex-col items-center justify-center gap-4 h-full text-center px-6">
                    <p class="text-sm font-semibold" style="color:var(--color-text);">Nota no encontrada</p>
                    <a href="/notes" class="text-sm font-medium hover:underline" style="color:var(--color-primary);">Volver</a>
                </div>`;
            return;
        }

        const data = await res.json();
        renderNote(data);
        updateHeaderTitle(data.title);

        if (pushState) {
            history.pushState({ noteId: id }, '', `/notes/${id}`);
        }
    } catch {
        if (window.showToast) window.showToast('error', 'Error al cargar la nota');
    }
}

// ── Interceptar clics en links de notas ──────────────────────────
document.addEventListener('click', e => {
    const link = e.target.closest('.note-link');
    if (!link) return;
    e.preventDefault();
    const id = link.dataset.noteId;
    if (!id) return;
    loadNote(id);
});

// ── Historial del navegador ───────────────────────────────────────
window.addEventListener('popstate', e => {
    if (e.state?.noteId) {
        loadNote(e.state.noteId, false);
    } else {
        activeNoteId = null;
        updateActiveInList(null);
        showEmptyState();
        updateHeaderTitle(null);
    }
});

// Si hay una nota en la URL al cargar (viniendo del servidor)
const pathMatch = location.pathname.match(/^\/notes\/(\d+)$/);
if (pathMatch) {
    activeNoteId = pathMatch[1];
    history.replaceState({ noteId: activeNoteId }, '', location.href);
}

// ── Task toggles ─────────────────────────────────────────────────
function initTaskToggles() {
    const container = document.getElementById('note-markdown-content');
    if (!container) return;

    const urlTemplate = container.dataset.toggleTaskUrlTemplate;
    const csrf        = container.dataset.csrfToken;
    const boxes       = [...container.querySelectorAll('li input[type="checkbox"]')];

    boxes.forEach((cb, i) => {
        cb.disabled = false;
        cb.dataset.taskIndex = String(i);
        cb.classList.add('cursor-pointer');
    });

    container.addEventListener('change', async e => {
        const cb = e.target.closest('input[type="checkbox"][data-task-index]');
        if (!cb || cb.dataset.syncing) return;
        const idx = cb.dataset.taskIndex;
        const checked = cb.checked;
        cb.dataset.syncing = '1';

        try {
            const r = await fetch(urlTemplate.replace('__TASK_INDEX__', idx), {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: new URLSearchParams({ _method: 'PATCH', completed: checked ? '1' : '0' }),
            });
            if (!r.ok) cb.checked = !checked;
            else if (window.showToast) window.showToast('success', checked ? 'Tarea completada ✓' : 'Tarea pendiente');
        } catch {
            cb.checked = !checked;
        } finally {
            delete cb.dataset.syncing;
        }
    });
}

// Inicializar toggles si la nota ya se cargó por SSR
initTaskToggles();

// Inicializar el botón eliminar si la nota ya se cargó por SSR
document.getElementById('delete-note-btn')?.addEventListener('click', () => {
    document.getElementById('delete-note-form')?.submit();
});
