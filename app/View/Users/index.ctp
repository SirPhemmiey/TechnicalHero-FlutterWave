<!--Main Layout-->
<main class="text-center py-5">
        <section>
            <div class="">
               <form class="form-horizontal" method="get" action="<?php echo $this->webroot?>users/service_providers">
                   <?php echo $this->Flash->render('duplicate');?>
                   <?php echo $this->Flash->render('success');?>
                   <?php echo $this->Flash->render('error');?>
                              <div id="forReview">
                 <select style="width:50%; max-width:80%;" class="select2_ col-md-12 form-control animated fadeIn" data-placeholder="Choose a service" name="id" id="select_school">
                <option value=""></option>
                <?php if(isset($services)){ foreach($services as $service){?>
                <option value="<?php echo $service['Service']['id']?>"><?php echo ucwords($service['Service']['name'])?></option>
                <?php }}?>
                 </select>
            <button type="submit" class="col-md-5 btn btn-primary waves-effect blue">Go!</button>
                   </div>
                     </form>
            </div>

        </section>
        
        
    </main>
    <!--Main Layout-->