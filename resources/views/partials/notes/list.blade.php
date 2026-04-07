{{-- Sección de lista de notas (dentro del sidebar) --}}
<div class="flex-1 flex flex-col min-h-0 overflow-hidden">

    {{-- Cabecera de lista --}}
    <div class="shrink-0 px-4 py-2 flex items-center justify-between">
        <span class="text-xs font-semibold uppercase tracking-widest" style="color: var(--color-text-subtle);">
            Notas
        </span>
        <span id="notesCount"
            class="text-xs tabular-nums px-1.5 py-0.5 rounded-full"
            style="background: var(--color-bg); color: var(--color-text-subtle);">
            {{ $notes->count() }}
        </span>
    </div>

    {{-- Lista scrolleable --}}
    <div id="notesList" class="flex-1 overflow-y-auto px-2 pb-2">
        @if ($notes->isEmpty())
            <div class="flex flex-col items-center justify-center gap-2 py-10 px-4 text-center">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                    style="background: var(--color-primary-soft);">
                    <svg class="w-5 h-5" style="color: var(--color-primary);" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/>
                    </svg>
                </div>
                <p class="text-xs font-medium" style="color: var(--color-text);">No hay notas</p>
                <p class="text-xs" style="color: var(--color-text-subtle);">Crea una o ajusta los filtros</p>
            </div>
        @else
            <ul role="list" class="space-y-0.5 pt-1">
                @foreach ($notes as $s_note)
                    @php
                        $isActive = isset($note) && $note && $note->id == $s_note->id;
                        $importance = $s_note->importance;
                        $due_date = $s_note->due_date
                            ? \Carbon\Carbon::parse($s_note->due_date)->format('d/m/Y')
                            : null;

                        $borderColor = match ($importance) {
                            'alta'  => '#EF4444',
                            'media' => '#F59E0B',
                            'baja'  => '#3B82F6',
                            default => 'transparent',
                        };

                        $badgeLabel = match ($importance) {
                            'alta'  => '🔴',
                            'media' => '🟡',
                            'baja'  => '🔵',
                            default => null,
                        };

                        $lastEdited = $s_note->updated_at ?? now();
                        $lastEditedLabel = method_exists($lastEdited, 'diffForHumans')
                            ? $lastEdited->diffForHumans()
                            : 'Hace un momento';

                        $excerpt = $s_note->content
                            ? \Illuminate\Support\Str::limit(
                                preg_replace('/\s+/', ' ', strip_tags(
                                    \Illuminate\Support\Str::markdown($s_note->content, ['html_input' => 'strip', 'allow_unsafe_links' => false])
                                )), 70
                              )
                            : null;
                    @endphp
                    <li class="note-item">
                        <a href="{{ route('notes.edit', $s_note->id) }}"
                            data-note-id="{{ $s_note->id }}"
                            class="note-link block px-3 py-2.5 rounded-xl border-l-2 transition-fast pr-9"
                            style="border-left-color: {{ $borderColor }}; background: {{ $isActive ? 'var(--color-primary-soft)' : 'transparent' }};"
                            @if(!$isActive)
                            onmouseover="this.style.background='var(--color-bg)';"
                            onmouseout="this.style.background='transparent';"
                            @endif
                            @if($isActive) data-active="true" @endif>

                            <div class="flex items-start gap-1.5 min-w-0">
                                @if($badgeLabel)
                                    <span class="shrink-0 text-xs leading-5">{{ $badgeLabel }}</span>
                                @endif
                                <span class="text-sm font-medium truncate leading-5" style="color: {{ $isActive ? 'var(--color-primary)' : 'var(--color-text)' }};">
                                    {{ $s_note->title ?? 'Nota sin título' }}
                                </span>
                            </div>

                            @if ($excerpt)
                                <p class="text-xs line-clamp-1 mt-0.5 leading-relaxed" style="color: var(--color-text-subtle);">
                                    {{ $excerpt }}
                                </p>
                            @endif

                            <div class="flex items-center gap-1.5 mt-1">
                                @if ($due_date)
                                    <span class="text-xs" style="color: var(--color-text-subtle);">📅 {{ $due_date }}</span>
                                @endif
                                <span class="text-xs ml-auto" style="color: var(--color-text-subtle);">{{ $lastEditedLabel }}</span>
                            </div>
                        </a>

                        {{-- Botón tres puntos --}}
                        <button type="button"
                            class="note-menu-btn"
                            data-menu-id="{{ $s_note->id }}"
                            aria-label="Opciones de nota"
                            title="Opciones">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 6.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 12.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5ZM12 18.75a.75.75 0 1 1 0-1.5.75.75 0 0 1 0 1.5Z"/>
                            </svg>
                        </button>

                        {{-- Menú desplegable --}}
                        <div class="note-menu-dropdown hidden" id="note-menu-{{ $s_note->id }}">
                            <form method="POST" action="{{ route('notes.destroy', $s_note->id) }}" class="m-0 p-0">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="note-menu-item danger"
                                    onclick="if(!confirm('¿Eliminar esta nota? Esta acción no se puede deshacer.')){return;} this.closest('form').submit();">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                    </svg>
                                    Eliminar nota
                                </button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
