@extends('layouts.master')
@section('body')
<div class="m-6 page-card pb-0">
    <div class="flex items-baseline px-6 pb-4 border-b">
        <h1 class="page-header mb-0 px-0 mr-4">Academic Programmes</h1>
        <button class="btn btn-magenta is-sm shadow-inset" @click.prevent="$modal.show('create-programme-form')">
            New
        </button>
    </div>
    <modal name="create-programme-form" height="auto">
        <div class="p-6">
            <h2 class="text-lg font-bold mb-8">New Programme</h2>
            <form action="/programmes" method="POST" class="flex items-end">
                @csrf
                <div class="flex-1 mr-2">
                    <label for="programme_code" class="w-full form-label">Programme Code</label>
                    <input id="programme_code" type="text" name="code" class="w-full form-input">
                </div>
                <div class="flex-1 mr-5">
                    <label for="programme_name" class="w-full form-label">Programme</label>
                    <input id="programme_name" type="text" name="name" class="w-full form-input">
                </div>
                <div>
                    <button type="submit" class="btn btn-magenta">Create</button>
                </div>
            </form>
        </div>
    </modal>
    <programme-update-modal name="programme-update-modal">@csrf @method('patch')</programme-update-modal>
    @foreach ($programmes as $programme)
        <div class="px-6 py-2 hover:bg-gray-100 border-b flex justify-between">
            <div class="flex items-baseline">
                <h4 class="text-sm font-semibold text-gray-600 mr-2 w-24">{{ $programme->code }}</h4>
                <h3 class="text-lg font-bold mr-2">
                    {{ ucwords($programme->name) }}
                </h3>
            </div>
            <div class="flex">
                <button class="p-1 hover:text-blue-500 mr-1" @click.prevent="$modal.show('programme-update-modal', {programme: {{ $programme->toJson() }}})">
                    <feather-icon class="h-current" name="edit">Edit</feather-icon>
                </button>
                <form action="/programmes/{{ $programme->id }}" method="POST">
                    @csrf @method('delete')
                    <button type="submit" class="p-1 hover:text-red-700">
                        <feather-icon class="h-current" name="trash-2">Trash</feather-icon>
                    </button>
                </form>
            </div>
        </div>
    @endforeach

</div>
@endsection