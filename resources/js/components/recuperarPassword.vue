<template>
  <div>
    <section id="cover" class="min-vh-100">
      <div id="cover-caption">
        <div class="container">
          <div class="row text-white">
            <div class="col-xl-5 col-lg-6 col-md-8 col-sm-10 mx-auto form p-4 fondoLogin">
              <h1 class="display-6 text-center py-2">Recuperar Contraseña</h1>
              <div class="px-2">
                <div class="justify-content-center">
                  <div class="form-group">
                    <label>Ingrese su correo, se le enviará un mensaje para restablecer su contraseña.</label>
                    <input
                      v-model="email"
                      name="correo"
                      type="text"
                      class="form-control"
                      placeholder="Correo"
                    />
                  </div>
                  <div class="row justify-content-center">
                    <button type="submit" class="btn btn-primary btn-lg" @click="sendEmail()">Enviar</button>
                  </div>
                  <a @click="url('/')">
                    <em style="cursor:pointer;" class="float-left">volver</em>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  data() {
    return {
      email: ""
    };
  },

  methods: {
    sendEmail() {
      const data = {
        email: this.email
      };
      this.axios.post("api/sendEmail", data).then(response => {
        alert(response.data.mensaje);
      });
    },
       
    url(ruta) {
      this.$router.push({ path: ruta }).catch(error => {
        if (error.name != "NavigationDuplicated") {
          throw error;
        }
      });
    }
  }
};
</script>

<style scoped>
#cover {
  /* background: #222 url('https://bonuscursos.com/wp-content/uploads/2019/12/Los-mejores-cursos-de-veterinaria.jpg') center center no-repeat; */
  background-size: cover;
  height: 100%;
  /* text-align: center; */
  display: flex;
  align-items: center;
  position: relative;
}
#cover-caption {
  width: 100%;
  position: relative;
  z-index: 1;
}
/* only used for background overlay not needed for centering */
.fondoLogin:before {
  content: "";
  height: 100%;
  left: 0;
  position: absolute;
  top: 0;
  width: 100%;
  background-color: rgba(0, 0, 0, 0.3);
  z-index: -1;
  border-radius: 10px;
}
</style>
