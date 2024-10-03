<div class="row">
    <div class="col">
        <div class="">
            <label for="">Field {{$required ? '*' : ''}}</label>
            <div class="input-group input-group-outline">
                <select name="field" id="" class="form-control" wire:change="changeField()" wire:model="selectedField">
                <option value="">Select Field</option>
                @foreach ($fields as $field)
                    <option value="{{$field->id}}" >{{$field->name}}</option>
                @endforeach
            </select>
            </div>
        </div>
    </div>
    <div class="col">
            <div class="">
                <label for="">Category {{$required ? '*' : ''}}</label>
                <div class="input-group input-group-outline">
                    <select name="category" id="" class="form-control">
                    <option value="">Select Category</option>
                    @foreach ($live_categories as $cat)
                        <option value="{{$cat->id}}" {{isset($selectedCategory) && $selectedCategory == $cat->id ? 'selected' : ''}}>{{$cat->category}}</option>
                    @endforeach
                </select>
                </div>
            </div>
        </div>
</div>
