document.addEventListener('DOMContentLoaded', function() {
    // Only run if donation form elements exist
    const donationOptions = document.querySelectorAll('.donation-option');
    const customAmountInput = document.getElementById('custom_amount');
    const donateButton = document.getElementById('donate_button');
    const paymentModal = document.getElementById('payment_modal');
    const closeModalBtn = document.getElementById('close_modal');
    const paymentMethodButtons = document.querySelectorAll('[data-payment-method]');
    // These elements may not exist for logged-in users, so we check later
    const guestFormModal = document.getElementById('guest_form_modal');
    const closeGuestModalBtn = document.getElementById('close_guest_modal');
    const guestDonationForm = document.getElementById('guest_donation_form');
    
    if (!donationOptions.length || !customAmountInput || !donateButton) {
        return; // Exit if elements don't exist
    }
    
    // Log initial state
    console.log('Donation form initialized:', {
        hasGuestModal: !!guestFormModal,
        hasPaymentModal: !!paymentModal
    });
    
    let selectedAmount = 0;
    let currentCampaignId = null;
    let guestData = {}; // Store guest form data
    
    // Check if user is authenticated - more robust check
    const authElement = document.querySelector('[data-is-authenticated]');
    const isGuest = !authElement || authElement.getAttribute('data-is-authenticated') !== 'true';

    // Minimum donation amount
    const MIN_DONATION = 10000;

    // Get campaign ID from data attribute or URL
    const campaignElement = document.querySelector('[data-campaign-id]');
    if (campaignElement) {
        currentCampaignId = campaignElement.dataset.campaignId;
    }

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

    // Handle donate button click - show guest form or payment modal
    donateButton.addEventListener('click', function(e) {
        e.preventDefault();
        
        if (selectedAmount >= MIN_DONATION && currentCampaignId) {
            // Check if guest form modal exists (only for guests)
            const guestModalElement = document.getElementById('guest_form_modal');
            const guestModalExists = guestModalElement !== null;
            
            // Debug logging
            console.log('Donate button clicked:', {
                isGuest,
                guestModalExists,
                selectedAmount,
                currentCampaignId
            });
            
            // If guest and guest form modal exists, show guest form first
            if (isGuest && guestModalExists && guestFormModal) {
                console.log('Showing guest form modal');
                const guestAmountDisplay = document.getElementById('guest_modal_amount_display');
                if (guestAmountDisplay) {
                    guestAmountDisplay.textContent = `Rp ${selectedAmount.toLocaleString('id-ID')}`;
                }
                guestFormModal.classList.remove('hidden');
                return;
            }
            
            // If logged in or guest form doesn't exist, show payment modal directly
            console.log('Showing payment modal directly');
            const modalAmountDisplay = document.getElementById('modal_amount_display');
            if (modalAmountDisplay) {
                modalAmountDisplay.textContent = `Rp ${selectedAmount.toLocaleString('id-ID')}`;
            }
            if (paymentModal) {
                paymentModal.classList.remove('hidden');
            }
        } else {
            console.warn('Cannot proceed: amount or campaign ID missing', {
                selectedAmount,
                currentCampaignId,
                minAmount: MIN_DONATION
            });
        }
    });

    // Handle guest form submission
    if (guestDonationForm) {
        guestDonationForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Collect guest form data
            guestData = {
                donor_name: document.getElementById('guest_donor_name').value,
                donor_phone: document.getElementById('guest_donor_phone').value,
                donor_email: document.getElementById('guest_donor_email').value,
                donor_message: document.getElementById('guest_donor_message').value,
                is_anonymous: document.getElementById('guest_is_anonymous').checked,
                is_guest: true
            };

            // Close guest form modal
            if (guestFormModal) {
                guestFormModal.classList.add('hidden');
            }

            // Show payment modal
            const modalAmountDisplay = document.getElementById('modal_amount_display');
            if (modalAmountDisplay) {
                modalAmountDisplay.textContent = `Rp ${selectedAmount.toLocaleString('id-ID')}`;
            }
            if (paymentModal) {
                paymentModal.classList.remove('hidden');
            }
        });
    }

    // Close guest modal handlers
    if (closeGuestModalBtn) {
        closeGuestModalBtn.addEventListener('click', function() {
            if (guestFormModal) {
                guestFormModal.classList.add('hidden');
            }
        });
    }

    // Close guest modal when clicking outside
    if (guestFormModal) {
        guestFormModal.addEventListener('click', function(e) {
            if (e.target === guestFormModal) {
                guestFormModal.classList.add('hidden');
            }
        });
    }

    // Close modal handlers
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', function() {
            if (paymentModal) {
                paymentModal.classList.add('hidden');
            }
        });
    }

    // Close modal when clicking outside
    if (paymentModal) {
        paymentModal.addEventListener('click', function(e) {
            if (e.target === paymentModal) {
                paymentModal.classList.add('hidden');
            }
        });
    }

    // Handle payment method selection
    paymentMethodButtons.forEach(button => {
        button.addEventListener('click', function() {
            const paymentMethod = this.dataset.paymentMethod;
            const loadingState = this.querySelector('.loading-state');
            const buttonText = this.querySelector('.button-text');
            
            // Disable all buttons
            paymentMethodButtons.forEach(btn => {
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
            });

            // Show loading state
            if (loadingState) loadingState.classList.remove('hidden');
            if (buttonText) buttonText.textContent = 'Memproses...';

            // Prepare request data
            const requestData = {
                amount: selectedAmount
            };

            // Add guest data if exists
            if (isGuest && Object.keys(guestData).length > 0) {
                Object.assign(requestData, guestData);
            } else if (!isGuest) {
                // For logged-in users, add anonymous option if checked
                const anonymousCheckbox = document.getElementById('is_anonymous');
                if (anonymousCheckbox && anonymousCheckbox.checked) {
                    requestData.is_anonymous = true;
                }
            }

            // Add channel-specific data
            if (paymentMethod === 'virtual_account') {
                // Get bank from button data attribute
                const bank = this.dataset.bank || 'bca';
                requestData.bank = bank;
                
                if (isGuest && guestData.donor_name) {
                    requestData.name = guestData.donor_name;
                } else {
                    const nameInput = document.getElementById('va_name');
                    if (nameInput && nameInput.value) {
                        requestData.name = nameInput.value;
                    } else {
                        requestData.name = 'Donatur';
                    }
                }
            }

            // Determine endpoint
            let endpoint = '';
            if (paymentMethod === 'qris') {
                endpoint = `/payment/kampanye/${currentCampaignId}/qris`;
            } else if (paymentMethod === 'virtual_account') {
                endpoint = `/payment/kampanye/${currentCampaignId}/virtual-account`;
            }

            // Make API request
            fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(requestData)
            })
            .then(async response => {
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    const text = await response.text();
                    console.error('Non-JSON response:', text);
                    throw new Error('Server returned non-JSON response');
                }
                return response.json();
            })
            .then(data => {
                console.log('Payment API response:', data);
                
                if (data.success) {
                    // Handle success based on payment method
                    if (paymentMethod === 'qris') {
                        // Show QR code
                        showQRCodeModal(data.payment, data.qr_string, data.qr_url);
                    } else if (paymentMethod === 'virtual_account') {
                        // Show VA number
                        showVAModal(data.payment, data.virtual_account_number);
                    }
                } else {
                    // Show error message from server
                    let errorMessage = 'Gagal membuat pembayaran';
                    if (data.message) {
                        errorMessage += ': ' + data.message;
                    } else if (data.error) {
                        if (typeof data.error === 'string') {
                            errorMessage += ': ' + data.error;
                        } else if (data.error.message) {
                            errorMessage += ': ' + data.error.message;
                        } else if (Array.isArray(data.error)) {
                            errorMessage += ': ' + data.error.join(', ');
                        }
                    }
                    alert(errorMessage);
                    resetPaymentButtons();
                }
            })
            .catch(error => {
                console.error('Payment API Error:', error);
                console.error('Request data:', requestData);
                console.error('Endpoint:', endpoint);
                
                let errorMessage = 'Terjadi kesalahan. Silakan coba lagi.';
                if (error.message) {
                    errorMessage += '\n\nDetail: ' + error.message;
                }
                alert(errorMessage);
                resetPaymentButtons();
            });
        });
    });

    // Reset payment buttons
    function resetPaymentButtons() {
        paymentMethodButtons.forEach(btn => {
            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed');
            const loadingState = btn.querySelector('.loading-state');
            const buttonText = btn.querySelector('.button-text');
            if (loadingState) loadingState.classList.add('hidden');
            if (buttonText) {
                const originalText = btn.dataset.originalText || buttonText.textContent;
                buttonText.textContent = originalText;
            }
        });
    }

    // Show QR Code modal
    function showQRCodeModal(payment, qrString, qrUrl) {
        const qrModal = document.getElementById('qr_modal');
        const qrImage = document.getElementById('qr_image');
        const qrAmount = document.getElementById('qr_amount');
        const qrPaymentId = document.getElementById('qr_payment_id');
        
        if (qrModal && qrImage && qrAmount) {
            qrImage.src = qrUrl || `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(qrString)}`;
            qrAmount.textContent = `Rp ${selectedAmount.toLocaleString('id-ID')}`;
            if (qrPaymentId) {
                qrPaymentId.textContent = payment.reference_id || payment.id;
            }
            
            // Close payment method modal
            if (paymentModal) {
                paymentModal.classList.add('hidden');
            }
            
            // Show QR modal
            qrModal.classList.remove('hidden');
            
            // Start polling for payment status
            if (payment.id) {
                pollPaymentStatus(payment.id);
            }
        }
    }

    // Show VA modal
    function showVAModal(payment, vaNumber) {
        const vaModal = document.getElementById('va_modal');
        const vaNumberDisplay = document.getElementById('va_number_display');
        const vaAmount = document.getElementById('va_amount');
        const vaPaymentId = document.getElementById('va_payment_id');
        const vaBankName = document.getElementById('va_bank_name');
        
        // Bank name mapping
        const bankNames = {
            'bca': 'BCA',
            'bri': 'BRI',
            'bni': 'BNI',
            'mandiri': 'Mandiri',
            'danamon': 'Danamon',
            'seabank': 'SeaBank'
        };
        
        if (vaModal && vaNumberDisplay && vaAmount) {
            vaNumberDisplay.textContent = vaNumber;
            vaAmount.textContent = `Rp ${selectedAmount.toLocaleString('id-ID')}`;
            if (vaPaymentId) {
                vaPaymentId.textContent = payment.reference_id || payment.id;
            }
            
            // Update bank name in modal
            const bankCode = payment.payment_channel || 'bca';
            const bankName = bankNames[bankCode] || 'Bank';
            if (vaBankName) {
                vaBankName.textContent = `Silakan transfer sesuai nominal di atas ke nomor Virtual Account ${bankName}`;
            }
            
            // Close payment method modal
            if (paymentModal) {
                paymentModal.classList.add('hidden');
            }
            
            // Show VA modal
            vaModal.classList.remove('hidden');
            
            // Start polling for payment status
            if (payment.id) {
                pollPaymentStatus(payment.id);
            }
        }
    }

    // Poll payment status
    function pollPaymentStatus(paymentId) {
        const maxAttempts = 60; // Poll for 5 minutes (5 seconds * 60)
        let attempts = 0;
        
        const pollInterval = setInterval(() => {
            attempts++;
            
            fetch(`/payment/${paymentId}/status`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.payment) {
                        const status = data.payment.status;
                        
                        if (status === 'paid' || status === 'settlement') {
                            clearInterval(pollInterval);
                            // Redirect to success page
                            window.location.href = `/payment/${paymentId}/success`;
                        } else if (status === 'failed' || status === 'expired' || status === 'cancel') {
                            clearInterval(pollInterval);
                            // Redirect to failed page
                            window.location.href = `/payment/${paymentId}/failed`;
                        } else if (attempts >= maxAttempts) {
                            clearInterval(pollInterval);
                            // Stop polling but keep modal open
                            console.log('Polling timeout');
                        }
                    }
                })
                .catch(error => {
                    console.error('Error polling payment status:', error);
                    if (attempts >= maxAttempts) {
                        clearInterval(pollInterval);
                    }
                });
        }, 5000); // Poll every 5 seconds
    }

    // Initialize button state
    updateDonateButton();
});
