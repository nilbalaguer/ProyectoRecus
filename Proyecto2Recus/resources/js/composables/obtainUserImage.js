

export function GetUsers() {
    axios.get('http://127.0.0.1:8000/api/friends/showFriends?search='+inputbusqueda.value)
    .then(response => 
    {
        users.value = response.data;
        loading.value = false;

        users.value.forEach(user => {
            try {
                user.image = "images/users/"+user.media[0].file_name;
            } catch (error) {
                user.image = "";
            }
        });

    })
    .catch(error => 
    {
        console.error("[SearchView.vue] Error:", error);
        loading.value = false;
    });
}