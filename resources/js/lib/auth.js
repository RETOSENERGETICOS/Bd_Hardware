import store from '../plugins/store';

export function setToken(token) {
    localStorage.setItem('token', token)
}

export function getToken() {
    const token = localStorage.getItem('token')
    if (token === null) {
        return null
    }
    return {
        headers: {
            Authorization: `Bearer ${token}`
        }
    }
}

export async function validateToken() {
    if (getToken() === null) {
        return null
    }
    return await axios.get('/api/user/verify', getToken())
}

export function verifyAccess(roles) {
    if (roles.length === 0) {
        return true
    }
    for(let role of roles) {
        if (role === store.getters["user/get_user"].role.id) {
            return true
        }
    }
    return false
}
