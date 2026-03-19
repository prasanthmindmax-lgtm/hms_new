<section id="popular-treatment">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2 class="common-head text-center">Popular Treatment Options</h2>
            </div>

            <div id="treatment-profile" class="owl-carousel owl-theme">
                <?php
                $treatments = json_decode($homepage->section_5 ?? '[]');
                if (!empty($treatments)) {
                    foreach ($treatments as $key => $val) {
                        $treatment = \SiteHelpers::getTreatments($val);
                ?>
                    <div class="item">
                        <div class="media">
                            <img src="{{ url('uploads/treatment').'/'.$treatment->image }}" 
                                 alt="{{ $treatment->name }}" 
                                 class="img-fluid">

                            <div class="media-body">
                                <h3 style="color:black;">{{ $treatment->name }}</h3>
                                <p style="color:black;">{{ $treatment->short_description }}</p>
                                <a href="{{ url('treatment').'/'.$treatment->link }}">Read More...</a>
                            </div>
                        </div>
                    </div>
                <?php } } ?>
            </div>
        </div>
    </div>
</section>
