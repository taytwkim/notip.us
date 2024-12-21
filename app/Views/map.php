<link href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css" rel="stylesheet"/>
<script src="https://code.jquery.com/ui/1.14.1/jquery-ui.min.js" integrity="sha256-AlTido85uXPlSyyaZNsjJXeCs07eSv3r43kyCVc8ChI=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js" integrity="sha512-0bEtK0USNd96MnO4XhH8jhv3nyRF0eK87pJke6pkYf3cM0uDIhNJy9ltuzqgypoIFXw3JSuiy04tVk4AjpZdZw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<link href="/assets/css/main.css?v=3" rel="stylesheet"/>
<script src="/assets/js/main.js?v=18"></script>
<div id="sidebar">
  <div class="handle fs-5">No Tip Places Around You</div>
  <div id="panel-content">
    <div class="card mb-4 p-3" id="place-detail" style="display:none"></div>
    <div class="card mb-4 p-3" id="new-place" style="display:none"></div>
    <h5 class="ms-1 mt-1 fs-5 panel-list-title">No Tip Places Around You</h5>
    <input id="search" class="form-control my-3" placeholder="Search for a place" type="text"/>
    <ul id="placeList" class="list-group"> 
    </ul>
  </div>  
</div>
<div id="map"></div>
<button id="addNew" class="btn btn-blue" onclick="setNewPlaceMap()">Add New</button>
<div id="newPlaceSearch">
  <h5><storng>Register New</storng></h5>
  <div class="mb-2">Search for a place or click on the map</div>
  <input id="autocomplete" class="form-control" placeholder="Search address" type="text"/>
  <button type="button" class="btn-close position-absolute m-2 top-0 end-0" aria-label="Close" onclick="offNewPlaceMap()"></button>
</div>
<!-- Modal -->
<div class="modal fade" id="modal-experience" tabindex="-1" aria-labelledby="modal-experience" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">Share what you saw and experienced!</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick=""></button>
      </div>
      <div class="modal-body">
        <p>If you visited the place yourself, please provide more detailed information. Was there no mention of tips at all on kiosks or payment terminals? Were there no options to leave a tip, and did the staff tell you that they don’t accept tips when you tried to leave one?</p>
        <p>If your experience is based on an external source rather than a personal visit, please let us know. Does the article or post clearly state that this place has a no-tip policy?</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Understood</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal -->
<div class="modal fade" id="modal-registered" tabindex="-1" aria-labelledby="modal-registered" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="staticBackdropLabel">The place has been shared with everyone!</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick=""></button>
      </div>
      <div class="modal-body">
        <p>Thank you for your contribution to society and our service. Please note that newly registered places will be continuously reviewed and may be deleted at our discretion or upon request by the business.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>