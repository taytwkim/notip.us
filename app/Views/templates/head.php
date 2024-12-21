<?php 
  header("Cross-Origin-Opener-Policy: same-origin-allow-popups");
  helper('common'); 
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>No Tip US</title>
    <!-- Bootstrap CSS -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
      crossorigin="anonymous"
    />
    <link rel="icon" type="image/x-icon" href="/assets/favicon/favicon.ico">
    <link href="/assets/css/common.css?v=4" rel="stylesheet"/>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="/assets/js/common.js?v=3"></script>
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/assets/sneat/vendor/fonts/boxicons.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@100..900&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
  </head>
  <body>
    <nav class="navbar navbar-expand navbar-light bg-white fixed-top">
      <div class="container-fluid">
        <a class="navbar-brand" href="/"><img src="/assets/imgs/logo3.png?v=2"></a>
        <div id="top-search" class="input-group ms-5 d-none d-lg-flex border rounded">
          <button class="btn" type="button" id="inputGroupFileAddon04"><i class="bi bi-search"></i></button>
          <input type="text" class="form-control border-0" placeholder="Search" aria-label="Search">
        </div>
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" href="/about">About us</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/list">List</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/map">Map</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="/brand">Brand</a>
          </li>
          <!-- User -->
          <?php if($user=getUser()) { ?>
          <li class="nav-item ms-auto navbar-dropdown dropdown-user dropdown">
            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
              <div class="avatar avatar-online">
                <img src="<?=$user['profilePicture']?>" id="profileImg" alt="Profile" class="profile-img ms-2">
              </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li>
                <a class="dropdown-item" href="#">
                  <div class="d-flex">
                    <div class="flex-shrink-0 me-3">
                      <div class="avatar avatar-online">
                        <img src="<?=$user['profilePicture']?>" id="profileImg" alt="Profile" class="profile-img ms-2">
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
                <a class="dropdown-item" href="/user/activity">
                  <i class="bx bx-cog me-2"></i>
                  <span class="align-middle">Activity</span>
                </a>
              </li>
              <li>
                <div class="dropdown-divider"></div>
              </li>
              <li>
                <a class="dropdown-item" href="/manage/dashboard" target="_blank">
                  <span class="d-flex align-items-center align-middle">
                    <i class="flex-shrink-0 bx bx-credit-card me-2"></i>
                    <span class="flex-grow-1 align-middle">Manage</span>
                    <span class="flex-shrink-0 badge badge-center rounded-pill bg-danger w-px-20 h-px-20">4</span>
                  </span>
                </a>
              </li>
              <li>
                <div class="dropdown-divider"></div>
              </li>
              <li>
                <a class="dropdown-item" id="signOutLink" href="#signout">
                  <i class="bx bx-power-off me-2"></i>
                  <span class="align-middle">Log Out</span>
                </a>
              </li>
            </ul>
          </li>
          <?php } else { ?>
          <li class="nav-item ms-auto navbar-dropdown dropdown-user dropdown">
            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
              <div class="avatar avatar-online">
                <img src="/assets/imgs/no_user.png" id="showLoginLayerBtn" alt="Profile" class="profile-img ms-2">
              </div>
            </a>
          </li>
          <?php } ?>
          <!--/ User -->
        </ul>
      </div>
    </nav>
    <div class="modal fade" id="loginLayer" tabindex="-1" aria-labelledby="loginLayerLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="loginLayerLabel">Login or Sign Up</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center pb-5">
          <h5><strong>Login with Google in just 2 second!</strong></h5>
          <p class="text-muted mb-4">We will never ask for your personal information.</p>
          <!-- 구글 로그인 버튼이 렌더링될 위치 -->
          <div id="googleSignInBtn"></div>
        </div>
      </div>
    </div>
  </div>
  <script>
      // Google 로그인 버튼 초기화
      function handleCredentialResponse(response) {
        $.ajax({
            url: '/user/auth',
            type: 'POST',
            dataType: 'json',
            data: { credential: response.credential },
            success: function(data) {
              if (data.success) {
                console.log(data);
                alert('Successfully Logged In');
                location.reload();
              } else {
                alert("Failed to log in with Google ID. Please contact customer support.");
              }
            }
        });
      }

      // defer과 async 옵션이 있으므로 on load를 사용한다.
      $(window).on('load', function() {
          google.accounts.id.initialize({
              client_id: '633588665707-p58qf2t6g18mcuo0rm738pjg2hsmo3kj.apps.googleusercontent.com',
              callback: handleCredentialResponse
          });
          google.accounts.id.renderButton(
              $('#googleSignInBtn')[0],
              { 
                  theme: 'outline', 
                  size: 'large', 
                  width: '100%',         // Attempt full width (if supported)
                  type: 'standard',       // Full button with text
                  text: 'continue_with',  // Text-only without user info
                  shape: 'rectangular',   // Standard rectangular button
                  logo_alignment: 'center' // Center Google logo
              }
          );
      });
      

      // 로그인 버튼 클릭 시 모달 표시
      $(document).ready(function() {
          $('#showLoginLayerBtn').click(function() {
              $('#loginLayer').modal('show');
          });

          $('#profileImg').on('click', function(event) {
            event.stopPropagation(); // 클릭 이벤트가 문서로 전파되지 않도록 방지
            $('#profileMenu').toggle();
          });

          $('#signOutLink').on('click', function(event) {
            event.preventDefault(); // 기본 링크 동작 방지

            $.ajax({
              url: '/user/signOut',
              type: 'POST',
              success: function(response) 
              {
                alert('Successfully Logged Out');
                location.reload();
              },
              error: function() {
                alert("Failed to log out. Please try again.");
              }
            });
          });

          // 다른 곳 클릭 시 레이어 숨기기
          $(document).on('click', function(event) {
            if (!$(event.target).closest('#profileMenu').length) {
              $('#profileMenu').hide();
            }
          });
      });
  </script>