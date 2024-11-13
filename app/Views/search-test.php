<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Place Search and Save</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
      let autocomplete;

      function initAutocomplete() {
        // Autocomplete 초기화
        autocomplete = new google.maps.places.Autocomplete(
          document.getElementById("autocomplete"),
          { types: ["establishment"] } // 특정 유형(식당, 카페 등)만 검색
        );

        autocomplete.addListener("place_changed", onPlaceChanged);
      }

      function onPlaceChanged() {
        const place = autocomplete.getPlace();

        if (!place.geometry) {
          console.error("No details available for this place.");
          return;
        }

        // Google Places API로부터 제공받은 정보들
        const placeDetails = {
          placeId: place.place_id,
          name: place.name,
          address: place.formatted_address || "",
          latitude: place.geometry.location.lat(),
          longitude: place.geometry.location.lng(),
          phone: place.formatted_phone_number || "",
          website: place.website || "",
          type: place.types ? place.types[0] : "", // 첫 번째 유형만 가져옴
        };

        // Address Components에서 state, city, zipcode 추출
        place.address_components.forEach((component) => {
          const types = component.types;
          if (types.includes("administrative_area_level_1")) {
            placeDetails.state = component.long_name;
          }
          if (types.includes("locality") || types.includes("sublocality")) {
            placeDetails.city = component.long_name;
          }
          if (types.includes("postal_code")) {
            placeDetails.zipcode = component.long_name;
          }
        });

        // 정보를 표시
        displayPlaceDetails(placeDetails);
      }

      function displayPlaceDetails(placeDetails) {
        // 선택한 장소 정보를 Bootstrap 스타일로 표시
        $("#place-info").html(`
          <div class="card p-3 mt-3">
            <h5 class="card-title">${placeDetails.name}</h5>
            <p class="card-text"><strong>주소:</strong> ${placeDetails.address}</p>
            <p class="card-text"><strong>전화번호:</strong> ${placeDetails.phone}</p>
            <p class="card-text"><strong>웹사이트:</strong> 
              <a href="${placeDetails.website}" target="_blank">${placeDetails.website || "없음"}</a>
            </p>
            <div class="mb-3">
              <label class="form-label"><strong>구분:</strong></label>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="tipType" value="no tip" id="noTip">
                <label class="form-check-label" for="noTip">No Tip</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="tipType" value="fair tip" id="fairTip">
                <label class="form-check-label" for="fairTip">Fair Tip</label>
              </div>
            </div>
            <div class="mb-3">
              <label for="description" class="form-label"><strong>설명:</strong></label>
              <textarea id="description" class="form-control" rows="3"></textarea>
            </div>
            <button id="savePlace" class="btn btn-primary">추가하기</button>
          </div>
        `);

        // "추가하기" 버튼 클릭 시 API 호출
        $("#savePlace").click(() => savePlaceToDatabase(placeDetails));
      }

      function savePlaceToDatabase(placeDetails) {
        // 선택된 구분과 설명 추가
        const tipType = $('input[name="tipType"]:checked').val() || "";
        const description = $("#description").val();

        // placeDetails에 구분과 설명 추가
        const dataToSave = {
          ...placeDetails,
          tipType,
          description,
        };

        $.ajax({
          url: "/api/savePlace",
          method: "POST",
          contentType: "application/json",
          data: JSON.stringify(dataToSave),
          success: (response) => {
            console.log("Place saved successfully:", response);
            alert("장소가 성공적으로 저장되었습니다!");
          },
          error: (error) => {
            console.error("Error saving place:", error);
            alert("장소 저장 중 오류가 발생했습니다.");
          },
        });
      }
    </script>
    <script
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA69MLRfjDCUoSHsSPgU1uYHo4OGonMXAM&libraries=places&language=en&callback=initAutocomplete"
      async
      defer
    ></script>
    <style>
      html,
      body {
        height: 100%;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
      }
      #autocomplete {
        width: 300px;
        height: 45px;
        font-size: 16px;
        margin-top: 20px;
      }
      #place-info {
        width: 300px;
      }
    </style>
  </head>
  <body>
    <input
      id="autocomplete"
      class="form-control"
      placeholder="Enter a place"
      type="text"
    />
    <div id="place-info"></div>
    <!-- Bootstrap JS (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
  </body>
</html>
