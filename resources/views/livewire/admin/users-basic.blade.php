<div>
    <div class="fixed top-8 left-1/2 -translate-x-1/2 z-50 animate-pulse" wire:loading>
        <x-tmk.preloader class="bg-lime-700/60 text-white border border-lime-700 shadow-2xl">
            {{$loading}}
        </x-tmk.preloader>
    </div>
    <x-tmk.section

        x-data="{open: false}"
        class="p-0 mb-4 flex flex-col gap-2">
        <div class="flex-1">
            <x-input id="search" type="text" placeholder="Filter Name"
                     wire:model.live.debounce.500ms="search"
                     wire:keydown.escape="resetValues()"
                     class="w-full shadow-md placeholder-gray-300"/>
        </div>
        <x-input-error for="filter" class="m-4 -mt-4 w-full"/>
    </x-tmk.section>

    <x-tmk.section>
        <div class="my-4">{{$users->links()}}</div>
        <table class="text-center w-full border border-gray-300">
            <colgroup>
                <col class="w-1/4">
                <col class="w-1/4">
                <col class="w-1/4">
                <col class="w-1/4">
            </colgroup>
            <thead>
            <tr class="bg-gray-100 text-gray-700 [&>th]:p-2 cursor-pointer">
                <th wire:click="resort('name')">
                    <span data-tippy-content="Order by name">Name</span>
                    <x-heroicon-s-chevron-up
                        class="w-5 text-slate-400
                            {{$orderAsc ?: 'rotate-180'}}
                            {{$orderBy === 'id' ? 'inline-block' : 'hidden'}}
                        "/>
                </th>
                <th wire:click="resort('email')">
                <span data-tippy-content="Order by email">
                    Email
                </span>
                    <x-heroicon-s-chevron-up
                        class="w-5 text-slate-400
                            {{$orderAsc ?: 'rotate-180'}}
                            {{$orderBy === 'id' ? 'inline-block' : 'hidden'}}
                        "/>
                </th>
                <th wire:click="resort('active')">
                    <span data-tippy-content="Order by active account">Active</span>
                    <x-heroicon-s-chevron-up
                        class="w-5 text-slate-400
                            {{$orderAsc ?: 'rotate-180'}}
                            {{$orderBy === 'id' ? 'inline-block' : 'hidden'}}
                        "/>
                </th>
                <th wire:click="resort('admin')">
                    <span data-tippy-content="Order by admin account">Admin</span>
                    <x-heroicon-s-chevron-up
                        class="w-5 text-slate-400
                            {{$orderAsc ?: 'rotate-180'}}
                            {{$orderBy === 'id' ? 'inline-block' : 'hidden'}}
                        "/>
                </th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr
                    wire:key="user-{{ $user->id }}"
                    class="border-t border-gray-300 [&>td]:p-2">
                    @if($editUser['id']!== $user->id)
                        <td
                            class="text-left cursor-pointer"
                            wire:click="edit({{ $user->id }})">{{$user->name}}
                        </td>
                    @else
                        <td>
                            <div class="flex flex-col text-left">
                                <x-input id="edit_{{$user->id}}" type="text"
                                         x-init="$el.focus()"
                                         @keydown.enter="$el.setAttribute('disabled',true);"
                                         @keydown.tab="$el.setAttribute('disabled',true);"
                                         @keydown.esc="$el.setAttribute('disabled',true);"
                                         wire:model="editUser.name"
                                         wire:keydown.enter="updateName({{$user->id}})"
                                         wire:keydown.tab="updateName({{$user->id}})"
                                         wire:keydown.escape="resetValues()"
                                         class="w-48"
                                />
                                <x-phosphor-arrows-clockwise
                                    wire:loading
                                    wire:target="updateName{{$user->id}}"
                                    class="w-5 h-5 text-gray-500 absolute animate-spin"/>
                                <x-input-error for="editUser.name" class="mt-2"/>
                            </div>
                        </td>
                    @endif
                    @if($editUser['id']!== $user->id)
                        <td
                            class="text-left cursor-pointer"
                            wire:click="edit({{ $user->id }})">{{$user->email}}
                        </td>
                    @else
                        <td>
                            <div class="flex flex-col text-left">
                                <x-input id="edit_{{$user->id}}" type="text"
                                         x-init="$el.focus()"
                                         @keydown.enter="$el.setAttribute('disabled',true);"
                                         @keydown.tab="$el.setAttribute('disabled',true);"
                                         @keydown.esc="$el.setAttribute('disabled',true);"
                                         wire:model="editUser.email"
                                         wire:keydown.enter="updateMail({{$user->id}})"
                                         wire:keydown.tab="updateMail({{$user->id}})"
                                         wire:keydown.escape="resetValues()"
                                         class="w-48"
                                />
                                <x-phosphor-arrows-clockwise
                                    wire:loading
                                    wire:target="updateMail{{$user->id}}"
                                    class="w-5 h-5 text-gray-500 absolute animate-spin"/>
                                <x-input-error for="editUser.email" class="mt-2"/>
                            </div>
                        </td>
                    @endif
                    <td>
                        {{$user->active}}
                    </td>
                    <td>{{$user->admin}}</td>
                    <td>
                        <div class="flex gap-1 justify-center [&>*]:cursor-pointer [&>*]:outline-0 [&>*]:transition">
                            @if($user->id === auth()->user()->admin)
                                <x-phosphor-trash-duotone
                                    wire:click="delete({{$user->id}})"
                                    wire:confirm="Are you sure you want to delete this user?"

                                    class="w-5 text-gray-300 hover:text-red-600" hidden/>
                            @else
                                <x-phosphor-trash-duotone
                                    wire:click="delete({{$user->id}})"
                                    wire:confirm="Are you sure you want to delete this user?"

                                    class="w-5 text-gray-300 hover:text-red-600"/>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach

            </tbody>
        </table>
        <div class="my-4">{{$users->links()}}</div>
    </x-tmk.section>
</div>
