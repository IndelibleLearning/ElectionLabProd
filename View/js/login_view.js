import * as request from "./request.js";
import * as user_common from "./user_common.js";

let usernameInput = document.querySelector("#username");
let passwordInput = document.querySelector("#password");

let submitButton = document.querySelector("#submit");
let googleSubmit = document.querySelector("#google-submit");

window.onload = function() {
    google.accounts.id.initialize({
        client_id: '887227153285-7ol0k70995346nef307c9ckesrai7rft.apps.googleusercontent.com',
        callback: handleCredentialResponse
    });
};


function setup()
{
    submitButton.addEventListener("click", function(event)
    {
        submitButton.setAttribute("disabled", true);
        
        let username = usernameInput.value;
        let password = passwordInput.value;
        let url = user_common.LOGIN_URL;
        let postData = {
            "user_name": username,
            "password": password
        }
        
        request.post(url, postData)
        .then(res => {
           console.log(res); 
           if (res.has_errors)
           {
               alert(res.err_msg);
               submitButton.removeAttribute("disabled");
               return;
           }
           
           // successful login
           user_common.setLoginToken(res.data.login_token);
           user_common.setUserName(username);
           
           // redirect to 
           window.location.href = user_common.DASHBOARD_URL;
        });
    });

    googleSubmit.addEventListener("click", onGoogleSignIn);
}

function onGoogleSignIn() {

    google.accounts.id.prompt();
}
// Define the callback function
function handleCredentialResponse(response) {
    // Here you would handle the response, typically by sending the response.credential (JWT token) to your server
    console.log(response);

    let url = user_common.LOGIN_GOOGLE_URL;
    let postData = {
        "google_id": response.credential
    }

    request.post(url, postData)
        .then(res => {
            console.log(res);
            if (res.has_errors)
            {
                alert(res.err_msg);
                submitButton.removeAttribute("disabled");
                return;
            }

            // successful login
            user_common.setLoginToken(res.data.login_token);
            user_common.setUserName(res.data.username);

            // redirect to
            window.location.href = user_common.DASHBOARD_URL;
        });
}

setup();