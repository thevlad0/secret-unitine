const forgottenPasswdLabel = document.getElementById("forgotten-password");
let verificationCode;

forgottenPasswdLabel.addEventListener('click', () => {
    const emailLabel = document.getElementById("hidden-email-label");
    emailLabel.style.display = "block";
    const emailField = document.getElementById("hidden-email");
    emailField.style.display = "block";   
    const sendCodeButton = document.getElementById("send-code-btn");
    sendCodeButton.style.display = "block";
});


function sendVerificationCode() {
      const verificationCode = Math.floor(100000 + Math.random() * 900000);
      const emailField = document.getElementById("hidden-email");
      const email = emailField.value.trim();
      const codeData = {
          email: email,
          code: verificationCode
      };

      fetch('./sendEmail.php', {
        method: 'POST',   
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(codeData),
    })
    .then(response => response.json().then(data => {
    data.status = response.status; 
    analyzeResponse(data);
}));
}

const sendCodeButton = document.getElementById("send-code-btn");
sendCodeButton.addEventListener('click', () => {
    const codeLabel = document.getElementById("code-label");
    codeLabel.style.display = "block";
    const codeInput = document.getElementById("code");
    codeInput.style.display = "block";

    const usernameLabel = document.getElementById("username-label");
    usernameLabel.style.display = "block";
    const usernameField = document.getElementById("username-field");
    usernameField.style.display = "block";
    const newPasswdInput = document.getElementById("new-password");
    newPasswdInput.style.display = "block";
    const forgottenPasswdButton = document.getElementById("forgotten-password-btn");
    forgottenPasswdButton.style.display = "block";

    //send code to email
    sendVerificationCode();
});



function createLabel(message, messageColor) {
     const responseLabel = document.getElementById("response-label");
     responseLabel.style.display = "block";
     responseLabel.textContent = message;
     responseLabel.style.color = messageColor;
}

function analyzeResponse(response, message) {
     if (response.status === 200) {
        createLabel(message, "green");    
        } else {
        createLabel(response["message"], "red");
}
}

const forgottenPasswdButton = document.getElementById("forgotten-password-btn");
forgottenPasswdButton.addEventListener('click', () => {    
    const codeInput = document.getElementById("code");
    //code check
    /*const code = codeInput.value.trim();
    if (code !== verificationCode.toString()) {
        createLabel("Грешен код! Моля, опитайте отново.", "red");
        return;
    }*/

    const usernameField = document.getElementById("username-field");
    const newPasswdInput = document.getElementById("new-password");
    const username = usernameField.value.trim();
    const newPassword = newPasswdInput.value.trim();

    const userData = {
        username: username,
        password: newPassword 
    };

     fetch('./resetPassword.php', {
        method: 'POST',   
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(userData),
    })
    .then(response => response.json().then(data => {
    data.status = response.status; 
    analyzeResponse(data, "Успешна промяна на паролата! Влезте с новите си данни.");
}))
});

const form = document.getElementById('login-form');
form.addEventListener('submit', (event) => {
    event.preventDefault();
    const emailField = document.getElementById("email");
    const passwordField = document.getElementById("password");
    const email = emailField.value.trim();
    const password = passwordField.value.trim();

    const userData = {
        email: email,
        password: password
    };

    fetch('./login.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(userData),
    })
    .then(response => response.json().then(data => {
        data.status = response.status; 
        analyzeResponse(data, "Успешно влизане! Пренасочване към началната страница...");
        if (data.status === 200) {
            setTimeout(() => {
                window.location.href = "/../index.html";
            }, 2000);
        }
    }));
});

const registerButton = document.getElementById("register-btn");
registerButton.addEventListener('click', () => {
    window.location.href = "registration-form.html";
});