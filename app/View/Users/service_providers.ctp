

    <div class="row">
    
    <?php $count=1; if (!empty($providers)) { foreach ($providers as $provider) {?>
        <div class="col-md-4">
    <div class="card text-center" style="width: 22rem;">
    <div class="card-header blue darken-1 white-text">
        #<?php echo $count?>
    </div>
    <div class="card-body">
        <h4 class="card-title"><?php echo ucwords($provider['Provider']['name'])?></h4>
        <p class="card-text">Service : <?php echo $provider['Service']['name']?></p>
        <p class="card-text">Profession : <?php echo $provider['Provider']['profession']?></p>
        <p class="card-text">Email : <?php echo $provider['Provider']['email']?></p>
        <p class="card-text">Mobile : <?php echo $provider['Provider']['phone']?></p>
        <p class="card-text">About : <?php echo $provider['Provider']['intro']?></p>
        <form action="<?php echo $this->webroot?>users/pay" method="post">
        <input type="hidden" name="amount" value="<?php echo $provider['Service']['amount']?>">
        <input type="hidden" name="provider_id" value="<?php echo $provider['Provider']['id']?>">
        <input type="hidden" name="service_id" value="<?php echo $provider['Service']['id']?>">
        <button class="btn blue darken-1 btn-sm" type="submit">Hire</button>
        </form>
    </div>
    <div class="card-footer text-muted blue darken-1 white-text">
    </div>
</div>
    </div>
    <?php $count++;}} else {?>
    <div class="card col-md-4 col-md-offset-6" style="margin-left:auto;margin-right:auto">
    <div class="card-body">
        No provider is registered under this service.
    </div>
  </div>
    <?php }?>

    </div>