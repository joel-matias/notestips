@section('page', 'editor')

@section('header-actions')
@php($importance = old('importance', $note->importance))
<div class="flex items-center gap-2 flex-wrap">
    <select name="importance" form="editor-form" id="importance"
        class="h-8 px-2 rounded-lg text-xs cursor-pointer focus:outline-none transition-fast"
        style="border: 1px solid var(--color-border); background: white; color: var(--color-text);"
        onfocus="this.style.borderColor='var(--color-primary)';"
        onblur="this.style.borderColor='var(--color-border)';">
        <option value="">Sin importancia</option>
        <option value="alta"  {{ $importance == 'alta'  ? 'selected' : '' }}>🔴 Alta</option>
        <option value="media" {{ $importance == 'media' ? 'selected' : '' }}>🟡 Media</option>
        <option value="baja"  {{ $importance == 'baja'  ? 'selected' : '' }}>🔵 Baja</option>
    </select>
    <input type="date" name="due_date" form="editor-form" id="due_date"
        value="{{ old('due_date', optional($note->due_date)->format('Y-m-d')) }}"
        class="h-8 px-2 rounded-lg text-xs focus:outline-none transition-fast"
        style="border: 1px solid var(--color-border); background: white; color: var(--color-text);"
        title="Fecha de entrega"
        onfocus="this.style.borderColor='var(--color-primary)';"
        onblur="this.style.borderColor='var(--color-border)';">
    <div class="w-px h-5 shrink-0" style="background: var(--color-border);"></div>
    <button type="submit" form="editor-form"
        class="inline-flex items-center gap-1.5 h-8 px-3 rounded-lg text-xs font-semibold text-white transition-fast"
        style="background: var(--color-primary);"
        onmouseover="this.style.background='var(--color-primary-hover)';"
        onmouseout="this.style.background='var(--color-primary)';">
        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
        </svg>
        Guardar
    </button>
    <a href="{{ route('notes.show', $note->id) }}"
        class="inline-flex items-center h-8 px-3 rounded-lg text-xs font-medium transition-fast"
        style="border: 1px solid var(--color-border); background: white; color: var(--color-text-muted);"
        onmouseover="this.style.background='var(--color-bg)';"
        onmouseout="this.style.background='white';">
        Cancelar
    </a>
    <span class="hidden sm:flex items-center gap-1 text-xs" style="color: var(--color-text-subtle);">
        <kbd class="px-1.5 py-0.5 rounded" style="border: 1px solid var(--color-border); background: var(--color-bg);">Ctrl+S</kbd>
    </span>
</div>
@endsection

<div class="max-w-3xl mx-auto px-4 py-6 sm:px-6 lg:px-8">

    <form id="editor-form" action="{{ route('notes.update', $note->id) }}" method="POST" data-editor>
        @csrf
        @method('PUT')
        {{-- Hidden textarea that receives block editor content --}}
        <textarea name="content" class="hidden" aria-hidden="true"
            @error('content') aria-invalid="true" @enderror>{{ old('content', $note->content) }}</textarea>

        {{-- Título --}}
        <input id="title" type="text" name="title" value="{{ old('title', $note->title) }}"
            placeholder="Título de la nota..."
            autofocus
            class="w-full px-0 py-2 text-2xl font-bold bg-transparent border-0 border-b-2 mb-5 focus:outline-none transition-fast"
            style="border-color: var(--color-border); color: var(--color-text);"
            onfocus="this.style.borderColor='var(--color-primary)';"
            onblur="this.style.borderColor='var(--color-border)';"
            @error('title') aria-invalid="true" @enderror>
        @error('title')
            <p class="text-sm -mt-4 mb-4" style="color: var(--color-error);" role="alert">{{ $message }}</p>
        @enderror
        @error('content')
            <p class="text-sm mb-2" style="color: var(--color-error);" role="alert">{{ $message }}</p>
        @enderror
    </form>

    {{-- Toolbar --}}
    <div class="flex items-center gap-1 flex-wrap mb-3" style="border-bottom: 1px solid var(--color-border); padding-bottom: 0.5rem;">
        @include('partials.notes.editor-toolbar')
    </div>

    {{-- Block editor --}}
    <div id="block-editor" class="block-editor prose-note"></div>

</div>
