<v-modal name="{{ $modalName }}" height="auto">
    <div class="p-6">
        <h2 class="text-lg font-bold mb-8">New Programme</h2>
        <form action="{{ route('programmes.store') }}" method="POST" class="px-6">
            @csrf_token
            <div class="mb-2">
                <label for="programme_code" class="w-full form-label">Code<span
                        class="h-current text-red-500 text-lg">*</span></label>
                <input id="programme_code" type="text" name="code" class="w-full form-input">
            </div>
            <div class="mb-2">
                <label for="programme_wef" class="w-full form-label">Date (w.e.f)<span
                        class="h-current text-red-500 text-lg">*</span></label>
                <input id="programme_wef" type="date" name="wef" class="w-full form-input">
            </div>
            <div class="mb-2">
                <label for="programme_name" class="w-full form-label">Name<span
                        class="h-current text-red-500 text-lg">*</span></label>
                <input id="programme_name" type="text" name="name" class="w-full form-input">
            </div>
            <div class="mb-2">
                <label for="programme_duration" class="w-full form-label">Duration<span
                        class="h-current text-red-500 text-lg">*</span></label>
                <input id="programme_duration" type="number" name="duration" class="w-full form-input">
            </div>
            <div class="mb-2">
                <label for="programme_type" class="w-full form-label">Type<span
                        class="h-current text-red-500 text-lg">*</span></label>
                <select class="w-full form-input" name="type" required>
                    <option value="Under Graduate(U.G.)"
                        {{ old('type', 'Under Graduate(U.G.)') === 'Under Graduate(U.G.)' ? 'selected' : ''}}>Under
                        Graduate(U.G.)</option>
                    <option value="Post Graduate(P.G.)"
                        {{ old('type', 'Post Graduate(P.G.)') === 'Post Graduate(P.G.)' ? 'selected' : ''}}>Post
                        Graduate(P.G.)</option>
                </select>
            </div>
            <div class="mb-2">
                <label for="courses[]" class="w-full form-label">Add Courses</label>
                <v-multi-typeahead
                    class="{{ $errors->has('courses') ? 'border-red-600' : ''}}"
                    name="courses[]"
                    source="/api/courses"
                    find-source="/api/courses/{value}"
                    limit="5"
                    value="{{ old('course_id')}}"
                    placeholder="Courses"
                    >
                </v-multi-typeahead>
                @if($errors->has('courses'))
                    <p class="mt-1 text-red-600">{{$errors->first('courses')}}</p>
                @endif
            </div>
            <div class="mb-2">
                <button type="submit" class="btn btn-magenta">Create</button>
            </div>
        </form>
    </div>
</v-modal>
