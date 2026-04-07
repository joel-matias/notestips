@php
    $importance    = $note->importance ?? null;
    $dueDate       = $note->due_date ?? null;
    $renderedContent = \Illuminate\Support\Str::markdown($note->content ?? '', [
        'html_input'          => 'strip',
        'allow_unsafe_links'  => false,
    ]);

    $badgeStyle = match ($importance) {
        'alta'  => 'background:#FEF2F2; color:#991B1B;',
        'media' => 'background:#FFFBEB; color:#92400E;',
        'baja'  => 'background:#EFF6FF; color:#1E40AF;',
        default => '',
    };
    $badgeLabel = match ($importance) {
        'alta'  => '🔴 Alta',
        'media' => '🟡 Media',
        'baja'  => '🔵 Baja',
        default => null,
    };

    $lastEdited = $note->updated_at ?? now();
    $lastEditedLabel = method_exists($lastEdited, 'diffForHumans') ? $lastEdited->diffForHumans() : 'Hace un momento';
    $dueDateLabel    = $dueDate ? \Carbon\Carbon::parse($dueDate)->format('d/m/Y') : null;

    $isOverdue = $dueDate && \Carbon\Carbon::parse($dueDate)->isPast();
@endphp

<div class="max-w-3xl mx-auto px-4 py-6 sm:px-6 lg:px-8">

    {{-- Cabecera de la nota --}}
    <div class="mb-6">
        <div class="flex items-start justify-between gap-4">
            <div class="min-w-0 flex-1">
                <h1 class="text-2xl sm:text-3xl font-bold leading-tight break-words" style="color: var(--color-text);">
                    {{ $note->title ?? 'Nota sin título' }}
                </h1>
            </div>

            {{-- Acciones --}}
            <div class="flex items-center gap-1.5 shrink-0">
                <a href="{{ route('notes.edit', $note->id) }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-sm font-medium transition-fast focus-visible:outline-none"
                    style="color: var(--color-text-muted); border: 1px solid var(--color-border); background: white;"
                    onmouseover="this.style.background='var(--color-bg)'; this.style.color='var(--color-text)';"
                    onmouseout="this.style.background='white'; this.style.color='var(--color-text-muted)';"
                    aria-label="Editar nota">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                    </svg>
                    <span class="hidden sm:inline">Editar</span>
                </a>

                <form method="POST" action="{{ route('notes.destroy', $note->id) }}" id="delete-note-form">
                    @csrf
                    @method('DELETE')
                    <button type="button" id="delete-note-btn"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-sm font-medium transition-fast focus-visible:outline-none"
                        style="color: #DC2626; border: 1px solid transparent; background: transparent;"
                        onmouseover="this.style.background='#FEF2F2'; this.style.borderColor='#FECACA';"
                        onmouseout="this.style.background='transparent'; this.style.borderColor='transparent';"
                        aria-label="Eliminar nota">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                        <span class="hidden sm:inline">Eliminar</span>
                    </button>
                </form>
            </div>
        </div>

        {{-- Metadata --}}
        <div class="mt-3 flex flex-wrap items-center gap-2">
            <span class="inline-flex items-center gap-1.5 text-xs" style="color: var(--color-text-subtle);">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                Editado {{ $lastEditedLabel }}
            </span>

            @if ($badgeLabel)
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
                    style="{{ $badgeStyle }}">
                    {{ $badgeLabel }}
                </span>
            @endif

            @if ($dueDateLabel)
                <span class="inline-flex items-center gap-1.5 text-xs rounded-full px-2.5 py-0.5 font-medium"
                    style="{{ $isOverdue ? 'background:#FEF2F2; color:#991B1B;' : 'background:var(--color-bg); color:var(--color-text-muted);' }}">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v7.5" />
                    </svg>
                    Entrega: {{ $dueDateLabel }}{{ $isOverdue ? ' · Vencida' : '' }}
                </span>
            @endif
        </div>
    </div>

    <hr style="border: none; border-top: 1px solid var(--color-border); margin-bottom: 1.5rem;">

    {{-- Contenido de la nota --}}
    <article
        id="note-markdown-content"
        data-toggle-task-url-template="{{ route('notes.tasks.toggle', ['note' => $note->id, 'taskIndex' => '__TASK_INDEX__'], false) }}"
        data-csrf-token="{{ csrf_token() }}"
        class="prose-note">
        {!! $renderedContent !!}
    </article>

</div>

@push('scripts')
<script>
(() => {
    // Confirmar eliminación
    const deleteBtn = document.getElementById('delete-note-btn');
    const deleteForm = document.getElementById('delete-note-form');
    if (deleteBtn && deleteForm) {
        deleteBtn.addEventListener('click', () => {
            if (confirm('¿Eliminar esta nota? Esta acción no se puede deshacer.')) {
                deleteForm.submit();
            }
        });
    }

    // Toggle de tareas
    const container = document.getElementById('note-markdown-content');
    if (!container) return;

    const urlTemplate = container.dataset.toggleTaskUrlTemplate;
    const csrfToken   = container.dataset.csrfToken;
    const taskCheckboxes = Array.from(container.querySelectorAll('li input[type="checkbox"]'));

    taskCheckboxes.forEach((checkbox, taskIndex) => {
        checkbox.disabled = false;
        checkbox.dataset.taskIndex = String(taskIndex);
        checkbox.classList.add('cursor-pointer');
    });

    container.addEventListener('change', async (event) => {
        const checkbox = event.target.closest('input[type="checkbox"][data-task-index]');
        if (!checkbox || checkbox.dataset.syncing === '1') return;

        const taskIndex = checkbox.dataset.taskIndex;
        const checked   = checkbox.checked;
        checkbox.dataset.syncing = '1';

        try {
            const response = await fetch(urlTemplate.replace('__TASK_INDEX__', taskIndex), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: new URLSearchParams({ _method: 'PATCH', completed: checked ? '1' : '0' }),
            });

            if (!response.ok) checkbox.checked = !checked;
            else if (window.showToast) window.showToast('success', checked ? 'Tarea completada' : 'Tarea pendiente');
        } catch {
            checkbox.checked = !checked;
        } finally {
            delete checkbox.dataset.syncing;
        }
    });
})();
</script>
@endpush
