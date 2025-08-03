@extends('layouts.admin')

@section('content')
    <div>
        <form action="{{ route('admin.lectures.update', $lecture) }}" method="POST" class="mw-form p-3">
            @csrf
            @method('PATCH')

            <h3 class=" text-primary mb-3">
                Upraviť lekciu
            </h3>

            {{-- Title --}}
            <div class="mb-3">
                <label for="titleInput" class="form-label">Názov</label>
                <input type="text" name="title" id="titleInput" class="form-control @error('title') is-invalid @enderror"
                    placeholder="Základy" value="{{ old('title', $lecture->title) }}" required>

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
                    rows="3" required>{{ old('description', $lecture->description) }}</textarea>

                @error('description')
                    <span class="text-danger mt-1">
                        {{ $message }}
                    </span>
                @enderror
            </div>

            {{-- Blocks --}}
            <div class="mb-3">
                <label for="blocksInput" class="form-label">Lekcia:</label>
                <input type="hidden" name="blocks" id="blocksInput" value="{{ old('blocks', $lecture->blocks) }}" />
                <admin-editor></admin-editor>

                @error('blocks')
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
                        <option @selected(old('status', $lecture->status) == $status) value="{{ $status }}">{{ __('status.' . $status) }}
                        </option>
                    @endforeach
                </select>

                @error('status')
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
