document.addEventListener("DOMContentLoaded", () => {
    const forms = document.querySelectorAll("form"); 
    forms.forEach(form => {
        const errorLabel = form.querySelector("#error-label");
        if (!errorLabel) return;

        errorLabel.style.display = "none";

        
        const params = new URLSearchParams(window.location.search);
        if (params.get("error") === "1") {
            errorLabel.style.display = "block";
        }

        
        form.querySelectorAll("input").forEach(input => {
            input.addEventListener("input", () => {
                errorLabel.style.display = "none";
            });
        });
    });
});

window.addEventListener("pageshow", (event) => {
    document.querySelectorAll("form").forEach(form => {
        const errorLabel = form.querySelector("#error-label");
        if (!errorLabel) return;

        if (event.persisted) {
            form.reset();
            errorLabel.style.display = "none";
        }
    });
});
