<!DOCTYPE html>
<html>
  <head>
    <title>Custom Markers with AJAX and Hidden POI</title>
    <script>
      let map, infowindow;

      function initMap() {
        // 지도 스타일 설정 - 기본 POI 숨기기
        const mapStyles = [
          {
            featureType: "poi",
            elementType: "labels.icon",
            stylers: [{ visibility: "off" }],
          },
          {
            featureType: "poi.business",
            stylers: [{ visibility: "off" }],
          },
        ];

        // 지도 초기화
        map = new google.maps.Map(document.getElementById("map"), {
          center: { lat: 40.749933, lng: -73.98633 },
          zoom: 13,
          styles: mapStyles, // 기본 POI 숨기기 스타일 적용
        });

        infowindow = new google.maps.InfoWindow();

        // 서버에서 데이터를 AJAX로 불러오기
        fetchMarkers();
      }

      function fetchMarkers() {
        // API 호출하여 JSON 데이터를 가져옵니다
        fetch('/api/listPlaces')
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              console.log("Data received from API:", data.data);
              addMarkers(data.data); // data.data로 접근
            } else {
              console.error("API returned an error:", data);
            }
          })
          .catch(error => console.error("Error fetching marker data:", error));
      }

      function addMarkers(restaurantData) {
        // 서버에서 받은 데이터로 각 마커 추가
        restaurantData.forEach((restaurant) => {
          const { place_id, latitude, longitude, name, address } = restaurant;

          // lat, lng 값을 숫자로 변환
          const lat = parseFloat(latitude);
          const lng = parseFloat(longitude);

          // 마커 생성
          const marker = new google.maps.Marker({
            position: { lat, lng },
            map: map,
            title: name,
            label: {
              text: name,
              fontSize: "12px",
              fontWeight: "bold",
            },
            icon: {
              url: "http://maps.google.com/mapfiles/ms/icons/red-dot.png",
              labelOrigin: new google.maps.Point(15, 40), // 마커 아래로 라벨 위치 조정
            },
          });

          // 마커 클릭 시 InfoWindow에 장소 세부 정보 표시
          marker.addListener("click", () => {
            const service = new google.maps.places.PlacesService(map);
            service.getDetails({  placeId: place_id }, (placeDetails, status) => {
              if (status === google.maps.places.PlacesServiceStatus.OK) {
                const contentString = `
                  <h2>${placeDetails.name}</h2>
                  <p>주소: ${placeDetails.formatted_address}</p>
                  <p>전화번호: ${placeDetails.formatted_phone_number || "정보 없음"}</p>
                  <p>평점: ${placeDetails.rating || "N/A"}</p>
                  <p>웹사이트: <a href="${placeDetails.website}" target="_blank">${placeDetails.website || "없음"}</a></p>
                `;
                infowindow.setContent(contentString);
                infowindow.open(map, marker);
              } else {
                infowindow.setContent(`<h2>${name}</h2><p>세부 정보가 없습니다.</p>`);
                infowindow.open(map, marker);
              }
            });
          });
        });
      }
    </script>
    <script
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA69MLRfjDCUoSHsSPgU1uYHo4OGonMXAM&libraries=places&callback=initMap"
      async
      defer
    ></script>
    <style>
      html,
      body {
        height: 100%;
        margin: 0;
        padding: 0;
      }

      #map {
        height: 100%;
      }
    </style>
  </head>
  <body>
    <div id="map"></div>
  </body>
</html>
