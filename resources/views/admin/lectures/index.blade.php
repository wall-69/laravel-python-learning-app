@extends('layouts.admin')

@section('content')
    <div>
        <nav class="navbar navbar-expand-md mb-3">
            <div class="container-fluid">
                <span class="navbar-brand">Lekcie</span>
                <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav">
                    <i class="navbar-toggler-icon"></i>
                </button>
                <div id="nav" class="collapse navbar-collapse justify-content-center">
                    <div
                        class="ms-auto d-flex gap-3 flex-column flex-md-row mt-3 mt-md-0 justify-content-center align-items-start align-items-md-center">
                        <form action="{{ route('admin.lectures') }}" method="GET" class="d-flex" role="search">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Názov lekcie"
                                    value="{{ request('search') }}">
                                <button class="btn btn-secondary" type="submit">Hľadať</button>
                            </div>
                        </form>
                        <a href="{{ route('admin.lectures.create') }}" class="btn btn-primary">Vytvoriť</a>
                    </div>
                </div>
            </div>
        </nav>

        <div class="container-fluid d-flex flex-column gap-3">
            @if ($lectures->isEmpty())
                <p>Neboli nájdené žiadne lekcie.</p>
            @else
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th class="d-none d-xl-table-cell">ID</th>
                            <th>Názov</th>
                            <th class="d-none d-sm-table-cell">Popis</th>
                            <th class="d-none d-lg-table-cell">Kategória</th>
                            <th>Videnia</th>
                            <th class="d-none d-sm-table-cell">Status</th>
                            <th>Akcie</th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">
                        @foreach ($lectures as $lecture)
                            <tr>
                                <td class="text-break d-none d-xl-table-cell">
                                    <a href="{{ route('lectures.show', [$lecture->id, $lecture->slug]) }}" target="_blank">
                                        {{ $lecture->id }}
                                    </a>
                                </td>
                                <td class="text-break">{{ $lecture->title }}</td>
                                <td class="text-break d-none d-sm-table-cell">
                                    <div class="admin-table-cell-line-limit">
                                        {{ $lecture->description }}
                                    </div>
                                </td>
                                <td class="text-break d-none d-lg-table-cell">
                                    <a href="{{ route('admin.categories') }}?search={{ $lecture->category->title }}">
                                        {{ $lecture->category->title }}
                                    </a>
                                </td>
                                <td>{{ $lecture->views }}</td>
                                <td class="d-none d-sm-table-cell">
                                    {{ __('status.' . $lecture->status) }}
                                </td>

                                <td>
                                    <div class="d-flex
                                        gap-2">
                                        <a href="{{ route('admin.lectures.edit', $lecture) }}"
                                            class="btn btn-secondary d-flex align-items-center">
                                            <i class="bx bxs-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.lectures.destroy', $lecture->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="btn btn-danger d-flex align-items-center">
                                                <i class="bx bxs-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{ $lectures->links() }}
            @endif
        </div>
    </div>
@endsection
