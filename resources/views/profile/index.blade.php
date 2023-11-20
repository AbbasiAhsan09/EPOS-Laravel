@extends('layouts.app')
@section('content')

<div class="row justify-content-center align-items-center" style="height: 80vh">
    <div class="col-lg-4">
        
<div class="card login-form">
	<div class="card-body">
		<h3 class="card-title">Change Password</h3>
		
		<!--Password must contain one lowercase letter, one number, and be at least 7 characters long.-->
		
		<div class="card-text">
			<form action="{{route("password.change")}}" method="POST">
                @csrf
				<div class="form-group">
					<label for="exampleInputEmail1">Current Password</label>
                    <div class="input-group input-group-outline">
					<input type="password" name="current_password" class="form-control">
                    </div>
				</div>
                <div class="form-group">
					<label for="exampleInputEmail1">New Password</label>
                    <div class="input-group input-group-outline">
					<input type="password" name="new_password" class="form-control">
                    </div>
				</div>
				<div class="form-group">
					<label for="exampleInputEmail1">Confirm Password</label>
					<div class="input-group input-group-outline">
                        <input type="password" name="new_password_confirmation" class="form-control">
                        </div>
				</div>
				<button type="submit" class="btn my-2 btn-primary btn-block submit-btn">Confirm</button>
			</form>
		</div>
	</div>
</div>
    </div>
</div>


{{--  --}}
@endsection