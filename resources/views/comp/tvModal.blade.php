{{-- <!-- Button trigger modal -->
<button type="button" class="btn btn-outline-primary" id="tutorialBtn" data-bs-toggle="modal" data-bs-target="#exampleModal">
 Tutorial <i class="fa fa-play"></i>
</button>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg ">
    <div class="modal-content ">
      <div class="modal-header ">
        <h5 class="modal-title" id="exampleModalLabel">Tutorial</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
      <iframe id="youtube-tutorial" width="100%" height="350px" src="{{$src}}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
      </div>
    </div>
  </div>
</div> 

<script>
  $(document).ready(function() {
      $('#exampleModal').on('hidden.bs.modal', function (e) {
        
        var attr = $('#youtube-tutorial').attr('src');
        $('#youtube-tutorial').attr('src',attr);
      });
  });
  </script>

  <style>
    #tutorialBtn{
      position: fixed;
      bottom: 16px;
      right: 100px;
      z-index: 1
    }
  </style>
 --}}
