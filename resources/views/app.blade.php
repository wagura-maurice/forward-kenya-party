<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <title inertia>{{ config("app.name", "Laravel") }}</title>

        <!-- Favicon -->
        <link
            rel="apple-touch-icon"
            sizes="180x180"
            href="{{ asset('assets/favicon_io/apple-touch-icon.png') }}"
        />
        <link
            rel="icon"
            type="image/png"
            sizes="32x32"
            href="{{ asset('assets/favicon_io/favicon-32x32.png') }}"
        />
        <link
            rel="icon"
            type="image/png"
            sizes="16x16"
            href="{{ asset('assets/favicon_io/favicon-16x16.png') }}"
        />
        <link
            rel="manifest"
            href="{{ asset('assets/favicon_io/site.webmanifest') }}"
        />

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link
            href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap"
            rel="stylesheet"
        />
        <link
            rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        />

        <!-- intl-tel-input CSS -->
        <link
            rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css"
        />

        <!-- PWD Accessibility -->
        <script src="https://app.embed.im/accessibility.js" defer></script>

        <!-- PDF.js -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.15.349/pdf.min.js"></script>
        <script>
            // Initialize PDF.js worker after the library is loaded
            document.addEventListener("DOMContentLoaded", function () {
                if (window.pdfjsLib) {
                    window.pdfjsLib.GlobalWorkerOptions.workerSrc =
                        "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.15.349/pdf.worker.min.js";
                }
            });
        </script>

        <style>
            /* Preloader Styles */
            #preloader {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: #ffffff;
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 9999;
                transition: opacity 0.5s ease-out, visibility 0.5s ease-out;
            }

            #preloader.hidden {
                opacity: 0;
                visibility: hidden;
            }

            .preloader-content {
                text-align: center;
            }

            .logo-container {
                width: 200px;
                height: 200px;
                margin: 0 auto 20px;
                position: relative;
            }

            .logo-img {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                object-fit: contain;
                opacity: 0;
                transition: opacity 0.5s ease-in-out;
                will-change: opacity;
                backface-visibility: hidden;
                transform: translateZ(0);
            }

            .logo-img.active {
                opacity: 1;
                z-index: 1;
            }

            .loading-text {
                font-size: 1.2rem;
                color: #1a365d;
                margin-top: 20px;
                font-weight: 500;
            }
            .loading-text .loading-dots {
                display: inline-flex;
                align-items: center;
                height: 1em;
                margin-top: 20px;
            }
            .loading-text .loading-dots span {
                display: inline-block;
                width: 10px;
                height: 10px;
                border-radius: 50%;
                background-color: #10b981; /* Green color */
                margin: 0 3px;
                animation: bounce 1.4s infinite ease-in-out both;
            }
            .loading-text .loading-dots span:nth-child(1) {
                animation-delay: -0.32s;
            }
            .loading-text .loading-dots span:nth-child(2) {
                animation-delay: -0.16s;
            }
            @keyframes bounce {
                0%,
                80%,
                100% {
                    transform: scale(0);
                    opacity: 0.3;
                }
                40% {
                    transform: scale(1);
                    opacity: 1;
                }
            }
        </style>
    </head>

    <body class="font-sans antialiased">
        <!-- Preloader -->
        <div id="preloader">
            <div class="preloader-content">
                <div class="logo-container">
                    @for($i = 6; $i <= 10; $i++)
                    <img
                        src="{{ asset('assets/FKP COLLATERALS/FKP PNG/Primary Logo/Asset ' . ($i === 10 ? '10' : $i) . 'FKP.png') }}"
                        alt="Forward Kenya Party Logo {{ $i }}"
                        class="logo-img {{ $i === 6 ? 'active' : '' }}"
                        data-index="{{ $i - 6 }}"
                    />
                    @endfor
                </div>
                <div class="loading-text">
                    <div class="loading-dots">
                        <span></span><span></span><span></span>
                    </div>
                </div>
            </div>
        </div>

        @inertia

        <!-- Scripts -->
        @routes @vite(['resources/js/app.js',
        "resources/js/Pages/{$page['component']}.vue"]) @inertiaHead

        <script>
            // Preloader animation
            document.addEventListener("DOMContentLoaded", function () {
                const preloader = document.getElementById("preloader");
                const logoImages = document.querySelectorAll(".logo-img");
                const loadingDots = document.querySelector(".loading-dots");
                let currentImage = 0;
                let dots = "";
                let dotCount = 0;

                // No need for JavaScript dots animation - using CSS animation instead
                const animateDots = setInterval(() => {
                    // Animation is handled by CSS
                }, 500);

                // Preload all images first
                const loadPromises = Array.from(logoImages).map((img) => {
                    return new Promise((resolve) => {
                        if (img.complete) {
                            resolve();
                        } else {
                            img.onload = resolve;
                            img.onerror = resolve; // Continue even if an image fails to load
                        }
                    });
                });

                // Start animation after all images are loaded
                Promise.all(loadPromises).then(() => {
                    // Show first logo
                    logoImages[0].classList.add("active");
                    let nextImage = 1;

                    // Function to handle smooth transition to next image
                    const showNextImage = () => {
                        if (nextImage >= logoImages.length) {
                            // All images shown, start over
                            nextImage = 0;
                            logoImages[logoImages.length - 1].classList.remove(
                                "active"
                            );
                        } else if (nextImage > 0) {
                            // Fade out previous image
                            logoImages[nextImage - 1].classList.remove(
                                "active"
                            );
                        }

                        // Fade in next image
                        if (nextImage < logoImages.length) {
                            logoImages[nextImage].classList.add("active");
                            nextImage++;
                        }
                    };

                    // Start the rotation
                    const logoInterval = setInterval(showNextImage, 200); // Faster logo rotation (200ms per logo)

                    // Hide preloader after 1 second
                    setTimeout(() => {
                        clearInterval(animateDots);
                        clearInterval(logoInterval);

                        // Fade out the preloader
                        preloader.style.transition = "opacity 0.3s ease-out";
                        preloader.style.opacity = "0";

                        // Remove preloader from DOM after fade out
                        setTimeout(() => {
                            preloader.style.display = "none";
                        }, 300);
                    }, 1000); // 1 second total
                });
            });
        </script>
    </body>
</html>
