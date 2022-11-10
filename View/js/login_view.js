import * as request from "./request.js";
import * as user_common from "./user_common.js";

let usernameInput = document.querySelector("#username");
let passwordInput = document.querySelector("#password");

let submitButton = document.querySelector("#submit");

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
}

setup();