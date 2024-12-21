<link href="/assets/css/list.css?v=2" rel="stylesheet"/>
<script src="/assets/js/list.js?v=13"></script>
<div class="container">
  <div class="p-3 p-md-5 mb-5 bg-body-tertiary rounded-3">
    <div class="container-fluid py-3">
      <h1 class="display-5 fw-bold mb-4">Tired of Tipping?</h1>
      <p class="fs-5 mb-5">There are many restaurants, cafes, and bars around you that don’t accept tips. These establishments pay their employees fair wages from the start, making them more honest and socially responsible businesses.<br>Try visiting No-tip and Fair-tip spots – you'll want to go to these places from now on.</p>
      <div class="input-group">
        <select class="form-select form-select-lg" id="State" aria-label="Example select with button addon">
          <option selected>Sate...</option>
          <option value="1">One</option>
          <option value="2">Two</option>
          <option value="3">Three</option>
        </select>
        <select class="form-select form-select-lg" id="City" aria-label="Example select with button addon">
          <option selected>City...</option>
          <option value="1">One</option>
          <option value="2">Two</option>
          <option value="3">Three</option>
        </select>
        <input type="text" class="form-control input-group-lg" placeholder="Zipcode">
        <button class="btn btn-primary" type="button">
          <span class="d-none d-md-inline">Let's Search!</span>
          <span class="d-inline d-md-none">Go!</span>
        </button>
      </div>
    </div>
  </div>
  <h4 class="mb-4">No-tip of Fair-tip Places Around You</h4>
  <div id="main-list" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
    <?php for($i=0; $i<6; $i++) { ?>
    <div class="col">
      <div class="card shadow-sm">
        <div class="main-image card__header header__img skeleton"></div>
        <div class="card-body">
          <h3 class="card__header header__title" id="card-title">
            <div class="skeleton skeleton-text"></div>
            <div class="skeleton skeleton-text"></div>
          </h3>
          <div class="card__body body__text" id="card-details">
            <div class="skeleton skeleton-text skeleton-text__body"></div>
          </div>
          <div class="d-flex justify-content-between align-items-center">
            <div class="btn-group">
              <button type="button" class="btn btn-sm btn-outline-secondary">View</button>
              <button type="button" class="btn btn-sm btn-outline-secondary">Edit</button>
            </div>
            <small class="text-body-secondary">9 mins</small>
          </div>
        </div>
      </div>
    </div>
    <?php } ?>
  </div>
</div>