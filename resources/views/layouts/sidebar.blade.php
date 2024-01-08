<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="index.html" class="app-brand-link">
            <span class="app-brand-logo demo">
                <svg width="32" height="22" viewBox="0 0 32 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M0.00172773 0V6.85398C0.00172773 6.85398 -0.133178 9.01207 1.98092 10.8388L13.6912 21.9964L19.7809 21.9181L18.8042 9.88248L16.4951 7.17289L9.23799 0H0.00172773Z"
                        fill="#7367F0" />
                    <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd"
                        d="M7.69824 16.4364L12.5199 3.23696L16.5541 7.25596L7.69824 16.4364Z" fill="#161616" />
                    <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd"
                        d="M8.07751 15.9175L13.9419 4.63989L16.5849 7.28475L8.07751 15.9175Z" fill="#161616" />
                    <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M7.77295 16.3566L23.6563 0H32V6.88383C32 6.88383 31.8262 9.17836 30.6591 10.4057L19.7824 22H13.6938L7.77295 16.3566Z"
                        fill="#7367F0" />
                </svg>
            </span>
            <span class="app-brand-text demo menu-text fw-bold">SunnahSync</span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-middle"></i>
            <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboards -->
        



        <!-- Apps & Pages -->
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">FEED</span>
        </li>
        <li class="menu-item {{ Request::url() == route('dashboard-users') ? 'active' : '' }}">
            <a href="{{ route('dashboard-users') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-user"></i>
                <div data-i18n="User">User</div>
            </a>
        </li>

        <li class="menu-item {{ Request::url() == route('dashboard-verify-users') ? 'active' : '' }}">
            <a href="{{ route('dashboard-verify-users') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-user"></i>
                <div data-i18n="User">Verify Users Request</div>
            </a>
        </li>



        

        <li
            class="menu-item {{ Request::url() == route('dashboard-report-report', 'active') ? 'active open' : '' }} || {{ Request::url() == route('dashboard-report-report', 'close') ? 'active open' : '' }} || {{ Request::url() == route('dashboard-report-category') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-file-report"></i>
                <div data-i18n="Reports">Report</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ Request::url() == route('dashboard-report-report', 'active') ? 'active' : '' }}">
                    <a href="{{ route('dashboard-report-report', 'active') }}" class="menu-link">
                        <div>Active Reports</div>
                    </a>
                </li>
                <li class="menu-item {{ Request::url() == route('dashboard-report-report', 'close') ? 'active' : '' }} ">
                    <a href="{{ route('dashboard-report-report', 'close') }}" class="menu-link">
                        <div>Close Reports</div>
                    </a>
                </li>
                <li class="menu-item {{ Request::url() == route('dashboard-report-category') ? 'active' : '' }}">
                    <a href="{{ route('dashboard-report-category') }}" class="menu-link">
                        <div>Category</div>
                    </a>
                </li>
            </ul>
        </li>


        <li
            class="menu-item {{ Request::url() == route('dashboard-donation-donation-request') ? 'active open' : '' }} || {{ Request::url() == route('dashboard-donation-all-donation') ? 'active open' : '' }} || {{ Request::url() == route('dashboard-donation-category') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-chart-donut-2"></i>
                <div data-i18n="Donation">Donation</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ Request::url() == route('dashboard-donation-all-donation') ? 'active' : '' }} ">
                    <a href="{{ route('dashboard-donation-all-donation') }}" class="menu-link">
                        <div>All Donation</div>
                    </a>
                </li>
                <li class="menu-item {{ Request::url() == route('dashboard-donation-donation-request') ? 'active' : '' }}">
                    <a href="{{ route('dashboard-donation-donation-request') }}" class="menu-link">
                        <div>Donation Request</div>
                    </a>
                </li>
                <li class="menu-item {{ Request::url() == route('dashboard-donation-category') ? 'active' : '' }}">
                    <a href="{{ route('dashboard-donation-category') }}" class="menu-link">
                        <div>Category</div>
                    </a>
                </li>
            </ul>
        </li>


        <li class="menu-item {{ Request::url() == route('dashboard-mosque-lsit','all') ? 'active open' : '' }} || {{ Request::url() == route('dashboard-mosque-lsit' , 'pending') ? 'active open' : '' }} ">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-home-2"></i>
                <div data-i18n="Mosque">Mosque</div>
            </a>
            
            <ul class="menu-sub">
                <li class="menu-item {{ Request::url() == route('dashboard-mosque-lsit' ,'all') ? 'active' : '' }} ">
                    <a href="{{ route('dashboard-mosque-lsit','all') }}" class="menu-link">
                        <div>All Mosque</div>
                    </a>
                </li>
                <li class="menu-item {{ Request::url() == route('dashboard-mosque-lsit' ,'pending') ? 'active' : '' }}">
                    <a href="{{ route('dashboard-mosque-lsit' , 'pending') }}" class="menu-link">
                        <div>Mosque Request</div>
                    </a>
                </li>                
            </ul>
        </li>

        <li class="menu-item {{ Request::url() == route('dashboard-para-add') ? 'active open' : '' }} || {{ Request::url() == route('dashboard-surah-add') ? 'active open' : '' }} ">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-home-2"></i>
                <div data-i18n="Quran">Quran</div>
            </a>
            
            <ul class="menu-sub">
                <li class="menu-item {{ Request::url() == route('dashboard-para-add') ? 'active' : '' }} ">
                    <a href="{{ route('dashboard-para-add') }}" class="menu-link">
                        <div>Add Para</div>
                    </a>
                </li>
                <li class="menu-item {{ Request::url() == route('dashboard-surah-add') ? 'active' : '' }}">
                    <a href="{{ route('dashboard-surah-add') }}" class="menu-link">
                        <div>Add Surah</div>
                    </a>
                </li>                
            </ul>
        </li>


        
        <li class="menu-item {{ Request::url() == route('dashboard-consultation-category') ? 'active open' : '' }} ">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-home-2"></i>
                <div data-i18n="Mosque">Consultation</div>
            </a>
            
            <ul class="menu-sub">
                <li class="menu-item {{ Request::url() == route('dashboard-consultation-category') ? 'active' : '' }} ">
                    <a href="{{ route('dashboard-consultation-category') }}" class="menu-link">
                        <div>List Category</div>
                    </a>
                </li>
                               
            </ul>
        </li>


        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Pages</span>
        </li>

        <li class="menu-item {{ Request::url() == route('dashboard-stream-list') ? 'active open' : '' }} || {{ Request::url() == route('dashboard-stream-') ? 'active open' : '' }} ">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-layout-grid-add"></i>
                <div data-i18n="Stream">Stream</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ Request::url() == route('dashboard-stream-list') ? 'active' : '' }}">
                    <a href="{{ route('dashboard-stream-list') }}" class="menu-link">
                        <div data-i18n="Live Stream">Stream List</div>
                    </a>
                </li>
                {{-- <li class="menu-item {{ Request::url() == route('dashboard-stream-') ? 'active' : '' }}">
                    <a href="{{ route('dashboard-stream-') }}" class="menu-link">
                        <div data-i18n="Live Stream">Add Stream</div>
                    </a>
                </li> --}}
                     
            </ul>
        </li>

        <li class="menu-item {{ Request::url() == route('dashboard-dua-add') ? 'active open' : '' }} ||  {{ Request::url() == route('dashboard-dua-category') ? 'active open' : '' }} ||  {{ Request::url() == route('dashboard-dua-') ? 'active open' : '' }}  ">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-pray"></i>
                <div data-i18n="Dua's">Dua's</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ Request::url() == route('dashboard-dua-') ? 'active' : '' }}">
                    <a href="{{ route('dashboard-dua-') }}" class="menu-link">
                        <div>List Dua's</div>
                    </a>
                </li>
                {{-- <li class="menu-item {{ Request::url() == route('dashboard-dua-add') ? 'active' : '' }}">
                    <a href="{{ route('dashboard-dua-add') }}" class="menu-link">
                        <div>Add Dua</div>
                    </a>
                </li> --}}
                <li class="menu-item {{ Request::url() == route('dashboard-dua-category') ? 'active' : '' }}">
                    <a href="{{ route('dashboard-dua-category') }}" class="menu-link">
                        <div>Category</div>
                    </a>
                </li>
                     
            </ul>
        </li>

        <li class="menu-item  {{ Request::url() == route('dashboard-hadith-category') ? 'active open' : '' }}   ||  {{ Request::url() == route('dashboard-hadith-add') ? 'active open' : '' }} ||  {{ Request::url() == route('dashboard-hadith-list') ? 'active open' : '' }}"> 
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-vocabulary"></i>
                <div data-i18n="Dua's">Hadith's</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ Request::url() == route('dashboard-hadith-list') ? 'active' : '' }}">
                    <a href="{{ route('dashboard-hadith-list') }}" class="menu-link">
                        <div>Hadiths List</div>
                    </a>
                </li>  

                {{-- <li class="menu-item {{ Request::url() == route('dashboard-hadith-add') ? 'active' : '' }}">
                    <a href="{{ route('dashboard-hadith-add') }}" class="menu-link">
                        <div>Add Hadith</div>
                    </a>
                </li> --}}
                <li class="menu-item {{ Request::url() == route('dashboard-hadith-category') ? 'active' : '' }}">
                    <a href="{{ route('dashboard-hadith-category') }}" class="menu-link">
                        <div>Books</div>
                    </a>
                </li>  

                {{-- <li class="menu-item {{ Request::url() == route('dashboard-hadith-subcategory') ? 'active' : '' }}">
                    <a href="{{ route('dashboard-hadith-subcategory') }}" class="menu-link">
                        <div>Books Category</div>
                    </a>
                </li>   --}}


                
                
            </ul>
        </li>
        <li class="menu-item {{ Request::url() == route('dashboard-link') ? 'active' : '' }}">
            <a href="{{ route('dashboard-link') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-external-link"></i>
                <div data-i18n="Link's">Link's</div>
            </a>
        </li>


       
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">OTHERS</span>
        </li>
        <li class="menu-item {{ Request::url() == route('dashboard-posts') ? 'active' : '' }}">
            <a href="{{ route('dashboard-posts') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-files"></i>
                <div data-i18n="Posts">Posts</div>
            </a>
        </li>

        <li class="menu-item {{ Request::url() == route('dashboard-faqs') ? 'active' : '' }}">
            <a href="{{ route('dashboard-faqs') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-report"></i>
                <div data-i18n="Reported Posts">Reported Posts</div>
            </a>
        </li>
        <li class="menu-item {{ Request::url() == route('dashboard-video-create') ? 'active' : '' }}">
            <a href="{{ route('dashboard-video-create') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-help"></i>
                <div data-i18n="Video">Video</div>
            </a>
        </li>
     
        <li class="menu-item {{ Request::url() == route('dashboard-faqs') ? 'active' : '' }}">
            <a href="{{ route('dashboard-faqs') }}" class="menu-link">
                <i class="menu-icon tf-icons ti ti-help"></i>
                <div data-i18n="FAQ'S">FAQ'S</div>
            </a>
        </li>

       
    </ul>
</aside>
