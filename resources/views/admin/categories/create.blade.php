@extends('layouts.admin')

@section('content')
    <div>
        <form action="{{ route('admin.categories.store') }}" method="POST" class="mw-form p-3">
            @csrf
            @method('POST')

            <h3 class=" text-primary mb-3">
                Vytvoriť kategóriu
            </h3>

            {{-- Title --}}
            <div class="mb-3">
                <label for="titleInput" class="form-label">Nadpis</label>
                <input type="text" name="title" id="titleInput" class="form-control @error('title') is-invalid @enderror"
                    placeholder="Python základy" value="{{ old('title') }}" required>

                @error('title')
                    <span class="text-danger mt-1">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            {{-- Description --}}
            <div class="mb-3">
                <label for="descriptionInput" class="form-label">Popis</label>
                <textarea name="description" id="descriptionInput" class="form-control @error('title') is-invalid @enderror"
                    rows="3" required>{{ old('description') }}</textarea>

                @error('description')
                    <span class="text-danger mt-1">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            {{-- Create --}}
            <div class="">
                <button type="submit" class="btn btn-primary btn-sm py-2">
                    Vytvoriť
                </button>
            </div>
        </form>
    </div>
@endsection
