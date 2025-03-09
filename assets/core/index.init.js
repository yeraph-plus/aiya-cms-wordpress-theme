(function () {
    //event load
    window.addEventListener('DOMContentLoaded', function () {
        replaceLozad();
        initSwup();
    });
    //screen loader
    window.addEventListener('load', function () {
        const screen_loader = document.getElementsByClassName('screen_loader');
        if (screen_loader?.length) {
            screen_loader[0].classList.add('animate__fadeOut');
            setTimeout(() => {
                document.body.removeChild(screen_loader[0]);
            }, 200);
        }
        //alpineJS store init
        Alpine.store('app').resetCustomizer();
        Alpine.store('app').setRTLLayout();
        Alpine.store('app').setLoopMode();
    });
    //LozadJS init
    const replaceLozad = () => {
        document.querySelectorAll('img:not([data-src])').forEach(img => {
            if (img.src && !img.hasAttribute('data-src')) {
                img.dataset.src = img.src;
                img.removeAttribute('src');
                img.setAttribute('loading', 'lazy');
            }
            img.classList.add('lozad');
        });
        //observer
        const observer = lozad('.lozad', {
            rootMargin: '0px 0px', // CSS Margin
            threshold: 0.5, // ratio of element convergence
            enableAutoReload: true, // it will reload the new image when validating attributes changes
            //load: function (el) {},
            loaded(el) {
                el.classList.add('loaded');
            }
        });
        observer.observe();
    };
    //swup4JS init
    const initSwup = () => {
        const swup = new Swup({
            containers: ['#swup-versatile', '#swup-scripts-reload'],
            //plugins: [new SwupProgressPlugin(), new SwupPreloadPlugin()]
        });
        //First screen event
        //swup.hooks.on('page:view', () => {}, { once: true });
        //Content replace event
        swup.hooks.on('content:replace', () => {
            //window.location.reload();
            Alpine.initTree(document.body);
            feather.replace({});
            //PrismJS
            Prism.highlightAll();
            replaceLozad();
        });
    };
    //perfect-scrollbarJS init
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
    //Feather-icons replace all icons
    feather.replace({});
    //alpineJS
    document.addEventListener('alpine:init', () => {
        //config
        const $themeConfig = $settingsConfig;
        console.log($themeConfig);
        //persist
        Alpine.persist = {
            default: 'localStorage'
        };
        //main - custom functions
        Alpine.data('main', (value) => ({}));
        //main - components
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
        Alpine.data('header', () => ({
            notificationList: $siteNotification,
            userInfo: $userLogindata,
            init() {
                const selector = document.querySelector('ul.horizontal-menu a[href="' + window.location.pathname + '"]');
                if (selector) {
                    selector.classList.add('active');
                    const ul = selector.closest('ul.sub-menu');
                    if (ul) {
                        let ele = ul.closest('li.menu').querySelectorAll('.nav-link');
                        if (ele) {
                            ele = ele[0];
                            setTimeout(() => {
                                ele.classList.add('active');
                            });
                        }
                    }
                }
            },
        }));
        Alpine.data('navbar', () => ({
            init() {
                const selector = document.querySelector('.sidebar ul a[href="' + window.location.pathname + '"]');
                if (selector) {
                    selector.classList.add('active');
                    const ul = selector.closest('ul.sub-menu');
                    if (ul) {
                        let ele = ul.closest('li.menu').querySelectorAll('.nav-link');
                        if (ele) {
                            ele = ele[0];
                            setTimeout(() => {
                                ele.click();
                            });
                        }
                    }
                }
            },
        }));
        Alpine.data('customizer', () => ({
            showCustomizer: false,
        }));
        Alpine.data('cookieconsent', () => ({
            init() {
            },
        }));
        Alpine.data('editorComponent', () => ({
            init() {
                //viewerJS init
                const container = document.querySelector('.editor-modality');
                const viewer = new Viewer(container, {
                    navbar: false,
                    toolbar: false,
                    url: 'src',
                });
            }
        }));
        Alpine.data('ajaxClickLikes', () => ({
            saved: Alpine.$persist([]).as('LikesList'),
            responseLikes: 0,
            init() {
                this.responseLikes = parseInt(
                    this.$el.dataset.initialLikes || 0
                );
            },
            sendClickLikes(postID) {
                if (this.saved.includes(postID)) return;

                fetch($ajaxObj.url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-WP-Nonce': $ajaxObj.nonce
                    },
                    body: new URLSearchParams({
                        action: 'click_likes',
                        post_id: postID,
                    }),
                }).then(response => response.json()).then(data => {
                    if (data.status === 'done') {
                        this.responseLikes++;
                    }
                });
                this.saved.push(postID);
            }
        }));
        Alpine.data('autoCountdown', () => ({
            timeLeft: 10,
            interval: null,
            startCountdown: function () {
                this.interval = setInterval(() => {
                    if (this.timeLeft > 0) {
                        this.timeLeft -= 1;
                    } else {
                        this.redirectHome();
                    }
                }, 1000);
            },
            redirectHome: function () {
                clearInterval(this.interval);
                window.location.href = '/';
            }
        }));
        Alpine.data('scrollToTop', () => ({
            showTopButton: false,
            init() {
                window.onscroll = () => {
                    this.scrollFunction();
                };
            },
            scrollFunction() {
                if (document.body.scrollTop > 50 || document.documentElement.scrollTop > 50) {
                    this.showTopButton = true;
                } else {
                    this.showTopButton = false;
                }
            },
            goToTop() {
                document.body.scrollTop = 0;
                document.documentElement.scrollTop = 0;
            },
        }));
        Alpine.data('swup4Control', () => ({
            init() {
                const links = document.querySelectorAll('a');
                links.forEach(link => {
                    link.addEventListener('mouseover', () => {
                        if (link.href && link.href !== '#' && link.href !== 'javascript:;') {
                            fetch(link.href);
                        }
                    });
                });
            }
        }));
        Alpine.data('copyTextBtn', (targetId) => ({
            isCopied: false,
            init() {
                // 通过目标 ID 获取内容
                const clipElement = document.querySelector('#' + targetId);
                if (!clipElement) { return; }

                //init ClipboardJS
                const clipboard = new ClipboardJS(this.$refs.copyBtn, {
                    text: () => clipElement.innerHTML,
                });
                clipboard.on('success', (e) => {
                    this.isCopied = true;
                    setTimeout(() => this.isCopied = false, 5000);
                    //clipboard.destroy();
                });
                clipboard.on('error', (e) => {
                    console.error('Copy failed:', e.action);
                    //clipboard.destroy();
                });
            },
        }));
        //app - global store
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
            // user can manage theme
            handleCustomizer: $themeConfig.themeCustomizer,

            resetCustomizer() {
                if (!this.handleCustomizer) {
                    this.navbarMenu = $themeConfig.navbarMenu;
                    this.bodyLayout = $themeConfig.bodyLayout;
                    this.navbarSticky = $themeConfig.navbarSticky;
                    this.colorSemidark = $themeConfig.colorSemidark;
                    this.animation = $themeConfig.animation;
                    this.loopGridCol = $themeConfig.loopGridCol;
                }
            },
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
    //Info
    console.log("\n\n %c AIYA-CMS %c https://www.yeraph.com \n", "color:#f1ab0e;background:#222222;padding:5px;", "background:#eee;padding:5px;");
})();
