<div>
    {{-- show preloader while fetching data in the background --}}
    <div class="fixed top-8 left-1/2 -translate-x-1/2 z-50 animate-pulse" wire:loading>
        <x-tmk.preloader class="bg-lime-700/60 text-white border border-lime-700 shadow-2xl">
            {{$loading}}
        </x-tmk.preloader>
    </div>
    {{-- filter section: artist or title, genre, max price and records per page --}}
    <div class="grid grid-cols-10 gap-4">
        <div class="col-span-10 md:col-span-5 lg:col-span-3">
            <x-label for="name" value="Filter"/>
            <div
                class="relative">
                <x-input id="name" type="text"
                         wire:model.live.debounce.300ms="name"
                         class="block mt-1 w-full"
                         placeholder="Filter Artist Or Record"/>
                <button
                    @click="$wire.set('name','')"
                    class="w-5 absolute right-4 top-3">
                    <x-phosphor-x/>
                </button>
            </div>
        </div>
        <div class="col-span-5 md:col-span-2 lg:col-span-2">
            <x-label for="genre" value="Genre"/>
            <x-tmk.form.select id="genre"
                               wire:model.live="genre"
                               class="block mt-1 w-full">
                <option value="%">All Genres</option>
                @foreach($allGenres as $g)
                    <option value="{{$g->id}}">
                        {{$g->name}} ({{$g->records_count}})
                    </option>
                @endforeach
            </x-tmk.form.select>
        </div>
        <div class="col-span-5 md:col-span-3 lg:col-span-2">
            <x-label for="perPage" value="Records per page"/>
            <x-tmk.form.select id="perPage"
                               wire:model.live="perPage"
                               class="block mt-1 w-full">
                @foreach ([3,6,9,12,15,18,24] as $value)
                    <option value="{{ $value }}">{{ $value }}</option>
                @endforeach
            </x-tmk.form.select>
        </div>
        <div class="col-span-10 lg:col-span-3">
            <x-label for="price">Price &le;
                <output id="pricefilter" name="pricefilter">{{ $price }}</output>
            </x-label>
            <x-input type="range" id="price" name="price"
                     wire:model.live="price"
                     min="{{ $priceMin }}"
                     max="{{ $priceMax }}"
                     oninput="pricefilter.value = price.value"
                     class="block mt-4 w-full h-2 bg-indigo-100 accent-indigo-600 appearance-none"/>
        </div>
    </div>
    {{-- master section: cards with paginationlinks --}}
    <div class="my-4">{{$records->links()}}</div>
    <div class="grid grid-cols-1 lg:grid-cols-2 2xl:grid-cols-3 gap-8 mt-8">
        @foreach($records as $record)
            <div
                wire:key="record-{{$record->id}}"
                class="flex bg-white border border-gray-300 shadow-md rounded-lg overflow-hidden">
                <img class="w-52 h-52 border-r border-gray-300 object-cover"
                     src="{{ $record->cover }}"
                     alt="{{$record->title}}"
                     title="{{$record->title}}">
                <div class="flex-1 flex flex-col">
                    <div class="flex-1 p-4">
                        <p class="text-lg font-medium">{{$record->artist}}</p>
                        <p class="italic pb-2">{{$record->title}}</p>
                        <p class="italic font-thin text-right pt-2 mb-2">{{$record->genre_name}}</p>
                    </div>
                    <div class="flex justify-between border-t border-gray-300 bg-gray-100 px-4 py-2">
                        <div>{{$record->price_euro}}</div>
                        <div class="flex space-x-4">
                            @if($record->stock > 0)
                                <button class="w-6 hover:text-red-900"
                                        data-tippy-content="Add to basket <br><span class='text-red-300'>NOT IMPLEMENTED YET</span>">
                                    <x-phosphor-shopping-bag-light class="outline-0"/>
                                </button>
                            @else
                                <p class="font-extrabold text-red-700">SOLD OUT</p>
                            @endif
                            <button class="w-6 hover:text-red-900"
                                    data-tippy-content="Show tracks">
                                <x-phosphor-music-notes-light
                                    wire:click="showTracks({{$record->id}})"
                                    class="outline-0"/>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <div class="my-4">{{$records->links()}}</div>

    {{-- No records found --}}
    @if($records->isEmpty())
        @if($genre === '%')
            <x-tmk.alert type="danger" class="w-full">
                Can't find any artist or album with <b>'{{ $name }}'</b> and a price <b>'{{$price}}'</b>
            </x-tmk.alert>
        @else
            <x-tmk.alert type="danger" class="w-full">
                Can't find any artist or album with <b>'{{$name}}'</b> for the genre <b>'{{ $allGenres->where('id',$genre)->first()->name }}'</b> and a price <b>'{{$price}}'</b>
            </x-tmk.alert>
        @endif
    @endif
    {{-- Detail section --}}
    <x-dialog-modal wire:model="showModal">
        <x-slot name="title">
            <div class="flex items-center border-b border-gray-300 pb-2 gap-4">
                <img class="w-20 h-20" src="{{$selectedRecord->cover ?? asset('storage/covers/no-cover.png')}}" alt="">
                <h3 class="font-medium">
                    {{$selectedRecord->title ?? ''}} <br/>
                    <span class="font-light">{{$selectedRecord->artist ?? ''}}</span>
                </h3>
            </div>
        </x-slot>
        <x-slot name="content">
            @isset($selectedRecord->tracks)
                <table class="w-full text-left align-top">
                    <thead>
                    <tr>
                        <th class="px-4 py-2">#</th>
                        <th class="px-4 py-2">Track</th>
                        <th class="px-4 py-2">Length</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($selectedRecord['tracks'] as $track)
                        <tr class="border-t border-gray-100">
                            <td class="px-4 py-2">{{$track['position']}}</td>
                            <td class="px-4 py-2">{{$track['title']}}</td>
                            <td class="px-4 py-2">{{\Carbon\Carbon::createFromTimestampMs($track['length'])->format('i:s')}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endisset
        </x-slot>
        <x-slot name="footer"></x-slot>
    </x-dialog-modal>
    <x-tmk.livewire-log/>
</div>
