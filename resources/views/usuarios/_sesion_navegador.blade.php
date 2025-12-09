@csrf
<div class="container py-1 h-500">
    <div class="row d-flex justify-content-center align-items-center h-500">
        <div class="col col-lg-12 mb-6 mb-lg-0">
            <div class="card mb-3" style="border-radius: .5rem;">
                <div class="row g-0">
                    <div class="col-md-4 gradient-custom text-center text-white"
                        style="border-top-left-radius: .5rem; border-bottom-left-radius: .5rem;">
                        <h5 class="img-fluid my-5 negritas-negro">Sesiones del navegador</h5>
                        <p class="px-4 negritas-negro" style="text-align: justify; text-align-last: left;">
                            Administre y cierre sesión en sus sesiones activas en otros navegadores y dispositivos.</p>
                    </div>
                    <div class="col-md-8">
                        <div class="card-body p-4">
                            <!-- Cierre sesión -->
                            <div class="max-w-xl text-sm text-gray-600">
                                Si es necesario, puede cerrar la sesión en todas las sesiones abiertas en otros navegadores y dispositivos. A continuación, se muestran algunas de sus sesiones recientes; sin embargo, esta lista puede no ser exhaustiva. Si cree que su cuenta ha sido comprometida, también debe actualizar su contraseña.
                            </div>

                            @if (count($sessions) > 0)
                                <div class="mt-5 space-y-6">
                                    <!-- Other Browser Sessions -->
                                    @foreach ($sessions as $session)
                                        <div class="flex items-center">
                                            <div>
                                                @if ($session->agent->isDesktop())
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-gray-500">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                                                    </svg>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-gray-500">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                                                    </svg>
                                                @endif
                                            </div>

                                            <div class="ml-3">
                                                <div class="text-sm text-gray-600">
                                                    {{ $session->agent->platform() ? $session->agent->platform() : 'Unknown' }} - {{ $session->agent->browser() ? $session->agent->browser() : 'Unknown' }}
                                                </div>

                                                <div>
                                                    <div class="text-xs text-gray-500">
                                                        {{ $session->ip_address }},

                                                        @if ($session->is_current_device)
                                                            <span class="text-green-500 font-semibold">Este dispositivo</span>
                                                        @else
                                                            Última actividad: {{ $session->last_active }}
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <div class="flex items-center mt-5">
                                <button type="button" class="btn btn-primary btn-sm" wire:click="confirmLogout" wire:loading.attr="disabled">
                                    Cerrar sesión en otras sesiones
                                </button>

                                <p class="ml-3" wire:loading wire:target="confirmLogout" style="color: #1E90FF;">Cerrando sesiones...</p>

                                <p class="ml-3 text-success" wire:loading.remove wire:target="confirmLogout">Listo.</p>
                            </div>

                            <!-- Log Out Other Devices Confirmation Modal -->
                            @if ($confirmingLogout)
                                <div class="modal fade show" id="confirmLogoutModal" tabindex="-1" aria-modal="true" role="dialog">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Cerrar sesión en otras sesiones</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Por favor, introduzca su contraseña para confirmar que desea cerrar la sesión en sus otras sesiones en todos sus dispositivos.</p>
                                                <input type="password" class="form-control" wire:model.defer="password">
                                                @error('password') <span class="error">{{ $message }}</span> @enderror
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" wire:click="$toggle('confirmingLogout')">Cancelar</button>
                                                <button type="button" class="btn btn-primary" wire:click="logoutOtherBrowserSessions" wire:loading.attr="disabled">Cerrar sesión en otras sesiones</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


