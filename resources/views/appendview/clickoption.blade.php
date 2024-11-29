
@if(!empty($contextTypes))
    @if ($type=='rightclick')
        <ul class="ullist">
            @foreach ($contextTypes as $contextType)
                    @if ($contextType->is_options != 1)
                        <a href="#" class="clickmenu {{ $contextType->function }} {{ session()->has($contextType->conditional) ? '' : 'hidden' }}" data-option="{{ $contextType->is_options }}">
                            <li class="flex items-center justify-between px-4 py-2">
                                <p class="text-c-black text-sm">{{ $contextType->name }}</p>
                                <p class="menu-sidename">{{ $contextType->shortcut }}</p>
                            </li>
                        </a>
                    @else
                        <li class="flex items-center justify-between px-4 py-2">
                            <p class="text-c-black text-sm">{{ $contextType->name }}</p>
                            <i class="ri-arrow-right-s-line"></i>
                            <ul class="submenu clickmenu newfile-submenu absolute shadow-md rounded-md hidden {{ $contextType->function }}" data-option="{{ $contextType->is_options }}">
                                @if (!empty($contextType->contextOptions))
                                    @foreach ($contextType->contextOptions as $option)
                                        <a href="#" class="clickmenu {{ $contextType->function }} {{ session()->has($contextType->conditional) ? '' : 'hidden' }}" data-type="{{ $option->function }}">
                                            <li class="flex items-center px-5 py-2 gap-2">
                                                @if (checkIconExist($option->image, 'menu'))
                                                    <img class="w-4" src="{{ checkIconExist($option->image, 'menu') }}" alt="{{ $option->name }}" />
                                                @else
                                                    {!! $option->icon !!}
                                                @endif
                                                <p class="text-c-black text-sm">{{ $option->name }}</p>
                                                @if($contextType->function == 'sortFunction')
                                                    <i class="ri-check-line pr-3 ri-lg mt-1 sortingcheck sorting{{ $option->function }} {{ (session()->has('sortby') && session()->has('sortorder') && session('sortby').'-'.session('sortorder') == $option->function) ? : 'hidden'}}"></i>
                                                @endif
                                                @if($contextType->function == 'resizeFunction')
                                                    <i class="ri-check-line pr-3 ri-lg mt-1 resizecheck resize{{ $option->function }} {{ (session()->has('iconsize') && session('iconsize') == $option->function) ? : 'hidden'}}"></i>
                                                @endif
                                            </li>
                                        </a>
                                    @endforeach
                                @endif
                            </ul>
                        </li>
                    @endif
            @endforeach
        </ul>
    @else
        <!-- Apps context-menu -->
        <ul class="ullist">
            <!-- Always show default options -->
            @foreach ($contextTypes as $contextType)
                    <a href="#" class="allappoptions appoptions openrightclick clickmenu {{ $contextType->function }}" data-option="{{ $contextType->is_options }}">
                        <li class="flex items-center justify-between px-2 py-2">
                            <p class="text-c-black text-sm">{{ $contextType->name }}</p>
                            <p class="menu-sidename">{{ $contextType->shortcut }}</p>
                        </li>
                    </a>
            @endforeach 
        </ul>
    @endif
@endif
