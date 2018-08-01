
    
    <div class="row">
    <div class="card col-md-4 col-md-offset-6" style="margin-left:auto;margin-right:auto">
    <div class="card-body">
        <?php if ($msg == 'success') {?>
            Your payment of NGN <?php echo $chargeAmount;?> is successful. A message has been sent to your chosen provider also. You will get a response shortly.
        <?php }?>
    </div>
  </div>
    </div>
  <div class="row">
    <?php $count=1; if (!empty($provider)) { ?>
        <div class="col-md-4 col-md-offset-6" style="margin-left:auto;margin-right:auto">
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
        <p class="card-text">Status : </p>
    </div>
    <div class="card-footer text-muted blue darken-1 white-text">
    <p class="mb-0"><a href="<?php echo $this->webroot?>users/">Go Back</a></p>
    </div>
</div>
    </div>
    <?php $count++;} else {?>
    <div class="card col-md-4 col-md-offset-6" style="margin-left:auto;margin-right:auto">
    <div class="card-body">
        No provider is registered under this service.
    </div>
  </div>
    <?php }?>

    </div>