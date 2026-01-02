@extends('layouts.admin')

@section('content')
    <div>
        <nav class="navbar navbar-expand-md mb-3">
            <div class="container-fluid">
                <span class="navbar-brand">Používatelia</span>
                <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav">
                    <i class="navbar-toggler-icon"></i>
                </button>
                <div id="nav" class="collapse navbar-collapse justify-content-center">
                    <div
                        class="ms-auto d-flex gap-3 flex-column flex-md-row mt-3 mt-md-0 justify-content-center align-items-start align-items-md-center">
                        <form action="{{ route('admin.users') }}" method="GET" class="d-flex" role="search">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Meno používateľa"
                                    value="{{ request('search') }}">
                                <button class="btn btn-secondary" type="submit">Hľadať</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <div class="container-fluid d-flex flex-column gap-3">
            @if ($users->isEmpty())
                <p>Neboli nájdené žiadne používatelia.</p>
            @else
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="d-none d-sm-table-cell">Meno</th>
                            <th>Email</th>
                            <th class="d-none d-md-table-cell">Zaregistrovaný</th>
                            <th>Zablokovaný</th>
                            <th>Akcie</th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">
                        @foreach ($users as $user)
                            <tr>
                                <td class="d-none d-sm-table-cell">
                                    {{ $user->first_name }} {{ $user->last_name }}
                                </td>
                                <td class="text-nowrap">
                                    {{ $user->email }}
                                </td>
                                <td class="d-none d-md-table-cell">
                                    {{ $user->created_at->format('d.m.Y H:i') }}
                                </td>
                                <td>
                                    @if ($user->banned_at)
                                        Áno
                                    @else
                                        Nie
                                    @endif
                                </td>

                                @if ($user->banned_at)
                                    <td>
                                        <div class="d-flex gap-2">
                                            <form action="{{ route('admin.users.unban', $user->id) }}" method="POST">
                                                @csrf
                                                @method('POST')

                                                <button type="submit" class="btn btn-success d-flex align-items-center"
                                                    onclick="return confirm('Ste si istý, že chcete odblokovať účet {{ $user->first_name }} {{ $user->last_name }}?')">
                                                    <i class="bx bxs-lock-open-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                @else
                                    <td>
                                        <div class="d-flex gap-2">
                                            <form action="{{ route('admin.users.ban', $user->id) }}" method="POST">
                                                @csrf
                                                @method('POST')

                                                <button type="submit" class="btn btn-danger d-flex align-items-center"
                                                    onclick="return confirm('Ste si istý, že chcete zablokovať účet {{ $user->first_name }} {{ $user->last_name }}?')">
                                                    <i class="bx bxs-lock"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $users->links() }}
            @endif
        </div>
    </div>
@endsection
