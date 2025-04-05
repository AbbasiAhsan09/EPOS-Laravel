<div>
   <select  class="{{$classes}}"  wire:model="selected_id" name="{{$name}}">
    <option value="">Select Party</option>
    @foreach ($parties as $group => $items)
    {{-- @dump($group) --}}
        <optgroup label="{{ucfirst($items["group_name"])}}">
            @foreach ($items["parties"] as $item)
                <option value="{{$item["id"]}}" >{{$item["party_name"]}}</option>
            @endforeach
        </optgroup>
    @endforeach
   </select>
</div>
