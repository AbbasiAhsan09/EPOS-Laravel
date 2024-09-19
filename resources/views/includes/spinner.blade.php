<style>
    /* Full Page Spinner CSS */
    .spinner-overlay {
        display: none; /* Hidden by default */
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.8); /* White background with opacity */
        z-index: 9999; /* High z-index to cover everything */
        justify-content: center;
        align-items: center;
    }

    .spinner {
        border: 8px solid #f3f3f3; /* Light grey */
        border-top: 8px solid #3498db; /* Blue */
        border-radius: 50%;
        width: 60px;
        height: 60px;
        animation: spin 1s linear infinite; /* Rotate animation */
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>


    <!-- Spinner Overlay -->
    <div class="spinner-overlay" id="spinner-overlay">
        <div class="spinner"></div>
    </div>

    <script>
        // Show spinner
        function showSpinner() {
            document.getElementById("spinner-overlay").style.display = "flex";
        }

        // Hide spinner
        function hideSpinner() {
            document.getElementById("spinner-overlay").style.display = "none";
        }

    </script>