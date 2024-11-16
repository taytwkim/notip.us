<?php 
  header("Cross-Origin-Opener-Policy: same-origin-allow-popups");
  helper('common'); 
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tip Frankr</title>
    <!-- Bootstrap CSS -->
    <link href="/assets/css/common.css" rel="stylesheet"/>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
      crossorigin="anonymous"
    />
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="/assets/js/common.js?v=1"></script>
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@100..900&family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white fixed-top">
      <div class="container-fluid">
        <a class="navbar-brand" href="/"><img src="/assets/imgs/logo.png?v=2" style="width:150px"></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
            <li class="nav-item me-4">
              <a class="nav-link" href="/list">About us</a>
            </li>
            <li class="nav-item me-4">
              <a class="nav-link" href="/list">List</a>
            </li>
            <li class="nav-item me-4">
              <a class="nav-link" href="/map">Map</a>
            </li>
          </ul>
        </div>
        <button id="addNew" class="btn btn-light">Add New</button>
        <div class="d-flex align-items-center">
          <?php if($user=getUser()) { ?>
          <img src="<?=$user['profilePicture']?>" id="profileImg" alt="Profile" class="profile-img ms-2">
          <div class="dropdown-menu p-2 shadow" id="profileMenu">
            <a class="dropdown-item" href="#activity">Activity</a>
            <a class="dropdown-item" id="signOutLink" href="#signout">Sign Out</a>
          </div>
          <?php } else { ?>
          <i id="showLoginLayerBtn" class="bi bi-person-circle fs-2 ms-3"></i>
          <?php } ?>
        </div>
      </div>
    </nav>
    <div id="newPlaceSearch">
      <h5><storng>Register New</storng></h5>
      <div class="mb-2">Search for a place or click on the map</div>
      <input id="autocomplete" class="form-control" placeholder="Search address" type="text"/>
      <button type="button" class="btn-close position-absolute m-2 top-0 end-0" aria-label="Close" onclick="$(this).parent().hide()"></button>
    </div>
    <div class="modal fade" id="loginLayer" tabindex="-1" aria-labelledby="loginLayerLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="loginLayerLabel">Sign In/Up</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <p>Log in with your Google account in just 2 second!</p>
          <p><?php print_r($_SESSION) ?></p>
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