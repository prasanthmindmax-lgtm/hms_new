@extends('layouts.iswarya')

@section('content')
<style>
    .req-star{
        color:#dc1124;
    }

    .training_btn{
        background:White !important;
        border-color:white !important;
        color:#96488b!important;
        width: 130px;
        border-radius: 7px;
        box-shadow: 2px 2px 2px 2px black;
        font-weight: bold;
        /* -webkit-text-stroke: 0.1em #5e2f58; */
    }
    @media only screen and (max-width: 767px) {
        .training_btn {
            display: block  !important;
        }
    }
    table, th, td {
     border:1px solid #934788;
     font-size: 11pt;
    font-family: Arial, sans-serif;
    color: rgb(0, 0, 0);
    padding: .75rem;
    }
    
</style>
<section id="banner" class="inner-banner" style="background-image: url(assets/iswarya/images/our-treatment/embryo_clinic.jpg);">

    <div class="container">

        <div class="row">

            <div class="col-md-7">

                    <h1 style="color:#ffffff; text-shadow: 2px 2px #180615;     font-size: xx-large;">M.Sc Clinical Embryology</h1>
                    <a href="https://www.draravindsivf.com/training/course-registration" class="btn pink-btn training_btn">Apply</a>

            </div>

        </div>

    </div>

</section>
<section id="our-treatment">

    <div class="container">

        <div class="row">

        	 <div class="col-md-12">

                <h2 class="common-head">M.Sc Clinical Embryology</h2>

            </div>

            <div class="col-md-7">
                <!-- <h2 class="s-head">Course Description</h2> -->

                <p style="color:black;line-height:30px;">In partnership with St.Peter’s Institute of Higher Education and Research, we offer an M.Sc. in Clinical Embryology designed to equip you with the skills and knowledge to become a successful embryologist. With expert instructors, top notch facilities, and a hands on approach, this program is your first step toward an exciting career in reproductive science.
                </p>
                

                <!-- WHAT YOU WILL LEARN STARTS -->
                <h2 class="s-head">What You will Learn</h2> 
                <ul class="list-unstyled mb-0">
                    <li style="color:black;"><b>*</b> You will work in fully equipped labs, learning by doing, not just reading.</li>
                    <li style="color:black;"><b>*</b> Participate in scientific meetings and discussions to expand your knowledge.</li>
                    <li style="color:black;"><b>*</b> Keep track of your learning and development through a personal logbook, meeting global standards.<li>
                    <li style="color:black;"><b>*</b> Complete an applied embryology project, consolidating skills and knowledge in a practical setting.</li>
                </ul>
                <!-- WHAT YOU WILL LEARN ENDS -->



                <!-- TAKE THE FIRST STEP  STARTS -->
                <h2 class="s-head">Take the First Step Toward Your Career in Clinical Embryology!</h2>
                <p style="color:black;line-height:30px;">
                At Dr. Aravind’s Fertility Academy, we are dedicated to helping you succeed. From expert training to financial support, we provide everything you need to excel. Apply today and start your journey toward becoming a skilled, in demand embryologist!</p>
                <!-- TAKE THE FIRST STEP ENDS -->


                <!-- Dr. ARAVIND's IVF CLININCAL EMBRYOLOGY CONTACT STARTS -->
                <h2 class="s-head">Dr. Aravind's IVF M.Sc Clinical Embryology Contact</h2> 
                <p style="color:black;">If you have any questions about the program or the application process, please feel free to contact us. Our team is ready to give you all the information you need. 
                </p>  
                <p style="color:black;">Mail: <span style="color:rgb(115, 24, 66);">academics@draravindsivf.in</span></p>
                <p style="color:black;">CC to: <span style="color:rgb(115, 24, 66);">info@draravindsivf.com</span></p> 

                <!-- Dr. ARAVIND's IVF CLININCAL EMBRYOLOGY CONTACT ENDS -->
            </div>
            <div class="col-md-5 training-bg"> 
                <table style="width:100%">
                        <tr>
                            <th>Program Info</th>
                            <th>What You Need to Know</th>
                            
                        </tr>
                        <tr>
                            <td>Eligibility</td>
                            <td>B.Sc. or M.Sc  in any Life Sciences, MBBS, BDS, or BHMS from a UGC recognized university.</td>     
                        </tr>
                        <tr>
                            <td>Seats Available</td>
                            <td>100 students per branch.</td>
                        </tr>
                        <tr>
                            <td>Mode of Course</td>
                            <td>Both online and offline learning options.</td>
                        </tr>
                        <tr>
                            <td>Course Duration</td>
                            <td>2 years.</td>
                        </tr>
                        <tr>
                            <td>Stipend</td>
                            <td>will be provided for candidates posted in ivf lab (individualised).</td>
                        </tr>
                        <tr>
                            <td>Financial Support</td>
                            <td>Helps in getting educational loans and scholarships.</td>
                        </tr>
                        <tr>
                            <td>Certification</td>
                            <td>Receive an University certificate upon completion.</td>
                        </tr>
                        <tr>
                            <td>Job Placement</td>
                            <td>Candidates will receive job placements with competitive salary offers.</td>
                        </tr>
                        <tr>
                            <td>Evaluation</td>
                            <td>Regular internal assessments to track progress.</td>
                        </tr>
                </table>
            </div>



         
       

        </div>

    </div>

</section>
@stop