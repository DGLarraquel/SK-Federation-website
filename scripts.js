/* ==========================
   Close Sign In Modal
========================== */
const closeSignin = document.getElementById('closeSignin');
const signinModal = document.getElementById('signinModal');
if (closeSignin && signinModal) {
  closeSignin.onclick = () => {
    signinModal.style.display = 'none';
  };
}

/* ==========================
   Sign Up Modal
========================== */
document.addEventListener('DOMContentLoaded', function() {
  const signupBtn = document.getElementById('signupBtn');
  const signupModal = document.getElementById('signupModal');
  const closeSignup = document.getElementById('closeSignup');

  if (signupBtn && signupModal && closeSignup) {
    // Open modal
    signupBtn.addEventListener('click', () => {
      signupModal.style.display = 'flex'; // Use flex for centering modal per CSS
    });

    // Close modal when clicking X
    closeSignup.addEventListener('click', () => {
      signupModal.style.display = 'none';
    });

    // Close modal when clicking outside modal content
    window.addEventListener('click', (event) => {
      if (event.target === signupModal) {
        signupModal.style.display = 'none';
      }
    });

    // Optional: Enhance form submit handler inside modal to allow validations
    const form = signupModal.querySelector('form');
    if (form) {
      form.addEventListener('submit', function(e) {
        // Add client-side validation here if needed
        // Example:
        // if (!form.checkValidity()) {
        //   e.preventDefault();
        //   alert("Please fill out the form correctly.");
        // }
        // Otherwise, allow default submit action to process form server-side
      });
    }
  }
});

/* ==========================
   Optional Helper Functions
========================== */
function openSignup() {
  const signupModal = document.getElementById('signupModal');
  if (signupModal) signupModal.style.display = 'flex';
}

function closeSignup() {
  const signupModal = document.getElementById('signupModal');
  if (signupModal) signupModal.style.display = 'none';
}
