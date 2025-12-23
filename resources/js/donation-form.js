document.addEventListener('DOMContentLoaded', function() {
    // Only run if donation form elements exist
    const donationOptions = document.querySelectorAll('.donation-option');
    const customAmountInput = document.getElementById('custom_amount');
    const donateButton = document.getElementById('donate_button');
    
    if (!donationOptions.length || !customAmountInput || !donateButton) {
        return; // Exit if elements don't exist
    }
    
    let selectedAmount = 0;

    // Minimum donation amount
    const MIN_DONATION = 10000;

    // Function to update button state
    function updateDonateButton() {
        if (selectedAmount >= MIN_DONATION) {
            donateButton.disabled = false;
            donateButton.classList.remove('bg-[#9DAE81]/50', 'cursor-not-allowed');
            donateButton.classList.add('bg-[#9DAE81]', 'hover:bg-[#8FA171]');
        } else {
            donateButton.disabled = true;
            donateButton.classList.remove('bg-[#9DAE81]', 'hover:bg-[#8FA171]');
            donateButton.classList.add('bg-[#9DAE81]/50', 'cursor-not-allowed');
        }
    }

    // Handle donation option clicks
    donationOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Remove active state from all options
            donationOptions.forEach(opt => {
                opt.classList.remove('bg-[#9DAE81]', 'text-white', 'border-[#9DAE81]');
                opt.classList.add('bg-white', 'text-[#23252F]', 'border-[#E7E0B8]');
            });

            // Add active state to clicked option
            this.classList.remove('bg-white', 'text-[#23252F]', 'border-[#E7E0B8]');
            this.classList.add('bg-[#9DAE81]', 'text-white', 'border-[#9DAE81]');

            // Set selected amount
            selectedAmount = parseInt(this.dataset.donationAmount);
            
            // Clear custom input
            customAmountInput.value = '';
            
            // Update button
            updateDonateButton();
        });
    });

    // Handle custom amount input
    customAmountInput.addEventListener('input', function() {
        const value = parseInt(this.value) || 0;

        // Remove active state from all options
        donationOptions.forEach(opt => {
            opt.classList.remove('bg-[#9DAE81]', 'text-white', 'border-[#9DAE81]');
            opt.classList.add('bg-white', 'text-[#23252F]', 'border-[#E7E0B8]');
        });

        if (value >= MIN_DONATION) {
            selectedAmount = value;
            this.classList.remove('border-red-300');
            this.classList.add('border-[#E7E0B8]');
        } else if (value > 0) {
            selectedAmount = 0;
            this.classList.remove('border-[#E7E0B8]');
            this.classList.add('border-red-300');
        } else {
            selectedAmount = 0;
            this.classList.remove('border-red-300');
            this.classList.add('border-[#E7E0B8]');
        }

        updateDonateButton();
    });

    // Handle donate button click
    donateButton.addEventListener('click', function() {
        if (selectedAmount >= MIN_DONATION) {
            // Here you can add the donation logic
            // For now, just show an alert
            alert(`Terima kasih! Anda akan mendonasikan Rp ${selectedAmount.toLocaleString('id-ID')}`);
            // In the future, you can redirect to payment page or submit a form
            // window.location.href = `/kampanye/{{ $campaign->id }}/donasi?amount=${selectedAmount}`;
        }
    });

    // Initialize button state
    updateDonateButton();
});

