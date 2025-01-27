<div class="left main-sidebar">
	
		<div class="sidebar-inner leftscroll">

			<div id="sidebar-menu">
        
			<ul>
                @if (session('sidebarMenus'))
                    @foreach (session('sidebarMenus') as $modul)
                        @php
                            $isActiveModule = $modul->menus->contains(function($menu) {
                                return request()->fullUrlIs(route($menu->route) . '*') || request()->routeIs($menu->route);
                            });
                        @endphp
                        <li class="submenu {{ $isActiveModule ? 'active' : '' }}">
                            <a href="#">
                                <i class="{{ $modul->icon }}"></i>
                                <span>{{ $modul->name }}</span>
                                <span class="menu-arrow"></span>
                            </a>
                            @if ($modul->menus->isNotEmpty())
                                <ul class="list-unstyled" style="{{ $isActiveModule ? 'display: block;' : '' }}">
                                    @foreach ($modul->menus as $menu)
                                        <li class="{{ request()->fullUrlIs(route($menu->route) . '*') || request()->routeIs($menu->route) ? 'active' : '' }}">
                                            <a href="{{ route($menu->route) }}">
                                                {{ $menu->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                @endif

               	
            </ul>

            <div class="clearfix"></div>

			</div>
        
			<div class="clearfix"></div>

		</div>

	</div>