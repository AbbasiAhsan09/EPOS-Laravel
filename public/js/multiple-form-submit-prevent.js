document.addEventListener('DOMContentLoaded', function () {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function (event) {
            // Check if form is already submitting
            if (form.dataset.submitting === 'true') {
                event.preventDefault(); // Prevent duplicate submission
                return;
            }

            // Set a data attribute to mark the form as submitting
            form.dataset.submitting = 'true';

            // Optionally, disable all submit buttons and change text
            const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
            submitButtons.forEach(button => {
                button.disabled = true;
                button.innerText = 'Submitting...'; // Optional text change
            });
        });
    });
});