@extends('layouts.admin')

@section('content')
    <div>
        <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="mw-form p-3">
            @csrf
            @method('PATCH')

            <h3 class=" text-primary mb-3">
                Upraviť kategóriu
            </h3>

            {{-- Title --}}
            <div class="mb-3">
                <label for="titleInput" class="form-label">Názov</label>
                <input type="text" name="title" id="titleInput" class="form-control @error('title') is-invalid @enderror"
                    placeholder="Python základy" value="{{ old('title', $category->title) }}" required>

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
                    rows="3" required>{{ old('description', $category->description) }}</textarea>

                @error('description')
                    <span class="text-danger mt-1">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            {{-- Update --}}
            <div>
                <button type="submit" class="btn btn-primary btn-sm py-2">
                    Upraviť
                </button>
            </div>
        </form>
    </div>
@endsection
