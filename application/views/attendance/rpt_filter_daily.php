<?php
$this->load->view('template_groundwork/head');
$this->load->view('template_groundwork/body_header');
$this->load->view('template_groundwork/body_menu');
?>
    <div class="container">
      <div class="padded">
        <div class="row">
          <div class="one whole bounceInRight animated">
            <h3 class="zero museo-slab">Laporan Presensi Per Tanggal</h3>
            <!--<p class="quicksand">Laporan Presensi Per Tanggal</p>-->
          </div>
        </div>
      </div>
      <?php if (!empty($message)) { ?>
      <div class="row bounceInLeft animated">
        <div class="one half padded align-center">
          <div class="row"><p class="message dismissible<?php echo (!empty($message_type))?' '.$message_type:' error'; ?>"><?php echo $message; ?><p></div>
        </div>
      </div>
      <?php } else { ?>
      <br/>    
      <?php } ?>
      <div class="row bounceInRight animated">
        <div class="four fifth padded">
          <form action="<?php echo $form_action_url; ?>" method="post">
            <fieldset>
              <div class="row">
                <div class="two fourth padded">
                  <label for="name">Tanggal</label>
                  <span class="select-wrap"><?php
                  $datepicker = array(
                    'name'        => 'tanggal',
                    'id'          => 'datepicker'
                  );
                  echo form_input($datepicker);
                  ?></span>
                </div>
              </div>
              <div class="row">
                <div class="two fourth padded">
                  <label for="month">Bagian/Prodi</label>
                </div>
              </div>
              <div class="row">
              <?php
              $str_check = '';
              $checknum = 0;
              foreach ($department as $key => $value) {
                  if (($checknum % 4) == 0) {
                      $str_check = $str_check.'<div class="row half-gap-bottom">';
                  }
                  
                  $data_check = array(
                    'name'        => 'department['.$key.']',
                    'value'       => 1,
                  );
                  
                  $str_check = $str_check.'<div class="one fourth">';
                  $str_check = $str_check.'<div class="two twelfth">';
                  $str_check = $str_check.form_checkbox($data_check);
                  $str_check = $str_check.'</div>';
                  $str_check = $str_check.'<div class="nine twelfth">';
                  $str_check = $str_check.$value;
                  $str_check = $str_check.'</div>';
                  $str_check = $str_check.'</div>';
                  
                  $checknum++;
                  if (($checknum % 4) == 0) {
                      $str_check = $str_check.'</div>';
                  }
              }
              
              if (($checknum % 4) > 0) {
                $str_check = $str_check.'</div>';
              }
              
              echo $str_check;
              ?>
              </div>
              <div class="row">
                <div class="two fifth padded">
                  <input type="submit" value="Lanjut"> 
                  <!--<a role="button" href="#" rel="next" class="gap-bottom gap-right">Lanjut</a>-->
                </div>
              </div>
            </fieldset>
          </form>
        </div>
      </div>
      <br/><br/>
    </div>
<?php
$this->load->view('template_groundwork/body_link');
$this->load->view('template_groundwork/body_footer');
?>