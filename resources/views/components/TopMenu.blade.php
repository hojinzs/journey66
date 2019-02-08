<!-- 
navbar ref:: http://bootstrap4.kr/docs/4.0/components/navbar/
Mix and match with other components and utilities as needed.
-->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="/">Journey66</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarText">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="/write">Write</a>
            </li>
            {{-- <li class="nav-item">
                <a class="nav-link" href="#">Features</a>
            </li> --}}
            {{-- <li class="nav-item">
                <a class="nav-link" href="#">Pricing</a>
            </li> --}}
        </ul>
        <span class="navbar-text">
            lang: {{App::getLocale()}}
        </span>
    </div>
</nav>
