import { ref } from "vue";
import { authStore } from "../store/auth";

const AuthenticatedLayout = () => import('../layouts/Authenticated.vue')
const AuthenticatedUserLayout = () => import('../layouts/AuthenticatedUser.vue')
const GuestLayout = () => import('../layouts/Guest.vue');
const PostsIndex = () => import('../views/admin/posts/Index.vue');
const PostsCreate = () => import('../views/admin/posts/Create.vue');
const PostsEdit = () => import('../views/admin/posts/Edit.vue');

async function requireLogin(to, from, next) {
    const auth = authStore();
    let isLogin = !!auth.authenticated;

    if (isLogin) {
        next()
    } else {
        next('/login')
    }
}

async function redirectToUserProfile(to, from, next) {
    const auth = authStore();
    const user = auth.user;
    const username = user ? user.username : null;

    next(`/profile/` + username);
}


function hasAdmin(roles) {
    for (let rol of roles) {
        if (rol.name && rol.name.toLowerCase().includes('admin')) {
            return true;
        }
    }
    return false;
}
async function guest(to, from, next) {
    const auth = authStore()

    let isLogin = !!auth.authenticated;

    if (isLogin) {
        next('/')
    } else {
        next()
    }
}

async function requireAdmin(to, from, next) {

    const auth = authStore();
    let isLogin = !!auth.authenticated;
    let user = auth.user;

    if (isLogin) {
        if (hasAdmin(user.roles)) {
            next()
        } else {
            next('/app')
        }
    } else {
        next('/login')
    }
}

async function redirectIfAuth(to, from, next) {
    const auth = authStore();
    let isLogin = !!auth.authenticated;

    if (isLogin) {
        next('/profile');
    } else {
        next();
    }
}
function redirectToHome(to, from, next) {
    next('/home');
}


export default [
    {
        path: '/',
        redirect: { name: 'login' },
        component: GuestLayout,
        children: [
            {
                path: '/',
                name: 'guestHome',
                component: () => import('../views/home/index.vue'),
                beforeEnter: redirectToHome,
            },       
            {
                path: '/testApi',
                name: 'testApi',

                component: () => import('../views/testApi/testApi.vue'),
            },
            {
                path: 'posts',
                name: 'public-posts.index',
                component: () => import('../views/posts/index.vue'),
            },
            {
                path: 'posts/:id',
                name: 'public-posts.details',
                component: () => import('../views/posts/details.vue'),
            },
            {
                path: 'category/:id',
                name: 'category-posts.index',
                component: () => import('../views/category/posts.vue'),
            },
            {
                path: 'login',
                name: 'auth.login',
                component: () => import('../views/login/Login.vue'),
                beforeEnter: redirectIfAuth
            },
            {
                path: 'register',
                name: 'auth.register',
                component: () => import('../views/register/index.vue'),
                beforeEnter: redirectIfAuth
            },
            {
                path: 'forgot-password',
                name: 'auth.forgot-password',
                component: () => import('../views/auth/passwords/Email.vue'),
                beforeEnter: redirectIfAuth
            },
            {
                path: 'reset-password/:token',
                name: 'auth.reset-password',
                component: () => import('../views/auth/passwords/Reset.vue'),
                beforeEnter: redirectIfAuth
            }
        ]
    },

    {
        path: '/',
        component: AuthenticatedUserLayout,
        name: 'app',
        beforeEnter: requireLogin,
        meta: { breadCrumb: 'Dashboard' },
        children: [     
            {
                path: '/home',
                name: 'home',
                component: () => import('../views/home/index.vue'),
            },
            {
                path: '/profile',
                name: 'profileRedirect',
                beforeEnter: redirectToUserProfile,
            },          
            {
                path: '/profile/:username',
                name: 'profile',
                component: () => import('../views/profile/profileView.vue'),
                props: true,
            },            
            {
                path: '/search',
                name: 'search',
                component: () => import('../views/search/searchView.vue'),
            },
            {
                path: '/friends',
                name: 'friends',
                component: () => import('../views/friends/friendsView.vue'),
            },
            {
                path: '/settings',
                name: 'settings',

                component: () => import('../views/settings/SettingsView.vue'),
            },
            {
                path: '/feed',
                name: 'feed',

                component: () => import('../views/feed/feedView.vue'),
            },
            {
                path: '/dummy',
                name: 'Dummy',
                component: {
                    template: '<div></div>' // un componente vacío o temporal
                }
            },
        ]
    },


    {
        path: '/admin',
        component: AuthenticatedLayout,
        redirect: {
            name: 'admin.index'
        },
        beforeEnter: requireAdmin,
        meta: { breadCrumb: 'Dashboard' },
        children: [
            {
                name: 'admin.index',
                path: '',
                component: () => import('../views/admin/index.vue'),
                meta: { breadCrumb: 'Admin' }
            },
            {
                name: 'profile.index',
                path: 'profile',
                component: () => import('../views/admin/profile/index.vue'),
                meta: { breadCrumb: 'Profile' }
            },
            {
                name: 'posts.index',
                path: 'posts',
                component: PostsIndex,
                meta: { breadCrumb: 'Posts' }
            },
            {
                name: 'posts.create',
                path: 'posts/create',
                component: PostsCreate,
                meta: { breadCrumb: 'Add new post' }
            },
            {
                name: 'posts.edit',
                path: 'posts/edit/:id',
                component: PostsEdit,
                meta: { breadCrumb: 'Edit post' }
            },
            {
                name: 'categories',
                path: 'categories',
                meta: { breadCrumb: 'Categories' },
                children: [
                    {
                        name: 'categories.index',
                        path: '',
                        component: () => import('../views/admin/categories/Index.vue'),
                        meta: { breadCrumb: 'View category' }
                    },
                    {
                        name: 'categories.create',
                        path: 'create',
                        component: () => import('../views/admin/categories/Create.vue'),
                        meta: {
                            breadCrumb: 'Add new category',
                            linked: false,
                        }
                    },
                    {
                        name: 'categories.edit',
                        path: 'edit/:id',
                        component: () => import('../views/admin/categories/Edit.vue'),
                        meta: {
                            breadCrumb: 'Edit category',
                            linked: false,
                        }
                    }
                ]
            },
            {
                name: 'permissions',
                path: 'permissions',
                meta: { breadCrumb: 'Permisos' },
                children: [
                    {
                        name: 'permissions.index',
                        path: '',
                        component: () => import('../views/admin/permissions/Index.vue'),
                        meta: { breadCrumb: 'Permissions' }
                    },
                    {
                        name: 'permissions.create',
                        path: 'create',
                        component: () => import('../views/admin/permissions/Create.vue'),
                        meta: {
                            breadCrumb: 'Create Permission',
                            linked: false,
                        }
                    },
                    {
                        name: 'permissions.edit',
                        path: 'edit/:id',
                        component: () => import('../views/admin/permissions/Edit.vue'),
                        meta: {
                            breadCrumb: 'Permission Edit',
                            linked: false,
                        }
                    }
                ]
            },
            {
                name: 'users',
                path: 'users',
                meta: { breadCrumb: 'Usuarios' },
                children: [
                    {
                        name: 'users.index',
                        path: '',
                        component: () => import('../views/admin/users/Index.vue'),
                        meta: { breadCrumb: 'Usuarios' }
                    },
                    {
                        name: 'users.create',
                        path: 'create',
                        component: () => import('../views/admin/users/Create.vue'),
                        meta: {
                            breadCrumb: 'Crear Usuario',
                            linked: false
                        }
                    },
                    {
                        name: 'users.edit',
                        path: 'edit/:id',
                        component: () => import('../views/admin/users/Edit.vue'),
                        meta: {
                            breadCrumb: 'Editar Usuario',
                            linked: false
                        }
                    }
                ]
            },
            {
                name: 'markers',
                path: 'markers',

                component: () => import('../views/admin/markers/Index.vue'),
            },
            {
                name: 'markersLists',
                path: 'markersLists',

                component: () => import('../views/admin/markersList/Index.vue'),
            },
            {
                name: 'markersLists.edit',
                path: 'markersLists/edit/:id',

                component: () => import('../views/admin/markersList/Edit.vue'),
                meta: { breadCrumb: 'Marker List Edit' }
            },
            {
                name: 'roles.index',
                path: 'roles',
                component: () => import('../views/admin/roles/Index.vue'),
                meta: { breadCrumb: 'Roles' }
            },
            {
                name: 'roles.create',
                path: 'roles/create',
                component: () => import('../views/admin/roles/Create.vue'),
                meta: { breadCrumb: 'Create Role' }
            },
            {
                name: 'roles.edit',
                path: 'roles/edit/:id',
                component: () => import('../views/admin/roles/Edit.vue'),
                meta: { breadCrumb: 'Role Edit' }
            },

        ]
    },
    {
        path: "/:pathMatch(.*)*",
        name: 'NotFound',
        component: () => import("../views/errors/404.vue"),
    },
];
