@section('page', 'editor')

@section('header-actions')
<div class="flex items-center gap-2 flex-wrap">
    <select name="importance" form="editor-form" id="importance"
        class="h-8 px-2 rounded-lg text-xs cursor-pointer focus:outline-none transition-fast"
        style="border: 1px solid var(--color-border); background: white; color: var(--color-text);"
        onfocus="this.style.borderColor='var(--color-primary)';"
        onblur="this.style.borderColor='var(--color-border)';">
        <option value="">Sin importancia</option>
        <option value="alta"  {{ old('importance') == 'alta'  ? 'selected' : '' }}>🔴 Alta</option>
        <option value="media" {{ old('importance') == 'media' ? 'selected' : '' }}>🟡 Media</option>
        <option value="baja"  {{ old('importance') == 'baja'  ? 'selected' : '' }}>🔵 Baja</option>
    </select>
    <input type="date" name="due_date" form="editor-form" id="due_date"
        value="{{ old('due_date') }}"
        class="h-8 px-2 rounded-lg text-xs focus:outline-none transition-fast"
        style="border: 1px solid var(--color-border); background: white; color: var(--color-text);"
        title="Fecha de entrega"
        onfocus="this.style.borderColor='var(--color-primary)';"
        onblur="this.style.borderColor='var(--color-border)';">
    <div class="w-px h-5 shrink-0" style="background: var(--color-border);"></div>

    {{-- Toggles de modo (solo sm+) --}}
    <div class="hidden sm:flex items-center rounded-lg overflow-hidden" style="border: 1px solid var(--color-border);" title="Modo de visualización">
        <button type="button" class="editor-mode-btn" data-editor-mode="editor" title="Solo editor">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Z"/>
            </svg>
        </button>
        <button type="button" class="editor-mode-btn" data-editor-mode="split" title="Vista dividida">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 4.5v15m6-15v15M3.75 4.5h16.5M3.75 19.5h16.5M12 4.5v15"/>
            </svg>
        </button>
        <button type="button" class="editor-mode-btn" data-editor-mode="preview" title="Solo vista previa">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
            </svg>
        </button>
    </div>

    <div class="w-px h-5 shrink-0 hidden sm:block" style="background: var(--color-border);"></div>
    <button type="submit" form="editor-form"
        class="inline-flex items-center gap-1.5 h-8 px-3 rounded-lg text-xs font-semibold text-white transition-fast"
        style="background: var(--color-primary);"
        onmouseover="this.style.background='var(--color-primary-hover)';"
        onmouseout="this.style.background='var(--color-primary)';">
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
        </svg>
        Crear
    </button>
    <a href="{{ url()->previous() }}"
        class="inline-flex items-center h-8 px-3 rounded-lg text-xs font-medium transition-fast"
        style="border: 1px solid var(--color-border); background: white; color: var(--color-text-muted);"
        onmouseover="this.style.background='var(--color-bg)';"
        onmouseout="this.style.background='white';">
        Cancelar
    </a>
    <span class="hidden md:flex items-center gap-1 text-xs" style="color: var(--color-text-subtle);">
        <kbd class="px-1.5 py-0.5 rounded" style="border: 1px solid var(--color-border); background: var(--color-bg);">Ctrl+S</kbd>
    </span>
</div>
@endsection

{{-- Contenedor principal del editor (ocupa el alto completo) --}}
<div class="h-full flex flex-col overflow-hidden">

    {{-- Formulario --}}
    <form id="editor-form" action="{{ route('notes.store') }}" method="POST" data-editor>
        @csrf
        <textarea name="content" class="hidden" aria-hidden="true"
            @error('content') aria-invalid="true" @enderror></textarea>
    </form>

    {{-- Área del título --}}
    <div class="shrink-0 px-4 sm:px-6 pt-5 pb-3">
        <input id="title" type="text" name="title" form="editor-form"
            value="{{ old('title') }}"
            placeholder="Título de la nota..."
            autofocus
            class="w-full px-0 py-1 text-2xl font-bold bg-transparent border-0 border-b-2 focus:outline-none transition-fast"
            style="border-color: var(--color-border); color: var(--color-text);"
            onfocus="this.style.borderColor='var(--color-primary)';"
            onblur="this.style.borderColor='var(--color-border)';"
            @error('title') aria-invalid="true" @enderror>
        @error('title')
            <p class="text-sm mt-1" style="color: var(--color-error);" role="alert">{{ $message }}</p>
        @enderror
        @error('content')
            <p class="text-sm mt-1" style="color: var(--color-error);" role="alert">{{ $message }}</p>
        @enderror
    </div>

    {{-- Toolbar --}}
    <div id="editor-toolbar-row" class="shrink-0 flex items-center gap-1 flex-wrap px-4 sm:px-6 py-2"
        style="border-top: 1px solid var(--color-border); border-bottom: 1px solid var(--color-border);">
        @include('partials.notes.editor-toolbar')
    </div>

    {{-- Área dividida: editor | preview --}}
    <div class="flex-1 min-h-0 flex overflow-hidden" id="editor-split-area">

        {{-- Panel editor --}}
        <div class="flex-1 min-w-0 overflow-y-auto" id="editor-pane">
            <div class="max-w-2xl mx-auto px-4 sm:px-6 py-5">
                <div id="block-editor" class="block-editor prose-note"></div>
            </div>
        </div>

        {{-- Panel preview (live) --}}
        <div class="flex-1 min-w-0 overflow-y-auto hidden" id="preview-pane"
            style="border-left: 1px solid var(--color-border); background: var(--color-bg);">
            <div class="max-w-2xl mx-auto px-4 sm:px-6 py-5">
                <h1 id="preview-note-title"
                    class="text-2xl font-bold mb-1 break-words"
                    style="color: var(--color-text);">Nueva nota</h1>
                <div class="mb-5" style="height: 2px; background: var(--color-border);"></div>
                <div id="editor-preview-content" class="prose-note">
                    <p class="block-placeholder">Empieza a escribir para ver la vista previa...</p>
                </div>
            </div>
        </div>
    </div>
</div>
