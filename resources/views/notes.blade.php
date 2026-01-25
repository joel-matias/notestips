@extends('layout.app')

@section('titulo', 'Notes | NotesTips')

@section('main-content')
    <h1>Notas</h1>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit">
            Cerrar sesion
        </button>
    </form>
@endsection
