
<aside id="layout-menu" class="layout-menu-horizontal menu-horizontal menu flex-grow-0">
              <div class="container-xxl d-flex h-100">
                <ul class="menu-inner">
                  <!-- Dashboards -->
                  <li class="menu-item">
                    <a href="/dashboard" class="menu-link">
                      <i class="menu-icon icon-base ti tabler-smart-home"></i>
                      <div data-i18n="Inicio">Inicio</div>
                    </a>                   
                  </li>
                  
                  @can('Administracion')
                  <li class="menu-item">
                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                      <i class="menu-icon fa-solid fa-sliders"></i>
                      <div data-i18n="Administración">Administración</div>
                    </a>
                   
                    <ul class="menu-sub"> 
                       @can('Ver Usuarios')                     
                      <li class="menu-item">
                        <a href="/users" class="menu-link">
                          <i class="menu-icon fa-solid fa-users"></i>
                          <div data-i18n="Usuarios">Usuarios</div>
                        </a>
                      </li>                     
                      @endcan
                      @can('Ver Usuarios')                                        
                      <li class="menu-item">
                        <a href="/customers" class="menu-link">
                          <i class="menu-icon fa-solid fa-users"></i>
                          <div data-i18n="Clientes">Clientes</div>
                        </a>
                      </li>                     
                      @endcan
                      
                      @can('Editar Permisos')
                      <li class="menu-item">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                          <i class="menu-icon fa-solid fa-user-shield"></i>
                          <div data-i18n="Permisos">Permisos</div>
                        </a>
                        <ul class="menu-sub">
                          <input type="hidden" value="{{$roles = Spatie\Permission\Models\Role::get()}}">
                          @foreach ($roles as $role)                          
                          <li class="menu-item">
                            <a href="/roles/{{ $role->id }}/permissions/edit" class="menu-link">
                              <div data-i18n="{{ $role->name }}">{{ $role->name }}</div>
                            </a>
                          </li>                          
                          @endforeach
                        
                        </ul>
                      </li>
                      @endcan
                      


                    </ul>
                  </li>
                  @endcan
                 
                  @can('Gestionar Configuracion')
                  <!-- Layouts -->
                  <li class="menu-item">
                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                      <i class="menu-icon fa-solid fa-gears"></i>
                      <div data-i18n="Configuración">Configuración</div>
                    </a>

                    <ul class="menu-sub">
                      @can('Ver Categorias')
                      <li class="menu-item">
                        <a href="/categories" class="menu-link">
                          <i class="menu-icon fa-solid fa-layer-group"></i>
                          <div data-i18n="Categorias">Categorias</div>
                        </a>
                      </li>
                      @endcan
                      @can('Ver Productos')
                      <li class="menu-item">
                        <a href="/products" class="menu-link">
                          <i class="menu-icon fa-solid fa-bag-shopping"></i>
                          <div data-i18n="Productos">Productos</div>
                        </a>
                      </li>
                      @endcan
                      @can('Ver Mesas')
                      <li class="menu-item">
                        <a href="/tables" class="menu-link">
                          <i class="menu-icon fa-solid fa-toilet-portable"></i>
                          <div data-i18n="Mesas">Mesas</div>
                        </a>
                      </li> 
                      @endcan                     
                    </ul>
                  </li>
                  @endcan
                  @can('Gestionar Ordenes')
                  <li class="menu-item">
                    <a href="javascript:void(0)" class="menu-link menu-toggle">
                      <i class="menu-icon fa-solid fa-bag-shopping"></i>
                      <div data-i18n="Ordenes">Ordenes</div>
                    </a>

                    <ul class="menu-sub">
                      @can('Crear Ordenes')
                      <li class="menu-item">
                        <a href="/orders" class="menu-link">
                          <i class="menu-icon fa-solid fa-cart-plus"></i>
                          <div data-i18n="Crear Orden">Crear Orden</div>
                        </a>                   
                      </li>
                      @endcan
                      @can('Ver Ordenes')
                      <li class="menu-item">
                        <a href="/reports/orders" class="menu-link">
                          <i class="menu-icon fa-solid fa-cart-shopping"></i>
                          <div data-i18n="Ordenes">Ordenes</div>
                        </a>
                      </li>
                      @endcan
                                           
                    </ul>
                  </li>
                  @endcan
                  @can('Reportes')
                  <li class="menu-item">
                    <a href="/reports/sales" class="menu-link">
                      <i class="menu-icon fa-solid fa-chart-line"></i>
                      <div data-i18n="Reporte Ventas">Reporte Ventas</div>
                    </a>                   
                  </li>
                  @endcan
                  
                  
                </ul>
              </div>
            </aside>


