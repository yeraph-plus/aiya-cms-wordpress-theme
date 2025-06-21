import Swiper from 'swiper';
import { Navigation, Zoom, Keyboard, A11y } from 'swiper/modules';

// 导入 Swiper 样式
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import 'swiper/css/zoom';
import 'swiper/css/a11y';

export class ImageLightbox {
    private lightboxElement: HTMLElement | null = null;
    private swiperInstance: Swiper | null = null;
    private articleImages: HTMLImageElement[] = [];

    constructor() {
        this.findArticleImages();

        if (this.articleImages.length > 0) {
            this.createLightboxStructure();
            this.setupEventListeners();
        }
    }

    //查找文章中的所有图片
    private findArticleImages(): void {
        const images = document.querySelectorAll<HTMLImageElement>('.article-content img');
        this.articleImages = Array.from(images);

        // 为所有图片添加指针样式，提示可点击
        this.articleImages.forEach(img => {
            img.style.cursor = 'pointer';
        });
    }

    //定义灯箱DOM结构
    private createLightboxStructure(): void {
        // 创建主容器
        this.lightboxElement = document.createElement('div');
        this.lightboxElement.className = 'swiper lightbox-container';
        this.lightboxElement.setAttribute('role', 'dialog');
        this.lightboxElement.setAttribute('aria-label', '图片查看器');
        this.lightboxElement.style.cssText = `
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9);
            z-index: 9999;
            opacity: 0;
            transition: opacity 0.3s ease;
        `;

        // 创建幻灯片包装器
        const swiperWrapper = document.createElement('div');
        swiperWrapper.className = 'swiper-wrapper';

        // 为每张图片创建幻灯片
        this.articleImages.forEach(img => {
            const slide = document.createElement('div');
            slide.className = 'swiper-slide';

            const zoomContainer = document.createElement('div');
            zoomContainer.className = 'swiper-zoom-container';

            const image = document.createElement('img');
            image.src = img.src;
            image.alt = img.alt || '';
            image.setAttribute('loading', 'lazy');

            zoomContainer.appendChild(image);
            slide.appendChild(zoomContainer);
            swiperWrapper.appendChild(slide);
        });

        // 创建导航按钮
        const prevButton = document.createElement('div');
        prevButton.className = 'swiper-button-prev';
        prevButton.setAttribute('aria-label', 'Prev Slide');

        const nextButton = document.createElement('div');
        nextButton.className = 'swiper-button-next';
        nextButton.setAttribute('aria-label', 'Next Slide');

        // 创建关闭按钮
        const closeButton = document.createElement('button');
        closeButton.className = 'lightbox-close-btn';
        closeButton.innerHTML = '&times;';
        closeButton.setAttribute('aria-label', 'Close Lightbox');
        closeButton.style.cssText = `
            position: absolute;
            top: 20px;
            right: 20px;
            background: none;
            border: none;
            color: white;
            font-size: 30px;
            cursor: pointer;
            z-index: 10000;
        `;

        // 组装灯箱
        this.lightboxElement.appendChild(swiperWrapper);
        this.lightboxElement.appendChild(prevButton);
        this.lightboxElement.appendChild(nextButton);
        this.lightboxElement.appendChild(closeButton);

        // 添加到DOM
        document.body.appendChild(this.lightboxElement);
    }

    //设置事件监听器
    private setupEventListeners(): void {
        // 图片点击事件
        this.articleImages.forEach((img, index) => {
            img.addEventListener('click', (e) => {
                e.preventDefault();
                this.openLightbox(index);
            });
        });

        // 点击灯箱背景关闭
        if (this.lightboxElement) {
            this.lightboxElement.addEventListener('click', (e) => {
                if (e.target === this.lightboxElement) {
                    this.closeLightbox();
                }
            });

            // 关闭按钮点击事件
            const closeBtn = this.lightboxElement.querySelector('.lightbox-close-btn');
            if (closeBtn) {
                closeBtn.addEventListener('click', () => this.closeLightbox());
            }
        }

        // 键盘ESC关闭
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.lightboxElement &&
                this.lightboxElement.style.display === 'block') {
                this.closeLightbox();
            }
        });
    }

    private initSwiper(): void {
        if (!this.lightboxElement) return;

        this.swiperInstance = new Swiper(this.lightboxElement, {
            modules: [Navigation, Zoom, Keyboard, A11y],
            slidesPerView: 1,
            spaceBetween: 30,
            grabCursor: true,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            zoom: {
                maxRatio: 3,
                minRatio: 1,
                toggle: true,
            },
            keyboard: {
                enabled: true,
                onlyInViewport: false,
            },
            a11y: {
                enabled: true,
                prevSlideMessage: '上一张图片',
                nextSlideMessage: '下一张图片',
            },
            on: {
                zoomChange: (swiper, scale) => {
                    // 缩放时禁用/启用导航
                    const navigation = this.lightboxElement?.querySelectorAll('.swiper-button-prev, .swiper-button-next');
                    navigation?.forEach(nav => {
                        (nav as HTMLElement).style.opacity = scale > 1 ? '0' : '1';
                    });
                }
            }
        });
    }

    private openLightbox(index: number): void {
        if (!this.lightboxElement) return;

        // 初始化Swiper（如果尚未初始化）
        if (!this.swiperInstance) {
            this.initSwiper();
        }

        // 显示灯箱
        this.lightboxElement.style.display = 'block';

        // 启用淡入动画
        setTimeout(() => {
            if (this.lightboxElement) {
                this.lightboxElement.style.opacity = '1';
            }
        }, 10);

        // 跳到指定幻灯片
        this.swiperInstance?.slideTo(index, 0);

        // 阻止背景滚动
        document.body.style.overflow = 'hidden';
    }

    private closeLightbox(): void {
        if (!this.lightboxElement) return;

        // 淡出动画
        this.lightboxElement.style.opacity = '0';

        // 动画完成后隐藏
        setTimeout(() => {
            if (this.lightboxElement) {
                this.lightboxElement.style.display = 'none';
            }

            // 恢复背景滚动
            document.body.style.overflow = '';
        }, 300);
    }
}

export function initLightbox(): void {
    if (document.querySelector('.article-content')) {
        new ImageLightbox();
    }
}
