
document.addEventListener("DOMContentLoaded", function () {

    // 1. Set the earliest allowed booking date to today
    const dateInput = document.getElementById("date");

    if (dateInput) {
        const today = new Date();

        const localDate = new Date(
            today.getTime() - today.getTimezoneOffset() * 60000
        ).toISOString().split("T")[0];

        dateInput.min = localDate;
    }

    // 2. Preselect a service from links on the Services page
    const serviceSelect = document.getElementById("service");

    if (serviceSelect) {
        const params = new URLSearchParams(window.location.search);
        const requestedService = params.get("service");

        if (requestedService) {
            const validServices = [
                "cleaning",
                "plumbing",
                "electrical"
            ];

            if (validServices.includes(requestedService)) {
                serviceSelect.value = requestedService;
            }
        }
    }

    // 3. Handle booking form submissions
    const bookingForm = document.getElementById("bookingForm");
    const bookingMessage = document.getElementById("bookingMessage");

    if (bookingForm && bookingMessage) {
        bookingForm.addEventListener("submit", function (event) {

            if (!bookingForm.checkValidity()) {
                event.preventDefault();
                bookingForm.reportValidity();
                return;
            }

            // Allow valid bookings to be submitted to PHP
            bookingMessage.textContent = "Submitting your booking...";
            bookingMessage.style.color = "#087653";
        });
    }

   
const contactForm = document.getElementById("contactForm");
const contactMessage = document.getElementById("contactMessage");

if (contactForm) {
    contactForm.addEventListener("submit", function (event) {
        if (!contactForm.checkValidity()) {
            event.preventDefault();
            contactForm.reportValidity();
            return;
        }

        // Do not prevent the normal form submission.
        if (contactMessage) {
            contactMessage.textContent = "Sending your message...";
        }
    });
}


});
