
const bind = () => {
  // WebOTP support is required
  if (!('OTPCredential' in window)) {
    return
  }

  // Find form and input
  const input = document.querySelector('input[autocomplete="one-time-code"]')
  const form = input ? input.closest('form') : null

  if (!input || !form) {
    return
  }

  // Initiate a controller
  const abortController = new AbortController()

  // Cancel listening to OTP codes when the user manually
  // submits the form
  form.addEventListener('submit', () => {
    abortController.abort()
  }, {
    once: true,
    passive: true
  })

  // Listen for OTP codes
  navigator.credentials.get({
    // Request listening to SMS
    otp: { transport: ['sms'] },

    // Allow the abortController to abort listening
    // in case the user manually confirms
    signal: abortController.signal
  }).then((otp) => {
    // We've got a code, submit the form.
    input.value = otp.code
    form.submit()
  }).catch(error => {
    console.log('[WebOTP] Error while listening to OTP codes: %o', error)
  })
}

export default bind
