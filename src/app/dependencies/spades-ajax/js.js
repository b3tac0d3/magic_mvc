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
        href = "src/routes/script_routes.php?Script=" + href;

    if (element.getAttribute("confirm_box") && !confirm(element.getAttribute("confirm_box"))) 
        return false;

    // try {
        const response = await fetch(href);
        if (response.ok) {
            const responseData = await response.json();
            ajaxSuccess(element, document.querySelector(element.getAttribute("container")), responseData);
        } else {
            console.error("Error fetching the link!");
        }
    // } catch (error) {
    //     console.error("Error in spadeLink function:", error);
    // }
}

async function spadeForm(event){
    event.preventDefault();
    const form = event.target.form;
    let href = form.getAttribute("action") || location.href;
    const method = form.getAttribute("method") ? form.getAttribute("method").toUpperCase() : "GET";
    const container = document.querySelector(form.getAttribute("container"));

    form.querySelectorAll("button").forEach(btn => btn.disabled = true);


    if(form.classList.contains("spadeScript")) // spadeMe ajaxes the form || spadeScript uses the form_routes script
        href = "src/routes/form_routes.php?Script=" + href;

    try {
        let fetchOptions = {method: method};
        
        if (method === "POST"){
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
            const responseData = await response.json();
            console.log(responseData);
            ajaxSuccess(form, container, responseData);
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
function ajaxSuccess(element, container, responseData) {
    /*
        Response Options:
            Refresh == 1 | Refresh current page
            Redirect(not null) | Redirect current page to redirect link
            Html | Contains HTML for pre-determined element of current page
            Alert(not null) | Give an alert box on the current page

            FormMessage | A message and class combo sent back to the current form to be shown to user instead of proceeding
            BadInputs | A JSON list of form inputs that need to be addressed
            
            Status | Shows our value of status in response {Not an action, just check data to function off of}
    */

    // refresh the page
    if (responseData.Refresh === 1) {
        location.reload();
        element.querySelectorAll("button").forEach(function(btn) {
            btn.disabled = false;
        });
        return false;
    }

    // open a colorbox (can be a subsequent colorbox called from a colorbox as well)
    if (responseData.RedirectColorbox === 1) {
        cbOpen(responseData.RedirectColorboxLink);
        return false;
    }

    // redirect if the script calls for it
    if (responseData.RedirectTrue === 1) {
        window.location = responseData.Redirect;
        return false;
    }

    // if there"s an external (or internal) container and the status isn"t set to throw a message
    if (container && responseData.Status !== 0) {
        container.innerHTML = responseData.Html;
        element.querySelectorAll("button").forEach(function(btn) {
            btn.disabled = false;
        });
        return false;
    }

    // if there"s an alert to pop up
    if (responseData.AlertTrue === 1) {
        alert(responseData.alertTxt);
        return false;
    }

    // show bad inputs
    if (responseData.badInputs) {
        const y = JSON.parse(JSON.stringify(responseData.badInputs));
        for (const i = 0, len = y.length; i < len; i++) {
            document.querySelector(y[i]).classList.add("error");
        }
    }

    // if we make it here (and there"s no container), the message needs to be displayed
    const formMessage = element.querySelector(".form_message");
    formMessage.className += responseData.Classes;
    formMessage.innerHTML = responseData.Message;
    element.querySelectorAll("button").forEach(function(btn) {
        btn.disabled = false;
    });
    // scroll to top of form 
    document.body.scrollTop = element.offsetTop;
} // ajaxSuccess()