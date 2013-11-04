<?php
$this->load->view('layout/head');
$this->load->view('layout/body_header');
$this->load->view('layout/body_menu');
?>
    <div class="container">
      <div class="padded">
        <div class="row">
          <div class="one whole bounceInRight animated">
            <h3 class="zero museo-slab">Laporan Presensi Karyawan/Dosen Per Bulan</h3>
            <!--<p class="quicksand">Laporan Presensi Karyawan/Dosen Per Bulan</p>-->
          </div>
        </div>
      </div>
      <div class="row bounceInRight animated">
        <div class="one half padded">
          <form action="<?php echo site_url('attendance/personnel_monthly_rpt')?>" method="post">
            <fieldset>
              <div class="row">
                <div class="four fifth padded">
                  <label for="name">Nama Karyawan/Dosen</label>
                  <span class="select-wrap"><?php echo form_dropdown('personnel', isset($personnel_option)?$personnel_option:array()); ?></span>
                </div>
              </div>
              <div class="row">
                <div class="two fifth padded">
                  <label for="month">Bulan</label>
                  <span class="select-wrap"><?php echo form_dropdown('month', isset($month_option)?$month_option:array()); ?></span>
                </div>
                <div class="one fifth padded">
                  <label for="year">Tahun</label>
                  <span class="select-wrap"><?php echo form_dropdown('year', isset($year_option)?$year_option:array()); ?></span>
                </div>
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
$this->load->view('layout/body_link');
$this->load->view('layout/body_footer');
?>