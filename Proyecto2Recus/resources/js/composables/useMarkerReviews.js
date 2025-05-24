import { authStore } from '../store/auth.js';

export async function GetAvgStarsByMarkerId(marker_id)
{
    try {
        const response = await axios.get('/api/markerReviews/getAvgStarsByMarkerId/' + marker_id);
        return response.data;
    } catch (error) {
        console.error("[@/composables/useMarkerReviews.vue] Error al cargar reviews del marcador:", error);
        return error;
    }
}

export async function SetReviewToMarker(marker_id, numberStars) 
{
    if(numberStars == 0 || numberStars == null)
    {
        DeleteReviewByMarkerId(marker_id);
        console.log("Deleting rating on marker: " + marker_id);
        return;
    }

    axios.post('/api/markerReviews', {
        review_stars: numberStars,
        review_content: 'Excelente lugar para visitar',
        marker_id: marker_id
    })
    .then(response => {
        console.log('Review añadida:', response.data);
    })
    .catch(error => {
        if (error.response) {
            console.error('Error en la respuesta:', error.response.data);
        } else {
            console.error('Error:', error.message);
        }
    });
    
}

export async function DeleteReviewByMarkerId(marker_id) 
{
    try {
        const response = await axios.delete('/api/markerReviews/' + marker_id);
        console.log('Review eliminada:', response.data);
        return response.data;
    } catch (error) {
        if (error.response) {
            console.error('Error en la respuesta:', error.response.data);
        } else {
            console.error('Error:', error.message);
        }
        return error;
    }
}

export async function GetMyReviewByMarkerId(marker_id)
{
    try {
        const response = await axios.get('/api/markerReviews/getReviewByMarkerId/' + marker_id);
        return response.data;
    } catch (error) {
        console.error("[@/composables/useMarkerReviews.vue] Error al cargar reviews del marcador:", error);
        return error;
    }
}