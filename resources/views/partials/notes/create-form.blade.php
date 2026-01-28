<form action="{{ route('notes.store') }}" method="POST">
    @csrf
    <label for="due_date">fecha a realizar</label>
    <input type="date" name="due_date" value="{{ old('due_date') }}">
    @error('due_date')
        <p>{{ $message }}</p>
    @enderror
    <label for="importance">importancia</label>
    <select name="importance" id="importance">
        <option value="">-- Sin importancia --</option>
        <option value="baja" {{ old('importance') == 'baja' ? 'selected' : '' }}>baja</option>
        <option value="media" {{ old('importance') == 'media' ? 'selected' : '' }}>media</option>
        <option value="alta" {{ old('importance') == 'alta' ? 'selected' : '' }}>alta</option>
    </select>
    @error('importance')
        <p>{{ $message }}</p>
    @enderror
    <input type="text" name="title" value="{{ old('title', 'Titulo de Ejemplo') }}" placeholder="Titulo">
    @error('title')
        <p>{{ $message }}</p>
    @enderror
    <textarea id="content" name="content" placeholder="Contenido de la nota">{{ old('content', 'Contenido de ejemplo, puedes editarlo o crear una nueva nota') }}</textarea>
    @error('content')
        <p>{{ $message }}</p>
    @enderror
    <button type="submit">Guardar nota</button>
    <button type="submit">Cancelar</button>
</form>
