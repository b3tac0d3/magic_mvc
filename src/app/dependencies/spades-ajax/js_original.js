document.addEventListener("DOMContentLoaded", function() {
    document.addEventListener("click", function(event) {
        if(event.target.form && event.target.tagName == "BUTTON"){
            if(event.target.form.classList.contains("spadeMe")) 
                spadeForm(event);
        } else if(event.target.classList.contains("spadeMe") || event.target.classList.contains("spadeScript")){
            spadeLink(event);
        }
    });
});

async function spadeLink(event){
    event.preventDefault();
    const element = event.target;
    let href = element.getAttribute("href") || '';
    if(element.classList.contains("spadeScript"))
        href = "src/routes/scripts_routes.php?Script=" + href;

    if (element.getAttribute("confirm_box") && !confirm(element.getAttribute("confirm_box"))) 
        return false;

    try {
        const response = await fetch(href);
        if (response.ok) {
            const return_data = await response.json();
            ajaxSuccess(element, document.querySelector(element.getAttribute("container")), return_data);
        } else {
            console.error("Error fetching the link!");
        }
    } catch (error) {
        console.error("Error in spadeLink function:", error);
    }
}

async function spadeForm(event){
    event.preventDefault();
    const form = event.target.form;
    let href = form.getAttribute("action") || location.href;
    const method = form.getAttribute("method") ? form.getAttribute("method").toUpperCase() : "GET";
    const container = document.querySelector(form.getAttribute("container"));

    form.querySelectorAll("button").forEach(btn => btn.disabled = true);


    if(form.classList.contains("spadeMe"))
        href = "src/routes/form_routes.php?Script=" + href;

    try {
        let fetchOptions = { method: method };

        if (method === "POST") {
            let bodyData;
            if (form.getAttribute("enctype") === "multipart/form-data") {
                bodyData = new FormData(form);
            } else {
                bodyData = new URLSearchParams(new FormData(form)).toString();
            }
            fetchOptions.body = bodyData;
        } else if (method === "GET") {
            const params = new URLSearchParams(new FormData(form)).toString();
            href += (href.includes("?") ? "&" : "?") + params;
        }

        const response = await fetch(href, fetchOptions);

        if (response.ok) {
            const return_data = await response.json();
            ajaxSuccess(form, container, return_data);
        } else {
            console.error("Error submitting form!");
        }
    } catch (error) {
        console.error("Error in spadeForm function:", error);
    } finally {
        form.querySelectorAll("button").forEach(btn => btn.disabled = false);
    }
} // spadeForm()


/* Successful callback functions */
function ajaxSuccess(element, container, return_data) {
    // refresh the page
    if (return_data.Refresh === 1) {
        location.reload();
        element.querySelectorAll("button").forEach(function(btn) {
            btn.disabled = false;
        });
        return false;
    }

    // open a colorbox (can be a subsequent colorbox called from a colorbox as well)
    if (return_data.RedirectColorbox === 1) {
        cbOpen(return_data.RedirectColorboxLink);
        return false;
    }

    // redirect if the script calls for it
    if (return_data.RedirectTrue === 1) {
        window.Location = return_data.Redirect;
        return false;
    }

    // if there"s an external (or internal) container and the status isn"t set to throw a message
    if (container && return_data.Status !== 0) {
        container.innerHTML = return_data.Html;
        element.querySelectorAll("button").forEach(function(btn) {
            btn.disabled = false;
        });
        return false;
    }

    // if there"s an alert to pop up
    if (return_data.AlertTrue === 1) {
        alert(return_data.alertTxt);
        return false;
    }

    // show bad inputs
    if (return_data.badInputs) {
        const y = JSON.parse(JSON.stringify(return_data.badInputs));
        for (const i = 0, len = y.length; i < len; i++) {
            document.querySelector(y[i]).classList.add("error");
        }
    }

    // if we make it here (and there"s no container), the message needs to be displayed
    const formMessage = element.querySelector(".form_message");
    formMessage.className += return_data.Classes;
    formMessage.innerHTML = return_data.Message;
    element.querySelectorAll("button").forEach(function(btn) {
        btn.disabled = false;
    });
    // scroll to top of form 
    document.body.scrollTop = element.offsetTop;
} // ajaxSuccess()