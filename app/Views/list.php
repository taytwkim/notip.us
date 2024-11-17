<link href="/assets/css/list.css?v=2" rel="stylesheet"/>
<script src="/assets/js/list.js?v=13"></script>
<div class="container">
  <div class="row mt-3">
  </div>
  <div id="main-list" class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
    <?php for($i=0; $i<6; $i++) { ?>
    <div class="col">
      <div class="card shadow-sm">
        <div class="main-image card__header header__img skeleton"></div>
        <div class="card-body">
          <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
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