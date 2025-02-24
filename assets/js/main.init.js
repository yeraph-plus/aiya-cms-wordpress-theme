(function () {
    //event load
    window.addEventListener('DOMContentLoaded', function () {
        // screen loader
        const screen_loader = document.getElementsByClassName('screen_loader');
        if (screen_loader?.length) {
            screen_loader[0].classList.add('animate__fadeOut');
            setTimeout(() => {
                document.body.removeChild(screen_loader[0]);
            }, 200);
        }

        //AlpineJS init
        Alpine.store('app').setRTLLayout();
        Alpine.store('app').setLoopMode();
    });

    //perfect-scrollbarJS: init
    const initPerfectScrollbar = () => {
        const container = document.querySelectorAll('.perfect-scrollbar');
        for (let i = 0; i < container.length; i++) {
            new PerfectScrollbar(container[i], {
                wheelPropagation: true,
                suppressScrollX: true,
            });
        }
    };
    initPerfectScrollbar();

    document.addEventListener('alpine:init', () => {
        //AlpineJS config
        const $themeConfig = $settingsConfig;

        console.log($themeConfig);

        Alpine.persist = {
            default: 'localStorage'
        };

        //lozadJS
        Alpine.data('LozadControl', () => ({
            init() {
                const images = document.querySelectorAll('img');
                images.forEach(img => {
                    if (!img.classList.contains('lozad')) {
                        img.classList.add('lozad');
                        img.setAttribute('data-src', img.src);
                        img.removeAttribute('src');
                    }
                });

                const observer = lozad('.lozad', {
                    loaded: function (el) {
                        el.classList.add('loaded');
                    }
                });
                observer.observe();
            }
        }));

        //SwupJS
        Alpine.data('Swup4Control', () => ({
            init() {
                const links = document.querySelectorAll('a');

                links.forEach(link => {
                    link.addEventListener('mouseover', () => {
                        if (link.href && link.href !== '#' && link.href !== 'javascript:;') {
                            fetch(link.href);
                        }
                    });
                });

                const swup = new Swup({
                    containers: ['#main-content'],
                    plugins: [new SwupPreloadPlugin()]
                });

                swup.hooks.on('content:replace', () => {
                    Alpine.disposeTree(document.querySelector('#main-content'));
                    Alpine.initTree(document.querySelector('#main-content'));
                    Alpine.$persist.rehydrate();
                });

                swup.hooks.on('page:preload', () => {
                    Alpine.disposeTree(document.querySelector('#main-content'));
                });
            }
        }));

        // main - custom functions
        Alpine.data('main', (value) => ({}));

        // main - components
        Alpine.data('collapse', () => ({
            collapse: false,

            collapseSidebar() {
                this.collapse = !this.collapse;
            },
        }));
        Alpine.data('dropdown', (initialOpenState = false) => ({
            open: initialOpenState,

            toggle() {
                this.open = !this.open;
            },
        }));
        Alpine.data('modal', (initialOpenState = false) => ({
            open: initialOpenState,

            toggle() {
                this.open = !this.open;
            },
        }));
        Alpine.data('clickLikesCounter', () => ({

        }));

        // app - global store
        Alpine.store('app', {
            // color scheme
            colorScheme: Alpine.$persist($themeConfig.colorScheme),
            // dark mode
            isDarkMode: Alpine.$persist(true),
            // navigation menu
            navbarMenu: Alpine.$persist($themeConfig.navbarMenu),
            // layout
            bodyLayout: Alpine.$persist($themeConfig.bodyLayout),
            // navbar type
            navbarSticky: Alpine.$persist($themeConfig.navbarSticky),
            // semidark
            colorSemidark: Alpine.$persist($themeConfig.colorSemidark),
            // left sidebar
            menuBar: false,
            // animation
            animation: Alpine.$persist($themeConfig.animation),
            // loop grid mode column
            loopGridCol: Alpine.$persist($themeConfig.loopGridCol),
            loopGridClass: '',

            setRTLLayout() {
                document.querySelector('html').setAttribute('dir', $themeConfig.rtlClass);
            },
            setLoopMode() {
                this.toggleGridColumn($themeConfig.loopGridCol);
            },
            toggleDarkMode(val) {
                if (!val) {
                    val = this.colorScheme || $themeConfig.colorScheme;
                }

                this.colorScheme = val; // light, dark, system

                if (this.colorScheme == 'light') {
                    this.isDarkMode = false;
                } else if (this.colorScheme == 'dark') {
                    this.isDarkMode = true;
                } else if (this.colorScheme == 'system') {
                    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                        this.isDarkMode = true;
                    } else {
                        this.isDarkMode = false;
                    }
                }
            },
            toggleMenu(val) {
                if (!val) {
                    val = this.navbarMenu || $themeConfig.navbarMenu;
                }

                this.menuBar = false; // reset left sidebar state

                this.navbarMenu = val; // vertical, collapsible-vertical, horizontal
            },
            toggleLayout(val) {
                if (!val) {
                    val = this.bodyLayout || $themeConfig.bodyLayout;
                }

                this.bodyLayout = val; // full, boxed-layout
            },
            toggleAnimation(val) {
                if (!val) {
                    val = this.animation || $themeConfig.animation;
                }

                val = val?.trim();

                this.animation = val; // animate__fadeIn, animate__fadeInDown, animate__fadeInUp, animate__fadeInLeft, animate__fadeInRight, animate__slideInDown, animate__slideInLeft, animate__slideInRight, animate__zoomIn
            },
            toggleNavbar(val) {
                if (!val) {
                    val = this.navbarSticky || $themeConfig.navbarSticky;
                }

                this.navbarSticky = val; // navbar-sticky, navbar-floating, navbar-static
            },

            toggleSemidark(val) {
                if (!val) {
                    val = this.colorSemidark || $themeConfig.colorSemidark;
                }

                this.colorSemidark = val;
            },
            toggleMenuBar() {
                this.menuBar = !this.menuBar;
            },
            toggleGridColumn(val) {
                if (!val) {
                    val = this.loopGridCol || $themeConfig.loopGridCol;
                }

                this.loopGridCol = val;

                switch (val) {
                    case 'col-2':
                        this.loopGridClass = 'md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-2';
                        break;
                    case 'col-3':
                        this.loopGridClass = 'md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3';
                        break;
                    case 'col-4':
                        this.loopGridClass = 'md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-4';
                        break;
                    case 'col-5':
                        this.loopGridClass = 'md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5';
                        break;
                    case 'col-1':
                        this.loopGridClass = '';
                        break;
                }
            },
        });
    });

    // set current year in footer
    const yearEle = document.querySelector('#footer-year');

    if (yearEle) {
        yearEle.innerHTML = new Date().getFullYear();
    }

    //Feather icons：replace all icons
    feather.replace({});

    //Info
    console.log("\n\n %c AIYA-CMS %c https://www.yeraph.com \n", "color:#f1ab0e;background:#222222;padding:5px;", "background:#eee;padding:5px;");
})();
