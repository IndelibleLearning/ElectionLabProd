export const API_URL_BASE = "https://indeliblelearning.com/test/david/Controller/Api/";
export const VIEW_URL_BASE = "https://indeliblelearning.com/test/david/View/";

export function get_request(url)
{
    return fetch(url, {
		method: "GET",
		headers: {
          'Content-Type': 'application/json'
        }
	})
   .then(response => response.json())
    .catch((error) => {
      console.log('Error: ' + error);
    });
}

export function post_request(url, data)
{
    return fetch(url, {
		method: "POST",
		headers: {
          'Content-Type': 'application/json'
        },
		body: JSON.stringify(data)
	})
   .then(response => response.json())
    .catch((error) => {
      console.log('Error: ', error);
    });
}