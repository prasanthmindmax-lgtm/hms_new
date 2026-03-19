<!doctype html>
<html lang="en">
  <!-- [Head] start -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @include('admin.superadminhead')
  <!-- [Head] end -->
  <!-- [Body] Start -->
 
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
  <style>
        #progress-container {
            width: 100%;
            background-color: #eee;
            border: 1px solid #aaa;
            height: 30px;
            margin-top: 20px;
        }
        #progress-bar {
            width: 0%;
            height: 100%;
            background-color: #4caf50;
            text-align: center;
            color: white;
            line-height: 30px;
            transition: width 0.3s ease;
        }
        #done-message {
            display: none;
            color: green;
        }
    </style>
  <body style="overflow-x: hidden;">
    
    <!-- [ Sidebar Menu ] start -->
    @include('admin.superadminnav')
<!-- [ Sidebar Menu ] end -->
<!-- [ Header Topbar ] start -->
    @include('admin.superadminheader')  

<!-- [ Header ] end -->

 
<h2>Laravel AJAX Progress Bar Demo</h2>

<div id="progress-container">
    <div id="progress-bar">0%</div>
</div>
             
 <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>		



<script>
function updateProgress() {
    $.ajax({
        url: '/api/progress',
        method: 'GET',
        success: function(data) {
            let percent = data.progress;
            $('#progress-bar').css('width', percent + '%').text(percent + '%');

            if (percent < 100) {
                setTimeout(updateProgress, 500); // Continue polling
            } else {
                console.log("Done!");
                // Hide the progress bar after 1 second and show completion message
                setTimeout(() => {
                    $('#progress-container').fadeOut();
                    $('#done-message').fadeIn();
                }, 1000);
            }
        },
        error: function(err) {
            console.error("AJAX error:", err);
        }
    });
}

updateProgress(); // Start polling on page load
</script>


    <!-- [ Main Content ] end -->
     @include('admin.superadminfooter')
  </body>
  <!-- [Body] end -->
</html>
