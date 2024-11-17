let map, infowindow, autocomplete, selectedMarker;
let isPOIVisible = false; // Flag to check if POIs are visible
const markers = [];

// 기본 스타일 및 검색 포커스 스타일 설정
const defaultStyle = [
{
  featureType: "poi",
  elementType: "labels.icon",
  stylers: [{ visibility: "off" }], // 기본적으로 POI 숨김
},
];

const focusStyle = [
{
  featureType: "poi",
  elementType: "labels.icon",
  stylers: [{ visibility: "on" }], // 검색창 포커스 시 POI 보이도록 설정
},
];

$(document).ready(function () {
  // Google Maps API 로드 후 initMap 호출
  $.getScript(
    "https://maps.googleapis.com/maps/api/js?key=AIzaSyA69MLRfjDCUoSHsSPgU1uYHo4OGonMXAM&libraries=places&language=en",
    function () {
      initMap();
    });

// Add New 버튼 클릭 시 장소 검색 UI 표시
$("#addNew").click(function() {
  if(!$("#newPlaceSearch").is(":visible")) {
    $("#newPlaceSearch").fadeIn();
    map.setOptions({ styles: focusStyle }); // 스타일 변경
    isPOIVisible = true; 
  }
});

$("#newPlaceSearch .btn-close").click(function() {
  map.setOptions({ styles: defaultStyle }); // 스타일 변경
  isPOIVisible = false; 
});

// 모바일 화면에서 플로팅 버튼 클릭 시 sidebar 토글
  $("#floatingButton").click(function () {
    $("#sidebar").slideDown();
    $("#floatingButton").hide('200');
  });

  $("#sidebar .btn-close").click(function () {
    $("#sidebar").fadeOut('200');
    $("#floatingButton").show('200');
  });

  $(window).resize(function () {
    if ($(window).width() > 768) {
    // PC 화면일 때 강제로 사이드바를 보여줌
      $("#sidebar").show();
    }
  });
});

function initMap() {
// HTML5 Geolocation API로 현재 위치 가져오기
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      (position) => {
        const userLocation = {
          lat: position.coords.latitude,
          lng: position.coords.longitude,
        };

        map = new google.maps.Map(document.getElementById("map"), {
          center: userLocation,
          zoom: 15,
          styles: defaultStyle,
        });

        infowindow = new google.maps.InfoWindow();
        selectedMarker = new google.maps.Marker({
          map,
        visible: false, // 초기에는 보이지 않도록 설정
      });

      map.addListener("zoom_changed", toggleMarkerLabels);
      map.addListener("click", (event) => {
        if (isPOIVisible) {
          console.log("Map Clicked");
          console.log(event);
          if (event.placeId) {
            event.stop(); // 기본 클릭 이벤트 중지
            loadPlaceDetails(event.placeId); // placeId로 POI 세부 정보 로드
          }
        }
      });  

      initAutocomplete();
      loadPlaces();
      },
      () => {
        console.error("Geolocation service failed.");
        defaultMapInitialization();
    });
  } 
  else 
  {
    console.error("Browser doesn't support Geolocation.");
    defaultMapInitialization();
  }
}

function toggleMarkerLabels() {
  const currentZoom = map.getZoom();

  console.log(currentZoom);

  markers.forEach(marker => {
    marker.setLabel(currentZoom >= 14 ? marker._originalLabel : null);
  });
}

function defaultMapInitialization() {
  map = new google.maps.Map(document.getElementById("map"), {
    center: { lat: 40.749933, lng: -73.98633 }, // New York
    zoom: 13,
    styles: defaultStyle,
  });
  infowindow = new google.maps.InfoWindow();
  selectedMarker = new google.maps.Marker({
    map,
    visible: false,
  });
  initAutocomplete();
  loadPlaces();
}

// 마커를 API 데이터로 로드
function loadPlaces() {
  $.getJSON("/place/list", (data) => {
    data.data.forEach((place) => {
      // 장소 유형에 따른 마커 아이콘 설정
      let iconUrl;
      if (place.type === "cafe") {
        iconUrl = "/assets/imgs/marker_cafe.png";
      } else if (place.type === "restaurant") {
        iconUrl = "/assets/imgs/marker_restaurant.png";
      } else {
        iconUrl = "/assets/imgs/marker_etc.png";
      }

      const marker = new google.maps.Marker({
        position: {
          lat: parseFloat(place.latitude),
          lng: parseFloat(place.longitude),
        },
        map,
        title: place.name,
        label: {
          text: place.name,
          fontSize: "12px",
          fontWeight: "bold",
        },
        icon: {
          url: iconUrl,
          scaledSize: new google.maps.Size(40, 40),
          labelOrigin: new google.maps.Point(20, 45), 
        },
      });

      marker._originalLabel = marker.getLabel();

      markers.push(marker);

      marker.addListener("click", () => {
        loadRegisteredPlaceInfo(place.place_id);
      });

      let mainPhoto;
      if (place.photos && place.photos.length > 0) {
          mainPhoto = '/file/image/'+place.photos[0].file_id+'/360x200';
      } else {
          mainPhoto = '/assets/imgs/sample/restaurant1.png'; 
      }

      $("#placeList").append(`
        <li onclick="loadRegisteredPlaceInfo('${place.place_id}')">
        <div class="list-main-photo" style="background:URL('${mainPhoto}')"></div>
        <div class="mt-2"><strong>${place.name}</strong> <span class="text-info small ms-1">${place.type}</span></div>
        <div class="mt-2">${place.address}</div>
        <div class="mt-2"><span class="badge text-bg-primary">No Tip</span></div>
        </li>
      `);

    });
  });
}

function addPlaceUI() {
  $("input[name=tipPolicy]").change(function() {
    if($(this).val() == "noTip") {
      $(".forNoTip").fadeIn();
      $(".forFairTip").hide();
    }
    else if($(this).val() == "fairTip") {
      $(".forFairTip").fadeIn();
      $(".forNoTip").hide();
    }
  });
}


function displayPlaceDetails(placeDetails) {
  $("#new-place").html(`
    <button type="button" class="btn-close position-absolute m-2 top-0 end-0" aria-label="Close" onclick="$(this).parent().hide()"></button>
    <h5><b>Register New</b></h5>
    ${placeDetails.photoUrl ? `<img src="${placeDetails.photoUrl}" alt="${placeDetails.name}" class="img-fluid rounded mt-3">` : ""}
    <div class="mt-3"><span class="fs-5"><strong>${placeDetails.name}</strong></span> <span class="text-info small ms-1">${placeDetails.type}</span></div>
    <p>${placeDetails.address}</p>
    <p><strong>Will you add this place as a No-Tip place or a Fair-Tip place?</strong></p> 
    <p><strong>Tip Policy</strong></p>
    <div class="btn-group" role="group">
      <input type="radio" class="btn-check" name="tipPolicy" id="tipPolicy-noTip" value="noTip" autocomplete="off">
      <label class="btn btn-outline-primary" for="tipPolicy-noTip">No Tip</label>
      <input type="radio" class="btn-check" name="tipPolicy" id="tipPolicy-fairTip" value="fairTip" autocomplete="off">
      <label class="btn btn-outline-primary" for="tipPolicy-fairTip">Fair Tip</label>
    </div>
    <div>
    <div class="forNoTip">
      <p class="mt-2"><i class="bi bi-exclamation-circle"></i> "No Tip" means:</p>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
        <label class="form-check-label" for="flexCheckDefault">
          This place does not accept tips by policy.
        </label>
      </div>
    </div>
    <div class="forFairTip">
      <p class="mt-2"><i class="bi bi-exclamation-circle"></i> "Fair Tip" means one of the following applies:</p>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
        <label class="form-check-label" for="flexCheckDefault">
          The suggested minimum tip is 10% or lower.
        </label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
        <label class="form-check-label" for="flexCheckDefault">
          The suggested maximum tip is 20% or lower.
        </label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
        <label class="form-check-label" for="flexCheckDefault">
          No tip amount is suggested.
        </label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
        <label class="form-check-label" for="flexCheckDefault">
          They state that minimum wage is guaranteed for employees without relying on tips.
        </label>
      </div>
    </div>
    <p class="mt-3"><strong>How do you know?</strong></p> 
    <div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
        <label class="form-check-label" for="flexCheckDefault">
          I experienced it myself.
        </label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
        <label class="form-check-label" for="flexCheckDefault">
          I confirmed it from other sources.
        </label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
        <label class="form-check-label" for="flexCheckDefault">
          I confirmed it on the official website.
        </label>
      </div>
    </div>
    <p class="mt-3 mb-1"><strong>Official State</strong></p> 
    <div><input type="text" class="form-control" placeholder="https://"></div>
    <p class="mt-3 mb-1"><strong>External Source</strong></p> 
    <div><input type="text" class="form-control" placeholder="https://"></div>
    <p class="mt-3 mb-1"><strong>Please explain more about what you saw or experienced.</strong></p> 
    <div><textarea type="text" class="form-control"></textarea></div>
    <div class="mt-3">
      <button class="btn btn-primary" onclick="savePlaceToDatabase()">Register</button>
      <button class="btn btn-secondary" onclick="cancelPlaceDetails()">Cancel</button>
    </div>
    `);
  $("#new-place").show();
  addPlaceUI();
  window.placeDetailsForSave = placeDetails;
}

// 초기 상태 복원 함수
function cancelPlaceDetails() {
  $("#new-place").empty(); // UI 초기화
  map.setOptions({ styles: defaultStyle }); // 기본 스타일로 복원
  selectedMarker.setVisible(false); // 선택된 마커 숨기기
}

// 등록된 장소 클릭 시 상세 정보 로드 및 UI 표시
function loadRegisteredPlaceInfo(placeId) {
  console.log('loading data from the database');
  $.getJSON(`/place/info/${placeId}`, (res) => {
    if (res.success) {
      renderPlaceDetailsUI(res.data);
      $("#new-place").hide();
    } else {
      console.error("Failed to load place info.");
    }
  }).fail(() => {
    console.error("Failed to load place info.");
  });
}

function renderPlaceDetailsUI(place) 
{
  let mainPhoto = place.photos[0].file_id;

  $("#place-detail").html(`
    <p class="text-capitalize text-muted">${place.type}</p>
    <button type="button" class="btn-close position-absolute m-2 top-0 end-0" aria-label="Close" onclick="$(this).parent().hide()"></button>
    <div>
    <h5>${place.name}</h5>
    <div class="my-3"><img src="/file/image/${mainPhoto}/360x200" class="w-100 rounded"></div>
    <p>${place.address}</p>
    <div class="mt-3"><a class="btn btn-outline-dark btn-sm" href="https://www.google.com/maps/place/?q=place_id:${place.place_id}" target="_blank">View on Google Maps</a></div>
    <hr>
    <div class="small border" style="margin:-4px; padding:8px">
      <div class="mt-2">Registered by <i class="bi bi-robot"></i> Notip-bot, as</div>
      <div class="my-2 text-center">
        <div class="gold-shimmer">No Tip</div>
      </div>
      <div class="my-3"><b>Basis</b>: I confirmed it from other sources.</div>
      <div class="text-info text-truncate small p-3">
        <a href="https://www.grubstreet.com/2015/12/all-nyc-restaurants-no-tipping.html" target="_blank">
          https://www.grubstreet.com/2015/12/all-nyc-restaurants-no-tipping.html
        </a>
      </div>
    </div>
    <hr>
    <h5 class="mb-3">Reviews</h5>
    <ul id="review-list">
    </ul>
    <div class="mt-3">
    <h5 class="mt-4 mb-3">Add Your Review</h5>
    <form id="review-form" onsubmit="return submitReview($(this))">
    <input type="hidden" name="place-id" id="place-id" value="${place.place_id}">
    <textarea name="review-content" class="form-control" placeholder="Write your review..."></textarea>
    <div class="my-2 small text-muted">Don't need to choose.. <i class="bi bi-question-circle" data-bs-toggle="tooltip" data-bs-placement="top" title="Selecting the following checkbox is optional. If you're unsure, feel free to skip it and just leave a review."></i></div>
    <div class="form-check">
      <input class="form-check-input" name="review-confirm" type="checkbox" value="1" id="review-confirm-check">
      <label class="form-check-label" for="review-confirm-check">
        Confirmed as a No-tip Place.
      </label>
    </div>
    <div class="form-check">
      <input class="form-check-input" name="review-dispute" type="checkbox" value="1" id="review-dispute-check">
      <label class="form-check-label" for="review-dispute-check">
        This is not a No-tip Place.
      </label>
    </div>
    
    <div class="text-end">
    <button class="btn btn-secondary mt-2">Post Review</button>
    </form>
    </div>
    </div>
    </div>
  `);

  $("#place-detail").fadeIn();
  $("#sidebar").scrollTop(0);
  initTooltip();
  loadReviews();
}

function loadReviews() 
{
  placeId = $("#place-id").val();
  if(!placeId) 
  {
    console.log('place id is required');
    return false;
  }

  sampleReviews = [
    {
      profile_picture: "/assets/imgs/sample/user1.jpg", // 예시 이미지 URL
      name: "Namit",
      content: "Great place! The atmosphere was amazing, and the staff was very friendly. Highly recommended!",
      time: "Just now"
    },
    {
      profile_picture: "/assets/imgs/sample/user2.jpg", // 예시 이미지 URL
      name: "Ishita",
      content: "Had a fantastic experience here. The food was delicious, and I'll definitely be back again!",
      time: "Few days ago"
    }
  ];

  $.ajax({
    url: "/review/list/"+placeId,
    dataType: "json",
    success: function (response) {
      console.log(response);
      const reviews = response.data || [];
      console.log(reviews);
      renderReviews(reviews.length > 0 ? reviews : sampleReviews);
    },
    error: function (error) {
      console.log("Failed to lad review. Please try again.");
      console.error("Error:", error);
    }
  });

  function renderReviews(reviews) {
    const reviewsHtml = reviews.map((review) => `
      <li class="media mb-3"> 
        <img src="${review.profile_picture}" class="mr-3 rounded-circle" alt="User photo" width="36" height="36">
        <span class="mt-0">${review.name}</span>
        <div class="media-body mt-2 small">
          ${review.content}
        </div>
        <div class="text-end text-muted small mb-2">
          ${review.time}
        </div>
      </li>
    `).join("");
    $("#review-list").html(reviewsHtml);
  }
}

function submitReview(form) 
{
  serializedData = form.serialize();
  console.log(serializedData);  

  $.ajax({
    url: "/review/add",
    method: "POST",
    data: serializedData,
    success: function (response) {
      loadReviews();
    },
    error: function (error) {
      alert("Failed to submit review. Please try again.");
      console.error("Error:", error);
    }
  });

  return false;
}

// 검색창 자동완성 초기화
function initAutocomplete() {
  autocomplete = new google.maps.places.Autocomplete(
    document.getElementById("autocomplete"),
    { types: ["establishment"] }
    );
  autocomplete.addListener("place_changed", () => onPlaceChanged(autocomplete.getPlace()));
}

function onPlaceChanged(place) {
  if (!place.geometry) {
    console.error("No details available for this place.");
    return;
  }

  // photos 배열에서 첫 번째 사진의 URL 가져오기
  const photoUrl = place.photos ? place.photos[0].getUrl({ maxWidth: 400 }) : "";

  // Initialize variables for city, state, and zipcode
  let city = "", state = "", zipcode = "";

  // Extract address components for city, state, and zipcode
  if (place.address_components) {
    place.address_components.forEach(component => {
      const types = component.types;

      if (types.includes("locality")) {
        city = component.long_name; // City
      }
      if (types.includes("administrative_area_level_1")) {
        state = component.short_name; // State (abbreviated)
      }
      if (types.includes("postal_code")) {
        zipcode = component.long_name; // Zip code
      }
    });
  }

  const placeDetails = {
    placeId: place.place_id,
    name: place.name,
    address: place.formatted_address || "",
    latitude: place.geometry.location.lat(),
    longitude: place.geometry.location.lng(),
    phone: place.formatted_phone_number || "",
    website: place.website || "",
    type: place.types ? place.types[0] : "",
    photoUrl: photoUrl, // photoUrl 속성 추가
    city: city,
    state: state,
    zipcode: zipcode,
  };

  moveToLocation(placeDetails.latitude, placeDetails.longitude);
  displayPlaceDetails(placeDetails);
  $("#place-detail").hide();
  $("#sidebar").scrollTop(0);
}

// Place Details API로 기본 POI 정보 가져오기
function loadPlaceDetails(placeId) {
  const service = new google.maps.places.PlacesService(map);
  service.getDetails({ placeId }, (place, status) => {
    if (status === google.maps.places.PlacesServiceStatus.OK) {
      onPlaceChanged(place);
    } else {
      console.error("Failed to get place details:", status);
    }
  });

  map.setOptions({ styles: defaultStyle }); // 스타일 변경
  isPOIVisible = false;

  if(!$("#sidebar").is(":visible")) {
    $("#sidebar").slideDown();
  }
}

// 지도와 선택 마커를 특정 위치로 이동
function moveToLocation(lat, lng) {
  const location = new google.maps.LatLng(lat, lng);
  map.setCenter(location);
  selectedMarker.setPosition(location);
  selectedMarker.setVisible(true);
}

// 선택된 장소 정보 저장
function savePlaceToDatabase() {
  const placeDetails = window.placeDetailsForSave;
  $.ajax({
    url: "/place/save",
    method: "POST",
    contentType: "application/json",
    data: JSON.stringify(placeDetails),
    success: () => alert("장소가 저장되었습니다!"),
    error: (error) => alert("저장 중 오류 발생"),
  });
}

