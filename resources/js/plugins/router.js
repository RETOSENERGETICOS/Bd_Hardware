import Router from 'vue-router'
import { validateToken, verifyAccess } from "../lib/auth";
import store from '../plugins/store'

const router = new Router({
    // Rutas
    routes: [
        {
            path: process.env.MIX_ROUTER_BASE, name: 'app.home', component: require('../components/app').default, children: [
                { path: 'usuarios', name: 'users.index', component: require('../components/users/index').default, meta: { roles: [1] } },
                { path: 'herramientas/agregar', name: 'tools.create', component: require('../components/tools/create').default, meta: { roles: [1,2] } },
                { path: 'herramientas/editar', name: 'tools.edit', component: require('../components/tools/edit').default, meta: { roles: [1,2] } },
                { path: 'herramientas/eliminar', name: 'tools.destroy', component: require('../components/tools/delete').default, meta: { roles: [1,2] } },
                { path: 'busqueda', name: 'search.index', component: require('../components/search/index').default, meta: { roles: [] } },
                { path: 'historial', name: 'tools.history', component: require('../components/tools/history').default, meta: { roles: [1] } }
            ], meta: {  }
        }
    ],
    mode: "history"
})

router.beforeEach(async (to, from, next) => {
    if (store.getters["user/get_user"].email === "") {
        next()
        return
    }
    if (to.meta.roles === undefined) {
        next()
    } else {
        if (verifyAccess(to.meta.roles)) {
            next()
        } else {
            window.location.href = '/app'
        }
    }
})

export default router
