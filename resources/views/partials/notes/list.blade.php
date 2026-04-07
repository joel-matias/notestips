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

                        $badgeStyle = match ($importance) {
                            'alta'  => 'background:#FEF2F2; color:#991B1B;',
                            'media' => 'background:#FFFBEB; color:#92400E;',
                            'baja'  => 'background:#EFF6FF; color:#1E40AF;',
                            default => '',
                        };
                        $badgeLabel = match ($importance) {
                            'alta'  => '🔴',
                            'media' => '🟡',
                            'baja'  => '🔵',
                            default => null,
                        };
                        $borderColor = match ($importance) {
                            'alta'  => '#EF4444',
                            'media' => '#F59E0B',
                            'baja'  => '#3B82F6',
                            default => 'transparent',
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
                    <li>
                        <a href="{{ route('notes.show', $s_note->id) }}"
                            data-note-id="{{ $s_note->id }}"
                            class="note-link block px-3 py-2.5 rounded-xl border-l-2 transition-fast text-decoration-none"
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
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
