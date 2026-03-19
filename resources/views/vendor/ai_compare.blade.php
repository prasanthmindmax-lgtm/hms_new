<!doctype html>
<html lang="en">
  <!-- [Head] start -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @include('superadmin.superadminhead')
  <!-- [Head] end -->
  <!-- [Body] Start -->
<link rel="stylesheet" href="{{ asset('/assets/css/vendor.css') }}" id="main-style-link" />
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />

<style>
        .card { border: 1px solid #ddd; padding: 24px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        textarea { width: 100%; height: 120px; margin-bottom: 16px; padding: 12px; border: 1px solid #ddd; border-radius: 4px; font-family: inherit; }
        .file-input-container { margin-bottom: 16px; }
        .file-list { margin-top: 8px; }
        .file-item { display: flex; align-items: center; margin-bottom: 4px; }
        .file-item button { margin-left: 8px; background: #dc2626; color: white; border: none; border-radius: 4px; padding: 2px 6px; cursor: pointer; }
        button { padding: 10px 20px; border: none; background: #2563eb; color: #fff; border-radius: 6px; cursor: pointer; font-weight: bold; }
        button:hover { background: #1d4ed8; }
        button:disabled { background: #9ca3af; cursor: not-allowed; }
        #status { margin-left: 12px; color: #4b5563; }
        pre { background: #1e293b; color: #f8fafc; padding: 16px; border-radius: 6px; overflow: auto; white-space: pre-wrap; margin-top: 16px; }
        .progress-container { margin-top: 16px; display: none; }
        progress { width: 100%; height: 8px; border-radius: 4px; }
</style>
  <body style="overflow-x: hidden;">

    {{-- @php
        dd($serial);
    @endphp --}}
    <div class="page-loader">
      <div class="bar"></div>
    </div>
    <!-- [ Sidebar Menu ] start -->
    @include('superadmin.superadminnav')
    <!-- [ Sidebar Menu ] end -->
    <!-- [ Header Topbar ] start -->
    @include('superadmin.superadminheader')
    <!-- [ Header ] end -->
    <div class="pc-container">
        <div class="pc-content">


          <div class="card">
              <h2>Gemini AI Prompt with File Upload</h2>
              <textarea id="prompt" placeholder="Enter your prompt here..."></textarea>

              <div class="file-input-container">
                  <label for="files">Upload files (PDF, TXT, CSV, DOCX, etc.):</label>
                  <input type="file" id="files" multiple style='display:block'>
                  <div id="fileList" class="file-list"></div>
              </div>

              <div class="progress-container" id="progressContainer">
                  <progress id="progressBar" value="0" max="100"></progress>
              </div>

              <div>
                  <button id="run">Run Prompt</button>
                  <span id="status"></span>
              </div>
          </div>

          <div id="resultCard" class="card" style="display:none;">
              <h3>AI Response</h3>
              <pre id="result"></pre>
          </div>
        </div>
  </div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script src="{{ asset('/assets/js/plugins/dropzone-amd-module.min.js') }}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
{{-- <script src="{{ asset('/assets/js/purchase/vendor.js') }}"></script> --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

@if ($errors->any())
<script>
    $(document).ready(function () {
        $('#exampleModal').modal('show');
    });
</script>
@endif
<script>
$(document).ready(function() {
    let files = [];

    // Handle file selection
    $('#files').on('change', function() {
        files = Array.from(this.files);
        updateFileList();
    });

    // Update the file list display
    function updateFileList() {
        const fileList = $('#fileList');
        fileList.empty();

        if (files.length === 0) {
            fileList.append('<div>No files selected</div>');
            return;
        }

        files.forEach((file, index) => {
            const fileItem = $('<div class="file-item"></div>');
            fileItem.append(`<span>${file.name} (${formatFileSize(file.size)})</span>`);

            const removeBtn = $(`<button type="button" data-index="${index}">×</button>`);
            removeBtn.on('click', function() {
                files.splice($(this).data('index'), 1);
                updateFileList();
            });

            fileItem.append(removeBtn);
            fileList.append(fileItem);
        });
    }

    // Format file size
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Handle form submission
    $('#run').on('click', function(){
        const $runBtn = $(this);
        const $status = $('#status');
        const $progressContainer = $('#progressContainer');
        const $progressBar = $('#progressBar');

        $runBtn.prop('disabled', true);
        $status.text('Processing...');
        $progressContainer.show();
        $progressBar.val(0);

        let fd = new FormData();
        fd.append('prompt', $('#prompt').val());

        // Add files if any
        files.forEach((file, index) => {
            fd.append(`files[${index}]`, file);
        });

        fd.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        $.ajax({
            url: "{{ route('ai.compare.run') }}",
            method: "POST",
            data: fd,
            processData: false,
            contentType: false,
            xhr: function() {
                const xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener('progress', function(e) {
                    if (e.lengthComputable) {
                        const percent = Math.round((e.loaded / e.total) * 100);
                        $progressBar.val(percent);
                    }
                }, false);
                return xhr;
            },
            success: function(res) {
                $status.text('Done');
                $('#resultCard').show();
                $('#result').text(res.result);
            },
            error: function(xhr) {
                $status.text('Error');
                let errorMsg = 'Something went wrong';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMsg = xhr.responseJSON.error;
                } else if (xhr.statusText) {
                    errorMsg = xhr.statusText;
                }
                alert(errorMsg);
            },
            complete: function() {
                $runBtn.prop('disabled', false);
                $progressContainer.hide();
            }
        });
    });

    // Initialize file list display
    updateFileList();
});
</script>

    <!-- [ Main Content ] end -->
     @include('superadmin.superadminfooter')
  </body>
  <!-- [Body] end -->
</html>