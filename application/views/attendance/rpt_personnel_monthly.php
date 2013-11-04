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
      <div class="row">
        <div class="one half padded">
          <div class="bounceInLeft animated tablelike">
            <div class="equalize row">
              <div class="one sixth half-padded align-center">Tanggal</div>
              <div class="one sixth half-padded align-center">Jam Masuk</div>
              <div class="one sixth half-padded align-center">Jam Keluar</div>
              <div class="one sixth half-padded align-center">Durasi Keterlambatan</div>
              <div class="two sixth half-padded align-center">Keterangan</div>
            </div>
            <div class="equalize row">
              <div class="one sixth half-padded align-center">01/09/2013</div>
              <div class="one sixth half-padded align-center">07:40</div>
              <div class="one sixth half-padded align-center">16:30</div>
              <div class="one sixth half-padded align-center">00:00</div>
              <div class="two sixth half-padded align-center"><span class="select-wrap"><?php echo form_dropdown('keterangan', isset($keterangan_option)?$keterangan_option:array()); ?></span></div>
            </div>
            <div class="equalize row">
              <div class="one sixth half-padded align-center red-bg">01/09/2013</div>
              <div class="one sixth half-padded align-center red">07:40</div>
              <div class="one sixth half-padded align-center">16:30</div>
              <div class="one sixth half-padded align-center">00:00</div>
              <div class="two sixth half-padded align-center"><span class="select-wrap"><?php echo form_dropdown('personnel', isset($personnel_option)?$personnel_option:array()); ?></span></div>
            </div>
            <div class="equalize row">
              <div class="one sixth half-padded align-center">01/09/2013</div>
              <div class="one sixth half-padded align-center">07:40</div>
              <div class="one sixth half-padded align-center">16:30</div>
              <div class="one sixth half-padded align-center">00:00</div>
              <div class="two sixth half-padded align-center"><span class="select-wrap"><?php echo form_dropdown('personnel', isset($personnel_option)?$personnel_option:array()); ?></span></div>
            </div>
            <div class="equalize row red-bg">
              <div class="one sixth align-center">01/09/2013</div>
              <div class="one sixth align-center">07:40</div>
              <div class="one sixth align-center">16:30</div>
              <div class="one sixth align-center">00:00</div>
              <div class="two sixth align-center"><span class="select-wrap"><?php echo form_dropdown('personnel', isset($personnel_option)?$personnel_option:array()); ?></span></div>
            </div>
            <div class="equalize row">
              <div class="one sixth half-padded align-center">01/09/2013</div>
              <div class="one sixth half-padded align-center">07:40</div>
              <div class="one sixth half-padded align-center">16:30</div>
              <div class="one sixth half-padded align-center">00:00</div>
              <div class="two sixth half-padded align-center"><span class="select-wrap"><?php echo form_dropdown('personnel', isset($personnel_option)?$personnel_option:array()); ?></span></div>
            </div>
          </div>
          <div class="bounceInLeft animated">  
            
            
            <div class="row">
              <div class="one whole padded">
                <table data-max="14" class="responsive blue">
                  <thead>
                    <tr>
                      <th>Tanggal</th>
                      <th>Jam Masuk</th>
                      <th>Jam Keluar</th>
                      <th>Durasi Keterlambatan</th>
                      <th colspan="2">Keterangan</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Table cell</td>
                      <td>Table cell</td>
                      <td>Table cell</td>
                      <td>Table cell</td>
                      <td colspan="2"><span class="select-wrap"><?php echo form_dropdown('personnel', isset($personnel_option)?$personnel_option:array()); ?></span></td>
                    </tr>
                    <tr>
                      <td>Table cell</td>
                      <td>Table cell</td>
                      <td>Table cell</td>
                      <td>Table cell</td>
                      <td>Table cell</td>
                      <td>Table cell</td>
                    </tr>
                    <tr>
                      <td>Table cell</td>
                      <td>Table cell</td>
                      <td>Table cell</td>
                      <td>Table cell</td>
                      <td>Table cell</td>
                      <td>Table cell</td>
                    </tr>
                    <tr>
                      <td>Table cell</td>
                      <td>Table cell</td>
                      <td>Table cell</td>
                      <td>Table cell</td>
                      <td>Table cell</td>
                      <td>Table cell</td>
                    </tr>
                  </tbody>
                  <tfoot>
                    <tr>
                      <td>Footer cell</td>
                      <td>Footer cell</td>
                      <td>Footer cell</td>
                      <td>Footer cell</td>
                      <td>Table cell</td>
                      <td>Table cell</td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
<?php
$this->load->view('layout/body_link');
$this->load->view('layout/body_footer');
?>