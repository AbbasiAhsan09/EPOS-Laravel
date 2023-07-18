@extends('layouts.client')
@section('content')
<section class="vh-100 reg-screen" >
    <div class="container h-100 ">
      <div class="row d-flex justify-content-center align-items-center h-100 ">
        <div class="col-lg-12 col-xl-11">
          <div class="card text-black transparent-s" style="border-radius: 25px;">
            <div class="card-body p-md-5">
            <img src="{{asset('images/logo.png')}}" width="180px" style="display: block; margin :auto " alt="">
            
            <h1 class="text-center h1 fw-bold mb-4 mx-1 mx-md-4 mt-4 title">Annual Subscription (Rs. 5000/-)</h1>
            <div class="">
                <h3 class="title">Account Details:</h3>
                <div class="row mb-3">
                    <div class="col">
                        <div class="acc-item">
                            <h5 class="title-sub">Easypaisa:</h5>
                        <p>Account No. 03200681969 <br>
                        Account Title : Muhammad Ihsan</p>
                        </div>
                    </div>
                    <div class="col">
                        <div class="acc-item">
                            <h5 class="title-sub">JazzCash:</h5>
                        <p>Account No. 03200681969 <br>
                        Account Title : Muhammad Ihsan</p>
                        </div>
                    </div>
                    <div class="col">
                        <div class="acc-item">
                            <h5 class="title-sub">Soneri Bank:</h5>
                        <p>Account No. 500330000321617 <br>
                        Account Title : Muhammad Ahsan</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="steps-wrapper">
                <div class="step-item">
                    <h4 class="step-no title">Step 1</h4>
                    <p>
                        Transfer the amount and share the recipt with our team at (info@tradewise.com) or (WhatsApp: 03200681969).
                    </p>
                </div>
                <div class="step-item">
                    <h4 class="step-no title">Step 2</h4>
                    <p>
                       Our representative will contact you within 5-15 minutes and approve the payment and setup everything.
                    </p>
                </div>
                <div class="step-item">
                    <h4 class="step-no title">Step 3</h4>
                    <p>
                        Now you can use your POS Software by <a href="{{url("/login")}}">click here</a>.
                    </p>
                </div>
            </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection