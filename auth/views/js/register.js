const form = document.getElementById("register-form");

    form.addEventListener("submit", async (event) => {
        event.preventDefault();

        const user = {
            username: document.getElementById("username").value,
            email: document.getElementById("email").value,
            password: document.getElementById("password").value,
            confirmPassword: document.getElementById("confirm-password").value
        };

        try {
            const response = await fetch("./Register.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(user)
            });

            const result = await response.json();
            console.log(result);

            if (result.status === "success") {
                window.location.href = "../../messages/InboxPage.php";
            } else {
                document.querySelector(".error-message").innerHTML = result.message;
            }
        } catch (error) {
            console.log("Error:", error);
            document.querySelector(".error-message").textContent = "Възникна грешка при изпращането.";
        }
    });