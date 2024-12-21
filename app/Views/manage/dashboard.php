<style>
  .review-wrap {height:562px; overflow-y:scroll}
</style>

<script>
$(document).ready(function() {
  loadReviews();
});

function loadReviews() {
  $.getJSON("/review/listAll", (data) => {
    $("#review-table tbody").empty();
    data.data.forEach((review) => {
      // Sentiment badge
      let sentimentBadge = "";
      if (review.sentiment == 1) {
        sentimentBadge = '<span class="badge bg-label-success me-1">Positive</span>';
      } else if (review.sentiment == 0) {
        sentimentBadge = '<span class="badge bg-label-secondary me-1">Neutral</span>';
      } else if (review.sentiment == -1) {
        sentimentBadge = '<span class="badge bg-label-danger me-1">Negative</span>';
      }

      // Additional badges
      let badges = "";
      if (review.good_ambience == 1) {
        badges += '<span class="badge bg-label-primary me-1">Ambience</span>';
      }
      if (review.good_service == 1) {
        badges += '<span class="badge bg-label-primary me-1">Service</span>';
      }
      if (review.good_taste == 1) {
        badges += '<span class="badge bg-label-primary me-1">Taste</span>';
      }
      if (review.good_price == 1) {
        badges += '<span class="badge bg-label-primary me-1">Price</span>';
      }

      $("#review-table tbody").append(`
        <tr>
          <td>
            <div class="text-truncate">
              <img src="${review.profile_picture}" class="mr-3 rounded-circle" alt="User photo" width="30" height="30">
              <span>${review.name}</span>
            </div>
            <span class="small text-muted">${review.time}</span>
          </td>
          <td>${review.content}</td>
          <td>
            <div>${sentimentBadge}</div>
            <div>${badges}</div>
          </td>
        </tr>
      `);
    });
  });
}
</script>

<!-- Layout container -->
<div class="layout-page">
<!-- Navbar -->
<nav
class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
id="layout-navbar"
>
<div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
  <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
    <i class="bx bx-menu bx-sm"></i>
  </a>
</div>

<div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
  <!-- Search -->
  <div class="navbar-nav align-items-center">
    <div class="nav-item d-flex align-items-center">
      <i class="bx bx-search fs-4 lh-0"></i>
      <input
        type="text"
        class="form-control border-0 shadow-none"
        placeholder="Search..."
        aria-label="Search..."
      />
    </div>
  </div>
  <!-- /Search -->

  <ul class="navbar-nav flex-row align-items-center ms-auto">
    <!-- User -->
    <li class="nav-item navbar-dropdown dropdown-user dropdown">
      <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
        <div class="avatar avatar-online">
          <img src="/assets/sneat/img/avatars/1.png" alt class="w-px-40 h-auto rounded-circle" />
        </div>
      </a>
      <ul class="dropdown-menu dropdown-menu-end">
        <li>
          <a class="dropdown-item" href="#">
            <div class="d-flex">
              <div class="flex-shrink-0 me-3">
                <div class="avatar avatar-online">
                  <img src="/assets/sneat/img/avatars/1.png" alt class="w-px-40 h-auto rounded-circle" />
                </div>
              </div>
              <div class="flex-grow-1">
                <span class="fw-semibold d-block">John Doe</span>
                <small class="text-muted">Admin</small>
              </div>
            </div>
          </a>
        </li>
        <li>
          <div class="dropdown-divider"></div>
        </li>
        <li>
          <a class="dropdown-item" href="#">
            <i class="bx bx-user me-2"></i>
            <span class="align-middle">My Profile</span>
          </a>
        </li>
        <li>
          <a class="dropdown-item" href="#">
            <i class="bx bx-cog me-2"></i>
            <span class="align-middle">Settings</span>
          </a>
        </li>
        <li>
          <a class="dropdown-item" href="#">
            <span class="d-flex align-items-center align-middle">
              <i class="flex-shrink-0 bx bx-credit-card me-2"></i>
              <span class="flex-grow-1 align-middle">Billing</span>
              <span class="flex-shrink-0 badge badge-center rounded-pill bg-danger w-px-20 h-px-20">4</span>
            </span>
          </a>
        </li>
        <li>
          <div class="dropdown-divider"></div>
        </li>
        <li>
          <a class="dropdown-item" href="auth-login-basic.html">
            <i class="bx bx-power-off me-2"></i>
            <span class="align-middle">Log Out</span>
          </a>
        </li>
      </ul>
    </li>
    <!--/ User -->
  </ul>
</div>
</nav>

<!-- / Navbar -->

<!-- Content wrapper -->
<div class="content-wrapper">
<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row">
    <div class="col-lg-8 mb-4 order-0">
      <div class="card">
        <div class="card-body review-wrap">
          <div class="card-title mb-0">
            <h5 class="m-0 me-2">New Reviews</h5>
            <small class="text-muted">3 New Reviews Today</small>
            <table class="table" id="review-table">
              <colgroup>
                <col width="150px"></colgroup>
                <col></colgroup>
                <col width="150px"></colgroup>
              </colgroup>
              <thead>
                <tr>
                  <th>User</th>
                  <th>Content</th>
                  <th>Analysis</th>
                </tr>
              </thead>
              <tbody class="table-border-bottom-0">
                <tr>
                  <td>
                    <i class="bx bxl-angular bx-md text-danger me-4"></i>
                    <span>Angular Project</span>
                  </td>
                  <td>Albert Cook</td>
                  <td>
                    <div>
                      <span class="badge bg-label-primary me-1">Positive</span>
                    </div>
                    <div>
                      <span class="badge bg-label-primary me-1">Good Taste</span>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4 col-md-4 order-1">
      <div class="row">
        <div class="col-lg-6 col-md-12 col-6 mb-4">
          <div class="card">
            <div class="card-body">
              <div class="card-title d-flex align-items-start justify-content-between">
                <div class="avatar flex-shrink-0">
                  <img
                    src="/assets/sneat/img/icons/unicons/chart-success.png"
                    alt="chart success"
                    class="rounded"
                  />
                </div>
              </div>
              <span class="fw-semibold d-block mb-1">Listed</span>
              <h3 class="card-title mb-2">12,628</h3>
              <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> +72.80%</small>
            </div>
          </div>
        </div>
        <div class="col-lg-6 col-md-12 col-6 mb-4">
          <div class="card">
            <div class="card-body">
              <div class="card-title d-flex align-items-start justify-content-between">
                <div class="avatar flex-shrink-0">
                  <img
                    src="/assets/sneat/img/icons/unicons/wallet-info.png"
                    alt="Credit Card"
                    class="rounded"
                  />
                </div>
              </div>
              <span class="fw-semibold d-block mb-1">Clicked</span>
              <h3 class="card-title text-nowrap mb-1">3,679</h3>
              <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> +28.42%</small>
            </div>
          </div>
        </div>
        <div class="col-6 mb-4">
          <div class="card">
            <div class="card-body">
              <div class="card-title d-flex align-items-start justify-content-between">
                <div class="avatar flex-shrink-0">
                  <img src="/assets/sneat/img/icons/unicons/paypal.png" alt="Credit Card" class="rounded" />
                </div>
                <div class="dropdown">
                  <button
                    class="btn p-0"
                    type="button"
                    id="cardOpt4"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                  >
                    <i class="bx bx-dots-vertical-rounded"></i>
                  </button>
                  <div class="dropdown-menu dropdown-menu-end" aria-labelledby="cardOpt4">
                    <a class="dropdown-item" href="javascript:void(0);">View More</a>
                    <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                  </div>
                </div>
              </div>
              <span class="fw-semibold d-block mb-1">Reviews</span>
              <h3 class="card-title text-nowrap mb-2">2,456</h3>
              <small class="text-danger fw-semibold"><i class="bx bx-down-arrow-alt"></i> Weekly -14.82%</small>
            </div>
          </div>
        </div>
        <div class="col-6 mb-4">
          <div class="card">
            <div class="card-body">
              <div class="card-title d-flex align-items-start justify-content-between">
                <div class="avatar flex-shrink-0">
                  <img src="/assets/sneat/img/icons/unicons/cc-primary.png" alt="Credit Card" class="rounded" />
                </div>
                <div class="dropdown">
                  <button
                    class="btn p-0"
                    type="button"
                    id="cardOpt1"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                  >
                    <i class="bx bx-dots-vertical-rounded"></i>
                  </button>
                  <div class="dropdown-menu" aria-labelledby="cardOpt1">
                    <a class="dropdown-item" href="javascript:void(0);">View More</a>
                    <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                  </div>
                </div>
              </div>
              <span class="fw-semibold d-block mb-1">Credits</span>
              <h3 class="card-title mb-2">$14,857</h3>
              <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> +28.14%</small>
            </div>
          </div>
        </div>
        <div class="col-12 mb-4">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between flex-sm-row flex-column gap-3">
                <div class="d-flex flex-sm-column flex-row align-items-start justify-content-between">
                  <div class="card-title">
                    <h5 class="text-nowrap mb-2">Popularity</h5>
                  </div>
                  <div class="mt-sm-auto">
                    <small class="text-success text-nowrap fw-semibold"
                      ><i class="bx bx-chevron-up"></i> 68.2%</small
                    >
                    <h3 class="mb-0">84,686p</h3>
                  </div>
                </div>
                <div id="profileReportChart"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <!-- Sentiment Analysis -->
    <div class="col-md-6 col-lg-4 col-xl-4 order-0 mb-4">
      <div class="card h-100">
        <div class="card-header d-flex align-items-center justify-content-between pb-0">
          <div class="card-title mb-0">
            <h5 class="m-0 me-2">Sentiment Analysis</h5>
            <small class="text-muted">3 New Reviews Today</small>
          </div>
        </div>
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex flex-column align-items-center gap-1">
              <h2 class="mb-2">8,258</h2>
              <span>Total Reviews</span>
            </div>
            <div id="orderStatisticsChart"></div>
          </div>
          <ul class="p-0 m-0">
            <li class="d-flex mb-4 pb-1">
              <div class="avatar flex-shrink-0 me-3">
                <span class="avatar-initial rounded bg-label-primary"
                  ><i class="bx bx-mobile-alt"></i
                ></span>
              </div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                  <h6 class="mb-0">Positive</h6>
                  <small class="text-muted">Mobile, Earbuds, TV</small>
                </div>
                <div class="user-progress">
                  <small class="fw-semibold">82.5k</small>
                </div>
              </div>
            </li>
            <li class="d-flex mb-4 pb-1">
              <div class="avatar flex-shrink-0 me-3">
                <span class="avatar-initial rounded bg-label-success"><i class="bx bx-closet"></i></span>
              </div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                  <h6 class="mb-0">Neutral</h6>
                  <small class="text-muted">T-shirt, Jeans, Shoes</small>
                </div>
                <div class="user-progress">
                  <small class="fw-semibold">23.8k</small>
                </div>
              </div>
            </li>
            <li class="d-flex mb-4 pb-1">
              <div class="avatar flex-shrink-0 me-3">
                <span class="avatar-initial rounded bg-label-info"><i class="bx bx-home-alt"></i></span>
              </div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                  <h6 class="mb-0">Negative</h6>
                  <small class="text-muted">Fine Art, Dining</small>
                </div>
                <div class="user-progress">
                  <small class="fw-semibold">849k</small>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <!--/ Sentiment Analysis -->

    <!-- Aspect Analysis -->
    <div class="col-md-6 col-lg-4 col-xl-4 order-0 mb-4">
      <div class="card h-100">
        <div class="card-header d-flex align-items-center justify-content-between pb-0">
          <div class="card-title mb-0">
            <h5 class="m-0 me-2">Aspect Analysis</h5>
            <small class="text-muted">3 New Reviews Today</small>
          </div>
        </div>
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex flex-column align-items-center gap-1">
              <h2 class="mb-2">8,258</h2>
              <span>Total Reviews</span>
            </div>
            <div id="orderStatisticsChart"></div>
          </div>
          <ul class="p-0 m-0">
            <li class="d-flex mb-4 pb-1">
              <div class="avatar flex-shrink-0 me-3">
                <span class="avatar-initial rounded bg-label-primary"
                  ><i class="bx bx-mobile-alt"></i
                ></span>
              </div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                  <h6 class="mb-0">Good Taste</h6>
                  <small class="text-muted">Mobile, Earbuds, TV</small>
                </div>
                <div class="user-progress">
                  <small class="fw-semibold">82.5k</small>
                </div>
              </div>
            </li>
            <li class="d-flex mb-4 pb-1">
              <div class="avatar flex-shrink-0 me-3">
                <span class="avatar-initial rounded bg-label-success"><i class="bx bx-closet"></i></span>
              </div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                  <h6 class="mb-0">Good Service</h6>
                  <small class="text-muted">T-shirt, Jeans, Shoes</small>
                </div>
                <div class="user-progress">
                  <small class="fw-semibold">23.8k</small>
                </div>
              </div>
            </li>
            <li class="d-flex mb-4 pb-1">
              <div class="avatar flex-shrink-0 me-3">
                <span class="avatar-initial rounded bg-label-info"><i class="bx bx-home-alt"></i></span>
              </div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                  <h6 class="mb-0">Good Price</h6>
                  <small class="text-muted">Fine Art, Dining</small>
                </div>
                <div class="user-progress">
                  <small class="fw-semibold">849k</small>
                </div>
              </div>
            </li>
            <li class="d-flex mb-4 pb-1">
              <div class="avatar flex-shrink-0 me-3">
                <span class="avatar-initial rounded bg-label-info"><i class="bx bx-home-alt"></i></span>
              </div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                  <h6 class="mb-0">Good Ambience</h6>
                  <small class="text-muted">Fine Art, Dining</small>
                </div>
                <div class="user-progress">
                  <small class="fw-semibold">849k</small>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <!--/ Aspect Analysis -->

    <!-- Transactions -->
    <div class="col-md-6 col-lg-4 order-2 mb-4">
      <div class="card h-100">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h5 class="card-title m-0 me-2">Transactions</h5>
          <div class="dropdown">
            <button
              class="btn p-0"
              type="button"
              id="transactionID"
              data-bs-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false"
            >
              <i class="bx bx-dots-vertical-rounded"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="transactionID">
              <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
              <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
              <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
            </div>
          </div>
        </div>
        <div class="card-body">
          <ul class="p-0 m-0">
            <li class="d-flex mb-4 pb-1">
              <div class="avatar flex-shrink-0 me-3">
                <img src="/assets/sneat/img/icons/unicons/paypal.png" alt="User" class="rounded" />
              </div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                  <small class="text-muted d-block mb-1">Paypal</small>
                  <h6 class="mb-0">Send money</h6>
                </div>
                <div class="user-progress d-flex align-items-center gap-1">
                  <h6 class="mb-0">+82.6</h6>
                  <span class="text-muted">USD</span>
                </div>
              </div>
            </li>
            <li class="d-flex mb-4 pb-1">
              <div class="avatar flex-shrink-0 me-3">
                <img src="/assets/sneat/img/icons/unicons/wallet.png" alt="User" class="rounded" />
              </div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                  <small class="text-muted d-block mb-1">Wallet</small>
                  <h6 class="mb-0">Mac'D</h6>
                </div>
                <div class="user-progress d-flex align-items-center gap-1">
                  <h6 class="mb-0">+270.69</h6>
                  <span class="text-muted">USD</span>
                </div>
              </div>
            </li>
            <li class="d-flex mb-4 pb-1">
              <div class="avatar flex-shrink-0 me-3">
                <img src="/assets/sneat/img/icons/unicons/chart.png" alt="User" class="rounded" />
              </div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                  <small class="text-muted d-block mb-1">Transfer</small>
                  <h6 class="mb-0">Refund</h6>
                </div>
                <div class="user-progress d-flex align-items-center gap-1">
                  <h6 class="mb-0">+637.91</h6>
                  <span class="text-muted">USD</span>
                </div>
              </div>
            </li>
            <li class="d-flex mb-4 pb-1">
              <div class="avatar flex-shrink-0 me-3">
                <img src="/assets/sneat/img/icons/unicons/cc-success.png" alt="User" class="rounded" />
              </div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                  <small class="text-muted d-block mb-1">Credit Card</small>
                  <h6 class="mb-0">Ordered Food</h6>
                </div>
                <div class="user-progress d-flex align-items-center gap-1">
                  <h6 class="mb-0">-838.71</h6>
                  <span class="text-muted">USD</span>
                </div>
              </div>
            </li>
            <li class="d-flex mb-4 pb-1">
              <div class="avatar flex-shrink-0 me-3">
                <img src="/assets/sneat/img/icons/unicons/wallet.png" alt="User" class="rounded" />
              </div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                  <small class="text-muted d-block mb-1">Wallet</small>
                  <h6 class="mb-0">Starbucks</h6>
                </div>
                <div class="user-progress d-flex align-items-center gap-1">
                  <h6 class="mb-0">+203.33</h6>
                  <span class="text-muted">USD</span>
                </div>
              </div>
            </li>
            <li class="d-flex">
              <div class="avatar flex-shrink-0 me-3">
                <img src="/assets/sneat/img/icons/unicons/cc-warning.png" alt="User" class="rounded" />
              </div>
              <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                <div class="me-2">
                  <small class="text-muted d-block mb-1">Mastercard</small>
                  <h6 class="mb-0">Ordered Food</h6>
                </div>
                <div class="user-progress d-flex align-items-center gap-1">
                  <h6 class="mb-0">-92.45</h6>
                  <span class="text-muted">USD</span>
                </div>
              </div>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <!--/ Transactions -->
  </div>
</div>
<!-- / Content -->