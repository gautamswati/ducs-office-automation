<form id="remove-noc" method="POST" onsubmit="return confirm('Do you really want to delete co-author?');">
    @csrf_token @method('DELETE')
</form>
<form action="{{ $route }}"
    method="post" class="px-6"
    enctype="multipart/form-data"
    x-data="{
        publication_type: '{{$publication->type}}',
        is_published: '{{array_key_exists('is_published', old()) ? true : $publication->isPublished()}}',
        'month': '{{ old('date.month', optional($publication->date)->format('F') ?? now()->format('F')) }}',
        'year': '{{ old('date.year', optional($publication->date)->format('Y') ?? now()->format('Y')) }}'
    }">
    @csrf_token
    @method('PATCH')
    <div class="mb-4">
        <label for="type" class="form-label block mb-1">
            Type of Publication <span class="text-red-600">*</span>
        </label>
        <input type="text" name="type" x-model="publication_type"
            class="form-input w-full cursor-not-allowed" readonly>
    </div>
    <div class="mb-4">
        <label for="paper_title" class="form-label block mb-1">
            Paper Title <span class="text-red-600">*</span>
        </label>
        <input type="text" value="{{ old('paper_title', $publication->paper_title) }}" name="paper_title"
            class="form-input w-full {{ $errors->has('paper_title') ? ' border-red-600' : ''}}"
            placeholder="Title of the publication">
    </div>
    @if(!$publication->isPublished())
        <div class="flex mb-4 items-center">
            <input type="checkbox" name="is_published" id="is_published"
                class="form-checkbox" x-model="is_published">
            <label for="is_published" class="form-label block ml-2">
                Is the paper published?
            </label>
        </div>
    @else
        <input type="hidden" name="is_published">
    @endif
    <template x-if="is_published">
        <div>
            @auth('scholars')
            @if(!$publication->isPublished())
                <div class="mb-4">
                    <label for="document" class="form-label block">
                        Upload the first page of the publication
                    </label>
                    <x-input.file name="document" id="document"
                    class="w-full form-input inline-flex items-center"
                    accept="application/pdf,image/*"
                    placeholder="Upload Document"
                    required/>
                </div>
            @endif
            @endauth
            <div class="mb-4">
                <label for="name" class="form-label block mb-1"
                x-show="publication_type == '{{App\Types\PublicationType::JOURNAL}}'">
                    Journal<span class="text-red-600">*</span>
                </label>
                <label for="name" class="form-label block mb-1"
                x-show="publication_type == '{{App\Types\PublicationType::CONFERENCE}}'">
                    Conference<span class="text-red-600">*</span>
                </label>
                <input type="text" value="{{ old('name', $publication->name) }}" name="name"
                    class="form-input w-full {{ $errors->has('name') ? ' border-red-600' : ''}}">
            </div>
            <div class="mb-4">
                <label for="paper_link" class="form-label block mb-1">
                    Link for the Paper
                </label>
                <input type="text" name="paper_link" id="paper_link" value="{{ old('paper_link', $publication->paper_link) }}"
                class="form-input w-full {{ $errors->has('paper_link') ? ' border-red-600' : ''}}">
            </div>
            <div class="flex mb-4">
                <div class="w-1/2">
                    <label for="date[]" class="form-label block mb-1">
                        Date <span class="text-red-600">*</span>
                    </label>
                    <div class="flex">
                        <select name="date[month]" id="date_month" class="form-select flex-1"
                            x-model="month" >
                            @foreach($months as $month)
                            <option value="{{ $month }}">
                                {{ $month }}
                            </option>
                            @endforeach
                        </select>
                        <select name="date[year]" id="date_year" class="form-select flex-1 ml-4"
                            x-model="year" >
                            @foreach(range($currentYear-10, $currentYear) as $year)
                            <option value="{{$year}}">
                                {{$year}}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="ml-4 w-1/2">
                    <label for="volume" class="form-label block mb-1">
                        Volume/ Edition
                    </label>
                    <input type="number" value="{{ old('volume', $publication->volume) }}" name="volume"
                        class="form-input w-full {{ $errors->has('volume') ? ' border-red-600' : ''}}">
                </div>
            </div>
            <div class="flex mb-4" x-show="'{{$publication->type}}' == '{{App\Types\PublicationType::JOURNAL}}'">
                <div class="w-1/2">
                    <label for="number" class="form-label block mb-1">
                        Number
                    </label>
                    <input type="number" value="{{ old('number', $publication->number) }}" name="number"
                        class="form-input w-full {{ $errors->has('number') ? ' border-red-600' : ''}}">
                </div>
                <div class="ml-4 w-1/2">
                    <label for="publisher" class="form-label block mb-1">
                        Publisher <span class="text-red-600">*</span>
                    </label>
                    <input type="text" value="{{ old('publisher', $publication->publisher) }}" name="publisher"
                        class="form-input w-full {{ $errors->has('publisher') ? ' border-red-600' : ''}}">
                </div>
            </div>
            <div class="mb-4 flex" x-show="'{{$publication->type}}' == '{{App\Types\PublicationType::CONFERENCE}}'">
                <div class="w-1/2">
                    <label for="city[]" class="form-label block mb-1">
                        City <span class="text-red-600">*</span>
                    </label>
                    <input type="text" value="{{ old('city', $publication->city) }}" name="city"
                    class="form-input text-sm w-full {{ $errors->has('city') ? ' border-red-600' : ''}}">
                </div>
                <div class="ml-4 w-1/2">
                    <label for="country" class="form-label block mb-1">
                        Country <span class="text-red-600">*</span>
                    </label>
                    <input type="text" value="{{ old('country', $publication->country) }}" name="country"
                        class="form-input text-sm w-full {{ $errors->has('country') ? ' border-red-600' : ''}}">
                </div>
            </div>
            <div class="mb-4">
                <label for="page_numbers[]" class="form-label block mb-1">
                    Page Numbers <span class="text-red-600">*</span>
                </label>
                <div class="flex">
                    <input type="number" value="{{ old('page_numbers.0', optional($publication->page_numbers)[0]) }}" name="page_numbers[]"
                        class="form-input text-sm w-1/2 {{ $errors->has('page_numbers[0]') ? ' border-red-600' : ''}}">
                    <input type="number" value="{{ old('page_numbers.1', optional($publication->page_numbers)[1]) }}" name="page_numbers[]"
                        class="form-input text-sm w-1/2 ml-4 {{ $errors->has('page_numbers[1]') ? ' border-red-600' : ''}}">
                </div>
            </div>
            <div class="mb-4">
                <label for="indexed_in[]" class="form-label block mb-1">
                    Indexed In <span class="text-red-600">*</span>
                </label>
                @foreach ($citationIndexes as $index)
                    @php($checked = in_array($index, array_map(
                        'strval', old('indexed_in', $publication->indexed_in ?? [] )
                    )))
                    <div class="flex mb-1">
                        <input id="indexed-in-{{ $index }}"
                            type="checkbox"
                            class="form-checkbox"
                            name="indexed_in[]"
                            value="{{ $index }}"
                            {{ $checked ? 'checked': ''}} >
                        <label for="indexed-in-{{ $index }}"
                            class="ml-2 form-label is-sm">
                            {{ $index }}
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
    </template>
    <div class="mt-6">
        <button type="submit" class="w-full btn btn-magenta">Update</button>
    </div>
</form>
