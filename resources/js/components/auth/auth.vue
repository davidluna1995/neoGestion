<template>
  <div>
    <b-navbar toggleable="lg" type="dark" variant="dark">
      <b-navbar-brand href="#">Neo-Gestion</b-navbar-brand>

      <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>

      <b-collapse id="nav-collapse" is-nav>
        <b-navbar-nav>
          <b-nav-item @click="url('index')">Panel de Control</b-nav-item>
          <b-nav-item @click="url('categorias')">Categorias</b-nav-item>
          <b-nav-item-dropdown text="Productos">
            <b-dropdown-item @click="url('administrarProducto')">Administrar Productos</b-dropdown-item>
            <b-dropdown-item @click="url('agregarProducto')">Agregar Productos</b-dropdown-item>
          </b-nav-item-dropdown>
          <!-- <b-nav-item-dropdown text="Ventas">
            <b-dropdown-item @click="url('#')">Administrar Ventas</b-dropdown-item>
            <b-dropdown-item @click="url('#')">Agregar Ventas</b-dropdown-item>
          </b-nav-item-dropdown> -->
          <b-nav-item-dropdown text="Ventas">
            <b-dropdown-item @click="url('ventas')">Visualizar Ventas</b-dropdown-item>
            <b-dropdown-item @click="url('#')">Reporte de Ventas</b-dropdown-item>
          </b-nav-item-dropdown>
        </b-navbar-nav>

        <!-- Right aligned nav items -->
        <b-navbar-nav class="ml-auto">
          <b-nav-item-dropdown right>
            <!-- Using 'button-content' slot -->
            <template v-slot:button-content>
              <em>NOMBRE DE USUARIO</em>
            </template>
            <b-dropdown-item href="#">Perfil</b-dropdown-item>
            <b-dropdown-item href="#">Cerrar Sesion</b-dropdown-item>
          </b-nav-item-dropdown>
        </b-navbar-nav>
      </b-collapse>
    </b-navbar>

    <transition name>
      <router-view></router-view>
    </transition>




  </div>
</template>

<script>
export default {
  data() {
      return{

      }
  },

  methods: {
    url(ruta) {
      this.$router.push({ path:ruta }).catch(error => {
        if (error.name != "NavigationDuplicated") {
          throw error;
        }
      });
    }
  }
};
</script>
