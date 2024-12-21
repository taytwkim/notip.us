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
      <?php if(isset($place['photos']) && is_array($place['photos']) && sizeof($place['photos']) > 0) { ?>
      <img class="d-block mx-auto mb-4 rounded" src="/file/image/<?=$place['photos'][0]['file_id']?>" style="width:50%; height:auto;">
      <?php } ?>
      <h2><?=$place['name']?></h2>
      <h5 class="text-muted"><?=$place['address']?></h5>
      <p class="lead">If recognized as a manager, you will be able to manage the store information directly!</p>
    </div>

    <div class="row g-5">
      <div class="col-12">
        <h4 class="mb-3">Register Information</h4>
        <form id="form-manager-register" method="post" onsubmit="return register()">
          <input type="hidden" name="place-id" value="<?=$place['place_id']?>">
          <div class="row g-3">
            <div class="col-sm-6">
              <label for="firstName" class="form-label">First name</label>
              <input type="text" class="form-control" name="first-name" id="firstName" placeholder="" value="" required="">
              <small class="text-muted">Please provide your legal name. This will not be disclosed externally.</small>
              <div class="invalid-feedback">
                Valid first name is required.
              </div>
            </div>

            <div class="col-sm-6">
              <label for="lastName" class="form-label">Last name</label>
              <input type="text" class="form-control" name="last-name" id="lastName" placeholder="" value="" required="">
              <div class="invalid-feedback">
                Valid last name is required.
              </div>
            </div>

            <div class="col-12">
              <label for="email" class="form-label">Work Email</label>
              <input type="email" class="form-control" id="email" placeholder="you@example.com">
              <div class="invalid-feedback">
                Please enter a valid email address.
              </div>
            </div>

            <div class="col-12">
              <label for="referrer" class="form-label">Proof</label>
              <input type="file" class="form-control" id="address2" placeholder="Apartment or suite">
              <small class="text-muted">Upload your business license and proof of employment.</small>
            </div>

            <div class="col-12">
              <label for="description" class="form-label">Description</label>
              <textarea rows="5" name="description" class="form-control" placeholder="Please provide a brief description of how you can verify your documents and prove that you are an employee. This is for our representatives to reference when they contact you."></textarea>
              <div class="invalid-feedback">
                Please enter a description.
              </div>
            </div>

            <div class="col-12">
              <label for="referrer" class="form-label">Referrer <span class="text-muted">(Optional)</span></label>
              <input type="text" class="form-control" id="referrer" placeholder="Name and contact information">
            </div>
          </div>

          <hr class="my-4">

          <div class="form-check">
            <input type="checkbox" class="form-check-input" id="same-address">
            <label class="form-check-label" for="same-address">I certify that the information I provide is true. I may be held legally responsible if I provide false information for malicious purposes.</label>
          </div>

          <hr class="my-4">

          <button class="w-100 btn btn-primary btn-lg" type="submit">Continue to request</button>
        </form>
      </div>
    </div>
  </main>

  <script>
    function register() 
    {
      let registerSerializedData = $("#form-manager-register").serialize();

      $.ajax({
        url: "/manage/register/"+$("#place-id").val(),
        dataType: "json",
        data: registerSerializedData,
        success: function (response) {
          console.log(response);
          
          
        },
        error: function (error) {
          console.error("Error:", error);
        }
      });
    }
  </script>

  <footer class="my-5 pt-5 text-muted text-center text-small">
    <p class="mb-1">© 2017–2021 Company Name</p>
    <ul class="list-inline">
      <li class="list-inline-item"><a href="#">Privacy</a></li>
      <li class="list-inline-item"><a href="#">Terms</a></li>
      <li class="list-inline-item"><a href="#">Support</a></li>
    </ul>
  </footer>
</div>