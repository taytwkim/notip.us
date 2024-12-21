let map, infowindow, autocomplete, selectedMarker;
let isPOIVisible = false; // Flag to check if POIs are visible
let center;

const markers = [];
var path = window.location.pathname;
var match = path.match(/\/map\/([^\/]+)/);
let placeId = null;
if (match) {
  placeId = match[1];
  console.log("Place ID:", placeId);
} else {
  console.log("Place ID not found in the URL");
}

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

$(function () {
  var animationInProgress = false; // 애니메이션 진행 중 여부를 확인하는 플래그
  var panelReset = false;
  var maxAllowedHeight = 0; // Initialize maxAllowedHeight

  $("#sidebar").resizable({
    handles: {
        s: ".handle" 
    },
    minWidth: $("#sidebar").width(), // 현재 너비를 최소 너비로 설정
    maxWidth: $("#sidebar").width(), // 현재 너비를 최대 너비로 설정
    maxHeight:800,
    start: function(e, ui) {
      e.stopPropagation(); // 이벤트 전파를 중지하여 지도에서의 mousedown 이벤트를 차단
      if(panelReset) {
        var parentHeight = $(this).parent().height();
        resetPanel(parentHeight);  
        panelReset = false;
      }
      console.log('● resizing started');
    },
    resize: function(event, ui) {
      var parentHeight = $(this).parent().innerHeight();
      var currentHeight = ui.size.height;
      var originalHeight = ui.originalSize.height;
      var deltaHeight = currentHeight - originalHeight;
      maxAllowedHeight = parentHeight * 0.9; 

      console.log('parentHeight: '+parentHeight);
      console.log('maxAllowedHeight: '+maxAllowedHeight);
      console.log('currentHeight: '+currentHeight);
      console.log('originalHeight: '+originalHeight);
      console.log('deltaHeight: '+deltaHeight);
      console.log('ui.position.top: '+ui.position.top);

      targetTop = ui.position.top + deltaHeight;
      var targetHeight = originalHeight - deltaHeight;

      $(this).css({
        top: targetTop,
        height: targetHeight,
        width: '100%'
      });

      if (targetHeight > maxAllowedHeight) {
        console.log('(Warning!) height hit the top');

        if (!animationInProgress) {
          animationInProgress = true; // Set flag to indicate animation in progress
          targetHeight = maxAllowedHeight-50;
          targetTop = parentHeight - maxAllowedHeight+50; // Adjust top value based on height limit

          $(this).animate({
            top: targetTop,
            height: targetHeight
          }, 200, function() {
              console.log('◆ ceil animation done');
              animationInProgress = false; // Reset flag after animation is complete
          });
        }
      }

      if(targetHeight < 50 || parentHeight - targetTop < 50) {
        console.log('(Warning!) height hit the bottom');

        if (!animationInProgress) {
          animationInProgress = true; // Set flag to indicate animation in progress
          targetHeight = 50;
          targetTop = parentHeight - 50;

          $(this).animate({
            top: targetTop,
            height: targetHeight
          }, 200, function() {
              console.log('◆ floor animation done');
              animationInProgress = false; // Reset flag after animation is complete
          });

          panelReset = true;
        }
      }
      
      console.log('targetTop: '+targetTop);
      console.log('targetHeight: '+targetHeight);
      
      return false;
    },
    stop: function(e, ui) {
      console.log('■ resizing stopped');
      console.log(ui);
    }
  });

  // Handle pointerdown to adjust height when at max limit
  $("#sidebar").on("pointerdown", function () {
    var currentHeight = $(this).height();
    if (currentHeight >= maxAllowedHeight) {
      $(this).height(currentHeight - 1); // Reduce height by 1 to enable resize
    }
  });
});

function resetPanel(parentHeight) {
  console.log('reset');
  $("#sidebar").animate({
    top: parentHeight - 300,
    height: 300
  }, 200); // 200ms 동안 애니메이션
}

$(document).ready(function () {
  // Google Maps API 로드 후 initMap 호출
  $.getScript(
    "https://maps.googleapis.com/maps/api/js?key=AIzaSyA69MLRfjDCUoSHsSPgU1uYHo4OGonMXAM&libraries=places&language=en",
    function () {
      initMap();
  });

  if(placeId) {
    loadPlaceInfo(placeId);
  }  

  $(window).resize(function () {
    if ($(window).width() > 768) {
    // PC 화면일 때 강제로 사이드바를 보여줌
      $("#sidebar").show();
    }
  });
});

function setNewPlaceMap() {
  if(!$("#newPlaceSearch").is(":visible")) {
    $("#newPlaceSearch").fadeIn();
    map.setOptions({ styles: focusStyle });
    isPOIVisible = true; 
  }
}

function offNewPlaceMap() {
  $("#new-place").hide();
  $("#new-place").empty();

  map.setOptions({ styles: defaultStyle });
  isPOIVisible = false; 
  $("#newPlaceSearch").hide();
}

// To prevent momentary layout shifts, fast pure JavaScript is used.
function imageLoaded(img) {
  const skeleton = img.previousElementSibling; // Selects the skeleton
  skeleton.classList.add('loaded'); // Hides the skeleton
  img.style.display = 'block'; // Ensures the image is visible
}

function initMap() {
  // Try HTML5 Geolocation API to find current location
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
        visible: false,
      });

      map.addListener("zoom_changed", toggleMarkerLabels);
      map.addListener("click", (event) => {
        if (isPOIVisible) {
          console.log("Map Clicked");
          console.log(event);
          if (event.placeId) {
            event.stop();
            loadPlaceDetails(event.placeId);
          }
        }
      });  

      map.addListener("dragend", () => {
          center = map.getCenter();
          loadPlaces();
      });

      center = map.getCenter();
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

  console.log('current zoom level: '+currentZoom);

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

function loadPlaces() {
  lat = center.lat();
  lng = center.lng();

  $.getJSON(`/place/list/location?lat=${lat}&lng=${lng}`, (result) => {

    markers.forEach(marker => marker.setMap(null));  // Remove existing markers from the map
      markers.length = 0;
    $("#placeList").empty();

    console.log(result);

    result.data.places.forEach((place) => {
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
        loadPlaceInfo(place.place_id);
      });

      let mainPhoto;
      if (place.photos && place.photos.length > 0) {
          mainPhoto = '/file/image/'+place.photos[0].file_id+'/360x200';
      } else {
          mainPhoto = getRandomSampleImage(); 
      }

      $("#placeList").append(`
        <li onclick="loadPlaceInfo('${place.place_id}')">
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
  $("input[name=policy]").change(function() {
    if($(this).val() == "noTip") {
      $(".forNoTip").fadeIn();
      $(".forFairTip").hide();
    }
    else if($(this).val() == "fairTip") {
      $(".forFairTip").fadeIn();
      $(".forNoTip").hide();
    }
  });

  $("#basedon-source").change(function() {
    if($("#basedon-source").is(":checked")) {
      $(".source-group").show();
    }
    else
    {
      $(".source-group").hide();
    }  
  });

  $("#basedon-official").change(function() {
    if($("#basedon-official").is(":checked")) {
      $(".official-group").show();
    }
    else
    {
      $(".official-group").hide();
    }  
  })
}


function displayPlaceDetails(placeDetails) {
  console.log(placeDetails);
  $("#new-place").html(`
    <button type="button" class="btn-close position-absolute m-2 top-0 end-0" aria-label="Close" onclick="offNewPlaceMap()"></button>
    <h5><b>Register New</b></h5>
    <form id="register-form" onsubmit="return savePlace($(this))">
    <input type="hidden" name="google-place-id" value="${placeDetails.placeId}">
    <input type="hidden" name="name" value="${placeDetails.name}">
    <input type="hidden" name="address" value="${placeDetails.address}">
    <input type="hidden" name="type" value="${placeDetails.type}">
    <input type="hidden" name="state" value="${placeDetails.state}">
    <input type="hidden" name="city" value="${placeDetails.city}">
    <input type="hidden" name="latitude" value="${placeDetails.latitude}">
    <input type="hidden" name="longitude" value="${placeDetails.longitude}">
    <input type="hidden" name="zipcode" value="${placeDetails.zipcode}">
    
    ${placeDetails.photoUrl ? `<img src="${placeDetails.photoUrl}" alt="${placeDetails.name}" class="img-fluid rounded mt-3">` : ""}
    <div class="mt-3"><span class="fs-5"><strong>${placeDetails.name}</strong></span> <span class="text-info small ms-1">${placeDetails.type}</span></div>
    <p>${placeDetails.address}</p>
    <p><strong>Will you add this place as a No-Tip place or a Fair-Tip place?</strong></p> 
    <p><strong>Tip Policy</strong></p>
    <div class="btn-group" role="group">
      <input type="radio" class="btn-check" name="policy" id="policy-noTip" value="noTip" autocomplete="off">
      <label class="btn btn-outline-primary" for="policy-noTip">No Tip</label>
      <input type="radio" class="btn-check" name="policy" id="policy-fairTip" value="fairTip" autocomplete="off">
      <label class="btn btn-outline-primary" for="policy-fairTip">Fair Tip</label>
    </div>
    <div>
    <div class="forNoTip">
      <p class="mt-2"><i class="bi bi-exclamation-circle"></i> "No Tip" means:</p>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" name="policy-notaccept" value="1" id="policy-notaccept">
        <label class="form-check-label" for="policy-notaccept">
          This place does not accept tips by policy.
        </label>
      </div>
    </div>
    <div class="forFairTip">
      <p class="mt-2"><i class="bi bi-exclamation-circle"></i> "Fair Tip" means one of the following applies:</p>
      <div class="form-check">
        <input class="form-check-input fairtip-policy" type="checkbox" name="policy-min10" value="1" id="policy-min10">
        <label class="form-check-label" for="policy-min10">
          The suggested minimum tip is 10% or lower.
        </label>
      </div>
      <div class="form-check">
        <input class="form-check-input fairtip-policy" type="checkbox" name="policy-max20" value="1" id="policy-max20">
        <label class="form-check-label" for="policy-max20">
          The suggested maximum tip is 20% or lower.
        </label>
      </div>
      <div class="form-check">
        <input class="form-check-input fairtip-policy" type="checkbox" name="policy-nosuggest" value="1" id="policy-nosuggest">
        <label class="form-check-label" for="policy-nosuggest">
          No tip amount is suggested.
        </label>
      </div>
      <div class="form-check">
        <input class="form-check-input fairtip-policy" type="checkbox" name="policy-guarantee" value="1" id="policy-guarantee">
        <label class="form-check-label" for="policy-guarantee">
          They state that minimum wage is guaranteed for employees without relying on tips.
        </label>
      </div>
    </div>
    <p class="mt-3"><strong>How do you know?</strong></p> 
    <div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="1" id="basedon-experience" name="basedon-experience">
        <label class="form-check-label" for="basedon-experience">
          I experienced it myself.
        </label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="1" id="basedon-source" name="basedon-source">
        <label class="form-check-label" for="basedon-source">
          I confirmed it from other sources.
        </label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="checkbox" value="1" id="basedon-official" name="basedon-official">
        <label class="form-check-label" for="basedon-official">
          I confirmed it on the official website.
        </label>
      </div>
    </div>
    <div class="source-group hidden">
      <p class="mt-3 mb-1"><strong>External Source</strong></p> 
      <div><input type="text" name="source-page" class="form-control" placeholder="https://"></div>
    </div>
    <div class="official-group hidden">
      <p class="mt-3 mb-1"><strong>Official State</strong></p> 
      <div><input type="text" name="official-page" class="form-control" placeholder="https://"></div>
    </div>
    <p class="mt-3 mb-1"><strong>Please explain more about what you saw or experienced.</strong></p> 
    <div><textarea type="text" name="experience" class="form-control"></textarea></div>
    </form>
    <div class="mt-3">
      <button type="submit" class="btn btn-primary">Register</button>
      <button type="button" class="btn btn-secondary" onclick="offNewPlaceMap()">Cancel</button>
    </div>
    `);
  $("#new-place").show();
  addPlaceUI();
  $("#panel-content").animate({ scrollTop: 0 }, 500);
}

function savePlace(form) {
  const policy = $('input[name="policy"]:checked').val();
  if(!policy) {
    alert('Please select whether this place has a "No Tip" policy or a "Fair Tip" policy.');
    return false;
  }

  if(policy=='noTip' && !$("#policy-notaccept").is(":checked")) {
    alert('If the place has a no-tip policy, you should confirm that this it does not accept tips by policy.');
    return false;
  }

  if(policy=='fairTip' && $('.fairtip-policy:checked').length < 1) {
    alert('If the place has a fair-tip policy, you should check at least one detail about the policy.');
    return false;
  }

  serializedPlaceData = form.serialize();
  console.log(serializedPlaceData);  

  $.ajax({
    url: "/place/save",
    method: "POST",
    dataType: "json",
    data: serializedPlaceData,
    success: function (response) {
      console.log(response);
      if(response.success) 
      {
        // 정상적으로 
        offNewPlaceMap();
        loadPlaces();
        selectedMarker.setVisible(false);
        loadPlaceInfo(response.data);
        $('#modal-registered').modal('show');
      }
      else
      {
        alert('An error occurred during registering');
        return false;
      }
    },
    error: function (error) {
      alert("Failed to save place. Please try again.");
      console.error("Error:", error);
    }
  });

  return false;
}

// 등록된 장소 클릭 시 상세 정보 로드 및 UI 표시
function loadPlaceInfo(placeId) {
  console.log('loading data from the database');
  $.getJSON(`/place/info/${placeId}`, (res) => {
    if (res.success) {
      renderPlaceDetails(res.data);
      $("#new-place").hide();

      var newUrl = '/map/' + encodeURIComponent(placeId);
      window.history.pushState({ path: newUrl }, '', newUrl);
    } else {
      console.error("Failed to load place info.");
    }
  }).fail(() => {
    console.error("Failed to load place info.");
  });
}

function renderPlaceDetails(place) 
{
  let mainPhoto;
  if (place.photos && place.photos.length > 0) {
      mainPhoto = '/file/image/'+place.photos[0].file_id+'/360x200';
  } else {
      mainPhoto = getRandomSampleImage(); 
  }

  $("#place-detail").html(`
    <div class="mb-3">
      <div class="gold-shimmer px-3 py-1 me-2" style="font-size:0.8rem">No Tip</div>
      <span class="text-capitalize text-muted align-middle"><b>${place.type}</b></span>
    </div>
    <button type="button" class="btn-close position-absolute m-2 top-0 end-0" aria-label="Close" onclick="$(this).parent().hide()"></button>
    <div>
    <h4>${place.name}</h4>
    <div class="image-container my-3">
      <div class="skeleton"></div>
      <img 
        src="${mainPhoto}" 
        class="w-100 rounded" 
        onload="imageLoaded(this)">
    </div>
    <p>${place.address}</p>
    <div class="mt-3"><a class="btn btn-outline-dark btn-sm" href="https://www.google.com/maps/place/?q=place_id:${place.google_place_id}" target="_blank">View on Google Maps</a></div>
    <hr>
    <div class="small border bg-light mb-3 p-3">
      <div class="mt-2">Registered by <i class="bi bi-robot"></i> Notip-bot</div>
      <div class="my-3"><i>"I confirmed it from other sources."</i></div>
      <div class="text-info small">
        <a href="https://www.grubstreet.com/2015/12/all-nyc-restaurants-no-tipping.html" target="_blank">
          https://www.grubstreet.com/2015/12/all-nyc-restaurants-no-tipping.html
        </a>
      </div>
    </div>
    <a class="btn btn-sm btn-outline-primary" href="/manage/register/${place.place_id}">Are you the manager of this place?</a>
    <hr>
    <section class="mt-3">
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
      </div>
      </form>
      <hr>
      <h5 class="mb-3">Reviews</h5>
      <ul id="review-list"></ul>
    </section>
    </div>
  `);

  $("#place-detail").fadeIn();
  $("#panel-content").scrollTop(0);
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
    dataType: "json",
    data: serializedData,
    success: function (response) {
      if(response.success) 
      {
        loadReviews();
      }
      else
      {
        alert(response.msg);
        return false;
      }
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

// 임시 테스트용


// 랜덤 이미지를 반환하는 함수
function getRandomSampleImage() {
  const imageList = [
      '/assets/imgs/sample/restaurant1.png',
      '/assets/imgs/sample/restaurant2.png',
      '/assets/imgs/sample/restaurant3.png',
      '/assets/imgs/sample/bar1.png',
      '/assets/imgs/sample/bar3.png',
      '/assets/imgs/sample/bar3.png',
      '/assets/imgs/sample/cafe1.png',
      '/assets/imgs/sample/cafe2.png',
      '/assets/imgs/sample/cafe3.png'
  ];
  const randomIndex = Math.floor(Math.random() * imageList.length);
  return imageList[randomIndex];
}

