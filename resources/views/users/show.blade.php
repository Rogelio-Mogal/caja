@extends('layouts.app')
@section('content')
	<div class="container my-4">
		<div class="row justify-content-center">

			<div class="col-md-6" style="min-width: 300px">
				<!-- Widget: user widget style 1 -->
				<div class="card card-widget widget-user">
					<!-- Add the bg color to the header using any of the bg-* classes -->
					<div class="widget-user-header bg-secondary">
						<h3 class="widget-user-username">{{ $user->name }}</h3>
						<h5 class="widget-user-desc">{{$user->email }}</h5>
					</div>
					<div class="widget-user-image">
						<img class="img-circle elevation-2 bg-white" src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}">
					</div>
					<div class="card-footer">
						<div class="row">
							<div class="col-sm-4 border-right">
								<div class="description-block">
									<h5 class="description-header">3,200</h5>
									<span class="description-text">VENTAS</span>
								</div>
								<!-- /.description-block -->
							</div>
							<!-- /.col -->
							<div class="col-sm-4 border-right">
								<div class="description-block">
									<h5 class="description-header">130</h5>
									<span class="description-text">CLIENTES</span>
								</div>
								<!-- /.description-block -->
							</div>
							<!-- /.col -->
							<div class="col-sm-4">
								<div class="description-block">
									<h5 class="description-header">35</h5>
									<span class="description-text">PRODUCTOS</span>
								</div>
								<!-- /.description-block -->
							</div>
							<!-- /.col -->
						</div>
						<!-- /.row -->

					</div>
				</div>
				<!-- /.widget-user -->
			</div>
			</div>




			<div>
				<div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
					@if (Laravel\Fortify\Features::canUpdateProfileInformation())
						@livewire('profile.update-profile-information-form')
		
						<x-jet-section-border />
					@endif
		
					@if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
						<div class="mt-10 sm:mt-0">
							@livewire('profile.update-password-form')
						</div>
		
						<x-jet-section-border />
					@endif
		
					@if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
						<div class="mt-10 sm:mt-0">
							@livewire('profile.two-factor-authentication-form')
						</div>
		
						<x-jet-section-border />
					@endif
		
					<div class="mt-10 sm:mt-0">
						@livewire('profile.logout-other-browser-sessions-form')
					</div>
		
					@if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
						<x-jet-section-border />
		
						<div class="mt-10 sm:mt-0">
							@livewire('profile.delete-user-form')
						</div>
					@endif
				</div>
			</div>








		</div>
	</div>

@stop

@section('js')
	<script type="text/javascript">
		$(document).ready(function(){
			console.log('jQuery is working...');
		});
	</script>
@stop