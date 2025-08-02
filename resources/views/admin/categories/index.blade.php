@extends('layouts.admin')

@section('content')
    <div>
        <nav class="navbar navbar-expand-md mb-3">
            <div class="container-fluid">
                <span class="navbar-brand">Kategórie</span>
                <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav">
                    <i class="navbar-toggler-icon"></i>
                </button>
                <div id="nav" class="collapse navbar-collapse justify-content-center">
                    <div
                        class="ms-auto d-flex gap-3 flex-column flex-md-row mt-3 mt-md-0 justify-content-center align-items-start align-items-md-center">
                        <form action="{{ route('admin.categories') }}" method="GET" class="d-flex" role="search">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Názov kategórie"
                                    value="{{ request('search') }}">
                                <button class="btn btn-secondary" type="submit">Hľadať</button>
                            </div>
                        </form>
                        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">Vytvoriť</a>
                    </div>
                </div>
            </div>
        </nav>

        <div class="container-fluid d-flex flex-column gap-3">
            @if ($categories->isEmpty())
                <p>Neboli nájdené žiadne kategórie.</p>
            @else
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nadpis</th>
                            <th class="d-none d-sm-table-cell">Popis</th>
                            <th>Akcie</th>
                        </tr>
                    </thead>
                    <tbody class="table-group-divider">
                        @foreach ($categories as $category)
                            <tr>
                                <td>{{ $category->title }}</td>
                                <td class="d-none d-sm-table-cell">
                                    <div class="admin-table-cell-line-limit">
                                        {{ $category->description }}
                                    </div>
                                </td>

                                <td>
                                    <div class="d-flex
                                        gap-2">
                                        <a href="{{ route('admin.categories.edit', $category) }}"
                                            class="btn btn-secondary d-flex align-items-center">
                                            <i class="bx bxs-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.categories.destroy', $category->id) }}"
                                            method="POST">
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

                {{ $categories->links() }}
            @endif
        </div>
    </div>
@endsection
