const SuccessAlertContainer = document.getElementById('success-alert-container');
if (SuccessAlertContainer !== null) {
  SuccessAlertContainer.classList.remove('d-none');
  setTimeout(() => {
    SuccessAlertContainer.classList.add('d-none');
  }, 5000);
}

const ErrorAlertContainer = document.getElementById('error-alert-container');
if (ErrorAlertContainer !== null) {
  ErrorAlertContainer.classList.remove('d-none');
  setTimeout(() => {
    ErrorAlertContainer.classList.add('d-none');
  }, 5000);
}