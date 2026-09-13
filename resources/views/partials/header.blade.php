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

            <a href="{{ route('events.search') }}">
                Event Finder
            </a> 

            {{-- Future feature links can go here --}}

            <a href="{{ route('organizations.manage.index') }}">
                Manage Organisations
            </a>

            <a href="{{ route('groups.manage.index') }}">
                Manage Groups
            </a>
            <a href="{{ route('events.manage.index') }}">
                Manage Events
            </a>

        </nav>

    </div>

</header>