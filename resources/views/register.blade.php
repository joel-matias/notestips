<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registro</title>
</head>

<body>
    <h1>Registro</h1>

    {{-- Errores de validación --}}
    @if ($errors->any())
        <div style="border:1px solid #c00; padding:12px; margin-bottom:12px;">
            <strong>Hay errores:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register.store') }}">
        @csrf

        <div style="margin-bottom:12px;">
            <label for="username">Username</label><br>
            <input id="username" type="text" name="username" value="{{ old('username') }}" required maxlength="32">
        </div>

        <div style="margin-bottom:12px;">
            <label for="password">Password</label><br>
            <input id="password" type="password" name="password" required>
        </div>

        <div style="margin-bottom:12px;">
            <label for="password_confirmation">Confirmar password</label><br>
            <input id="password_confirmation" type="password" name="password_confirmation" required>
        </div>

        <button type="submit">Crear cuenta</button>
    </form>

    <hr>

    <p><a href="{{ url('/') }}">Volver al inicio</a></p>
</body>

</html>
