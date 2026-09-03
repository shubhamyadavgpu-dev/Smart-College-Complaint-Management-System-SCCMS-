const tabs = document.querySelectorAll(".tab");
const forms = document.querySelectorAll(".auth-form");
const switchButtons = document.querySelectorAll(".switch-btn");

// Tab and Form switching logic
function showForm(formName) {
    tabs.forEach(tab => {
        tab.classList.toggle("active", tab.dataset.form === formName);
    });

    forms.forEach(form => {
        form.classList.toggle("active", form.id === `${formName}Form`);
    });
}

tabs.forEach(tab => {
    tab.addEventListener("click", () => {
        showForm(tab.dataset.form);
    });
});

switchButtons.forEach(button => {
    button.addEventListener("click", () => {
        const loginVisible = document.querySelector("#loginForm").classList.contains("active");
        showForm(loginVisible ? "signup" : "login");
    });
});

/* PASSWORD VISIBILITY TOGGLE */
const passwordToggles = document.querySelectorAll(".password-toggle");

passwordToggles.forEach(button => {
    button.addEventListener("click", () => {
        const input = document.getElementById(button.dataset.target);
        const icon = button.querySelector("i");

        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            input.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    });
});

/* LOGIN SUBMISSION */
const loginForm = document.querySelector("#loginForm");
if (loginForm) {
    loginForm.addEventListener("submit", (event) => {
        event.preventDefault();

        const email = document.querySelector("#loginEmail").value.trim();
        const password = document.querySelector("#loginPassword").value.trim();

        if (!email || !password) {
            alert("Please fill in all required fields.");
            return;
        }

        alert("SCCMS Login successful!");
    });
}

/* SIGN UP SUBMISSION */
const signupForm = document.querySelector("#signupForm");
if (signupForm) {
    signupForm.addEventListener("submit", (event) => {
        event.preventDefault();

        const fullName = document.querySelector("#signupName").value.trim();
        const email = document.querySelector("#signupEmail").value.trim();
        const password = document.querySelector("#signupPassword").value;
        const confirmPassword = document.querySelector("#confirmPassword").value;

        if (!fullName || !email || !password || !confirmPassword) {
            alert("Please fill in all fields.");
            return;
        }

        if (password !== confirmPassword) {
            alert("Passwords do not match!");
            return;
        }

        alert("SCCMS Account created successfully!");
        signupForm.reset();
        showForm("login"); // Switch to login after successful sign up
    });
}