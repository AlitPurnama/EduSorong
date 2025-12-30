document.addEventListener("DOMContentLoaded", function () {
    // Select all anchor links that contain a hash, which could be for section navigation
    const links = document.querySelectorAll('a[href*="#"]');

    links.forEach((link) => {
        link.addEventListener("click", function (e) {
            const url = new URL(link.href);
            const currentPath = window.location.pathname;
            const targetHash = url.hash;

            // We only want to apply smooth scroll if the user is currently on the landing page ('/').
            // We also check if the link's pathname is the landing page.
            if (currentPath === "/" && url.pathname === "/" && targetHash) {
                // Prevent the default browser jump
                e.preventDefault();

                const targetElement = document.querySelector(targetHash);

                if (targetElement) {
                    targetElement.scrollIntoView({
                        behavior: "smooth",
                    });
                }
            }
            // If the user is on another page (e.g., '/kampanye'), the default browser action
            // will occur: navigate to the homepage and then jump to the hash. This is standard behavior.
        });
    });
});
