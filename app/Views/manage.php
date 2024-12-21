<style>
  #progressbar {
    text-align: center;
    margin-bottom: 20px;
    overflow: hidden;
  }
  #progressbar li {
    list-style-type: none;
    color: #99a2a8;
    font-size: 9px;
    width: calc(100% / 3);
    float: left;
    position: relative;
    font: 500 13px/1 "Roboto", sans-serif;
  }
  #progressbar li:nth-child(2):before {
    content: "\F224";
  }
  #progressbar li:nth-child(3):before {
    content: "\F4D5";
  }
  #progressbar li:before {
    content: "\F3B9";
    font: normal normal normal 40px/50px bootstrap-icons;
    width: 80px;
    height: 80px;
    line-height: 80px;
    display: block;
    background: #eaf0f4;
    border-radius: 50%;
    margin: 0 auto 10px auto;
  }
  #progressbar li:after {
    content: "";
    width: 100%;
    height: 15px;
    background: #eaf0f4;
    position: absolute;
    left: -50%;
    top: 33px;
    z-index: -1;
  }
  #progressbar li:last-child:after {
    width: 150%;
  }
  #progressbar li.active {
    color: #5cb85c;
  }
  #progressbar li.active:before, #progressbar li.active:after {
    background: #5cb85c;
    color: white;
  }
</style>
<div class="container">
  <main>
    <ul id= "progressbar">
      <li class="active">Request Form</li>  
      <li>Verification</li> 
      <li>Approval Granted</li> <!--Not Approved-->
    </ul>
    <div class="py-5 text-center">
      <img class="d-block mx-auto mb-4 rounded" src="/file/image/<?=$place['photos'][0]['file_id']?>" style="width:50%; height:auto;">
      <h2><?=$place['name']?></h2>
      <p class="lead">If recognized as a manager, you will be able to manage the store information directly!</p>
    </div>

    <div class="row g-5">
      <div class="col-12">
        <h4 class="mb-3">Register Information</h4>
        <form class="needs-validation" novalidate="">
          <div class="row g-3">
            <div class="col-sm-6">
              <label for="firstName" class="form-label">First name</label>
              <input type="text" class="form-control" id="firstName" placeholder="" value="" required="">
              <div class="invalid-feedback">
                Valid first name is required.
              </div>
            </div>

            <div class="col-sm-6">
              <label for="lastName" class="form-label">Last name</label>
              <input type="text" class="form-control" id="lastName" placeholder="" value="" required="">
              <div class="invalid-feedback">
                Valid last name is required.
              </div>
            </div>

            <div class="col-12">
              <label for="email" class="form-label">Work Email</label>
              <input type="email" class="form-control" id="email" placeholder="you@example.com">
              <div class="invalid-feedback">
                Please enter a valid email address for shipping updates.
              </div>
            </div>

            <div class="col-12">
              <label for="referrer" class="form-label">Proof</label>
              <input type="file" class="form-control" id="address2" placeholder="Apartment or suite">
            </div>

            <div class="col-12">
              <label for="description" class="form-label">Description</label>
              <textarea class="form-control"></textarea>
              <div class="invalid-feedback">
                Please enter your shipping address.
              </div>
            </div>

            <div class="col-12">
              <label for="referrer" class="form-label">Referrer <span class="text-muted">(Optional)</span></label>
              <input type="text" class="form-control" id="address2" placeholder="Apartment or suite">
            </div>



            <div class="col-md-5">
              <label for="country" class="form-label">Country</label>
              <select class="form-select" id="country" required="">
                <option value="">Choose...</option>
                <option>United States</option>
              </select>
              <div class="invalid-feedback">
                Please select a valid country.
              </div>
            </div>

            <div class="col-md-4">
              <label for="state" class="form-label">State</label>
              <select class="form-select" id="state" required="">
                <option value="">Choose...</option>
                <option>California</option>
              </select>
              <div class="invalid-feedback">
                Please provide a valid state.
              </div>
            </div>

            <div class="col-md-3">
              <label for="zip" class="form-label">Zip</label>
              <input type="text" class="form-control" id="zip" placeholder="" required="">
              <div class="invalid-feedback">
                Zip code required.
              </div>
            </div>
          </div>

          <hr class="my-4">

          <div class="form-check">
            <input type="checkbox" class="form-check-input" id="same-address">
            <label class="form-check-label" for="same-address">Shipping address is the same as my billing address</label>
          </div>

          <div class="form-check">
            <input type="checkbox" class="form-check-input" id="save-info">
            <label class="form-check-label" for="save-info">Save this information for next time</label>
          </div>

          <hr class="my-4">

          <h4 class="mb-3">Payment</h4>

          <div class="my-3">
            <div class="form-check">
              <input id="credit" name="paymentMethod" type="radio" class="form-check-input" checked="" required="">
              <label class="form-check-label" for="credit">Credit card</label>
            </div>
            <div class="form-check">
              <input id="debit" name="paymentMethod" type="radio" class="form-check-input" required="">
              <label class="form-check-label" for="debit">Debit card</label>
            </div>
            <div class="form-check">
              <input id="paypal" name="paymentMethod" type="radio" class="form-check-input" required="">
              <label class="form-check-label" for="paypal">PayPal</label>
            </div>
          </div>

          <div class="row gy-3">
            <div class="col-md-6">
              <label for="cc-name" class="form-label">Name on card</label>
              <input type="text" class="form-control" id="cc-name" placeholder="" required="">
              <small class="text-muted">Full name as displayed on card</small>
              <div class="invalid-feedback">
                Name on card is required
              </div>
            </div>

            <div class="col-md-6">
              <label for="cc-number" class="form-label">Credit card number</label>
              <input type="text" class="form-control" id="cc-number" placeholder="" required="">
              <div class="invalid-feedback">
                Credit card number is required
              </div>
            </div>
          </div>

          <hr class="my-4">

          <button class="w-100 btn btn-primary btn-lg" type="submit">Continue to request</button>
        </form>
      </div>
    </div>
  </main>

  <footer class="my-5 pt-5 text-muted text-center text-small">
    <p class="mb-1">© 2017–2021 Company Name</p>
    <ul class="list-inline">
      <li class="list-inline-item"><a href="#">Privacy</a></li>
      <li class="list-inline-item"><a href="#">Terms</a></li>
      <li class="list-inline-item"><a href="#">Support</a></li>
    </ul>
  </footer>
</div>