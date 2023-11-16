import * as user_common from "./user_common.js";

const INCORRECT_TOKEN_CODE = "incorrect_token";

export function get(url)
{
    return fetch(url, {
		method: "GET",
		headers: {
          'Content-Type': 'application/json'
        }
	})
   .then(response => response.json())
    .catch((error) => {
      console.log('Error: ', error);
    });
}

export function post(url, data)
{
    return fetch(url, {
		method: "POST",
		headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
	})
   .then(response => response.json())
   .then(res=> {
       if (res.has_errors && res.err_code === INCORRECT_TOKEN_CODE)
       {
           user_common.goToLogin();
           return;
       }
       return res;
   })
    .catch((error) => {
      console.log('Error: ', error);
    });
}

export function queryParam(param_name)
{
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    return urlParams.get(param_name);
}