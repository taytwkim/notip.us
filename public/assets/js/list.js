$(document).ready(function() {
  loadPlaces();
});

function loadPlaces() {
  $.getJSON("/place/list", (data) => {
    $("#main-list").empty();
    data.data.forEach((place) => {
      let mainPhoto;
      if (place.photos && place.photos.length > 0) {
          mainPhoto = '/file/image/'+place.photos[0].file_id+'/360x200';
      } else {
          mainPhoto = '/assets/imgs/sample/restaurant1.png'; 
      }

      $("#main-list").append(`
        <div class="col">
          <div class="card shadow-sm">
            <div class="main-image card__header header__img skeleton" style="background:URL('${mainPhoto}')"></div>
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <p><strong>${place.name}</strong></p>
                <p class="text-info small">${place.type}</p>
              </div>
              <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content. This content is a little bit longer.</p>
              <div class="d-flex justify-content-between align-items-center">
                <div class="btn-group">
                  <button type="button" class="btn btn-sm btn-outline-secondary">View on Map</button>
                  <button type="button" class="btn btn-sm btn-outline-secondary">Edit</button>
                </div>
                <small class="text-body-secondary fw-bold">${place.policyText}</small>
              </div>
            </div>
          </div>
        </div>
      `);

    });
  });
}

/*
<li onclick="loadRegisteredPlaceInfo('${place.place_id}')">
<div><img src="/file/download/${mainPhoto}" class="w-100 rounded"></div>

<div class="mt-2"><strong>${place.name}</strong> <span class="text-info small ms-1">${place.type}</span></div>
<div class="mt-2">${place.address}</div>
<div class="mt-2"><span class="badge text-bg-primary">No Tip</span></div>
<div class="mt-3"><a class="btn btn-outline-dark btn-sm" href="https://www.google.com/maps/place/?q=place_id:${place.place_id}" target="_blank">View on Google Maps</a></div>
</li>
*/