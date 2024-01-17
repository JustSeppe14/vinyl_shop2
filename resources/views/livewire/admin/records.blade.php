<div>
    {{-- Filter --}}
    <x-tmk.section class="mb-4 flex gap-2">
        <div class="flex-1">
            <x-input id="search" type="text" placeholder="Filter Artist Or Record"
                     wire:model.live.debounce.500ms="search"
                     class="w-full shadow-md placeholder-gray-300"/>
        </div>
        <x-tmk.form.switch id="noStock"
                           wire:model.live="noStock"
                           text-off="No stock"
                           color-off="bg-gray-100 before:line-through"
                           text-on="No stock"
                           color-on="text-white bg-lime-600"
                           class="w-20 h-auto" />
        <x-tmk.form.switch id="noCover"
                           wire:model.live="noCover"
                           text-off="Records without cover"
                           color-off="bg-gray-100 before:line-through"
                           text-on="Records without cover"
                           color-on="text-white bg-lime-600"
                           class="w-44 h-auto" />
        <x-button wire:click="newRecord()">
            new record
        </x-button>
    </x-tmk.section>

    {{-- Table with records --}}
    <x-tmk.section>
        <div class="my-4">{{ $records->links() }}</div>
        <table class="text-center w-full border border-gray-300">
            <colgroup>
                <col class="w-14">
                <col class="w-20">
                <col class="w-20">
                <col class="w-14">
                <col class="w-max">
                <col class="w-24">
            </colgroup>
            <thead>
            <tr class="bg-gray-100 text-gray-700 [&>th]:p-2">
                <th>#</th>
                <th></th>
                <th>Price</th>
                <th>Stock</th>
                <th class="text-left">Record</th>
                <th>
                    <x-tmk.form.select id="perPage"
                                       wire:model.live="perPage"
                                       class="block mt-1 w-full">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="20">20</option>
                    </x-tmk.form.select>
                </th>
            </tr>
            </thead>
            <tbody>
            @forelse($records as $record)
                <tr
                    wire:key="{{$record->id}}"
                    class="border-t border-gray-300">
                    <td>{{ $record->id }}</td>
                    <td>
                        <img src="{{ $record->cover }}"
                             alt="{{ $record->title }} by {{ $record->artist }}"
                             class="my-2 border object-cover">
                    </td>
                    <td>{{ $record->price_euro }}</td>
                    <td>{{ $record->stock }}</td>
                    <td class="text-left">
                        <p class="text-lg font-medium">{{ $record->artist }}</p>
                        <p class="italic">{{ $record->title }}</p>
                        <p class="text-sm text-teal-700">{{ $record->genre_name }}</p>
                    </td>
                    <td>
                        <div class="border border-gray-300 rounded-md overflow-hidden m-2 grid grid-cols-2 h-10">
                            <button
                                wire:click="editRecord({{$record->id}})"
                                class="text-gray-400 hover:text-sky-100 hover:bg-sky-500 transition border-r border-gray-300">
                                <x-phosphor-pencil-line-duotone class="inline-block w-5 h-5"/>
                            </button>
                            <button
                                wire:click="deleteRecord({{$record->id}})"
                                wire:confirm="Are you sure you want to delete this record?"
                                class="text-gray-400 hover:text-red-100 hover:bg-red-500 transition">
                                <x-phosphor-trash-duotone class="inline-block w-5 h-5"/>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="border-t border-gray-300 p-4 text-center text-gray-500">
                        <div class="font-bold italic text-sky-800">No records found</div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
        <div class="my-4">{{ $records->links() }}</div>
    </x-tmk.section>

    {{-- Modal for add and update record --}}
    <x-dialog-modal id="recordModal"
                    wire:model.live="showModal">
        <x-slot name="title">
            <h2>{{is_null($form->id) ? 'New record' :  'Edit record'}}</h2>
        </x-slot>
        <x-slot name="content">
            {{-- error messages --}}
            @if ($errors->any())
                <x-tmk.alert type="danger">
                    <x-tmk.list>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </x-tmk.list>
                </x-tmk.alert>
            @endif
            {{-- show only if $form->id is empty --}}
            @if(!$form->id)
                <form wire:submit="getDataFromMusicbrainzApi()">
                    <x-label for="mb_id" value="MusicBrainz id"/>
                    <div class="flex flex-row gap-2 mt-2">
                        <x-input id="mb_id" type="text" placeholder="MusicBrainz ID"
                                 wire:model="form.mb_id"
                                 class="flex-1"/>
                        <x-button type="submit">
                            Get Record info
                        </x-button>
                    </div>
                </form>
            @endif
            <div class="flex flex-row gap-4 mt-4">
                <div class="flex-1 flex-col gap-2">
                    <p class="text-lg font-medium">{!! $form->artist ?? '&nbsp;' !!}</p>
                    <p class="italic">{!! $form->title ?? '&nbsp;' !!}</p>
                    <p class="text-sm text-teal-700">{!! $form->mb_id ? 'MusicBrainz id: ' . $form->mb_id : '&nbsp;' !!}</p>
                    <input type="hidden" wire:model="form.mb_id">
                    <x-label for="genre_id" value="Genre" class="mt-4"/>
                    <x-tmk.form.select wire:model="form.genre_id" id="genre_id" class="block mt-1 w-full">
                        <option value="">Select a genre</option>
                        @foreach($genres as $genre)
                            <option value="{{ $genre->id }}">{{ $genre->name }}</option>
                        @endforeach
                    </x-tmk.form.select>
                    <x-label for="price" value="Price" class="mt-4"/>
                    <x-input id="price" type="number" step="0.01"
                             wire:model="form.price"
                             class="mt-1 block w-full"/>
                    <x-label for="stock" value="Stock" class="mt-4"/>
                    <x-input id="stock" type="number"
                             wire:model="form.stock"
                             class="mt-1 block w-full"/>
                </div>
                <img src="{{ $form->cover }}" alt="" class="mt-4 w-40 h-40 border border-gray-300 object-cover">
            </div>
        </x-slot>
        <x-slot name="footer">
            <x-secondary-button @click="$wire.showModal = false">Cancel</x-secondary-button>
            @if(is_null($form->id))
                <x-tmk.form.button color="success"
                                   disabled="{{ $form->title ? 'false' : 'true' }}"
                                   wire:click="createRecord()"
                                   class="ml-2">Save new record
                </x-tmk.form.button>
            @else
                <x-tmk.form.button color="info"
                                   wire:click="updateRecord({{$form->id}})"
                                   class="ml-2">Save changes
                </x-tmk.form.button>
            @endif
        </x-slot>
    </x-dialog-modal>
</div>
