// ── Renderizador Markdown client-side ────────────────────────────
function esc(s) {
    return String(s ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');
}

function inlineRender(text) {
    return esc(text)
        // Código inline
        .replace(/`([^`\n]+)`/g, '<code>$1</code>')
        // Bold + italic
        .replace(/\*\*\*(.+?)\*\*\*/gs, '<strong><em>$1</em></strong>')
        // Bold
        .replace(/\*\*(.+?)\*\*/gs, '<strong>$1</strong>')
        // Italic
        .replace(/\*(.+?)\*/gs, '<em>$1</em>')
        // Tachado
        .replace(/~~(.+?)~~/gs, '<del>$1</del>')
        // Links
        .replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" target="_blank" rel="noopener">$1</a>');
}

function renderMarkdown(md) {
    if (!md.trim()) return '';

    const lines = md.split('\n');
    let html = '';
    let i = 0;

    while (i < lines.length) {
        const line = lines[i];
        const trimmed = line.trim();

        // Bloque de código
        if (line.startsWith('```')) {
            const lang = line.slice(3).trim();
            let code = '';
            i++;
            while (i < lines.length && !lines[i].startsWith('```')) {
                code += lines[i] + '\n';
                i++;
            }
            html += `<pre><code${lang ? ` class="language-${esc(lang)}"` : ''}>${esc(code.trimEnd())}</code></pre>`;
            i++;
            continue;
        }

        // Línea vacía
        if (!trimmed) { i++; continue; }

        // Encabezados
        const hm = trimmed.match(/^(#{1,6})\s+(.+)$/);
        if (hm) {
            const n = hm[1].length;
            html += `<h${n}>${inlineRender(hm[2])}</h${n}>`;
            i++; continue;
        }

        // Separador
        if (/^[-*_]{3,}$/.test(trimmed)) {
            html += '<hr>';
            i++; continue;
        }

        // Cita
        if (line.startsWith('> ')) {
            let bq = '';
            while (i < lines.length && lines[i].startsWith('> ')) {
                bq += lines[i].slice(2) + '\n';
                i++;
            }
            html += `<blockquote>${renderMarkdown(bq.trim())}</blockquote>`;
            continue;
        }

        // Lista sin orden (incluye checkboxes)
        if (/^[-*+] /.test(line)) {
            let items = '';
            while (i < lines.length && /^[-*+] /.test(lines[i])) {
                const l = lines[i];
                const cb = l.match(/^[-*+] \[( |x)\] (.*)$/i);
                if (cb) {
                    const chk = cb[1].toLowerCase() === 'x';
                    items += `<li style="list-style:none;display:flex;align-items:baseline;gap:0.5em;">
                        <input type="checkbox" ${chk ? 'checked' : ''} disabled
                            style="accent-color:var(--color-primary);margin-top:0.2em;flex-shrink:0;">
                        <span style="${chk ? 'text-decoration:line-through;opacity:0.6;' : ''}">${inlineRender(cb[2])}</span>
                    </li>`;
                } else {
                    items += `<li>${inlineRender(l.replace(/^[-*+] /, ''))}</li>`;
                }
                i++;
            }
            html += `<ul>${items}</ul>`;
            continue;
        }

        // Lista ordenada
        if (/^\d+\. /.test(line)) {
            let items = '';
            while (i < lines.length && /^\d+\. /.test(lines[i])) {
                items += `<li>${inlineRender(lines[i].replace(/^\d+\. /, ''))}</li>`;
                i++;
            }
            html += `<ol>${items}</ol>`;
            continue;
        }

        // Tabla simple (GFM)
        if (line.includes('|') && i + 1 < lines.length && /^[\s|:-]+$/.test(lines[i + 1])) {
            const headerCells = line.split('|').filter(c => c.trim() !== '').map(c => `<th>${inlineRender(c.trim())}</th>`).join('');
            i += 2; // skip header + separator
            let rows = '';
            while (i < lines.length && lines[i].includes('|')) {
                const cells = lines[i].split('|').filter(c => c.trim() !== '').map(c => `<td>${inlineRender(c.trim())}</td>`).join('');
                rows += `<tr>${cells}</tr>`;
                i++;
            }
            html += `<table><thead><tr>${headerCells}</tr></thead><tbody>${rows}</tbody></table>`;
            continue;
        }

        // Párrafo — junta líneas hasta que encuentra elemento de bloque o línea vacía
        let para = '';
        while (i < lines.length) {
            const l = lines[i];
            const t = l.trim();
            if (!t) break;
            if (/^#{1,6} /.test(t) || l.startsWith('```') || l.startsWith('> ')
                || /^[-*+] /.test(l) || /^\d+\. /.test(l)
                || /^[-*_]{3,}$/.test(t)) break;
            para += (para ? '\n' : '') + l;
            i++;
        }
        if (para.trim()) html += `<p>${inlineRender(para.replace(/\n/g, ' '))}</p>`;
    }

    return html;
}

// ── Editor de bloques ─────────────────────────────────────────────
class BlockEditor {
    constructor(container, hidden) {
        this.container  = container; // div#block-editor
        this.hidden     = hidden;    // textarea[name="content"] (oculto)
        this.blocks     = this._split(hidden.value || '');
        this._active    = null;      // índice del bloque activo
        this._init();
    }

    // Divide el markdown en bloques lógicos
    _split(content) {
        if (!content.trim()) return [''];
        const result = [];
        let current  = '';
        let inFence  = false;

        for (const line of content.split('\n')) {
            if (line.startsWith('```')) inFence = !inFence;
            if (!inFence && !line.trim() && current.trim()) {
                result.push(current.trimEnd());
                current = '';
            } else {
                if (current) current += '\n';
                current += line;
            }
        }
        if (current.trim()) result.push(current.trimEnd());
        return result.length ? result : [''];
    }

    _syncHidden() {
        this.hidden.value = this.blocks.join('\n\n');
        // Contador de palabras / chars en el toolbar
        const text  = this.hidden.value;
        const words = text.trim() ? text.trim().split(/\s+/).length : 0;
        const wc = document.getElementById('word-count');
        const cc = document.getElementById('char-count');
        if (wc) wc.textContent = words;
        if (cc) cc.textContent = text.length;
        // Actualizar preview en vivo
        this._updateLivePreview();
    }

    _updateLivePreview() {
        const previewContent = document.getElementById('editor-preview-content');
        if (!previewContent) return;

        const content = this.hidden.value;
        if (content.trim()) {
            previewContent.innerHTML = renderMarkdown(content);
        } else {
            previewContent.innerHTML = '<p class="block-placeholder">Empieza a escribir para ver la vista previa...</p>';
        }

        // Sincronizar título en el panel de preview
        const titleInput = document.getElementById('title');
        const previewTitle = document.getElementById('preview-note-title');
        if (titleInput && previewTitle) {
            previewTitle.textContent = titleInput.value.trim() || 'Sin título';
        }
    }

    _init() {
        this._renderAll();
        this._bindEvents();
        // Mostrar preview inicial
        this._updateLivePreview();
    }

    _renderAll() {
        this.container.innerHTML = '';
        this.blocks.forEach((b, i) => this._makeBlockEl(b, i));
        this._appendEndArea();
    }

    _makeBlockEl(markdown, index) {
        const div        = document.createElement('div');
        div.className    = 'block-wrapper';
        div.dataset.idx  = String(index);
        div.innerHTML    = markdown.trim()
            ? renderMarkdown(markdown)
            : `<p class="block-placeholder">Escribe algo...</p>`;
        this.container.insertBefore(div, this.container.querySelector('.editor-end-area'));
        return div;
    }

    _appendEndArea() {
        const el = document.createElement('div');
        el.className = 'editor-end-area';
        el.setAttribute('role', 'button');
        el.setAttribute('aria-label', 'Añadir texto');
        this.container.appendChild(el);
    }

    // Activa (edita) el bloque con ese índice
    activate(index) {
        if (this._active === index) return;
        if (this._active !== null) this.deactivate(this._active);

        const div = this._getEl(index);
        if (!div) return;
        this._active = index;
        div.classList.add('is-active');

        const markdown = this.blocks[index] ?? '';
        const ta       = document.createElement('textarea');
        ta.className   = 'block-textarea';
        ta.value       = markdown;
        ta.placeholder = index === 0
            ? 'Escribe el título o el primer párrafo...'
            : 'Escribe en Markdown... (# Título, **negrita**, - [ ] tarea, > cita)';

        div.innerHTML = '';
        div.appendChild(ta);

        // Preview en vivo del bloque activo
        const previewDiv = document.createElement('div');
        previewDiv.className = 'block-live-preview prose-note';
        previewDiv.style.display = 'none';
        div.appendChild(previewDiv);

        // Exponer el textarea activo (para la toolbar)
        window.__activeBlockTA = ta;

        const resize = () => {
            ta.style.height = 'auto';
            ta.style.height = Math.max(28, ta.scrollHeight) + 'px';
        };

        const updateBlockPreview = () => {
            const val = ta.value;
            if (val.trim()) {
                previewDiv.style.display = '';
                previewDiv.innerHTML = renderMarkdown(val);
            } else {
                previewDiv.style.display = 'none';
            }
        };

        ta.addEventListener('input', () => {
            this.blocks[index] = ta.value;
            this._syncHidden();
            resize();
            updateBlockPreview();
        });

        ta.addEventListener('keydown', e => this._handleKey(e, index, ta));

        ta.addEventListener('blur', () => {
            setTimeout(() => {
                if (this._active !== index) return;
                this.deactivate(index);
            }, 80);
        });

        resize();
        // Mostrar preview inicial del bloque
        updateBlockPreview();
        ta.focus();
        ta.setSelectionRange(ta.value.length, ta.value.length);
    }

    deactivate(index) {
        const div = this._getEl(index);
        if (!div) return;
        if (this._active === index) this._active = null;
        div.classList.remove('is-active');
        window.__activeBlockTA = null;

        const md = this.blocks[index] ?? '';
        div.innerHTML = md.trim()
            ? renderMarkdown(md)
            : `<p class="block-placeholder">Escribe algo...</p>`;
    }

    _handleKey(e, index, ta) {
        // Tab → 2 espacios
        if (e.key === 'Tab') {
            e.preventDefault();
            const s = ta.selectionStart;
            ta.value = ta.value.slice(0, s) + '  ' + ta.value.slice(s);
            ta.selectionStart = ta.selectionEnd = s + 2;
            ta.dispatchEvent(new Event('input'));
            return;
        }

        // Enter al final en encabezado u bloque largo → nuevo bloque
        if (e.key === 'Enter' && !e.shiftKey) {
            const atEnd   = ta.selectionStart === ta.value.length;
            const isBlock = /^#{1,6} /.test(ta.value.trim()) || ta.value.trim().length > 60;
            if (atEnd && isBlock) {
                e.preventDefault();
                this._insertAfter(index, '');
                return;
            }
        }

        // Backspace en bloque vacío → borrar bloque
        if (e.key === 'Backspace' && ta.value === '' && this.blocks.length > 1) {
            e.preventDefault();
            this._deleteBlock(index);
        }
    }

    _insertAfter(index, markdown) {
        this.deactivate(index);
        this.blocks.splice(index + 1, 0, markdown);
        this._syncHidden();
        this._rerender();
        this.activate(index + 1);
    }

    _deleteBlock(index) {
        if (this.blocks.length <= 1) return;
        this.blocks.splice(index, 1);
        this._syncHidden();
        this._rerender();
        this.activate(Math.max(0, index - 1));
    }

    _rerender() {
        this._active = null;
        window.__activeBlockTA = null;
        [...this.container.querySelectorAll('.block-wrapper')].forEach(el => el.remove());
        this.blocks.forEach((b, i) => this._makeBlockEl(b, i));
    }

    _getEl(index) {
        return this.container.querySelector(`[data-idx="${index}"]`) ?? null;
    }

    _bindEvents() {
        this.container.addEventListener('click', e => {
            if (e.target.type === 'checkbox') return;
            const wrapper = e.target.closest('.block-wrapper');
            if (wrapper) {
                this.activate(parseInt(wrapper.dataset.idx));
                return;
            }
            if (e.target.classList.contains('editor-end-area')
                || e.target === this.container) {
                const last = this.blocks.length - 1;
                if (this.blocks[last]?.trim()) {
                    this._insertAfter(last, '');
                } else {
                    this.activate(last);
                }
            }
        });
    }

    // API pública para la toolbar
    wrapSelection(before, after = '') {
        const ta = window.__activeBlockTA;
        if (!ta) return;
        const s   = ta.selectionStart, end = ta.selectionEnd;
        const sel = ta.value.slice(s, end) || 'texto';
        ta.value = ta.value.slice(0, s) + before + sel + after + ta.value.slice(end);
        ta.selectionStart = s + before.length;
        ta.selectionEnd   = s + before.length + sel.length;
        ta.focus();
        ta.dispatchEvent(new Event('input'));
    }

    prefixLine(prefix) {
        const ta = window.__activeBlockTA;
        if (!ta) return;
        const s  = ta.selectionStart, v = ta.value;
        const ls = v.lastIndexOf('\n', s - 1) + 1;
        const le = v.indexOf('\n', s);
        const end = le === -1 ? v.length : le;
        const line = v.slice(ls, end);
        if (line.startsWith(prefix)) {
            ta.value = v.slice(0, ls) + line.slice(prefix.length) + v.slice(end);
            ta.selectionStart = ta.selectionEnd = Math.max(ls, s - prefix.length);
        } else {
            ta.value = v.slice(0, ls) + prefix + line + v.slice(end);
            ta.selectionStart = ta.selectionEnd = s + prefix.length;
        }
        ta.focus();
        ta.dispatchEvent(new Event('input'));
    }
}

// ── Gestión de modo (Split / Editor / Preview) ────────────────────
const EDITOR_MODE_KEY = 'nt_editor_mode';

function applyEditorMode(mode) {
    const editorPane  = document.getElementById('editor-pane');
    const previewPane = document.getElementById('preview-pane');
    const toolbarRow  = document.getElementById('editor-toolbar-row');
    if (!editorPane || !previewPane) return;

    // En móvil siempre editor
    const effectiveMode = window.innerWidth < 640 ? 'editor' : mode;

    if (effectiveMode === 'split') {
        editorPane.classList.remove('hidden');
        previewPane.classList.remove('hidden');
        if (toolbarRow) toolbarRow.style.display = '';
    } else if (effectiveMode === 'preview') {
        editorPane.classList.add('hidden');
        previewPane.classList.remove('hidden');
        if (toolbarRow) toolbarRow.style.display = 'none';
    } else {
        editorPane.classList.remove('hidden');
        previewPane.classList.add('hidden');
        if (toolbarRow) toolbarRow.style.display = '';
    }

    // Marcar botón activo
    document.querySelectorAll('[data-editor-mode]').forEach(btn => {
        btn.classList.toggle('is-active', btn.dataset.editorMode === mode);
    });

    localStorage.setItem(EDITOR_MODE_KEY, mode);
}

// ── Inicialización ────────────────────────────────────────────────
(() => {
    const form   = document.querySelector('form[data-editor]');
    if (!form) return;

    const hidden = form.querySelector('textarea[name="content"]');
    const edDiv  = document.getElementById('block-editor');
    if (!hidden || !edDiv) return;

    const editor = new BlockEditor(edDiv, hidden);
    window.__blockEditor = editor;

    // ── Modo split view ──────────────────────────────────────────
    const savedMode = localStorage.getItem(EDITOR_MODE_KEY) || 'split';
    applyEditorMode(savedMode);

    document.querySelectorAll('[data-editor-mode]').forEach(btn => {
        btn.addEventListener('click', () => applyEditorMode(btn.dataset.editorMode));
    });

    // Título sincronizado con el preview en vivo
    const titleInput = document.getElementById('title');
    titleInput?.addEventListener('input', () => {
        const previewTitle = document.getElementById('preview-note-title');
        if (previewTitle) {
            previewTitle.textContent = titleInput.value.trim() || 'Sin título';
        }
    });

    // ── Toolbar ──────────────────────────────────────────────────
    document.addEventListener('click', e => {
        const btn = e.target.closest('[data-action]');
        if (!btn) return;

        const a = btn.dataset.action;
        if (a === 'bold')        editor.wrapSelection('**', '**');
        else if (a === 'italic') editor.wrapSelection('*', '*');
        else if (a === 'code')   editor.wrapSelection('`', '`');
        else if (a === 'h1')     editor.prefixLine('# ');
        else if (a === 'h2')     editor.prefixLine('## ');
        else if (a === 'h3')     editor.prefixLine('### ');
        else if (a === 'ul')     editor.prefixLine('- ');
        else if (a === 'ol')     editor.prefixLine('1. ');
        else if (a === 'checkbox') editor.prefixLine('- [ ] ');
        else if (a === 'quote')  editor.prefixLine('> ');
        else if (a === 'codeblock') {
            const ta = window.__activeBlockTA;
            if (!ta) return;
            const s   = ta.selectionStart;
            const sel = ta.value.slice(s, ta.selectionEnd) || 'código';
            const ins = '```\n' + sel + '\n```';
            ta.value = ta.value.slice(0, s) + ins + ta.value.slice(ta.selectionEnd);
            ta.selectionStart = s + 4;
            ta.selectionEnd   = s + 4 + sel.length;
            ta.focus();
            ta.dispatchEvent(new Event('input'));
        }
    });

    // ── Atajos de teclado ─────────────────────────────────────────
    document.addEventListener('keydown', e => {
        const ta = window.__activeBlockTA;

        if (ta && (e.ctrlKey || e.metaKey)) {
            if (e.key === 'b') { e.preventDefault(); editor.wrapSelection('**', '**'); }
            if (e.key === 'i') { e.preventDefault(); editor.wrapSelection('*', '*'); }
            if (e.key === 'k') { e.preventDefault(); editor.wrapSelection('`', '`'); }
        }

        // Ctrl+S → guardar
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            form.requestSubmit();
        }
    });

    // Activar primer bloque si el editor está vacío
    if (!hidden.value.trim()) {
        setTimeout(() => editor.activate(0), 50);
    }
})();
