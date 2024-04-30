import Axios from 'axios';

const Api = Axios.create({
    baseURL: window.WPAdminifyUserRoleEditor.rest_urls.baseUrl,
    headers: {
        Accept: 'application/json',
        'X-WP-Nonce': window.WPAdminifyUserRoleEditor.nonce
    }
});

export default Api;