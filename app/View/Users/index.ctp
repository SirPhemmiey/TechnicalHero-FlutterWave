<!--Main Layout-->
<main class="text-center py-5">
        <section>
            
            <!-- Registration Form Section -->
            <div class="">
               <form class="form-horizontal" method="post" action="<?php echo $this->webroot?>home">
                   <?php echo $this->Flash->render('duplicate');?>
                   <?php echo $this->Flash->render('success');?>
                   <?php echo $this->Flash->render('error');?>
                              <div id="forReview">
                 <select style="width:100%; max-width:80%;" class="select2_ col-md-12 form-control animated fadeIn" data-placeholder="Choose a service" name="data[Service][id]" id="select_school">
                            <option value=""></option>
                            <?php if(isset($services)){ foreach($services as $service){?>
                            <option value="<?php echo $service['Service']['id']?>"><?php echo $service['Service']['name']?></option>
                            <?php }}?>
                        </select>
            <button style="width:100%; max-width:80%;" type="button" id="review" class="btn waves-effect blue list-search-btn">Go!</button>
                   </div>
              
                
                 <section class="result" id="result">
            <div class="contanier">
                    <div class="row">
                               <div class="col-md-6">
                <h4>Academics : </h4>
                <input type="hidden" name="data[All_review][categories][]" value="Academics">
                <input type="hidden" name="data[All_review][categories_options][]" id="acad_rating">

                  <div class="form-group">
                    <label for="ex3">How seriously is academic achievement taken in this school? How well do teachers teach in this school?</label>
                   <input type="hidden" name="data[Parent_review][question_1]" value="How seriously is academic achievement taken in this school? How well do teachers teach in this school?">
                      <textarea name="data[Parent_review][answer_1]" class="form-control" required></textarea>
                  </div>
                    <div class="form-group">
                            <h5>Rate this category</h5>
                            </div>
                    <span id="acad_rate"></span>
                    <input type="hidden" name="data[All_review][rate][]" value="" id="rate_for_acad">
                        </div>
                        
                              <div class="col-md-6">
                <h4>Management : </h4>
                <input type="hidden" name="data[All_review][categories][]" value="Management">
                <input type="hidden" name="data[All_review][categories_options][]" id="mgt_rating">

                  <div class="form-group">
                    <label for="ex3">What’s leadership like in this school? How well is the school and how well kept are its facilities? Is security of kids taken seriously?</label>
                   <input type="hidden" name="data[Parent_review][question_2]" value="What’s leadership like in this school? How well is the school and how well kept are its facilities? Is security of kids taken seriously?">
                   
                      <textarea required name="data[Parent_review][answer_2]" class="form-control"></textarea>
                  </div>
                    <div class="form-group">
                            <h5>Rate this category</h5>
                            </div>
                    <span id="mgt_rate"></span>
                      <input type="hidden" name="data[All_review][rate][]" value="" id="rate_for_mgt">            
                        </div>
                        
                              <div class="col-md-6">
                <h4>Co-curricular : </h4>
                <input type="hidden" required name="data[All_review][categories][]" value="Co-curricular">
                                  <input type="hidden" name="data[All_review][categories_options][]" id="co_rating">

                  <div class="form-group">
                    <label for="ex3">How well is other aspects of a child’s life taken care of? How much club/extra curricular activities happen in the school?</label>
                   <input type="hidden" name="data[Parent_review][question_3]" value="How well is other aspects of a child’s life taken care of? How much club/extra curricular activities happen in the school?">
                      <textarea required class="form-control" name="data[Parent_review][answer_3]"></textarea>
                  </div>
                    <div class="form-group">
                            <h5>Rate this category</h5>
                            </div>
                                  <span id="co_rate"></span>
                                  <input type="hidden" name="data[All_review][rate][]" value="" id="rate_for_co">
                        </div>
                        
                </div>
                  
                    <button type="submit" class="col-md-5 btn btn-primary">Submit</button>
                   
            </div>
            
        </section>
                     </form>
            </div>

        </section>
        
        
    </main>
    <!--Main Layout-->