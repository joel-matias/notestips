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
        .replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2">$1</a>');
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
            html += `<pre><code>${esc(code.trimEnd())}</code></pre>`;
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
                    items += `<li style="list-style:none"><input type="checkbox" ${chk ? 'checked' : ''} disabled style="accent-color:var(--color-primary);margin-right:0.4em;"> ${inlineRender(cb[2])}</li>`;
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

        // Párrafo — junta líneas hasta que encuentra elemento de bloque o línea vacía
        let para = '';
        while (i < lines.length) {
            const l = lines[i];
            const t = l.trim();
            if (!t) break;
            if (/^#{1,6} /.test(t) || l.startsWith('```') || l.startsWith('> ')
                || /^[-*+] /.test(l) || /^\d+\. /.test(l)
                || /^[-*_]{3,}$/.test(t)) break;
            para += (para ? ' ' : '') + l;
            i++;
        }
        if (para.trim()) html += `<p>${inlineRender(para)}</p>`;
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
        // Contador de palabras / chars en el header
        const text  = this.hidden.value;
        const words = text.trim() ? text.trim().split(/\s+/).length : 0;
        const wc = document.getElementById('word-count');
        const cc = document.getElementById('char-count');
        if (wc) wc.textContent = words;
        if (cc) cc.textContent = text.length;
    }

    _init() {
        this._renderAll();
        this._bindEvents();
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
        ta.placeholder = index === 0 ? 'Escribe el título o el primer párrafo...' : 'Escribe en Markdown...';

        div.innerHTML = '';
        div.appendChild(ta);

        // Exponer el textarea activo (para la toolbar)
        window.__activeBlockTA = ta;

        const resize = () => {
            ta.style.height = 'auto';
            ta.style.height = Math.max(28, ta.scrollHeight) + 'px';
        };

        ta.addEventListener('input', () => {
            this.blocks[index] = ta.value;
            this._syncHidden();
            resize();
        });

        ta.addEventListener('keydown', e => this._handleKey(e, index, ta));

        ta.addEventListener('blur', () => {
            // Pequeño delay para que un clic en otro bloque no cause doble deactivate
            setTimeout(() => {
                if (this._active !== index) return;
                this.deactivate(index);
            }, 80);
        });

        resize();
        ta.focus();
        // Mover cursor al final
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
        // Quitar bloques existentes (no el end area)
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

// ── Inicialización ────────────────────────────────────────────────
(() => {
    const form   = document.querySelector('form[data-editor]');
    if (!form) return;

    const hidden = form.querySelector('textarea[name="content"]');
    const edDiv  = document.getElementById('block-editor');
    if (!hidden || !edDiv) return;

    const editor = new BlockEditor(edDiv, hidden);
    window.__blockEditor = editor;

    // ── Toolbar ──────────────────────────────────────────────────
    document.addEventListener('click', e => {
        const btn = e.target.closest('[data-action]');
        if (!btn) return;

        const a = btn.dataset.action;
        if (a === 'bold')       editor.wrapSelection('**', '**');
        else if (a === 'italic')     editor.wrapSelection('*', '*');
        else if (a === 'code')       editor.wrapSelection('`', '`');
        else if (a === 'h1')         editor.prefixLine('# ');
        else if (a === 'h2')         editor.prefixLine('## ');
        else if (a === 'h3')         editor.prefixLine('### ');
        else if (a === 'ul')         editor.prefixLine('- ');
        else if (a === 'ol')         editor.prefixLine('1. ');
        else if (a === 'checkbox')   editor.prefixLine('- [ ] ');
        else if (a === 'quote')      editor.prefixLine('> ');
        else if (a === 'codeblock') {
            const ta = window.__activeBlockTA;
            if (!ta) return;
            const s = ta.selectionStart;
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
