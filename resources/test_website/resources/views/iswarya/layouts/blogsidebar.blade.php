<div class="nav-tabs">

    <ul class="nav">

      <?php
      // echo '<pre>';
      // print_r($all_blogs);
      // echo '</pre>';
      ?>

      @foreach($all_blogs as $key => $val)
        <li class="nav-item">

            <a class="nav-link @if($pagename == $val->alias) active @endif" href="{{url('blogs').'/'.$val->alias}}">


            {{$val->title}}</a>

        </li>
        @endforeach

        <!-- <li class="nav-item">

            <a class="nav-link @if($pagename == 'blog-2') active @endif" href="{{url('blogs/blog-2')}}">What is Infertility | It’s Causes & Treatment</a>

        </li>

        <li class="nav-item">

            <a class="nav-link @if($pagename == 'blog-3') active @endif" href="{{url('blogs/blog-3')}}">Planning Pregnancy</a>

        </li>

        <li class="nav-item">

            <a class="nav-link @if($pagename == 'blog-4') active @endif" href="{{url('blogs/blog-4')}}">Path of Conception via IUI, IVF&ICSI</a>

        </li>
          <li class="nav-item">

            <a class="nav-link @if($pagename == 'blog-5') active @endif" href="{{url('blogs/blog-5')}}">Egg freezing</a>

        </li>
          <li class="nav-item">

            <a class="nav-link @if($pagename == 'blog-6') active @endif" href="{{url('blogs/blog-6')}}">Is IVF better than IUI?</a>

        </li>
          <li class="nav-item">

            <a class="nav-link @if($pagename == 'blog-7') active @endif" href="{{url('blogs/blog-7')}}">Food that needs to be avoided during pregnancy</a>

        </li>
          <li class="nav-item">

            <a class="nav-link @if($pagename == 'blog-8') active @endif" href="{{url('blogs/blog-8')}}">The Right Age To Have A Baby</a>

        </li>
         <li class="nav-item">

            <a class="nav-link @if($pagename == 'blog-9') active @endif" href="{{url('blogs/blog-9')}}">Can normal delivery be done for ivf babies?</a>

        </li>
          <li class="nav-item">

            <a class="nav-link @if($pagename == 'blog-10') active @endif" href="{{url('blogs/blog-10')}}">How Long Do You Need To Rest After An IVF Treatment?</a>

        </li>
          <li class="nav-item">

            <a class="nav-link @if($pagename == 'blog-11') active @endif" href="{{url('blogs/blog-11')}}">Myths on public perception of IVF treatment</a>

        </li>
          <li class="nav-item">

            <a class="nav-link @if($pagename == 'blog-12') active @endif" href="{{url('blogs/blog-12')}}">What You Should Keep In Mind Before IVF Treatment?</a>

        </li>
          <li class="nav-item">

            <a class="nav-link @if($pagename == 'blog-13') active @endif" href="{{url('blogs/blog-13')}}">When can a couple do their next IVF treatment?</a>

        </li>
          <li class="nav-item">

            <a class="nav-link @if($pagename == 'blog-14') active @endif" href="{{url('blogs/blog-14')}}">When should a couple go straight to IVF?</a>

        </li> -->
        
     
      
    </ul>

</div>