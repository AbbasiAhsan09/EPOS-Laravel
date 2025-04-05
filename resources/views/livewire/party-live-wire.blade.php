<div>


    <!-- Modal -->
    <div class="modal fade" wire:ignore.self id="addPartyModal" tabindex="-1" aria-labelledby="addPartyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="addPartyModalLabel">Create New Party</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
           <div class="modal-body">
                <form wire:submit.prevent="add_party">
                    <div class="row">
                        <div class="col-lg-6">
                            <label for="">Party  Name *</label>
                        <div class="input-group input-group-outline">
                          <input type="text" class="form-control" wire:model="party_name" required>
                        </div>
                        </div>  
                        <div class="col-lg-3">
                            <label for="">Party  Group *</label>
                        <div class="input-group input-group-outline">
                            <select wire:model="group_id" class="form-control"  required>
                                <option value="">Select Party Group</option>
                                @foreach ($party_groups as $pt_grp)
                                    <option value="{{$pt_grp->id}}">{{$pt_grp->group_name}}</option>
                                @endforeach
                            </select>
                        </div>
                        </div> 
                        @if (ConfigHelper::getStoreConfig()["use_accounting_module"])
                        <div class="col-lg-3">
                          <label for="">Opening Balance</label>
                      <div class="input-group input-group-outline">
                         <input type="number" wire:model="opening_balance" class="form-control" id="">
                      </div>
                      </div> 
                      @endif
                        <div class="col-lg-4">
                            <label for="">Party  Email</label>
                        <div class="input-group input-group-outline">
                          <input type="email" class="form-control" wire:model="email" >
                        </div>
                        </div>  
    
                        <div class="col-lg-4">
                            <label for="">Party  Phone *</label>
                        <div class="input-group input-group-outline">
                          <input type="text" class="form-control" wire:model="phone" >
                        </div>
                        </div>  
    
    
                        <div class="col-lg-4">
                          <label for="">Party  Business Name </label>
                      <div class="input-group input-group-outline">
                        <input type="text" class="form-control" wire:model="business_name"  placeholder="Demo Trades">
                      </div>
                      </div>
                        {{-- @livewire('location') --}}
    
                        <div class="col-lg-12">
                            <label for="">Party  Address </label>
                        <div class="input-group input-group-outline">
                          <textarea wire:model="location"  class="form-control" id="" cols="30" rows="3"></textarea>
                        </div>
                        </div> 
    
                    </div>
               
            </div> 
            <div class="modal-footer">
              
              <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
          </div>
        </div>
      </div>
      {{-- Modal --}}



      <script>
        document.addEventListener('livewire:load', function () {
            Livewire.on('party_added', () => {
                // Close the Bootstrap modal
                $('#addPartyModal').modal('hide');
            });
        });
    </script>
</div>
