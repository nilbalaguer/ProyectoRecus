const SATELLITE_COOKIE_KEY = "satelliteMapStyle";

export function SetSatelliteMapStyleCookie(value) {
    document.cookie = `${SATELLITE_COOKIE_KEY}=${value}; path=/; max-age=31536000`;
}

export function GetSatelliteMapStyleCookie() {
    const cookies = document.cookie.split('; ');
    const cookie = cookies.find(row => row.startsWith(SATELLITE_COOKIE_KEY + '='));
    return cookie ? cookie.split('=')[1] === "true" : false;
}
