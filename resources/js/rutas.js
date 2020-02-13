import Outer from './components/outer.vue';
import HomeComponent from './components/Home.vue';
import NotFound from './components/404.vue';
import Auth from './components/auth/auth.vue';
import IndexComponent from './components/auth/inicio/inicio.vue';
import CategoriasComponent from './components/auth/categorias/categorias.vue';
import AgregarProductoComponent from './components/auth/agregarProducto/agregarProducto.vue';
import AdministrarProductoComponent from './components/auth/administrarProducto/administrarProducto.vue';
import VentasComponent from './components/auth/ventas/ventas.vue';
import generarVentaComponent from './components/auth/generarVenta/generarVenta.vue';

const routes = [
  {
    path: '/',
    component: Outer,
    name: 'Admin',
    redirect: 'home',
    iconCls: 'el-icon-message',
    meta: { auth: false },

    children: [
      {
        name: 'home',
        path: '/',
        component: HomeComponent
      },

    ]
  },

  {
    path: '/',
    component: Auth,
    name: 'Auth',
    redirect: 'index',
    iconCls: 'el-icon-message',
    meta: { auth: false },

    children: [
      {
        name: 'index',
        path: '/index',
        component: IndexComponent
      },
      {
        name: 'categorias',
        path: '/categorias',
        component: CategoriasComponent
      },
      {
        name: 'agregarProducto',
        path: '/agregarProducto',
        component: AgregarProductoComponent
      },
      {
        name: 'administrarProducto',
        path: '/administrarProducto',
        component: AdministrarProductoComponent
      },
      {
        name: 'ventas',
        path: '/ventas',
        component: VentasComponent
      },
      {
        name: 'generarVenta',
        path: '/generarVenta',
        component: generarVentaComponent
      },
    ]
  },

  {
    path: '/404',
    component: NotFound,
    name: '',
    hidden: true
  },

];

export default routes;