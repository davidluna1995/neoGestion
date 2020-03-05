

<template>
  <div>
    <section id="cover" class="min-vh-100">
      <div id="cover-caption">
        <div class="container">
          <div class="row text-white">
            <div class="col-xl-5 col-lg-6 col-md-8 col-sm-10 mx-auto form p-4 fondoLogin">
              <h1 class="display-6 text-center py-2">Ingrese Nueva Contraseña</h1>
              <div class="px-2">
                <div class="justify-content-center">
                  <div class="form-group">
                    <label>Correo Electronico.</label>
                    <input
                      v-model="email"
                      name="correo"
                      type="email"
                      class="form-control"
                      placeholder="Correo"
                    />

                    <label>Nueva Contraseña.</label>
                    <input
                      v-model="password"
                      name="password"
                      type="password"
                      class="form-control"
                      placeholder="******"
                    />

                    <label>Repetir Contraseña.</label>
                    <input
                      v-model="repetirPassword"
                      name="repetirPassword"
                      type="password"
                      class="form-control"
                      placeholder="******"
                    />
                  </div>
                  <div class="row justify-content-center">
                    <button type="submit" class="btn btn-primary btn-lg" @click="actualizarPassword()">Reestablecer</button>
                  </div>
                  <!-- {{token}} -->
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
      email: "",
      password: "",
      repetirPassword: "",
      token: this.$route.params.token
    };
  },

  methods: {
    actualizarPassword() {
      if (this.password == this.repetirPassword) {
        const data = {
          'email': this.email,
          'password': this.password,
          'resetToken': this.token
        };
        this.axios.post("api/resetPassword", data).then(response => {
          alert(response.data.mensaje);
        });
      }
      else{
          alert("las contraseñas no coinciden");
      }
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
