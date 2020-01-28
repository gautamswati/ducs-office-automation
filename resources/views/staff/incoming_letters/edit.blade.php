@extends('layouts.master')
@section('body')
    <div class="page-card max-w-xl my-4 mx-auto">
        <div class="page-header flex items-baseline">
            <h2 class="mr-4">Update Incoming Letter</h2>
            <span class="ml-auto px-2 py-1 rounded text-sm uppercase text-white font-bold font-mono bg-gray-800">
                {{ $incoming_letter->serial_no }}
            </span>
        </div>
        <form action="{{ route('incoming_letters.update', $incoming_letter) }}"
            method="POST"
            class="px-6"
            enctype="multipart/form-data">
            @csrf_token
            @method('PATCH')
            <div class="mb-2">
                <label for="date" class="w-full form-label mb-1">
                    Date of Receipt <span class="text-red-600">*</span>
                </label>
                <input id="date"
                    type="date" name="date"
                    value="{{ old('date', $incoming_letter->date->format('Y-m-d')) }}"
                    class="w-full form-input{{ $errors->has('date') ? ' border-red-600' : ''}}"
                    requried>
                @if($errors->has('date'))
                    <p class="mt-1 text-red-600">{{ $errors->first('date') }}</p>
                @endif
            </div>
            <div class="mb-2">
                <label for="received_id" class="w-full form-label mb-1">
                    Received Letter Id <span class="text-red-600">*</span>
                </label>
                <input id="received_id"
                    type="text" name="received_id"
                    value="{{ old('received_id', $incoming_letter->received_id) }}"
                    class="w-full form-input{{ $errors->has('received_id') ? ' border-red-600' : ''}}"
                    placeholder="ID / Serial Number on the received Letter"
                    required>
                @if($errors->has('received_id'))
                    <p class="mt-1 text-red-600">{{ $errors->first('received_id') }}</p>
                @endif
            </div>
            <div class="mb-2">
                <label for="sender" class="w-full form-label mb-1">
                    Sender <span class="text-red-600">*</span>
                </label>
                <input id="sender"
                    type="text" name="sender"
                    value="{{ old('sender', $incoming_letter->sender) }}"
                    class="w-full form-input{{ $errors->has('sender') ? ' border-red-600' : ''}}"
                    placeholder="Sender (Received from)"
                    requried>
                @if($errors->has('sender'))
                    <p class="mt-1 text-red-600">{{ $errors->first('sender') }}</p>
                @endif
            </div>
            <div class="flex mb-2">
                <div class="flex-1 mr-1">
                    <label for="receiver" class="w-full form-label mb-1">
                        Recipient <span class="text-red-600">*</span>
                    </label>
                    <vue-typeahead
                        name="recipient_id"
                        source="/api/users"
                        find-source="/api/users/{value}"
                        limit="5"
                        value="{{ old('recipient_id', $incoming_letter->recipient_id) }}"
                        :has-errors="{{ $errors->has('recipient_id') ? 'true' : 'false'}}"
                        placeholder="Receiver (Received by)">
                    </vue-typeahead>
                    @if($errors->has('recipient_id'))
                        <p class="mt-1 text-red-600">{{ $errors->first('recipient_id') }}</p>
                    @endif
                </div>
                <div class="flex-1 ml-1">
                    <label for="handovers[]" class="w-full form-label mb-1">
                        Handovered To</label>
                    <v-multi-typeahead
                        name="handovers[]"
                        class="{{ $errors->has('handovers') ? ' border-red-600' : ''}}"
                        source="/api/users"
                        find-source="/api/users/{value}"
                        limit="5"
                        :value="{{ json_encode(old('handovers', $incoming_letter->handovers->map->id)) }}"
                        placeholder="Handovered To">
                    </v-multi-typeahead>
                    @if($errors->has('handovers'))
                        <p class="mt-1 text-red-600">{{ $errors->first('handovers') }}</p>
                    @endif
                </div>
            </div>
            <div class="mb-2">
                <label for="subject" class="w-full form-label mb-1">
                    Subject  <span class="text-red-600">*</span>
                </label>
                <input id="subject"
                    type="text" name="subject"
                    value="{{ old('subject', $incoming_letter->subject) }}"
                    class="w-full form-input{{ $errors->has('description') ? ' border-red-600' : ''}}"
                    placeholder="Subject of the letter"
                    required>
                @if($errors->has('subject'))
                    <p class="mt-1 text-red-600">{{ $errors->first('subject') }}</p>
                @endif
            </div>
            <div class="mb-2">
                <label for="priority" class="w-full form-label mb-1">Letter Priority</label>
                @php($priority = old('priority', $incoming_letter->priority))
                <select class="w-full form-input" name="priority">
                    <option value="">None</option>
                    <option value="1" {{ $priority == '1' ? 'selected' : ''}} >High</option>
                    <option value="2" {{ $priority == '2' ? 'selected' : ''}}>Medium</option>
                    <option value="3" {{ $priority == '3' ? 'selected' : ''}}>Low</option>
                </select>
                @if($errors->has('priority'))
                    <p class="mt-1 text-red-600">{{ $errors->first('priority') }}</p>
                @endif
            </div>
            <div class="mb-2">
                <label for="description" class="w-full form-label mb-1">Description</label>
                <textarea name="description" id="description"
                placeholder="What this letter is about?"
                class="w-full form-input" rows="3">{{ old('description') ?? $incoming_letter->description }}</textarea>
                @if($errors->has('description'))
                    <p class="mt-1 text-red-600">{{ $errors->first('description') }}</p>
                @endif
            </div>
            <div class="mb-2">
                @if($incoming_letter->attachments->count())
                    <label for="attachments" class="w-full form-label mb-1">Add Attachments</label>
                    <div class="flex flex-wrap -mx-2 mb-2">
                        @foreach ($incoming_letter->attachments as $attachment)
                        <div class="inline-flex items-center p-2 rounded border hover:bg-gray-300 text-gray-600 mx-2 my-1">
                            <a href="{{ route('attachments.show', $attachment) }}" target="__blank" class="inline-flex items-center mr-1">
                                <feather-icon name="paperclip" class="h-4 mr-2" stroke-width="2">View Attachment</feather-icon>
                                <span>{{ $attachment->original_name }}</span>
                            </a>
                            @can('delete', $attachment)
                                <button type="submit"
                                    form="remove-attachment"
                                    formaction="{{ route('attachments.destroy', $attachment) }}"
                                    class="p-1 rounded hover:bg-red-500 hover:text-white">
                                    <feather-icon name="x" class="h-4" stroke-width="2">Delete Attachment</feather-icon>
                                </button>
                            @endcan
                        </div>
                        @endforeach
                    </div>
                @else
                    <label for="attachments" class="w-full form-label mb-1">
                        Upload Attachments <span class="text-red-600">*</span>
                    </label>
                @endif
                <input type="file"
                    name="attachments[]"
                    accept="image/*, application/pdf"
                    class="w-full"
                    multiple
                    {{ $incoming_letter->attachments->count() < 1 ? 'required' : '' }}>
                @if($errors->has('file'))
                    <p class="mt-1 text-red-600">{{ $errors->first('file') }}</p>
                @endif
            </div>
            <div class="mt-6">
                <button type="submit" class="w-full btn btn-magenta">Update</button>
            </div>
        </form>
        <form id="remove-attachment"
            method="POST"
            onsubmit="return confirm('Do you really want to delete attachment?');">
            @csrf_token @method('DELETE')
        </form>
    </div>
@endsection
