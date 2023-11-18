@extends('layouts.client')
@section('content')
<section class="vh-100 reg-screen" >
    <div class="container h-100 ">
      <div class="row d-flex justify-content-center align-items-center h-100 ">
        <div class="col-lg-12 col-xl-11">
          <div class="card text-black transparent-s" style="border-radius: 25px;">
            <div class="card-body p-md-5">
            <img src="{{asset('images/logo.png')}}" width="180px" style="display: block; margin :auto " alt="">
            
            <p class="text-center h1 fw-bold mb-4 mx-1 mx-md-4 mt-4 title">Check Order Status</p>
            <form class="mx-1 mx-md-4 p-form w-40" action="{{url("/check-order")}}" method="GET" >
              @csrf
                <div class="row">
                    <div class="col-lg-12">
                        <div class="d-flex flex-row align-items-center mb-4">
                            <i class="fas fa-user fa-lg me-3 fa-fw"></i>
                            <div class="form-outline flex-fill mb-0">
                              <input type="text" id="form3Example1c" class="form-control" name="tran_no" value="{{old('business')}}" required />
                              <label class="form-label" for="form3Example1c">Username / Order #</label>
                              @error('business')
                                  <span class="text-danger">{{$message}}</span>
                              @enderror
                            </div>
                          </div>


                          <div class="d-flex flex-row align-items-center mb-4">
                            <i class="fas fa-user fa-lg me-3 fa-fw"></i>
                            <div class="form-outline flex-fill mb-0">
                              <input type="password" id="form3Example1c" class="form-control" value="{{old('name')}}"  required name="password"/>
                              <label class="form-label" for="form3Example1c">Password</label>
                              @error('name')
                                  <span class="text-danger">{{$message}}</span>
                              @enderror
                            </div>
                          </div>

                         

                    </div>
                  
                </div>
                  <div class="d-flex justify-content-center  mb-2 mb-lg-1">
                    <button type="submit" class=" p-btn">Check</button>
                  </div>
            </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection