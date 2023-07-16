@extends('layouts.client')
@section('content')
<section class="vh-100 reg-screen" >
    <div class="container h-100 ">
      <div class="row d-flex justify-content-center align-items-center h-100 ">
        <div class="col-lg-12 col-xl-11">
          <div class="card text-black transparent-s" style="border-radius: 25px;">
            <div class="card-body p-md-5">
            <img src="{{asset('images/logo.png')}}" width="180px" style="display: block; margin :auto " alt="">
            
            <p class="text-center h1 fw-bold mb-4 mx-1 mx-md-4 mt-4 title">Register Your Account</p>
            <form class="mx-1 mx-md-4 p-form" >
                <div class="row">
                    <div class="col-lg-6">
                        <div class="d-flex flex-row align-items-center mb-4">
                            <i class="fas fa-user fa-lg me-3 fa-fw"></i>
                            <div class="form-outline flex-fill mb-0">
                              <input type="text" id="form3Example1c" class="form-control" />
                              <label class="form-label" for="form3Example1c">Business Name</label>
                            </div>
                          </div>


                          <div class="d-flex flex-row align-items-center mb-4">
                            <i class="fas fa-user fa-lg me-3 fa-fw"></i>
                            <div class="form-outline flex-fill mb-0">
                              <input type="text" id="form3Example1c" class="form-control" />
                              <label class="form-label" for="form3Example1c">Your Name</label>
                            </div>
                          </div>


                          <div class="d-flex flex-row align-items-center mb-4">
                            <i class="fas fa-user fa-lg me-3 fa-fw"></i>
                            <div class="form-outline flex-fill mb-0">
                             <select name="" id="" class="form-control">
                                <option value="">Select</option>
                                <option value="">1-15</option>
                                <option value="">16-30</option>
                                <option value="">31-60</option>
                             </select>
                              <label class="form-label" for="form3Example1c">No. of Employees</label>
                            </div>
                          </div>


                          <div class="d-flex flex-row align-items-center mb-4">
                            <i class="fas fa-user fa-lg me-3 fa-fw"></i>
                            <div class="form-outline flex-fill mb-0">
                             <select name="" id="" class="form-control">
                                <option value="">Select</option>
                                <option value="">14-Days Trial</option>
                               <option value="">Rs.5000/Year</option>
                             </select>
                              <label class="form-label" for="form3Example1c">Choose Plan</label>
                            </div>
                          </div>

                         

                    </div>
                    <div class="col-lg-6">
                        <div class="d-flex flex-row align-items-center mb-4">
                            <i class="fas fa-user fa-lg me-3 fa-fw"></i>
                            <div class="form-outline flex-fill mb-0">
                              <input type="email" id="form3Example1c" class="form-control" />
                              <label class="form-label" for="form3Example1c">Your Email</label>
                            </div>
                          </div>

                          <div class="d-flex flex-row align-items-center mb-4">
                            <i class="fas fa-user fa-lg me-3 fa-fw"></i>
                            <div class="form-outline flex-fill mb-0">
                              <input type="text" id="form3Example1c" class="form-control" />
                              <label class="form-label" for="form3Example1c">Your Phone</label>
                            </div>
                          </div>


                          
                          <div class="d-flex flex-row align-items-center mb-4">
                            <i class="fas fa-user fa-lg me-3 fa-fw"></i>
                            <div class="form-outline flex-fill mb-0">
                              <input type="password" id="form3Example1c" class="form-control" />
                              <label class="form-label" for="form3Example1c">Passowrd</label>
                            </div>
                          </div>

                          <div class="d-flex flex-row align-items-center mb-4">
                            <i class="fas fa-user fa-lg me-3 fa-fw"></i>
                            <div class="form-outline flex-fill mb-0">
                              <input type="password" id="form3Example1c" class="form-control" />
                              <label class="form-label" for="form3Example1c">Confirm Passowrd</label>
                            </div>
                          </div>


                    </div>
                </div>
                <div class="form-check d-flex justify-content-center mb-2">
                    <input class="form-check-input me-2" type="checkbox" value="" id="form2Example3c" />
                    <label class="form-check-label" for="form2Example3">
                      I agree all statements in <a href="#!">Terms of service</a>
                    </label>
                  </div>

                  <div class="d-flex justify-content-center  mb-2 mb-lg-1">
                    <button type="button" class=" p-btn">Register</button>
                  </div>
            </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection