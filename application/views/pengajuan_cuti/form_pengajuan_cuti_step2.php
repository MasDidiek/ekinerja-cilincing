<form action="<?php echo base_url(); ?>cuti/create_session_step2" method="post" enctype="multipart/form-data" id="pengajuan_cuti2">
        <div class="modal-header">
            <h5 class="modal-title" id="scrollableModalTitle">Pengajuan Cuti</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12 mb-2">
                    <div class="form-group">
                        <div class="form-group">
                            <label for="pengganti">Pegawai Pengganti</label>

                            <select id="pengganti_select" name="id_pengganti" class="form-control" required>
                                <option value="">Pilih Pegawai Pengganti</option>
                                <?php foreach ($pegawai_pengganti as $row): ?>
                                    <option value="<?php echo $row->id_pegawai; ?>"><?php echo $row->nama; ?></option>
                                <?php endforeach; ?>
                            </select>

                        </div>

                    </div>
                </div>
                <div class="col-12">
                    <div class="form-group">
                        <label for="tgl_mulai">No Telepon yang dapat dihubungi</label>
                        <input type="text" id="tlp" name="tlp"  min="10" placeholder="08xxxx"  class="form-control" min="10">
                    </div>

                </div>
                <div class="col-12 mt-2">
                    <div class="form-group">
                        <label for="tgl_mulai">Alasan  Cuti</label>
                        <input type="text" id="alasan_cuti" required name="alasan_cuti" placeholder="tuliskan alasan cuti" class="form-control" min="10">
                    </div>

                </div>
                <div class="col-12 mt-3">
                    <div class="form-group">
                        <label for="tgl_mulai">Alamat Selama cuti</label>
                        <textarea name="alamat"  class="form-control mt-1"  min="10"></textarea>
                    </div>

                </div>


        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-success kirim" id="submit_btn">Selanjutnya</button>
    </div>
</form>

<script>
    $(document).ready(function () {
        $('#pengajuan_cuti2').submit(function() {

                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'),
                    data: $(this).serialize(),
                    success: function(response) {
                      $(".modal-content").html(response);
                    }
                })
                return false;
          });
    });

</script>
