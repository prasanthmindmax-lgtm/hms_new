

<div class="nav-tabs d-none d-md-block">
 
    <ul class="nav">
        <?php
        // echo '<pre>';
        // print_r($all_treatments);
        // echo '</pre>';

        ?>
       <!--  @foreach($all_treatments as $key => $val)
       {{$val->link}}
        @endforeach -->
@foreach($all_treatments as $key => $val)
        <li class="nav-item">

            <a class="nav-link @if($pagename == $val->link) active @endif" href="{{url('treatment').'/'.$val->link}}">{{$val->name}}</a>

        </li>
 @endforeach
        <!-- <li class="nav-item">

            <a class="nav-link @if($pagename == 'ivf') active @endif" href="{{url('ivf')}}">IVF</a>

        </li>

        <li class="nav-item">

            <a class="nav-link @if($pagename == 'icsi') active @endif" href="{{url('icsi')}}">ICSI</a>

        </li>
        <li class="nav-item">

            <a class="nav-link @if($pagename == 'imsi') active @endif" href="{{url('imsi')}}">IMSI</a>

        </li>
        <li class="nav-item">

            <a class="nav-link @if($pagename == 'pgs') active @endif" href="{{url('pgs')}}">PGS</a>

        </li>
        <li class="nav-item">

            <a class="nav-link @if($pagename == 'pgd') active @endif" href="{{url('pgd')}}">PGD</a>

        </li>
        <li class="nav-item">

            <a class="nav-link @if($pagename == 'surrogacy') active @endif" href="{{url('surrogacy')}}">Surrogacy</a>

        </li>
        <li class="nav-item">

            <a class="nav-link @if($pagename == 'azoospermia') active @endif" href="{{url('azoospermia')}}">Azoospermia</a>

        </li>
        <li class="nav-item">

            <a class="nav-link @if($pagename == 'eggdonor') active @endif" href="{{url('eggdonor')}}">Egg Donor</a>

        </li>
        <li class="nav-item">

            <a class="nav-link @if($pagename == 'andrology') active @endif" href="{{url('andrology')}}">Andrology</a>

        </li> -->

    </ul>

</div>

<div class="container d-block d-md-none">
  <div class="panel-group" id="accordionMenu" role="tablist" aria-multiselectable="true">
    <div class="panel panel-default">
      <div class="panel-heading" role="tab" id="headingOne">
        <h4 class="panel-title">
        <a role="button" data-toggle="collapse" data-parent="#accordionMenu" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
          Treatment List
        </a>
      </h4>
      </div>
      <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
        <div class="panel-body nav-tabs">
          <ul class="nav"> 
    
        <?php
        // echo '<pre>';
        // print_r($all_treatments);
        // echo '</pre>';

        ?>
       <!--  @foreach($all_treatments as $key => $val)
       {{$val->link}}
        @endforeach -->
 @foreach($all_treatments as $key => $val)
        <li class="nav-item">

            <a class="nav-link @if($pagename == $val->link) active @endif" href="{{url('treatment').'/'.$val->link}}">{{$val->name}}</a>

        </li>
 @endforeach
        </div>
      </div>
    </div>

  </div>
    </div>










