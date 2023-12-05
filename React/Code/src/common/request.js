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

export async function post(url, data) {
    try {
        const headers = {
            'Content-Type': 'application/json'
        };

        const response = await fetch(url, {
            method: 'POST',
            headers: headers,
            body: JSON.stringify(data)
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const responseData = await response.json();
        if (responseData.has_errors) {
            if (responseData.err_code === INCORRECT_TOKEN_CODE) {
                user_common.goToLogin();
                return;
            }
            throw new Error(`Server error: ${responseData.err_code}`);
        }

        return responseData.data;
    } catch (error) {
        console.error('Error in post request:', error);
        throw error; // Re-throw the error for the caller to handle
    }
}


export function queryParam(param_name)
{
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    return urlParams.get(param_name);
}