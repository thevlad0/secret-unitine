
//gets form input and sends it to "register.php" using fetch
const STUDENT_ROLE = 'student';

function createLabel(message, messageColor) {
     const responseLabel = document.getElementById("response-label");
     responseLabel.style.display = "block";
     responseLabel.textContent = message;
     responseLabel.style.color = messageColor;
}

function analyzeResponse(response) {
     if (response.status === 200) {
        createLabel("Успешна регистрация! Пренасочване към началната страница...", "green");
        //console.log("Registration successful!");
        setTimeout(() => {
            window.location.href = "login.html";
        }, 2000);
     } else {
        createLabel(response["message"], "red");
}
}

const rolesList = document.getElementById("roles");
const fnSection = document.getElementById("fn-input-section");

    rolesList.addEventListener("change", function () {
   
    if (this.value === STUDENT_ROLE) {
       fnSection.style.display = "block";
    }  else {
       fnSection.style.display = "none";
    }
}
);

const form = document.getElementById('register-form');
form.addEventListener('submit', (event) => {
    event.preventDefault();

    const enteredUsername = document.getElementById("username").value.trim();
    const name = document.getElementById("name").value.trim();
    const surname = document.getElementById("family-name").value.trim();
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();
    const role = document.getElementById("roles").value.trim();
    const fn = document.getElementById("fn-field").value.trim(); 


    const userData = {
        username: enteredUsername,
        name: name,
        surname: surname,
        email: email,
        password: password,
        role: role
    };

    if (role === STUDENT_ROLE) {
        userData.fn = fn;
    } else {
        userData.fn = ""; 
    }
      
    fetch('./register.php', {
        method: 'POST',   
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(userData),
    })
    .then(response => response.json().then(data => {
    data.status = response.status; 
    analyzeResponse(data);
}))
});

