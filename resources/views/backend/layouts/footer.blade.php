
      <!-- Footer -->
      <footer class="sticky-footer bg-white">
        <div class="container my-auto">
          <div class="copyright text-center my-auto">
            <span>Copyright &copy; <a href="https://github.com/Prajwal100" target="_blank">Prajwal R.</a> {{date('Y')}}</span>
          </div>
        </div>
      </footer>
      <!-- End of Footer -->

    </div>
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fa fa-angle-up"></i>
  </a>

  <!-- Logout Modal-->
  <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
          <button class="close" type="button" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
        <div class="modal-footer">
          <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
          <a class="btn btn-primary" href="login.html">Logout</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap core JavaScript-->
  <script src="{{asset('backend/vendor/jquery/jquery.min.js')}}"></script>
  <script src="{{asset('backend/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

  <!-- Core plugin JavaScript-->
  <script src="{{asset('backend/vendor/jquery-easing/jquery.easing.min.js')}}"></script>

  <!-- Custom scripts for all pages-->
  <script src="{{asset('backend/js/sb-admin-2.min.js')}}"></script>

  <!-- Page level plugins -->
  <script src="{{asset('backend/vendor/chart.js/Chart.min.js')}}"></script>

  <!-- Page level custom scripts -->
  {{-- <script src="{{asset('backend/js/demo/chart-area-demo.js')}}"></script> --}}
  {{-- <script src="{{asset('backend/js/demo/chart-pie-demo.js')}}"></script> --}}

  @stack('scripts')

  <script>
    setTimeout(function(){
      $('.alert').slideUp();
    },4000);

    // Hide Laravel pagination when DataTables is present in the same card to avoid double paginators
    $(function(){
      $('.card-body').each(function(){
        const hasDataTable = $(this).find('.dataTables_wrapper').length > 0;
        if(hasDataTable){
          $(this).find('nav[role="navigation"], .pagination').not('.dataTables_paginate').hide();
        }
      });
    });

    // Generic image preview handler: attach data-preview-target="#selector" to file inputs
    document.addEventListener('change', function (e) {
      const input = e.target;
      if (!input.matches('input[type="file"][data-preview-target]')) return;
      const target = document.querySelector(input.dataset.previewTarget);
      if (!target) return;
      const wrapper = target.closest('[data-preview-wrapper]');
      if (input.files && input.files[0]) {
        const url = URL.createObjectURL(input.files[0]);
        target.src = url;
        target.style.display = 'block';
        if (wrapper) wrapper.style.display = 'block';
      } else {
        target.removeAttribute('src');
        if (wrapper) wrapper.style.display = 'none';
      }
    });
  </script>
