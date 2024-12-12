function checkPasswords() {
  const password1 = document.getElementById("password1").value;
  const password2 = document.getElementById("password2").value;
  const message = document.getElementById("message");

  // Requisiti di sicurezza per la password
  const strongPassword =
    /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

  if (!strongPassword.test(password1)) {
    message.textContent =
      "Passwords must contain al least: 8 characters, 1 upper-case letter, 1 lower-case letter, 1 number, 1 special character.";
    return false;
  }

  if (password1 !== password2) {
    message.textContent = "Passwords not matching!";
    return false;
  }

  message.textContent = "";
  return true;
}

function validateForm() {
  const isPasswordValid = checkPasswords();
  if (!isPasswordValid) {
    alert("The module contains some errors.");
    return false;
  }
  return true;
}
