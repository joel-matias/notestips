{{-- Toolbar inline — se incluye dentro de la fila de tabs --}}
<button type="button" class="editor-toolbar-btn" data-action="bold" title="Negrita (Ctrl+B)"><strong>B</strong></button>
<button type="button" class="editor-toolbar-btn italic" data-action="italic" title="Cursiva (Ctrl+I)">I</button>
<button type="button" class="editor-toolbar-btn font-mono text-xs" data-action="code" title="Código (Ctrl+K)">`·`</button>

<div class="editor-toolbar-sep"></div>

<button type="button" class="editor-toolbar-btn text-xs font-bold" data-action="h1" title="Título 1">H1</button>
<button type="button" class="editor-toolbar-btn text-xs font-bold" data-action="h2" title="Título 2">H2</button>
<button type="button" class="editor-toolbar-btn text-xs font-bold" data-action="h3" title="Título 3">H3</button>

<div class="editor-toolbar-sep"></div>

<button type="button" class="editor-toolbar-btn" data-action="ul" title="Lista">
    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/>
    </svg>
</button>
<button type="button" class="editor-toolbar-btn" data-action="checkbox" title="Tarea (checklist)">
    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
    </svg>
</button>
<button type="button" class="editor-toolbar-btn" data-action="quote" title="Cita">
    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z"/>
    </svg>
</button>

{{-- Contador de palabras al final --}}
<span class="ml-auto text-xs tabular-nums" style="color: var(--color-text-subtle);">
    <span id="word-count">0</span>p · <span id="char-count">0</span>c
</span>
