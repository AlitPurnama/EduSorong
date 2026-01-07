document.addEventListener('DOMContentLoaded', function() {
    const feedContainer = document.getElementById('donation-feed-content');
    const feedWrapper = document.getElementById('donation-feed');
    
    if (!feedContainer || !feedWrapper) {
        return; // Exit if elements don't exist
    }

    let donations = [];
    let currentIndex = 0;
    let isScrolling = false;

    // Fetch recent donations
    function fetchDonations() {
        fetch('/api/donations/recent?limit=10')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.donations && data.donations.length > 0) {
                    donations = data.donations;
                    renderDonations();
                    
                    // Show feed if we have donations
                    if (feedWrapper) {
                        feedWrapper.classList.remove('hidden');
                    }
                } else {
                    // Hide feed if no donations
                    if (feedWrapper) {
                        feedWrapper.classList.add('hidden');
                    }
                }
            })
            .catch(error => {
                console.error('Error fetching donations:', error);
                // Hide feed on error
                if (feedWrapper) {
                    feedWrapper.classList.add('hidden');
                }
            });
    }

    // Render donations as running text
    function renderDonations() {
        if (donations.length === 0) return;

        // Clear existing content
        feedContainer.innerHTML = '';

        // Create duplicate items for seamless loop
        const itemsToShow = [...donations, ...donations]; // Duplicate for seamless scroll

        itemsToShow.forEach((donation, index) => {
            const item = document.createElement('div');
            item.className = 'flex items-center gap-2 text-[12px] whitespace-nowrap flex-shrink-0';
            item.innerHTML = `
                <span class="font-medium">${escapeHtml(donation.donor_name)}</span>
                <span>sudah berdonasi</span>
                <span class="font-semibold">Rp ${formatCurrency(donation.amount)}</span>
                <span>ke</span>
                <span class="font-medium">${escapeHtml(donation.campaign_title)}</span>
                <span class="text-[#E7E0B8]">•</span>
            `;
            feedContainer.appendChild(item);
        });

        // Start animation if not already scrolling
        if (!isScrolling) {
            startScrolling();
        }
    }

    // Start scrolling animation
    function startScrolling() {
        isScrolling = true;
        feedContainer.style.animation = 'none';
        
        // Force reflow
        void feedContainer.offsetWidth;
        
        // Calculate animation duration based on content width
        const contentWidth = feedContainer.scrollWidth;
        const viewportWidth = feedContainer.parentElement.offsetWidth;
        const distance = contentWidth / 2; // Since we duplicated the content
        const duration = Math.max(20, distance / 30); // 30px per second, minimum 20 seconds
        
        feedContainer.style.animation = `donation-scroll ${duration}s linear infinite`;
    }

    // Format currency
    function formatCurrency(amount) {
        return new Intl.NumberFormat('id-ID').format(amount);
    }

    // Escape HTML to prevent XSS
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Initial fetch
    fetchDonations();

    // Refresh every 60 seconds (cache is 30 seconds, so this ensures fresh data)
    setInterval(fetchDonations, 60000);
});

// Add CSS animation for scrolling (only once)
if (!document.getElementById('donation-feed-styles')) {
    const style = document.createElement('style');
    style.id = 'donation-feed-styles';
    style.textContent = `
        @keyframes donation-scroll {
            0% {
                transform: translateX(0);
            }
            100% {
                transform: translateX(-50%);
            }
        }
        
        #donation-feed-content {
            display: flex;
        }
        
        #donation-feed-content:hover {
            animation-play-state: paused;
        }
    `;
    document.head.appendChild(style);
}

