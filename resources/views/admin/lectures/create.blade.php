@extends('layouts.admin')

@section('content')
    <div>
        <form action="{{ route('admin.lectures.store') }}" method="POST" class="mw-form p-3">
            @csrf
            @method('POST')

            <h3 class=" text-primary mb-3">
                Vytvoriť lekciu
            </h3>

            {{-- Title --}}
            <div class="mb-3">
                <label for="titleInput" class="form-label">Názov</label>
                <input type="text" name="title" id="titleInput" class="form-control @error('title') is-invalid @enderror"
                    placeholder="Základy" value="{{ old('title') }}" required>

                @error('title')
                    <span class="text-danger mt-1">
                        {{ $message }}
                    </span>
                @enderror
                @error('slug')
                    <span class="text-danger mt-1">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            {{-- Description --}}
            <div class="mb-3">
                <label for="descriptionInput" class="form-label">Popis</label>
                <textarea name="description" id="descriptionInput" class="form-control @error('description') is-invalid @enderror"
                    rows="3" required>{{ old('description') }}</textarea>

                @error('description')
                    <span class="text-danger mt-1">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            {{-- Blocks --}}
            <div class="mb-3">
                <label for="blocksInput" class="form-label">Lekcia:</label>
                <input type="hidden" name="blocks" id="blocksInput" value="{{ old('blocks') }}" />
                <admin-editor></admin-editor>

                @error('blocks')
                    <span class="text-danger mt-1">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            {{-- Category --}}
            <div class="mb-3">
                <label for="categoryIdInput" class="form-label">Kategória:</label>
                <select class="form-select @error('category_id') is-invalid @enderror" name="category_id"
                    id="categoryIdInput">
                    <option selected disabled>Vyberte kategóriu</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->title }}</option>
                    @endforeach
                </select>

                @error('category_id')
                    <span class="text-danger mt-1">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            {{-- Category order --}}
            <div class="mb-3">
                <label for="categoryOrderInput" class="form-label">Poradie v kategórii:</label>
                <input type="number" name="category_order" id="categoryOrderInput"
                    class="form-control @error('category_order') is-invalid @enderror" value="{{ old('category_order') }}"
                    min="1" max="100" placeholder="1" required>

                @error('category_order')
                    <span class="text-danger mt-1">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            {{-- Status --}}
            <div class="mb-3">
                <label for="statusInput" class="form-label">Status</label>
                <select name="status" id="statusInput" class="form-select @error('status') is-invalid @enderror">
                    <option value="" disabled selected hidden>Vyberte status</option>
                    @foreach ($lectureStatuses as $status)
                        <option @selected(old('status') == $status) value="{{ $status }}">{{ __('status.' . $status) }}
                        </option>
                    @endforeach
                </select>

                @error('status')
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
