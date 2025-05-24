import mitt from 'mitt';
export const emitter = mitt();
import { GetSatelliteMapStyleCookie} from "@/composables/SettingsCookies";

export const MAP_STYLE_SATELLITE = "mapbox://styles/mapbox/standard-satellite";
export const MAP_STYLE_STANDARD = "mapbox://styles/mapbox/standard";

const API_TOKEN = 'pk.eyJ1IjoiZXRoYW5ncmFuZSIsImEiOiJjbTVyMWNsZDAwNmNsMnFxdTl5enQ2dXAxIn0.gCn0a-Ef8cuqw1pEozCo0Q'

let map = null;
let friendsDataList = [];
let markersDataList = [];

let currentSelectedMarker = null;
let centerMarker = null;

export function InitializeMap() {
    mapboxgl.accessToken = API_TOKEN;

    const center = { lng: 2.02690062977777, lat: 41.4113279581609 }; // Coordenadas de Barcelona (default)
    const style = GetSatelliteMapStyleCookie() ? MAP_STYLE_SATELLITE : MAP_STYLE_STANDARD;

    // Render Map on div
    map = new mapboxgl.Map({
        container: 'map',
        style: style,
        projection: 'globe',
        zoom: 5,
        center: [center.lng, center.lat],
        doubleClickZoom: false
    });
    map.addControl(
        new mapboxgl.GeolocateControl({
            positionOptions: {
                enableHighAccuracy: true
            },
            trackUserLocation: true,
            showUserHeading: true
        })
    );

    map.addControl(new mapboxgl.NavigationControl(), 'bottom-left');

    return map;
}

export function SetMapStyle(url)
{
    map.setStyle(url);
}

export function AddFriendMarkerToMap(lng, lat, name, profilePicture, map) {
    markersDataList.push({ lng: lng, lat: lat, name: name, profilePicture: profilePicture });

    ReloadMapMarkers(map);
}

export function SetFriends(_friendList) {
    friendsDataList.length = 0;
    friendsDataList.push(..._friendList);
}

export function SetMarkers(_markerList) {
    markersDataList.length = 0;

    _markerList.forEach(marker => {
        AddMarker(marker);
    });
}

export function AddMarker(markerData) {
    if (
        markerData.id !== undefined &&
        markerData.lat !== undefined &&
        markerData.lng !== undefined &&
        markerData.name &&
        markerData.description &&
        markerData.user_id !== undefined
    ) {
        markersDataList.push(markerData);
    }
    else {
        console.error("Marker Data invalid: ", markerData);
    }

}


export function ReloadMapMarkers() {
    const markersOnView = document.querySelectorAll('.marker');
    markersOnView.forEach(marker => marker.remove());

    for (let index = 0; index < friendsDataList.length; index++) {
        const friend = friendsDataList[index];
        AddFriendToMap(map, friend);
    }

    for (let index = 0; index < markersDataList.length; index++) {
        const marker = markersDataList[index];
        AddMarkerToMap(map, marker);
    }
}

export function GetMapCenterCoordinates() {
    // https://docs.mapbox.com/mapbox-gl-js/api/map/#map#getcenter
    return map.getCenter();
}

export function OnMapDblClick(callback) {
    if (!map) return;

    map.on('dblclick', (e) => {
        callback(e);
    });
}

export function GetMapImageUrlFromCoordsAndZoom(coords)
{
    return `https://api.mapbox.com/styles/v1/mapbox/streets-v11/static/${coords.lng},${coords.lat},13/256x256?access_token=${API_TOKEN}`;
}

function AddFriendToMap(map, friend) {
    const element = document.createElement('div');

    const sizeReduction = Math.floor(Math.random() * 32);
    const width = 64 - sizeReduction;
    const height = 64 - sizeReduction;

    let userPFP = "";
    let foto = (friend.media_url ? friend.media_url.split("localhost/")[1] : "");

    if (friend.media_url == null) {
        userPFP = "/images/default_pf.jpg";
    } else {
        userPFP = "/" + foto;
    }

    console.log("Imagen perfil: " + userPFP);

    element.className = 'map-marker';
    element.style.backgroundImage = `url("${userPFP}")`;
    element.style.width = `${width}px`;
    element.style.height = `${height}px`;
    element.style.backgroundSize = '100%';
    element.style.borderRadius = '100%';
    element.style.boxShadow = 'rgb(0 0 0 / 15%) 0px 16px 4px';

    element.dataset.originalWidth = width;
    element.dataset.originalHeight = height;

    element.addEventListener('click', () => {
        map.flyTo({center:[friend.last_lng, friend.last_lat]});
        SelectMarker(element);
    });
    
    new mapboxgl.Marker(element)
        .setLngLat([friend.last_lng, friend.last_lat])
        .addTo(map);
}

export function AddMarkerToMap(map, marker) {
    const element = document.createElement('div');

    element.className = 'map-marker';
    element.style.backgroundImage = `url(/images/emoji_pinRed.png)`;
    element.style.width = `${32}px`;
    element.style.height = `${32}px`;
    element.style.backgroundSize = 'contain';
    element.style.borderRadius = '100%';
    element.style.backgroundRepeat = 'no-repeat';
    element.style.backgroundPosition = 'center';

    element.dataset.originalWidth = 32;
    element.dataset.originalHeight = 32;

    element.addEventListener('click', () => {
        emitter.emit('marker-clicked', marker.id);
    });

    new mapboxgl.Marker(element)
        .setLngLat([marker.lng, marker.lat])
        .addTo(map);
}

export function ShowMarkerOnMapCenter() {
    if (centerMarker == null) {
        const element = document.createElement('div');
        const pin_element = document.createElement('div');
        const pin_shadow = document.createElement('div');
        element.appendChild(pin_shadow);
        element.appendChild(pin_element);

        // Main div (parent)
        element.className = 'map-marker';
        element.style.width = `${48}px`;
        element.style.height = `${48}px`;
        element.style.borderRadius = '100%';

        // Pin
        pin_element.style.position = 'absolute';
        pin_element.style.left = '0';
        pin_element.style.right = '0';
        pin_element.style.margin = '0 auto';
        pin_element.style.top = '50%';
        pin_element.style.backgroundImage = `url(/images/emoji_pinRed.png)`;
        pin_element.style.backgroundSize = 'contain';
        pin_element.style.backgroundRepeat = 'no-repeat';
        pin_element.style.backgroundPosition = 'center';
        pin_element.style.width = '100%';
        pin_element.style.height = '100%';
        pin_element.style.transform = 'translateY(-50%)';
        pin_element.style.zIndex = 999;

        // Shadow
        pin_shadow.style.position = 'absolute';
        pin_shadow.style.left = '0';
        pin_shadow.style.right = '0';
        pin_shadow.style.margin = '0 auto';
        pin_shadow.style.top = '50%';
        pin_shadow.style.backgroundImage = `url(/images/pin_shadow.png)`;
        pin_shadow.style.backgroundSize = 'contain';
        pin_shadow.style.backgroundRepeat = 'no-repeat';
        pin_shadow.style.backgroundPosition = 'center';
        pin_shadow.style.width = '100%';
        pin_shadow.style.height = '100%';

        element.dataset.originalWidth = 48;
        element.dataset.originalHeight = 48;
        const center = GetMapCenterCoordinates();

        centerMarker = new mapboxgl.Marker(element)
            .setLngLat([center.lng, center.lat])
            .addTo(map);
    }

    const center = GetMapCenterCoordinates();
    if (!center) {
        console.log("Center is null");
        return;
    }

    if (centerMarker) {
        centerMarker.getElement().style.display = "block";
        centerMarker.setLngLat([center.lng, center.lat]);
    }
    else
        console.error("Center marker is null");
}

export function HideCenterMarker() {
    if (centerMarker && centerMarker.getElement()) {
        centerMarker.getElement().style.display = "none";
    }
}


function SelectMarker(markerElement) {
    if (currentSelectedMarker !== null || currentSelectedMarker == markerElement) {
        currentSelectedMarker.style.width = `${currentSelectedMarker.dataset.originalWidth}px`;
        currentSelectedMarker.style.height = `${currentSelectedMarker.dataset.originalHeight}px`;
        currentSelectedMarker.style.zIndex = 0;
        currentSelectedMarker.style.boxShadow = `rgb(0 0 0 / 15%) 0px ${currentSelectedMarker.dataset.originalWidth / 2}px 4px`;

        if (currentSelectedMarker == markerElement) {
            currentSelectedMarker = null;
            return;
        }
    }

    currentSelectedMarker = markerElement;

    currentSelectedMarker.style.width = '128px';
    currentSelectedMarker.style.height = '128px';
    currentSelectedMarker.style.boxShadow = 'black 0 0 32px';
    currentSelectedMarker.style.zIndex = 1;
}

export function GetMap() {
    return map;
}

export function GetCurrentMapData()
{
    let map = GetMap();

    return{
    "center": map.getCenter(),
    "bearing": map.getBearing(),
    "pitch": map.getPitch(),
    "zoom": map.getZoom()
    };
}

export function flyMapPositionAndRotation(center, zoom, pitch, bearing) {
    const map = GetMap();

    if(!zoom)
        zoom = 5;

    map.flyTo({
        center: center,
        zoom: zoom,
        pitch: pitch,
        bearing: bearing,
        speed: 5,
        curve: 1.42,
        easing: t => t,
        essential: true
    });
}
