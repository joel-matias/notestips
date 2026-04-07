@extends('layout.notes.app')

@section('title', 'Nueva nota | NotesTips')

@section('header-title')
    <span class="text-sm font-medium" style="color: var(--color-text-muted);">Nueva nota</span>
@endsection

@section('main-content')
    <div class="h-full overflow-auto" style="background: var(--color-bg);">
        @include('partials.notes.create-form')
    </div>
@endsection
