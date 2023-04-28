let authButton = document.getElementById('authorize');
authButton.addEventListener('click', authorize);

var base_url = window.location.origin;

async function authorize(){
    // Send a request to the server to trigger the PHP function
    fetch(base_url + "/wp-content/plugins/gravity-basecamp-fusion/models/auth.php")
    .then(response => response.text())
    .then(data => console.log(data))
    .catch(error => console.error(error));
}


