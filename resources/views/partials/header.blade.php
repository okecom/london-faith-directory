<header class="site-header">

    <div class="container header-container">

        <a
            href="{{ route('organizations.search') }}"
            class="site-title"
        >
            London Faith Directory
        </a>

        <nav class="main-navigation">
            <a href="{{ route('organizations.search') }}">
                Organisation Finder
            </a>

            {{-- Future feature links can go here --}}

            <a href="{{ route('organizations.manage.index') }}">
                Manage Organisations
            </a>
        </nav>

    </div>

</header>