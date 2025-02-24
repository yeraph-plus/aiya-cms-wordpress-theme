<?php if (aya_opt('site_carousel_load_bool', 'homepage')): ?>
    <!-- autoplay -->
    <div x-data="carousel()" x-init="init()" class="relative">
        <!-- Slide.js -->
        <div class="slide-container">
            <div class="slides">
                <template x-for="slide in slides" :key="slide.id">
                    <div class="slide">
                        <img :src="slide.image" :alt="slide.alt" class="w-full h-64 object-cover">
                    </div>
                </template>
            </div>
            <!-- Add Pagination -->
            <div class="pagination"></div>
            <!-- Add Navigation -->
            <button class="prev" @click="prevSlide()">&#10094;</button>
            <button class="next" @click="nextSlide()">&#10095;</button>
        </div>
    </div>
    <script>
        function carousel() {
            return {
                slides: [],
                currentIndex: 0,
                init() {
                    fetch('/path/to/slides.json')
                        .then(response => response.json())
                        .then(data => {
                            this.slides = data;
                            this.showSlide(this.currentIndex);
                        });
                },
                showSlide(index) {
                    const slides = document.querySelectorAll('.slide');
                    slides.forEach((slide, i) => {
                        slide.style.display = i === index ? 'block' : 'none';
                    });
                },
                nextSlide() {
                    this.currentIndex = (this.currentIndex + 1) % this.slides.length;
                    this.showSlide(this.currentIndex);
                },
                prevSlide() {
                    this.currentIndex = (this.currentIndex - 1 + this.slides.length) % this.slides.length;
                    this.showSlide(this.currentIndex);
                }
            }
        }
    </script>
<?php endif; ?>